CREATE TABLE `user_details` (
  `user_id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_name` varchar(64) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `email_id` varchar(64) NOT NULL,
  `email_id_status` enum('Y,N') NOT NULL,
  `status` enum('Y,N') NOT NULL,
  `time_zone` varchar(64) NOT NULL,
  `last_login_time` int NOT NULL
) ENGINE='InnoDB';



ALTER TABLE `user_details`
CHANGE `email_id_status` `email_id_status` enum('Y','N') COLLATE 'latin1_swedish_ci' NOT NULL AFTER `email_id`,
CHANGE `status` `status` enum('Y','N') COLLATE 'latin1_swedish_ci' NOT NULL AFTER `email_id_status`,
CHANGE `last_login_time` `last_login_time` timestamp NOT NULL AFTER `time_zone`;

INSERT INTO `user_details` (`user_id`, `user_name`, `user_password`, `email_id`, `email_id_status`, `status`, `time_zone`, `last_login_time`)
VALUES ('0', 'testuser', '$2y$10$vWaHUXBZbc/JEursJrx26u0xPSemK5bQ/E3x.SrJ9HRflQeLEfbjW', 'testuser@testsomedomain.com', 1, 1, 'not set', now());
