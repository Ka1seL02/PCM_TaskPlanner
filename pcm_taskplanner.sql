-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2025 at 12:23 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pcm_taskplanner`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `taskname` varchar(100) NOT NULL,
  `taskdescription` text NOT NULL,
  `starttime` datetime NOT NULL DEFAULT current_timestamp(),
  `duetime` datetime NOT NULL,
  `assignedto` int(11) NOT NULL,
  `status` enum('Pending','Missed','In-Progress','Completed') NOT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `taskname`, `taskdescription`, `starttime`, `duetime`, `assignedto`, `status`, `proof`, `attachment`) VALUES
(1, 'Design New Logo', 'The task \"Changing Logo\" involves updating the existing logo across all relevant platforms, ensuring consistency in branding. This includes replacing the old logo on the website, social media, and internal documents while maintaining the correct dimensions and formats. A final review should be conducted to verify proper implementation before completion.\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n', '2025-02-27 22:59:29', '2025-02-27 15:57:57', 7, 'In-Progress', '', 'uploads/attachments/sample_attachment.pdf'),
(2, 'Update Website Banner', 'Update the homepage banner for the new promotion.', '2025-02-28 10:00:00', '2025-02-28 18:00:00', 6, 'In-Progress', NULL, NULL),
(3, 'Database Optimization', 'Optimize slow queries for better performance.', '2025-03-01 09:00:00', '2025-03-01 17:00:00', 6, 'In-Progress', NULL, NULL),
(4, 'Bug Fix: Login Issue', 'Resolve the login timeout bug reported by users.', '2025-03-02 08:30:00', '2025-03-02 12:00:00', 6, 'Completed', NULL, NULL),
(5, 'Create Social Media Ads', 'Design and schedule promotional ads for social media.', '2025-03-03 14:00:00', '2025-03-03 20:00:00', 6, 'Missed', NULL, NULL),
(6, 'Product Catalog Update', 'Update the product catalog with new arrivals.', '2025-03-04 09:00:00', '2025-03-04 15:00:00', 6, 'Pending', NULL, NULL),
(7, 'Write Blog Post', 'Draft and publish an article about eco-friendly products.', '2025-03-05 13:00:00', '2025-03-05 18:00:00', 6, 'In-Progress', NULL, NULL),
(8, 'Email Campaign', 'Prepare and send email marketing campaign.', '2025-03-06 10:00:00', '2025-03-06 16:00:00', 6, 'Completed', NULL, NULL),
(9, 'System Backup', 'Perform a full backup of the company database.', '2025-03-07 22:00:00', '2025-03-08 01:00:00', 6, 'Missed', NULL, NULL),
(10, 'Prepare Monthly Report', 'Generate financial and operational reports.', '2025-03-08 11:00:00', '2025-05-10 17:00:00', 11, 'Pending', NULL, 'uploads/attachments/004223af37a129a3080a7b3dac49fa3c.pdf'),
(11, 'UX Testing', 'Conduct user experience testing on the new interface.', '2025-03-09 10:00:00', '2025-03-09 14:00:00', 6, 'In-Progress', NULL, NULL),
(12, 'Server Maintenance', 'Perform routine maintenance on the main servers.', '2025-03-10 02:00:00', '2025-03-10 06:00:00', 6, 'Completed', 'uploads/proofs/ADMIN USER MANUAL_67c461287a703.pdf', NULL),
(13, 'Create Training Material', 'Develop training guides for new employees.', '2025-03-11 08:30:00', '2025-03-11 12:30:00', 6, 'Missed', NULL, NULL),
(14, 'SEO Optimization', 'Improve website SEO rankings.', '2025-03-12 09:00:00', '2025-03-12 15:00:00', 6, 'Pending', NULL, NULL),
(15, 'Customer Feedback Analysis', 'Analyze recent customer survey results.', '2025-03-13 12:00:00', '2025-03-13 16:00:00', 6, 'In-Progress', NULL, NULL),
(16, 'Mobile App Bug Fix', 'Fix critical bugs reported in the latest app version.', '2025-03-14 10:00:00', '2025-03-14 16:00:00', 6, 'In-Progress', NULL, NULL),
(17, 'Develop New Feature', 'Implement a wishlist feature in the e-commerce app.', '2025-03-15 09:00:00', '2025-03-15 18:00:00', 6, 'In-Progress', NULL, NULL),
(18, 'Marketing Strategy Update', 'Revise the Q2 digital marketing plan.', '2025-03-16 11:00:00', '2025-03-16 15:00:00', 6, 'Completed', 'uploads/proofs/ProjectOutput_FinalSummativeExam_67c44ed8d9f3a.pdf', NULL),
(19, 'Customer Support Training', 'Conduct training session for new support staff.', '2025-03-17 14:00:00', '2025-03-17 20:00:00', 6, 'Completed', 'uploads/proofs/9Gestures(3)(2)(2)_67c44ae6c06ea.pdf', NULL),
(20, 'Security Audit', 'Perform a security audit for the main website.', '2025-03-18 08:00:00', '2025-03-18 12:00:00', 6, 'In-Progress', 'uploads/proofs/profile1_67c44acc10b4a.png', NULL),
(21, 'Product Photography', 'Capture high-quality images for new products.', '2025-03-19 13:00:00', '2025-03-19 17:00:00', 6, 'Completed', 'uploads/proofs/OJT Resume_67c44aaa7dc50.pdf', NULL),
(22, 'User Survey Analysis', 'Analyze and summarize results from user surveys.', '2025-03-20 10:00:00', '2025-03-20 15:00:00', 6, 'Completed', 'uploads/proofs/FORM A - Official MOU for DLSU-D BIT OJT_67c45133b48bd.pdf', NULL),
(23, 'Website UI Redesign', 'Redesigned the main homepage for better user experience.', '2025-02-20 09:00:00', '2025-02-20 15:00:00', 6, 'Completed', NULL, NULL),
(24, 'Database Migration', 'Successfully migrated database to a new server.', '2025-02-21 10:30:00', '2025-02-21 18:00:00', 6, 'Completed', NULL, NULL),
(25, 'Social Media Campaign', 'Launched an advertising campaign on social platforms.', '2025-02-22 08:00:00', '2025-02-22 14:00:00', 6, 'Completed', NULL, NULL),
(26, 'Bug Fixing Sprint', 'Resolved major reported issues in the mobile application.', '2025-02-23 13:00:00', '2025-02-23 19:00:00', 6, 'Completed', NULL, NULL),
(27, 'Inventory Update', 'Updated stock information and verified all product listings.', '2025-02-24 11:00:00', '2025-02-24 17:00:00', 6, 'Completed', NULL, NULL),
(28, 'Quarterly Report', 'Prepared and submitted the Q1 company performance report.', '2025-02-25 09:30:00', '2025-02-25 16:00:00', 6, 'Completed', NULL, NULL),
(29, 'Designing a PubAds - Edsa', 'Lorem Ipsum.', '2025-03-03 06:19:00', '2025-03-20 06:19:00', 12, 'Pending', NULL, 'uploads/attachments/016de2ccf9619457a515c24f5d1591da.pdf'),
(30, 'Make New Reel', 'Lorem Ipsum.', '2025-03-03 06:19:00', '2025-03-07 06:19:00', 10, 'Pending', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `school` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(50) NOT NULL,
  `pfp` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `school`, `email`, `department`, `password`, `position`, `pfp`, `created_at`) VALUES
(1, 'Administrator', 'administrator', 'PCM Cosmetic Products Trading', 'administrator@pcm.ph', 'None', '$2y$10$wReodLUqPJuROSOh3f4VRepEYgkIHYk5YgdFQDS0n6vf9RF.Jk7L.', 'Admin', 'uploads/profile/admin.png', '2025-02-27 01:12:26'),
(2, 'IT Supervisor', 'itsupervisor', 'PCM Cosmetic Products Trading', 'itsupervisor@pcm.ph', 'IT', '$2y$10$f0VbCa90./y0dwmGD6SXRe1tRDnWJHJ2kcQqBm80DHIIEqXgxlRim', 'Supervisor', 'uploads/profile/itS.png', '2025-02-27 01:14:37'),
(3, 'HR Supervisor', 'hrsupervisor', 'PCM Cosmetic Products Trading', 'hrsupervisor@pcm.ph', 'HR', '$2y$10$hfaO4WrQIpFHNK7t9dckIeMzk5/jKaKA8A.40aHK4jnBY/sEsdI5K', 'Supervisor', 'uploads/profile/hrS.png', '2025-02-27 01:18:20'),
(4, 'Marketing Supervisor', 'marketingsupervisor', 'PCM Cosmetic Products Trading', 'marketingsupervisor@pcm.ph', 'Marketing', '$2y$10$kUYuZJM6GxfOSt0rxVc7Resx5cTRDQbLzRDI5Ha1AMlCk8eXpo9j2', 'Supervisor', 'uploads/profile/marketingS.png', '2025-02-27 01:20:27'),
(5, 'Admin Supervisor', 'adminsupervisor', 'PCM Cosmetic Products Trading', 'adminsupervisor@pcm.ph', 'Admin', '$2y$10$B7Gjr1TSzXi1zn9Fdj5eO.Vb3kEn3pXXsDjPbMsn1ddAH7br5x856', 'Supervisor', 'uploads/profile/adminS.png', '2025-02-27 01:21:08'),
(6, 'Aaron Bautista', 'aaronb', 'De La Salle University - Dasmariñas', 'abautista@dlsud.edu.ph', 'IT', '$2y$10$wReodLUqPJuROSOh3f4VRepEYgkIHYk5YgdFQDS0n6vf9RF.Jk7L.', 'Intern', 'uploads/profile/profile1.png', '2025-02-27 14:48:03'),
(7, 'Alvin Cedric Villanueva', 'cedric', 'De La Salle University - Dasmariñas', 'acv1698@dlsud.edu.ph', 'IT', '$2y$10$wReodLUqPJuROSOh3f4VRepEYgkIHYk5YgdFQDS0n6vf9RF.Jk7L.', 'Intern', 'uploads/profile/alvin_profile.png\r\n', '2025-02-27 14:51:22'),
(8, 'Angelica Cay', 'cay001', 'De La Salle University - Dasmariñas', 'angelica@dlsud.edu.ph', 'IT', '$2y$10$hDD9EAgKZvY9cyUjaFkXkemVHeLxlSrPl2OfvBXpDDtvFIR.fFvEm', 'Intern', 'uploads/angelica_profile.jpg', '2025-02-27 14:52:12'),
(9, 'Arbie Tomas', 'arbie', 'De La Salle University - Dasmariñas', 'tomas@dlsud.edu.ph', 'IT', '$2y$10$uuzEStQCgF1VZPqg41seTuLUbyWJdtYDsyUSwjmHn8ZVOhr0nLdGy', 'Intern', 'uploads/arbie_profile.png', '2025-02-27 14:53:11'),
(10, 'Shanaiah Grace Yngson', 'yngson1234', 'De La Salle University - Dasmariñas', 'yngson1234@dlsud.edu.ph', 'IT', '$2y$10$/70IHuGHrwfstC/APm1/0OVl950pqROLFPCVXDPsRPBMgJc0YBmHq', 'Intern', 'uploads/profile/profile1_67c40e74d3c98.png', '2025-03-02 07:53:24'),
(11, 'Blueix Abejo', 'blue', 'De La Salle University - Dasmariñas', 'blue@dlsud.edu.ph', 'IT', '$2y$10$KL08eTcsu/PlgAIo6qV.cuxZccL.pmRDG/lLXdspLyO5Y3qZLB11i', 'Intern', 'uploads/profileprofile1_67c40f0f98564.png', '2025-03-02 07:55:59'),
(12, 'Gian Klyde Lazaro', 'gianl', 'De La Salle University - Dasmariñas', 'gianlazaro37@gmail.com', 'Marketing', '$2y$10$Qs5a0ctwNvu2Dfc5WN3kC.5QjcSBAx3EsEyppghHMT9ynYvFbPnoq', 'Intern', 'uploads/profile/Vivid Essence Logo_67c4543fdf1e1.png', '2025-03-02 12:51:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
