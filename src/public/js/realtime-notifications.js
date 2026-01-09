/**
 * Real-time Notifications using Server-Sent Events (SSE)
 * 
 * Tự động kết nối đến server và nhận notifications real-time
 * Hỗ trợ reconnection tự động khi mất kết nối
 */

class RealtimeNotifications {
    constructor(options = {}) {
        // Use /php as base path
        this.endpoint = options.endpoint || '/php/api/sse-notifications.php';
        this.onNotification = options.onNotification || this.defaultNotificationHandler;
        this.onUnreadCount = options.onUnreadCount || this.defaultUnreadCountHandler;
        this.onConnect = options.onConnect || (() => {});
        this.onDisconnect = options.onDisconnect || (() => {});
        this.onError = options.onError || (() => {});
        
        this.eventSource = null;
        this.lastEventId = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 10;
        this.reconnectDelay = 3000; // 3 seconds
        this.isConnected = false;
        
        // Auto-connect if enabled
        if (options.autoConnect !== false) {
            this.connect();
        }
    }
    
    /**
     * Connect to SSE endpoint
     */
    connect() {
        if (this.eventSource) {
            this.disconnect();
        }
        
        let url = this.endpoint;
        if (this.lastEventId) {
            url += `?lastEventId=${encodeURIComponent(this.lastEventId)}`;
        }
        
        try {
            this.eventSource = new EventSource(url);
            
            // Connection opened
            this.eventSource.addEventListener('connected', (event) => {
                this.isConnected = true;
                this.reconnectAttempts = 0;
                const data = JSON.parse(event.data);
                console.log('[SSE] Connected:', data.message);
                this.onConnect(data);
            });
            
            // New notification received
            this.eventSource.addEventListener('notification', (event) => {
                if (event.lastEventId) {
                    this.lastEventId = event.lastEventId;
                }
                const notification = JSON.parse(event.data);
                this.onNotification(notification);
            });
            
            // Unread count update
            this.eventSource.addEventListener('unread_count', (event) => {
                const data = JSON.parse(event.data);
                this.onUnreadCount(data.count);
            });
            
            // Error event
            this.eventSource.addEventListener('error', (event) => {
                if (event.data) {
                    const data = JSON.parse(event.data);
                    console.error('[SSE] Server error:', data.error);
                    this.onError(data);
                }
            });
            
            // Connection error (network issues, server down, etc.)
            this.eventSource.onerror = (error) => {
                console.warn('[SSE] Connection error, will retry...');
                this.isConnected = false;
                this.onDisconnect();
                
                // EventSource will auto-reconnect, but we track attempts
                this.reconnectAttempts++;
                
                if (this.reconnectAttempts >= this.maxReconnectAttempts) {
                    console.error('[SSE] Max reconnect attempts reached, stopping');
                    this.disconnect();
                    this.onError({ message: 'Max reconnect attempts reached' });
                }
            };
            
            // Connection opened successfully
            this.eventSource.onopen = () => {
                console.log('[SSE] Connection opened');
            };
            
        } catch (error) {
            console.error('[SSE] Failed to create EventSource:', error);
            this.scheduleReconnect();
        }
    }
    
    /**
     * Disconnect from SSE
     */
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        this.isConnected = false;
    }
    
    /**
     * Schedule reconnection
     */
    scheduleReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            const delay = this.reconnectDelay * Math.pow(1.5, this.reconnectAttempts);
            console.log(`[SSE] Reconnecting in ${delay}ms...`);
            setTimeout(() => this.connect(), delay);
            this.reconnectAttempts++;
        }
    }
    
    /**
     * Default notification handler - shows toast and updates UI
     */
    defaultNotificationHandler(notification) {
        console.log('[SSE] New notification:', notification);
        
        // Show toast notification
        if (window.showToast) {
            window.showToast(notification.title, notification.message, 'info');
        }
        
        // Play notification sound if available
        this.playNotificationSound();
        
        // Show browser notification if permitted
        this.showBrowserNotification(notification);
        
        // Add to notification list if element exists
        this.addToNotificationList(notification);
    }
    
    /**
     * Default unread count handler - updates badge
     */
    defaultUnreadCountHandler(count) {
        // Update notification badge
        const badges = document.querySelectorAll('[data-notification-count]');
        badges.forEach(badge => {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
        
        // Update page title
        if (count > 0) {
            document.title = `(${count}) ${document.title.replace(/^\(\d+\)\s*/, '')}`;
        } else {
            document.title = document.title.replace(/^\(\d+\)\s*/, '');
        }
    }
    
    /**
     * Play notification sound
     */
    playNotificationSound() {
        try {
            // Check if user has interacted with page (required for audio)
            const audio = new Audio('/public/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play().catch(() => {
                // Ignore autoplay errors
            });
        } catch (e) {
            // Ignore audio errors
        }
    }
    
    /**
     * Show browser notification
     */
    showBrowserNotification(notification) {
        if (!('Notification' in window)) return;
        
        if (Notification.permission === 'granted') {
            const browserNotif = new Notification(notification.title, {
                body: notification.message,
                icon: '/public/images/logo-icon.png',
                tag: notification.id,
                data: { link: notification.link }
            });
            
            browserNotif.onclick = () => {
                window.focus();
                if (notification.link) {
                    window.location.href = notification.link;
                }
                browserNotif.close();
            };
            
            // Auto close after 5 seconds
            setTimeout(() => browserNotif.close(), 5000);
            
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission();
        }
    }
    
    /**
     * Add notification to dropdown list
     */
    addToNotificationList(notification) {
        const list = document.querySelector('[data-notification-list]');
        if (!list) return;
        
        // Remove "no notifications" message if exists
        const emptyMsg = list.querySelector('.empty-notifications');
        if (emptyMsg) emptyMsg.remove();
        
        // Create notification item
        const item = document.createElement('div');
        item.className = 'notification-item unread p-3 border-b hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer';
        item.dataset.notificationId = notification.id;
        
        const timeAgo = this.formatTimeAgo(new Date(notification.createdAt));
        
        item.innerHTML = `
            <div class="flex items-start gap-3">
                ${notification.actor?.avatar 
                    ? `<img src="${notification.actor.avatar}" class="w-8 h-8 rounded-full" alt="">`
                    : `<div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                         <i data-lucide="bell" class="w-4 h-4 text-blue-600"></i>
                       </div>`
                }
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm text-gray-900 dark:text-white">${notification.title}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate">${notification.message}</p>
                    <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                </div>
                <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2"></div>
            </div>
        `;
        
        // Add click handler
        item.addEventListener('click', () => {
            if (notification.link) {
                window.location.href = notification.link;
            }
        });
        
        // Insert at top of list
        list.insertBefore(item, list.firstChild);
        
        // Re-initialize Lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
    
    /**
     * Format time ago
     */
    formatTimeAgo(date) {
        const seconds = Math.floor((new Date() - date) / 1000);
        
        if (seconds < 60) return 'Vừa xong';
        if (seconds < 3600) return `${Math.floor(seconds / 60)} phút trước`;
        if (seconds < 86400) return `${Math.floor(seconds / 3600)} giờ trước`;
        if (seconds < 604800) return `${Math.floor(seconds / 86400)} ngày trước`;
        
        return date.toLocaleDateString('vi-VN');
    }
    
    /**
     * Request browser notification permission
     */
    static requestPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
}

// SSE Real-time notifications - DISABLED
// Reason: Causes session conflicts with PHP session handling
// The system uses polling instead (in header.php)
// To enable in future: need to implement separate session handling for SSE

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RealtimeNotifications;
}
