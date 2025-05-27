-- Table for CV data
CREATE TABLE IF NOT EXISTS `cv_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `personal_info` text COLLATE utf8mb4_unicode_ci,
  `experience` text COLLATE utf8mb4_unicode_ci,
  `education` text COLLATE utf8mb4_unicode_ci,
  `skills` text COLLATE utf8mb4_unicode_ci,
  `projects` text COLLATE utf8mb4_unicode_ci,
  `certifications` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `cv_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for Todo Lists
CREATE TABLE IF NOT EXISTS `todo_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','in_progress','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  KEY `due_date` (`due_date`),
  CONSTRAINT `todo_lists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample data for testing (optional)
INSERT INTO `todo_lists` (`user_id`, `title`, `description`, `status`, `priority`, `due_date`) VALUES
(1, 'Complete CV Builder Feature', 'Finish implementing the CV builder with all sections and PDF export functionality', 'in_progress', 'high', '2025-06-15'),
(1, 'Learn React.js', 'Study React.js fundamentals and build a small project', 'pending', 'medium', '2025-07-01'),
(1, 'Update Portfolio Website', 'Add new projects and update design', 'pending', 'low', '2025-06-30'),
(1, 'Prepare for Interview', 'Practice coding questions and review company background', 'completed', 'high', '2025-05-25');

-- Create indexes for better performance
CREATE INDEX idx_cv_data_user_id ON cv_data(user_id);
CREATE INDEX idx_todo_lists_user_status ON todo_lists(user_id, status);
CREATE INDEX idx_todo_lists_user_priority ON todo_lists(user_id, priority);
CREATE INDEX idx_todo_lists_due_date_status ON todo_lists(due_date, status);