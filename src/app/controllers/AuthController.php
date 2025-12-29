<?php
/**
 * Auth Controller
 */

namespace App\Controllers;

use App\Models\User;
use Core\Session;
use Core\Logger;
use Core\RateLimiter;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        if ($this->userId()) {
            $this->redirect('/php/index.php');
        }
        
        $this->view('auth/login', [], 'guest');
    }

    public function login(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateLimitKey = "login:{$ip}";
        
        // Check rate limit (5 attempts per minute)
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5, 1)) {
            $seconds = RateLimiter::availableIn($rateLimitKey, 1);
            $this->error("Quá nhiều lần thử. Vui lòng đợi {$seconds} giây.");
            Logger::warning('Login rate limited', ['ip' => $ip]);
            $this->redirect('/php/login.php');
            return;
        }
        
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->redirect('/php/login.php');
            return;
        }

        $email = $this->input('email');
        $password = $this->input('password');
        $remember = $this->input('remember') === 'on';

        $user = $this->userModel->verifyPassword($email, $password);

        if (!$user) {
            RateLimiter::hit($rateLimitKey);
            Logger::warning('Failed login attempt', ['email' => $email, 'ip' => $ip]);
            $this->error('Email hoặc mật khẩu không đúng');
            $this->redirect('/php/login.php');
            return;
        }

        if (!$user['is_active']) {
            Logger::warning('Login attempt on inactive account', ['email' => $email]);
            $this->error('Tài khoản đã bị vô hiệu hóa');
            $this->redirect('/php/login.php');
            return;
        }

        // Clear rate limit on successful login
        RateLimiter::clear($rateLimitKey);
        
        // Debug: Log user role
        Logger::info('User data from DB', [
            'user_id' => $user['id'], 
            'email' => $email,
            'role' => $user['role'] ?? 'NULL',
            'role_type' => gettype($user['role'] ?? null)
        ]);
        
        // Set session
        Session::set('user_id', $user['id']);
        Session::set('user_role', $user['role'] ?? 'member'); // Default to member if null
        Session::set('user_name', $user['full_name']);
        Session::regenerate();

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Remember me
        if ($remember) {
            $this->setRememberToken($user['id']);
        }

        Logger::info('User logged in', ['user_id' => $user['id'], 'email' => $email]);
        $this->success('Đăng nhập thành công');
        
        // Redirect based on role
        $intended = Session::get('intended_url');
        Session::remove('intended_url');
        
        if ($intended) {
            // If there's an intended URL, go there
            $this->redirect($intended);
        } elseif ($user['role'] === 'admin') {
            // Admin goes to Admin Panel
            $this->redirect('/php/admin/index.php');
        } else {
            // Others go to dashboard
            $this->redirect('/php/index.php');
        }
    }

    public function showRegister(): void
    {
        if ($this->userId()) {
            $this->redirect('/php/index.php');
        }
        
        $this->view('auth/register', [], 'guest');
    }

    public function register(): void
    {
        $errors = $this->validate([
            'full_name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->redirect('/php/register.php');
            return;
        }

        $email = $this->input('email');
        
        // Check email exists
        if ($this->userModel->findByEmail($email)) {
            $this->error('Email đã được sử dụng');
            $this->redirect('/php/register.php');
            return;
        }

        // Create user
        $userId = $this->userModel->createUser([
            'email' => $email,
            'full_name' => $this->input('full_name'),
            'password' => $this->input('password'),
            'role' => 'member',
            'is_active' => 1,
        ]);

        if ($userId) {
            $this->success('Đăng ký thành công! Vui lòng đăng nhập.');
            $this->redirect('/php/login.php');
        } else {
            $this->error('Có lỗi xảy ra, vui lòng thử lại');
            $this->redirect('/php/register.php');
        }
    }

    public function logout(): void
    {
        $userId = $this->userId();
        $this->clearRememberToken();
        
        Logger::info('User logged out', ['user_id' => $userId]);
        
        Session::destroy();
        $this->redirect('/php/login.php');
    }

    private function setRememberToken(string $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60);

        $this->userModel->update($userId, [
            'remember_token' => hash('sha256', $token),
            'remember_token_expiry' => date('Y-m-d H:i:s', $expiry),
        ]);

        setcookie('remember_token', $token, $expiry, '/', '', false, true);
    }

    private function clearRememberToken(): void
    {
        if ($this->userId()) {
            $this->userModel->update($this->userId(), [
                'remember_token' => null,
                'remember_token_expiry' => null,
            ]);
        }
        setcookie('remember_token', '', time() - 3600, '/');
    }
}
