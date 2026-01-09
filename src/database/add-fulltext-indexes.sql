-- =============================================
-- TASKFLOW - FULLTEXT INDEXES FOR SEARCH
-- Thêm full-text search capabilities
-- =============================================

-- Fulltext index cho tasks
ALTER TABLE `tasks` 
ADD FULLTEXT INDEX `ft_tasks_search` (`title`, `description`);

-- Fulltext index cho projects
ALTER TABLE `projects` 
ADD FULLTEXT INDEX `ft_projects_search` (`name`, `description`);

-- Fulltext index cho documents
ALTER TABLE `documents` 
ADD FULLTEXT INDEX `ft_documents_search` (`name`, `description`);

-- Fulltext index cho comments
ALTER TABLE `comments` 
ADD FULLTEXT INDEX `ft_comments_search` (`content`);

-- Fulltext index cho users
ALTER TABLE `users` 
ADD FULLTEXT INDEX `ft_users_search` (`full_name`, `email`, `department`, `position`);
