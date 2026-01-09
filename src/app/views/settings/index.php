<?php
/**
 * Settings View
 */
use Core\View;
use Core\Session;

$activeTab = $_GET['tab'] ?? 'profile';

View::section('content');
?>

<div class="max-w-4xl mx-auto">
    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="flex gap-6">
            <a href="?tab=profile" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'profile' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Hồ sơ
            </a>
            <a href="?tab=security" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'security' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Bảo mật
            </a>
            <a href="?tab=notifications" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'notifications' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Thông báo
            </a>
            <a href="?tab=appearance" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'appearance' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Giao diện
            </a>
        </nav>
    </div>

    <?php if ($activeTab === 'profile'): ?>
    <!-- Profile Settings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Thông tin cá nhân</h3>
            <p class="text-sm text-gray-500">Cập nhật thông tin hồ sơ của bạn</p>
        </div>
        
        <form method="POST" action="/php/api/update-profile.php" class="p-6 space-y-6">
            <input type="hidden" name="_token" value="<?= \Core\CSRF::generate() ?>">
            <!-- Avatar -->
            <div class="flex items-center gap-6">
                <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden" id="avatar-preview">
                    <?php if (!empty($user['avatar_url'])): ?>
                        <img src="/php/<?= View::e($user['avatar_url']) ?>" class="h-full w-full object-cover" id="avatar-img">
                    <?php else: ?>
                        <span class="text-2xl font-medium text-gray-600" id="avatar-initial">
                            <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <input type="file" id="avatar-input" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">
                    <button type="button" onclick="document.getElementById('avatar-input').click()" 
                            class="px-4 py-2 text-sm font-medium text-primary border border-primary rounded-lg hover:bg-primary/10">
                        Thay đổi ảnh
                    </button>
                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP. Tối đa 2MB</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                    <input type="text" name="full_name" value="<?= View::e($user['full_name'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= View::e($user['email'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chức danh</label>
                    <input type="text" name="position" value="<?= View::e($user['position'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                    <input type="text" name="department" value="<?= View::e($user['department'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

    <?php elseif ($activeTab === 'security'): ?>
    <!-- Security Settings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Đổi mật khẩu</h3>
            <p class="text-sm text-gray-500">Cập nhật mật khẩu tài khoản của bạn</p>
        </div>
        
        <form method="POST" action="/php/api/change-password.php" class="p-6 space-y-4">
            <input type="hidden" name="_token" value="<?= \Core\CSRF::generate() ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required
                       class="w-full max-w-md rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                <input type="password" name="new_password" required minlength="6"
                       class="w-full max-w-md rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <p class="text-xs text-gray-500 mt-1">Tối thiểu 6 ký tự</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" required
                       class="w-full max-w-md rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>
            <div class="pt-4">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">
                    Cập nhật mật khẩu
                </button>
            </div>
        </form>
    </div>

    <?php elseif ($activeTab === 'notifications'): ?>
    <!-- Notification Settings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Cài đặt thông báo</h3>
            <p class="text-sm text-gray-500">Quản lý cách bạn nhận thông báo</p>
        </div>
        
        <form method="POST" action="/php/api/update-settings.php" class="p-6 space-y-4">
            <input type="hidden" name="_token" value="<?= \Core\CSRF::generate() ?>">
            <?php 
            $options = [
                ['id' => 'task_assigned', 'key' => 'notification_task_assigned', 'title' => 'Được giao công việc', 'desc' => 'Khi bạn được giao một công việc mới'],
                ['id' => 'task_due', 'key' => 'notification_task_due', 'title' => 'Sắp đến hạn', 'desc' => 'Nhắc nhở trước khi công việc đến hạn'],
                ['id' => 'comment', 'key' => 'notification_comment', 'title' => 'Bình luận mới', 'desc' => 'Khi có người bình luận vào công việc của bạn'],
                ['id' => 'mention', 'key' => 'notification_mention', 'title' => 'Được nhắc đến', 'desc' => 'Khi có người nhắc đến bạn'],
            ];
            ?>
            
            <?php foreach ($options as $opt): ?>
            <?php $isChecked = ($settings[$opt['key']] ?? '1') == '1'; ?>
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <h4 class="font-medium text-gray-900"><?= $opt['title'] ?></h4>
                    <p class="text-sm text-gray-500"><?= $opt['desc'] ?></p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="<?= $opt['id'] ?>" value="1" <?= $isChecked ? 'checked' : '' ?> class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            <?php endforeach; ?>
            
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">
                    Lưu cài đặt
                </button>
            </div>
        </form>
    </div>

    <?php elseif ($activeTab === 'appearance'): ?>
    <!-- Appearance Settings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Giao diện</h3>
            <p class="text-sm text-gray-500">Tùy chỉnh giao diện ứng dụng</p>
        </div>
        
        <form method="POST" action="/php/api/update-settings.php" class="p-6 space-y-6">
            <input type="hidden" name="_token" value="<?= \Core\CSRF::generate() ?>">
            <?php $currentTheme = $settings['theme'] ?? 'light'; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Chế độ màu</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-primary <?= $currentTheme === 'light' ? 'border-primary bg-primary/5' : 'border-gray-200' ?>">
                        <input type="radio" name="theme" value="light" <?= $currentTheme === 'light' ? 'checked' : '' ?> class="text-primary focus:ring-primary">
                        <div class="flex items-center gap-2">
                            <i data-lucide="sun" class="h-5 w-5 text-yellow-500"></i>
                            <span class="font-medium">Sáng</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-primary <?= $currentTheme === 'dark' ? 'border-primary bg-primary/5' : 'border-gray-200' ?>">
                        <input type="radio" name="theme" value="dark" <?= $currentTheme === 'dark' ? 'checked' : '' ?> class="text-primary focus:ring-primary">
                        <div class="flex items-center gap-2">
                            <i data-lucide="moon" class="h-5 w-5 text-gray-600"></i>
                            <span class="font-medium">Tối</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 border rounded-lg cursor-pointer hover:border-primary <?= $currentTheme === 'system' ? 'border-primary bg-primary/5' : 'border-gray-200' ?>">
                        <input type="radio" name="theme" value="system" <?= $currentTheme === 'system' ? 'checked' : '' ?> class="text-primary focus:ring-primary">
                        <div class="flex items-center gap-2">
                            <i data-lucide="monitor" class="h-5 w-5 text-gray-600"></i>
                            <span class="font-medium">Hệ thống</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <?php $currentLang = $settings['language'] ?? 'vi'; ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngôn ngữ</label>
                <select name="language" class="w-full max-w-xs rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option value="vi" <?= $currentLang === 'vi' ? 'selected' : '' ?>>Tiếng Việt</option>
                    <option value="en" <?= $currentLang === 'en' ? 'selected' : '' ?>>English</option>
                </select>
            </div>
            
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">
                    Lưu cài đặt
                </button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
// Upload Avatar
document.getElementById('avatar-input')?.addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    
    // Validate
    if (file.size > 2 * 1024 * 1024) {
        alert('File ảnh không được vượt quá 2MB');
        return;
    }
    
    const formData = new FormData();
    formData.append('avatar', file);
    
    fetch('/php/api/upload-avatar.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Update preview
            const preview = document.getElementById('avatar-preview');
            const initial = document.getElementById('avatar-initial');
            if (initial) initial.remove();
            
            let img = document.getElementById('avatar-img');
            if (!img) {
                img = document.createElement('img');
                img.id = 'avatar-img';
                img.className = 'h-full w-full object-cover';
                preview.appendChild(img);
            }
            img.src = '/php/' + data.url + '?t=' + Date.now();
            
            alert('Cập nhật ảnh đại diện thành công!');
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
});

// Theme switcher
document.querySelectorAll('input[name="theme"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const theme = this.value;
        if (theme === 'dark') {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else if (theme === 'light') {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            localStorage.removeItem('theme');
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    });
});

// Load current theme setting
const currentTheme = '<?= View::e($settings['theme'] ?? 'system') ?>';
const themeRadio = document.querySelector(`input[name="theme"][value="${currentTheme}"]`);
if (themeRadio) themeRadio.checked = true;
</script>

<?php View::endSection(); ?>
