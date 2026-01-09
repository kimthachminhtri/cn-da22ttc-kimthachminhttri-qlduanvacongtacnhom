-- =============================================
-- TASKFLOW DATABASE INDEXES OPTIMIZATION
-- Version: 1.0
-- Mục đích: Thêm composite indexes để tối ưu performance
-- =============================================

-- =============================================
-- 1. TASKS - Composite indexes cho các queries phổ biến
-- =============================================

-- Index cho query lấy tasks theo project và status (Kanban board)
CREATE INDEX IF NOT EXISTS `idx_tasks_project_status` 
ON `tasks` (`project_id`, `status`, `position`);

-- Index cho query lấy tasks theo user và due_date (My Tasks, Calendar)
CREATE INDEX IF NOT EXISTS `idx_tasks_due_date_status` 
ON `tasks` (`due_date`, `status`);

-- Index cho query lấy overdue tasks
CREATE INDEX IF NOT EXISTS `idx_tasks_overdue` 
ON `tasks` (`status`, `due_date`, `priority`);

-- Index cho query search tasks
CREATE INDEX IF NOT EXISTS `idx_tasks_created_at_desc` 
ON `tasks` (`created_at` DESC);

-- =============================================
-- 2. PROJECT_MEMBERS - Composite indexes
-- =============================================

-- Index cho query kiểm tra quyền user trong project
CREATE INDEX IF NOT EXISTS `idx_pm_user_role` 
ON `project_members` (`user_id`, `role`);

-- =============================================
-- 3. TASK_ASSIGNEES - Composite indexes
-- =============================================

-- Index cho query lấy tasks của user
CREATE INDEX IF NOT EXISTS `idx_ta_user_task` 
ON `task_assignees` (`user_id`, `task_id`);

-- =============================================
-- 4. COMMENTS - Composite indexes
-- =============================================

-- Index cho query lấy comments theo entity và thời gian
CREATE INDEX IF NOT EXISTS `idx_comments_entity_created` 
ON `comments` (`entity_type`, `entity_id`, `created_at` DESC);

-- =============================================
-- 5. NOTIFICATIONS - Composite indexes
-- =============================================

-- Index cho query lấy unread notifications của user
CREATE INDEX IF NOT EXISTS `idx_notif_user_unread` 
ON `notifications` (`user_id`, `is_read`, `created_at` DESC);

-- =============================================
-- 6. DOCUMENTS - Composite indexes
-- =============================================

-- Index cho query lấy documents theo project và type
CREATE INDEX IF NOT EXISTS `idx_docs_project_type` 
ON `documents` (`project_id`, `type`, `name`);

-- Index cho query lấy starred documents của user
CREATE INDEX IF NOT EXISTS `idx_docs_starred_user` 
ON `documents` (`uploaded_by`, `is_starred`);

-- =============================================
-- 7. ACTIVITY_LOGS - Composite indexes
-- =============================================

-- Index cho query lấy activity theo entity và thời gian
CREATE INDEX IF NOT EXISTS `idx_activity_entity_time` 
ON `activity_logs` (`entity_type`, `entity_id`, `created_at` DESC);

-- =============================================
-- 8. CALENDAR_EVENTS - Composite indexes
-- =============================================

-- Index cho query lấy events trong khoảng thời gian
CREATE INDEX IF NOT EXISTS `idx_events_user_dates` 
ON `calendar_events` (`created_by`, `start_time`, `end_time`);
