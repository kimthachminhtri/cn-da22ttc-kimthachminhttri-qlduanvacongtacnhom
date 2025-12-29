<?php
/**
 * Settings Controller
 */

namespace App\Controllers;

use App\Models\User;
use App\Middleware\AuthMiddleware;

class SettingsController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $user = $this->userModel->find($this->userId());
        $settings = $this->userModel->getUserSettings($this->userId());
        
        $this->view('settings/index', [
            'user' => $user,
            'settings' => $settings,
            'pageTitle' => 'Cài đặt',
        ]);
    }

    public function updateProfile(): void
    {
        $data = [
            'full_name' => $this->input('full_name'),
            'email' => $this->input('email'),
            'position' => $this->input('position'),
            'department' => $this->input('department'),
            'phone' => $this->input('phone'),
        ];

        $errors = $this->validate([
            'full_name' => 'required|min:2',
            'email' => 'required|email',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->back();
            return;
        }

        $this->userModel->update($this->userId(), $data);
        $this->success('Cập nhật hồ sơ thành công');
        $this->redirect('/php/settings.php?tab=profile');
    }

    public function changePassword(): void
    {
        $current = $this->input('current_password');
        $new = $this->input('new_password');
        $confirm = $this->input('confirm_password');

        if (empty($current) || empty($new) || empty($confirm)) {
            $this->error('Vui lòng nhập đầy đủ thông tin');
            $this->back();
            return;
        }

        if ($new !== $confirm) {
            $this->error('Mật khẩu xác nhận không khớp');
            $this->back();
            return;
        }

        if (strlen($new) < 6) {
            $this->error('Mật khẩu mới phải có ít nhất 6 ký tự');
            $this->back();
            return;
        }

        $result = $this->userModel->changePassword($this->userId(), $current, $new);
        
        if ($result['success']) {
            $this->success($result['message']);
        } else {
            $this->error($result['message']);
        }
        
        $this->redirect('/php/settings.php?tab=security');
    }

    public function updateSettings(): void
    {
        $settings = [];
        
        // Theme
        if ($this->input('theme')) {
            $settings['theme'] = $this->input('theme');
        }
        
        // Language
        if ($this->input('language')) {
            $settings['language'] = $this->input('language');
        }
        
        // Notifications
        $settings['notification_task_assigned'] = $this->input('task_assigned') ? '1' : '0';
        $settings['notification_task_due'] = $this->input('task_due') ? '1' : '0';
        $settings['notification_comment'] = $this->input('comment') ? '1' : '0';
        $settings['notification_mention'] = $this->input('mention') ? '1' : '0';

        $this->userModel->updateUserSettings($this->userId(), $settings);
        $this->success('Cập nhật cài đặt thành công');
        $this->back();
    }

    public function uploadAvatar(): void
    {
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'error' => 'Vui lòng chọn file ảnh'], 400);
            return;
        }

        $result = $this->userModel->uploadAvatar($this->userId(), $_FILES['avatar']);
        
        if ($result['success']) {
            $this->json(['success' => true, 'url' => $result['url']]);
        } else {
            $this->json(['success' => false, 'error' => $result['message']], 400);
        }
    }
}
