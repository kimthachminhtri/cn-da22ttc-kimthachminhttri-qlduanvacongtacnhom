<?php
/**
 * Core Mailer Class
 * Gửi email sử dụng PHP mail() hoặc SMTP
 * 
 * Hỗ trợ:
 * - PHP mail() function (mặc định)
 * - SMTP với authentication
 * - HTML emails
 * - Template-based emails
 */

namespace Core;

class Mailer
{
    private static ?array $config = null;
    
    /**
     * Load mail configuration
     */
    private static function loadConfig(): array
    {
        if (self::$config === null) {
            $configFile = BASE_PATH . '/config/mail.php';
            if (file_exists($configFile)) {
                self::$config = require $configFile;
            } else {
                // Default configuration
                self::$config = [
                    'driver' => 'mail', // mail, smtp, log
                    'from_email' => 'noreply@taskflow.local',
                    'from_name' => 'TaskFlow',
                    'smtp' => [
                        'host' => 'localhost',
                        'port' => 587,
                        'username' => '',
                        'password' => '',
                        'encryption' => 'tls', // tls, ssl, none
                    ],
                ];
            }
        }
        return self::$config;
    }
    
    /**
     * Send email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param array $options Additional options
     * @return bool Success status
     */
    public static function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $config = self::loadConfig();
        $driver = $config['driver'] ?? 'mail';
        
        $fromEmail = $options['from_email'] ?? $config['from_email'];
        $fromName = $options['from_name'] ?? $config['from_name'];
        
        switch ($driver) {
            case 'smtp':
                return self::sendViaSMTP($to, $subject, $body, $fromEmail, $fromName, $config['smtp']);
            case 'log':
                return self::sendViaLog($to, $subject, $body, $fromEmail, $fromName);
            case 'mail':
            default:
                return self::sendViaMail($to, $subject, $body, $fromEmail, $fromName);
        }
    }

    /**
     * Send via PHP mail() function
     */
    private static function sendViaMail(string $to, string $subject, string $body, string $fromEmail, string $fromName): bool
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            "From: {$fromName} <{$fromEmail}>",
            "Reply-To: {$fromEmail}",
            'X-Mailer: PHP/' . phpversion(),
        ];
        
        $result = @mail($to, $subject, $body, implode("\r\n", $headers));
        
        if ($result) {
            Logger::info('Email sent via mail()', ['to' => $to, 'subject' => $subject]);
        } else {
            Logger::error('Failed to send email via mail()', ['to' => $to, 'subject' => $subject]);
        }
        
        return $result;
    }
    
    /**
     * Send via SMTP
     */
    private static function sendViaSMTP(string $to, string $subject, string $body, string $fromEmail, string $fromName, array $smtp): bool
    {
        try {
            $host = $smtp['host'];
            $port = $smtp['port'];
            $username = $smtp['username'];
            $password = $smtp['password'];
            $encryption = $smtp['encryption'] ?? 'tls';
            
            // Connect to SMTP server
            $prefix = $encryption === 'ssl' ? 'ssl://' : '';
            $socket = @fsockopen($prefix . $host, $port, $errno, $errstr, 30);
            
            if (!$socket) {
                Logger::error('SMTP connection failed', ['host' => $host, 'error' => $errstr]);
                return false;
            }
            
            // Read greeting
            self::smtpRead($socket);
            
            // EHLO
            self::smtpWrite($socket, "EHLO " . gethostname());
            self::smtpRead($socket);
            
            // STARTTLS if needed
            if ($encryption === 'tls') {
                self::smtpWrite($socket, "STARTTLS");
                self::smtpRead($socket);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                self::smtpWrite($socket, "EHLO " . gethostname());
                self::smtpRead($socket);
            }
            
            // AUTH LOGIN
            if (!empty($username)) {
                self::smtpWrite($socket, "AUTH LOGIN");
                self::smtpRead($socket);
                self::smtpWrite($socket, base64_encode($username));
                self::smtpRead($socket);
                self::smtpWrite($socket, base64_encode($password));
                $authResponse = self::smtpRead($socket);
                if (strpos($authResponse, '235') === false) {
                    Logger::error('SMTP authentication failed');
                    fclose($socket);
                    return false;
                }
            }
            
            // MAIL FROM
            self::smtpWrite($socket, "MAIL FROM:<{$fromEmail}>");
            self::smtpRead($socket);
            
            // RCPT TO
            self::smtpWrite($socket, "RCPT TO:<{$to}>");
            self::smtpRead($socket);
            
            // DATA
            self::smtpWrite($socket, "DATA");
            self::smtpRead($socket);
            
            // Email content
            $message = "From: {$fromName} <{$fromEmail}>\r\n";
            $message .= "To: {$to}\r\n";
            $message .= "Subject: {$subject}\r\n";
            $message .= "MIME-Version: 1.0\r\n";
            $message .= "Content-Type: text/html; charset=UTF-8\r\n";
            $message .= "\r\n";
            $message .= $body;
            $message .= "\r\n.";
            
            self::smtpWrite($socket, $message);
            self::smtpRead($socket);
            
            // QUIT
            self::smtpWrite($socket, "QUIT");
            fclose($socket);
            
            Logger::info('Email sent via SMTP', ['to' => $to, 'subject' => $subject]);
            return true;
            
        } catch (\Exception $e) {
            Logger::error('SMTP error', ['error' => $e->getMessage()]);
            return false;
        }
    }
    
    private static function smtpWrite($socket, string $data): void
    {
        fwrite($socket, $data . "\r\n");
    }
    
    private static function smtpRead($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') break;
        }
        return $response;
    }
    
    /**
     * Send via Log (for development/testing)
     */
    private static function sendViaLog(string $to, string $subject, string $body, string $fromEmail, string $fromName): bool
    {
        Logger::info('Email logged (not sent)', [
            'to' => $to,
            'from' => "{$fromName} <{$fromEmail}>",
            'subject' => $subject,
            'body_preview' => substr(strip_tags($body), 0, 200),
        ]);
        return true;
    }

    /**
     * Send password reset email
     */
    public static function sendPasswordReset(string $to, string $userName, string $resetUrl): bool
    {
        $subject = 'Đặt lại mật khẩu - TaskFlow';
        
        $body = self::getEmailTemplate('password_reset', [
            'user_name' => $userName,
            'reset_url' => $resetUrl,
            'expiry_time' => '1 giờ',
        ]);
        
        return self::send($to, $subject, $body);
    }
    
    /**
     * Send task assignment notification
     */
    public static function sendTaskAssigned(string $to, string $userName, array $task, string $assignerName): bool
    {
        $subject = "Bạn được giao công việc mới: {$task['title']} - TaskFlow";
        
        $body = self::getEmailTemplate('task_assigned', [
            'user_name' => $userName,
            'task_title' => $task['title'],
            'task_description' => $task['description'] ?? '',
            'task_due_date' => $task['due_date'] ?? 'Không có',
            'task_priority' => $task['priority'] ?? 'medium',
            'assigner_name' => $assignerName,
            'task_url' => "/php/task-detail.php?id={$task['id']}",
        ]);
        
        return self::send($to, $subject, $body);
    }
    
    /**
     * Send task deadline reminder
     */
    public static function sendDeadlineReminder(string $to, string $userName, array $task): bool
    {
        $subject = "Nhắc nhở: Công việc \"{$task['title']}\" sắp đến hạn - TaskFlow";
        
        $body = self::getEmailTemplate('deadline_reminder', [
            'user_name' => $userName,
            'task_title' => $task['title'],
            'task_due_date' => $task['due_date'],
            'task_url' => "/php/task-detail.php?id={$task['id']}",
        ]);
        
        return self::send($to, $subject, $body);
    }
    
    /**
     * Send welcome email
     */
    public static function sendWelcome(string $to, string $userName): bool
    {
        $subject = 'Chào mừng bạn đến với TaskFlow!';
        
        $body = self::getEmailTemplate('welcome', [
            'user_name' => $userName,
            'login_url' => '/php/login.php',
        ]);
        
        return self::send($to, $subject, $body);
    }
    
    /**
     * Get email template
     */
    private static function getEmailTemplate(string $template, array $data): string
    {
        $templateFile = BASE_PATH . "/app/views/emails/{$template}.php";
        
        if (file_exists($templateFile)) {
            ob_start();
            extract($data);
            include $templateFile;
            return ob_get_clean();
        }
        
        // Fallback to inline templates
        return self::getInlineTemplate($template, $data);
    }
    
    /**
     * Inline email templates (fallback)
     */
    private static function getInlineTemplate(string $template, array $data): string
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost/php';
        
        $header = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #3b82f6; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
                .button { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
            </style>
        </head>
        <body>
        <div class="container">
            <div class="header">
                <h1 style="margin:0;">TaskFlow</h1>
            </div>
            <div class="content">';
        
        $footer = '
            </div>
            <div class="footer">
                <p>Email này được gửi tự động từ TaskFlow.</p>
                <p>© ' . date('Y') . ' TaskFlow. All rights reserved.</p>
            </div>
        </div>
        </body>
        </html>';
        
        switch ($template) {
            case 'password_reset':
                $content = "
                <h2>Xin chào {$data['user_name']},</h2>
                <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản TaskFlow của mình.</p>
                <p>Nhấn vào nút bên dưới để đặt lại mật khẩu:</p>
                <p style='text-align:center;'>
                    <a href='{$baseUrl}{$data['reset_url']}' class='button'>Đặt lại mật khẩu</a>
                </p>
                <p><strong>Lưu ý:</strong> Link này sẽ hết hạn sau {$data['expiry_time']}.</p>
                <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                <hr style='border:none;border-top:1px solid #e5e7eb;margin:20px 0;'>
                <p style='font-size:12px;color:#6b7280;'>Nếu nút không hoạt động, copy link sau vào trình duyệt:<br>
                <code>{$baseUrl}{$data['reset_url']}</code></p>";
                break;
                
            case 'task_assigned':
                $priorityColors = ['low' => '#22c55e', 'medium' => '#3b82f6', 'high' => '#f97316', 'urgent' => '#ef4444'];
                $priorityNames = ['low' => 'Thấp', 'medium' => 'Trung bình', 'high' => 'Cao', 'urgent' => 'Khẩn cấp'];
                $priority = $data['task_priority'];
                $content = "
                <h2>Xin chào {$data['user_name']},</h2>
                <p><strong>{$data['assigner_name']}</strong> đã giao cho bạn một công việc mới:</p>
                <div style='background:white;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:20px 0;'>
                    <h3 style='margin-top:0;'>{$data['task_title']}</h3>
                    <p>{$data['task_description']}</p>
                    <p><strong>Hạn hoàn thành:</strong> {$data['task_due_date']}</p>
                    <p><strong>Độ ưu tiên:</strong> <span style='color:{$priorityColors[$priority]};'>{$priorityNames[$priority]}</span></p>
                </div>
                <p style='text-align:center;'>
                    <a href='{$baseUrl}{$data['task_url']}' class='button'>Xem chi tiết</a>
                </p>";
                break;
                
            case 'deadline_reminder':
                $content = "
                <h2>Xin chào {$data['user_name']},</h2>
                <p>Đây là nhắc nhở về công việc sắp đến hạn:</p>
                <div style='background:#fef3c7;border:1px solid #f59e0b;border-radius:8px;padding:20px;margin:20px 0;'>
                    <h3 style='margin-top:0;color:#92400e;'>⏰ {$data['task_title']}</h3>
                    <p><strong>Hạn hoàn thành:</strong> {$data['task_due_date']}</p>
                </div>
                <p style='text-align:center;'>
                    <a href='{$baseUrl}{$data['task_url']}' class='button'>Xem công việc</a>
                </p>";
                break;
                
            case 'welcome':
                $content = "
                <h2>Chào mừng {$data['user_name']}! 🎉</h2>
                <p>Cảm ơn bạn đã đăng ký tài khoản TaskFlow.</p>
                <p>TaskFlow giúp bạn quản lý công việc và dự án một cách hiệu quả với các tính năng:</p>
                <ul>
                    <li>📋 Quản lý dự án và công việc</li>
                    <li>👥 Làm việc nhóm hiệu quả</li>
                    <li>📅 Lịch và nhắc nhở deadline</li>
                    <li>📊 Báo cáo và thống kê</li>
                </ul>
                <p style='text-align:center;'>
                    <a href='{$baseUrl}{$data['login_url']}' class='button'>Đăng nhập ngay</a>
                </p>";
                break;
                
            default:
                $content = "<p>Nội dung email</p>";
        }
        
        return $header . $content . $footer;
    }
}
