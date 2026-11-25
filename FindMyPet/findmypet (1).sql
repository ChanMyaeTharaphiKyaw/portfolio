-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2026 at 11:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `findmypet`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('Emergency','Prevention','Health','Community') DEFAULT 'Community',
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `user_id`, `title`, `category`, `content`, `image`, `created_at`, `updated_at`) VALUES
(1, 8, 'What To Do When Your Pet Goes Missing in Yangon', 'Emergency', 'Losing a beloved pet can be a stressful and emotional experience. If your pet goes missing in Yangon, taking quick action can greatly increase the chance of finding them safely.\r\n\r\n1. Stay Calm and Search Nearby Areas\r\nStart by checking areas close to your home, as many lost pets are found within a short distance. Search places where your pet usually visits, such as parks, streets, markets, and nearby houses. Call your pet\'s name and bring their favourite treats or toys.\r\n\r\n2. Inform People Around Your Area\r\nAsk neighbours, security guards, shop owners, and local communities in your township to help look for your pet. More people searching increases the chance of receiving useful information.\r\n\r\n3. Create a Lost Pet Post on FindMyPet\r\nUpload a lost pet report with clear information including:\r\n- Pet name and breed\r\n- Pet colour and unique features\r\n- Recent photos\r\n- Last seen location\r\n- Date and time the pet went missing\r\n- Contact information\r\n\r\nAccurate details help other users identify and report possible matches.\r\n\r\n4. Share Your Lost Pet Information\r\nShare your FindMyPet post with friends, family members, and local animal communities. More visibility can help your missing pet reach someone who has seen them.\r\n\r\n5. Contact Nearby Animal Shelters and Veterinary Clinics\r\nContact animal rescue groups, shelters, and veterinary clinics in Yangon. Some people may bring found pets to these locations for safety.\r\n\r\n6. Check FindMyPet Regularly\r\nUse the search and match features regularly to check for possible found pet reports. The system can help identify potential matches based on pet information and location.\r\n\r\nRemember to update your post when your pet is found so that other users know the search has ended.\r\n\r\nTogether, the Yangon community can help reunite lost pets with their families.', '/uploads/blogs/blog_1785053501_6a65c13d5cbf4.jpg', '2026-07-21 10:08:58', '2026-07-26 14:41:41'),
(6, 8, 'Why ID Tags Are a Lifesaver for Your Pet', 'Prevention', 'An identification (ID) tag is one of the simplest and most effective ways to help a lost pet return home. A collar with an ID tag containing your pet\'s name and your contact number allows anyone who finds your pet to contact you quickly.\r\n\r\nWhile microchips are an excellent form of permanent identification, not everyone has access to a microchip scanner. An ID tag provides immediate contact information without requiring special equipment.\r\n\r\nRemember to regularly check that your pet\'s collar fits properly and update the contact information on the tag whenever your phone number or address changes.', '/uploads/blogs/blog_1785193792_6a67e540860f8.jpg', '2026-07-28 05:39:52', '2026-07-28 05:39:52'),
(10, 8, 'What to Do When You Find a Lost Pet', 'Community', 'If you find a pet wandering alone, approach it slowly and calmly. Some pets may be frightened and could run away if startled.\r\n\r\nIf it is safe to do so, check whether the pet is wearing an identification tag. Provide food and water if necessary, but avoid giving unfamiliar food that could upset its stomach.\r\n\r\nTake clear photos and create a Found Pet post on FindMyPet, including the location, date, and any distinguishing features. Avoid sharing every identifying detail publicly so the rightful owner can verify ownership.\r\n\r\nYour kindness and quick action could help reunite a family with their beloved pet.', '/uploads/blogs/blog_1785197810_6a67f4f26e3bd.jpeg', '2026-07-28 06:46:50', '2026-07-28 06:46:50'),
(12, 8, 'Why Microchipping Your Pet Matters', 'Prevention', 'A microchip is a small electronic chip placed beneath your pet\'s skin that contains a unique identification number. Unlike collars or tags, a microchip cannot fall off or be easily removed.\r\n\r\nWhen a lost pet is found, many veterinary clinics and animal shelters can scan the microchip to identify the owner. However, remember that a microchip is only effective if your contact information is kept up to date.\r\n\r\nCombining microchipping with online lost and found services such as FindMyPet provides an even greater chance of reuniting with your beloved companion.', '/uploads/blogs/blog_1786619036_6a7da49cacc1a.jpg', '2026-07-31 18:33:33', '2026-08-13 17:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `breed`
--

CREATE TABLE `breed` (
  `id` int(11) NOT NULL,
  `pet_type_id` int(11) NOT NULL,
  `breed_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `breed`
--

INSERT INTO `breed` (`id`, `pet_type_id`, `breed_name`) VALUES
(1, 1, 'Golden Retriever'),
(2, 1, 'Bulldog'),
(3, 1, 'Poodle'),
(4, 1, 'Pug'),
(5, 1, 'Beagle'),
(7, 2, 'Ragdoll'),
(8, 2, 'American shorthair'),
(9, 2, 'Burmese Cat'),
(12, 7, 'Holland Lop'),
(13, 7, 'Netherland Dwarf');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `id` int(11) NOT NULL,
  `city_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`id`, `city_name`) VALUES
(1, 'Yangon'),
(2, 'Mandalay'),
(4, 'Naypyitaw'),
(7, 'Mawlamyine'),
(8, 'Bago'),
(9, 'Pathein'),
(10, 'Myitkyina');

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment`
--

INSERT INTO `comment` (`id`, `user_id`, `post_id`, `comment_text`, `created_at`) VALUES
(37, 5, 1, 'I am sorry to hear this.', '2026-07-31 17:33:55'),
(39, 5, 62, 'I want to help in searching.', '2026-08-13 18:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `contact_message`
--

CREATE TABLE `contact_message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_message`
--

INSERT INTO `contact_message` (`id`, `user_id`, `full_name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 5, 'Zoro', 'zorochan307@gmail.com', 'Success Story', 'Hello, this is success story.', '2026-07-21 09:58:59'),
(2, NULL, 'chan', 'changmal@gmail.com', 'General Inquiry', 'Testing contact message function.', '2026-07-25 23:05:40'),
(7, NULL, 'Chan', 'chanmyaetharaphikyaw2020@gmail.com', 'Report a Problem', 'i want to report a proble', '2026-07-28 09:23:25'),
(8, 42, 'Chan', 'chanmyaetharaphikyaw2020@gmail.com', 'Success Story', 'Thanks to FindMyPet, I found the owner of a dog I rescued near my neighborhood. After creating a Found Pet post, I was matched with a Lost Pet report that had been posted a day earlier. We contacted each other and reunited the dog with its family. I truly appreciate this helpful service.', '2026-07-31 14:57:26'),
(9, NULL, 'Min Min', 'minmin24@gmail.com', 'General Inquiry', 'I recently came across your website while searching for information about lost and found pets. Although I don\'t have an account yet, I wanted to say that the platform looks very helpful and easy to use.\r\nI have a few questions before registering:\r\nIs creating an account required to contact pet owners?\r\nAre potential match notifications available only for registered users?', '2026-07-31 15:01:10');

-- --------------------------------------------------------

--
-- Table structure for table `content_report`
--

CREATE TABLE `content_report` (
  `id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `reason` enum('Spam','False Information','Inappropriate Content','Harassment','Other') NOT NULL,
  `details` text DEFAULT NULL,
  `status` enum('Pending','Dismissed','Resolved') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `content_report`
--

INSERT INTO `content_report` (`id`, `reporter_id`, `post_id`, `comment_id`, `reason`, `details`, `status`, `created_at`, `updated_at`) VALUES
(1, 5, 9, NULL, 'Spam', '', 'Resolved', '2026-07-20 18:24:37', '2026-07-28 09:30:53'),
(21, 42, 9, NULL, 'Inappropriate Content', 'This post contains inappropriate information.', 'Resolved', '2026-07-31 16:43:39', '2026-07-31 17:03:13'),
(22, 5, 1, NULL, 'False Information', 'This is not correct.', 'Dismissed', '2026-07-31 16:44:10', '2026-07-31 16:53:38');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_pushed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `post_id`, `message`, `type`, `link`, `is_read`, `is_pushed`, `created_at`) VALUES
(64, 6, NULL, 'Your content was reported for violating community guidelines. Please review our rules.', 'warning', NULL, 1, 1, '2026-07-25 14:18:58'),
(65, 5, NULL, 'Hello, you violated our rules.', 'warning', '../user/post_details.php?id=10', 1, 1, '2026-07-25 14:30:27'),
(70, 5, 26, 'A new found pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=26', 1, 1, '2026-07-25 14:44:15'),
(71, 8, 26, 'A new found pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=26', 0, 1, '2026-07-25 14:44:21'),
(72, 6, 26, 'We found 2 potential match(es) for your new found pet.', 'match', 'potential_matches.php?id=26', 1, 1, '2026-07-25 14:44:29'),
(73, 8, NULL, 'Testing - This is testing for sending announcements to all users.', 'announcement', NULL, 0, 1, '2026-07-26 06:11:47'),
(74, 2, NULL, 'Testing - This is testing for sending announcements to all users.', 'announcement', NULL, 0, 1, '2026-07-26 06:11:47'),
(75, 5, NULL, 'Testing - This is testing for sending announcements to all users.', 'announcement', NULL, 1, 1, '2026-07-26 06:11:47'),
(76, 6, NULL, 'Testing - This is testing for sending announcements to all users.', 'announcement', NULL, 1, 1, '2026-07-26 06:11:47'),
(77, 9, NULL, 'Testing - This is testing for sending announcements to all users.', 'announcement', NULL, 0, 1, '2026-07-26 06:11:47'),
(79, 11, NULL, 'Testing - This is testing for sending announcements to all users.', 'announcement', NULL, 0, 1, '2026-07-26 06:11:47'),
(176, 5, NULL, 'Your comment was removed by our administration team because it violated our community guidelines.', 'content_removed', NULL, 1, 1, '2026-07-28 00:40:25'),
(182, 5, NULL, 'Your post was removed by our administration team because it violated our community guidelines.', 'content_removed', NULL, 1, 1, '2026-07-28 02:54:59'),
(192, 2, 9, 'Chan commented on your post.', 'comment', 'post_details.php?id=9', 0, 1, '2026-07-28 03:36:12'),
(224, 5, 61, 'A new lost pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=61', 0, 1, '2026-07-31 04:17:23'),
(225, 6, 61, 'A new lost pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=61', 0, 1, '2026-07-31 04:17:28'),
(226, 8, 61, 'A new lost pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=61', 0, 1, '2026-07-31 04:17:32'),
(227, 42, 61, 'We found 1 potential match(es) for your new Lost pet.', 'match', 'potential_matches.php?id=61', 1, 1, '2026-07-31 04:17:36'),
(229, 5, 62, 'A new lost pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=62', 0, 1, '2026-07-31 07:50:26'),
(230, 6, 62, 'A new lost pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=62', 0, 1, '2026-07-31 07:50:26'),
(231, 8, 62, 'A new lost pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=62', 0, 1, '2026-07-31 07:50:31'),
(232, 5, 62, 'A potential match (50%) was found for your Found pet!', 'match', 'post_details.php?id=62', 0, 1, '2026-07-31 07:50:35'),
(233, 42, 62, 'We found 1 potential match(es) for your new Lost pet.', 'match', 'potential_matches.php?id=62', 1, 1, '2026-07-31 07:50:35'),
(234, 2, 9, 'Chan Myae Tharaphi Kyaw commented on your post.', 'comment', 'post_details.php?id=9', 0, 1, '2026-07-31 10:18:01'),
(235, 2, NULL, 'Your post was reported for violating community guidelines. Please review our rules.', 'warning', '../user/post_details.php?id=9', 0, 1, '2026-07-31 10:33:13'),
(236, 42, NULL, 'Your comment was removed by our administration team because it violated our community guidelines.', 'content_removed', NULL, 1, 1, '2026-07-31 10:40:55'),
(237, 5, 63, 'A new found pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=63', 0, 1, '2026-07-31 10:49:25'),
(238, 8, 63, 'A new found pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=63', 0, 1, '2026-07-31 10:49:25'),
(239, 42, 63, 'A new found pet post was created in your township, Chanmyathazi.', 'new_post', 'post_details.php?id=63', 1, 1, '2026-07-31 10:49:32'),
(240, 42, 63, 'A potential match (70%) was found for your Lost pet!', 'match', 'post_details.php?id=63', 1, 1, '2026-07-31 10:49:32'),
(241, 42, 63, 'A potential match (80%) was found for your Lost pet!', 'match', 'post_details.php?id=63', 1, 1, '2026-07-31 10:49:32'),
(242, 6, 63, 'We found 2 potential match(es) for your new Found pet.', 'match', 'potential_matches.php?id=63', 0, 1, '2026-07-31 10:49:32'),
(243, 2, 1, 'Zoro commented on your post.', 'comment', 'post_details.php?id=1', 0, 1, '2026-07-31 11:03:55'),
(244, 8, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(245, 2, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(246, 5, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(247, 6, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(248, 9, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(249, 11, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(250, 42, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 1, 1, '2026-07-31 11:52:09'),
(251, 43, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(252, 44, NULL, 'Scheduled System Maintenance - FindMyPet will undergo scheduled maintenance on Sunday from 1:00 AM to 3:00 AM. During this period, some features, including posting, notifications, and account management, may be temporarily unavailable. We apologize for an', 'announcement', NULL, 0, 1, '2026-07-31 11:52:09'),
(254, 42, 62, 'Zoro commented on your post.', 'comment', 'post_details.php?id=62', 0, 1, '2026-08-13 12:10:15');

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE `pet` (
  `id` int(11) NOT NULL,
  `breed_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Unknown') DEFAULT 'Unknown',
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet`
--

INSERT INTO `pet` (`id`, `breed_id`, `name`, `color`, `age`, `gender`, `image`) VALUES
(1, 2, 'Vicky', 'Black', 12, 'Male', 'uploads/pets/1784518728.jpg'),
(5, 2, 'Unknown', 'Brown', 10, 'Female', 'uploads/pets/1784524618.jpg'),
(6, 8, 'Dori', '', 0, '', 'uploads/pets/1784528832_6a5dbfc05158f.jpg'),
(7, 8, 'Unknown', '', 0, '', 'uploads/pets/1784532084_6a5dcc74d3f5f.jpg'),
(8, 8, 'Dori', '', NULL, 'Unknown', 'uploads/pets/1784533461_6a5dd1d5050ca.jpg'),
(9, 9, 'Vicky', 'Black', 18, 'Male', 'uploads/pets/1784535050_6a5dd80aef922.jpg'),
(11, 7, 'Unknown', 'White', 8, 'Female', 'uploads/pets/1784850064_6a62a690c7be1.jpg'),
(12, 7, 'Unknown', 'White', 15, 'Female', 'uploads/pets/1784854907_6a62b97b114f4.jpg'),
(13, 7, 'Bobbi', 'White', 10, 'Male', 'uploads/pets/1784855320_6a62bb18b414a.jpg'),
(16, 7, 'Doriri', 'White', 11, 'Male', 'uploads/pets/1784856014_6a62bdcee6bac.png'),
(17, 7, 'Dori', 'Black', 11, 'Male', 'uploads/pets/1784858779_6a62c89bcde2b.jpg'),
(18, 1, 'Unknown', 'Brown', 11, 'Female', 'uploads/pets/1784859889_6a62ccf11a970.jpg'),
(19, 5, 'Dori', 'Black', 11, 'Male', 'uploads/pets/1784860123_6a62cddba3115.jpg'),
(21, 2, 'Unknown', 'White', NULL, 'Unknown', 'uploads/pets/pet_1785193138_6a67e2b28470c.jpg'),
(23, 9, 'Unknown', '', 0, '', 'uploads/pets/1784890431_6a63443f53ccd.jpg'),
(25, 7, 'Dorri', 'White', 10, 'Male', 'uploads/pets/pet_1785193024_6a67e240ec95d.jpg'),
(26, 9, 'Unknown', 'White', 10, 'Male', 'uploads/pets/1784990655_6a64cbbf83381.jpg'),
(27, 9, 'Husy', 'Gray', 11, 'Female', 'uploads/pets/1785152020_6a6742147aab4.jpg'),
(28, 1, 'Unknown', 'Golden', 11, 'Male', 'uploads/pets/1785153942_6a674996b4ad0.png'),
(29, 8, 'Pudding', 'Cream', 11, 'Female', 'uploads/pets/1785161622_6a6767965d580.png'),
(30, 7, 'Mona', 'White', 11, 'Male', 'uploads/pets/1785194736_6a67e8f09f339.jpg'),
(31, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195745_6a67ece17ab03.jpg'),
(32, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195758_6a67ecee2797b.jpg'),
(33, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195769_6a67ecf9c12c8.jpg'),
(34, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195783_6a67ed0789a62.jpg'),
(35, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195796_6a67ed14d70d5.jpg'),
(36, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195810_6a67ed224dcea.jpg'),
(37, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195822_6a67ed2e51590.jpg'),
(38, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195833_6a67ed39c53fa.jpg'),
(39, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195845_6a67ed45df6be.jpg'),
(40, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195857_6a67ed517e84c.jpg'),
(41, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195869_6a67ed5dd4f16.jpg'),
(42, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195876_6a67ed64f29af.jpg'),
(43, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195877_6a67ed6519197.jpg'),
(44, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195877_6a67ed6574d46.jpg'),
(45, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195881_6a67ed69339e1.jpg'),
(46, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195881_6a67ed69b51ca.jpg'),
(47, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195882_6a67ed6a3d01f.jpg'),
(48, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785195882_6a67ed6a4f053.jpg'),
(49, 7, 'Vickt', 'White', 11, 'Male', 'uploads/pets/1785196585_6a67f02977eca.jpg'),
(50, 7, 'Vicky', 'White', 11, 'Male', 'uploads/pets/1785197534_6a67f3de023d1.jpg'),
(51, 7, 'Dori', 'Cream', 11, 'Male', 'uploads/pets/1785207086_6a68192e22e45.jpg'),
(53, 7, 'Unknown', 'White', 11, 'Female', 'uploads/pets/1785463699_6a6c0393c09a1.jpg'),
(60, 1, 'Unknown', 'Brown', 15, 'Male', 'uploads/pets/1785467983_6a6c144fee20d.jpg'),
(61, 7, 'Pudding', 'White', 10, 'Female', 'uploads/pets/pet_1786619000_6a7da47838998.jpg'),
(62, 9, 'Bobby', 'Black', 18, 'Female', 'uploads/pets/1785484226_6a6c53c28e691.jpg'),
(63, 9, 'Unknown', 'White', 18, 'Female', 'uploads/pets/1785494965_6a6c7db5c2c5c.jpg'),
(64, 9, 'Unknown', '', 0, '', 'uploads/pets/1786668666_6a7e667ade4f1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pet_type`
--

CREATE TABLE `pet_type` (
  `id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pet_type`
--

INSERT INTO `pet_type` (`id`, `type_name`) VALUES
(1, 'Dog'),
(2, 'Cat'),
(7, 'Rabbit');

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pet_id` int(11) NOT NULL,
  `township_id` int(11) NOT NULL,
  `post_type` enum('Lost','Found') NOT NULL,
  `description` text DEFAULT NULL,
  `last_seen_date` date DEFAULT NULL,
  `last_seen_time` time DEFAULT NULL,
  `last_seen_location` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `status` enum('Active','Reunited','Draft') DEFAULT 'Active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`id`, `user_id`, `pet_id`, `township_id`, `post_type`, `description`, `last_seen_date`, `last_seen_time`, `last_seen_location`, `contact_phone`, `contact_email`, `status`, `created_at`) VALUES
(1, 2, 1, 9, 'Lost', 'Pyay Road', '2026-07-12', '10:05:00', '110 Street, 56 Street', '09959004423', 'aung@gmail.com', 'Active', '2026-07-20 10:08:48'),
(9, 2, 9, 9, 'Found', 'Testing', '2026-07-14', '14:40:00', 'Pyay Road', '09875339876', 'aung@gmail.com', 'Active', '2026-07-20 14:40:50'),
(21, 6, 21, 8, 'Found', 'Testing', '2026-07-22', '00:00:00', '107 Street, 45x46 Street', '09771337796', 'chanmyaetharaphikyaw2020@gmail.com', 'Reunited', '2026-07-24 16:38:20'),
(23, 5, 23, 7, 'Found', '', '0000-00-00', '00:00:00', '', '', '', 'Draft', '2026-07-24 17:23:51'),
(26, 6, 26, 7, 'Found', 'Testing', '2026-07-22', '21:14:00', '110 Strret', '09773669986', 'chanmyae375@gmail.com', 'Active', '2026-07-25 21:14:15'),
(60, 42, 60, 7, 'Found', 'Very friendly, seems to be someone\'s pet with collar. If this is your cat, please message or call with proof of ownership (photos or collar description).', '2026-07-29', '09:48:00', '107 Street, 45x46 Street', '09123456789', 'chanmyaetharaphikyaw2020@gmail.com', 'Active', '2026-07-31 09:49:43'),
(61, 42, 61, 7, 'Lost', 'A friendly white ragdoll cat was lost on July 27, 2026, near 62 street and 110 street. The cat is healthy, well-groomed, and 10 months old. It has a distinctive white chest and white paws.', '2026-07-27', '10:46:00', '110 Street, 62 Street', '09123456789', 'chanmyaetharaphikyaw2020@gmail.com', 'Active', '2026-07-31 10:47:23'),
(62, 42, 62, 7, 'Lost', 'A friendly burmese cat was lost on July 29, 2026, near 62 street and 110 street. The cat is healthy and has a black and gray chest and yellow paws.', '2026-07-29', '14:19:00', '110 Street, 62 Street', '09123456789', 'chanmyaetharaphikyaw2020@gmail.com', 'Active', '2026-07-31 14:20:26'),
(63, 6, 63, 7, 'Found', 'Testing', '2026-07-30', '17:19:00', '110 Strret', '09773669986', 'chanmyae375@gmail.com', 'Active', '2026-07-31 17:19:25'),
(64, 42, 64, 13, 'Lost', '', '0000-00-00', '00:00:00', '', '', '', 'Draft', '2026-08-14 07:21:06');

-- --------------------------------------------------------

--
-- Table structure for table `post_match`
--

CREATE TABLE `post_match` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `matched_post_id` int(11) NOT NULL,
  `match_score` decimal(5,2) NOT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_match`
--

INSERT INTO `post_match` (`id`, `post_id`, `matched_post_id`, `match_score`, `status`, `created_at`) VALUES
(69, 63, 61, 70.00, 'Pending', '2026-07-31 17:19:32'),
(70, 61, 63, 70.00, 'Pending', '2026-07-31 17:19:32'),
(71, 63, 62, 80.00, 'Pending', '2026-07-31 17:19:32'),
(72, 62, 63, 80.00, 'Pending', '2026-07-31 17:19:32');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `role_name`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `township`
--

CREATE TABLE `township` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `township_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `township`
--

INSERT INTO `township` (`id`, `city_id`, `township_name`) VALUES
(7, 2, 'Chanmyathazi'),
(8, 2, 'Amarapura'),
(9, 1, 'Hlaing'),
(10, 1, 'Insein'),
(13, 2, 'Aungmyethazan'),
(14, 2, 'Chanayethazan'),
(15, 2, 'Maha Aungmye'),
(16, 2, 'Pyigyidagun'),
(17, 2, 'Patheingyi'),
(18, 1, 'Latha'),
(19, 1, 'Lanmadaw'),
(20, 1, 'Sanchaung'),
(21, 1, 'Dagon'),
(22, 1, 'Ahlone'),
(23, 1, 'Kamayut'),
(24, 1, 'Tamwe'),
(25, 1, 'South Okkalapa'),
(26, 1, 'North Okkalapa'),
(27, 4, 'Zabuthiri'),
(28, 4, 'Dekkhinathiri'),
(29, 4, 'Ottarathiri'),
(30, 4, 'Pohbbathiri'),
(31, 4, 'Zeyathiri'),
(32, 7, 'Chaungzon'),
(33, 7, 'Kyaikmaraw'),
(34, 7, 'Thanbyuzayat'),
(35, 8, 'Thanatpin'),
(36, 8, 'Gyobingauk'),
(37, 8, 'Letpadan'),
(38, 9, 'Kangyidaun'),
(39, 9, 'Thabaung'),
(40, 9, 'Ngapudaw'),
(41, 10, 'Tanai');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `township_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `status` enum('Active','Suspended','Banned') NOT NULL DEFAULT 'Active',
  `suspended_until` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  `email_notifications` tinyint(1) DEFAULT 1,
  `match_notifications` tinyint(1) DEFAULT 1,
  `post_notifications` tinyint(1) DEFAULT 1,
  `comment_notifications` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `township_id`, `full_name`, `email`, `password`, `phone`, `address`, `verification_token`, `token_expire`, `is_verified`, `status`, `suspended_until`, `reset_token`, `reset_expire`, `email_notifications`, `match_notifications`, `post_notifications`, `comment_notifications`, `created_at`) VALUES
(2, 2, 9, 'Aung', 'aung@gmail.com', '$2y$10$nKslhAHBDO89i72aFhcVGu8uxDk.D/U5bC4oi0mUgiJCgVXIGW13e', '09875339876', 'Pyay Road', 'a1afe1482319229bfcd13aa99cce4e21', NULL, 1, 'Active', NULL, NULL, NULL, 1, 1, 1, 1, '2026-07-20 09:03:52'),
(5, 2, 7, 'Zoro', 'zorochan307@gmail.com', '$2y$10$LLnp4sZQdcowJuXfrTpSiOr75Jcs/PrkaPKhx..DWkv9.auUo4tg.', '09123456789', '62 Street, 107 Street', NULL, NULL, 1, 'Active', NULL, 'd25dd6089f2761a9bc8ed4f884817e3ec7a61ba0b7ae72e5549c1ad4f01ac35b', '2026-07-28 07:54:53', 0, 1, 1, 1, '2026-07-20 09:54:34'),
(6, 2, 7, 'Chan Myae', 'chanmyae375@gmail.com', '$2y$10$PGVndf01x10.C49njJodDuGPiQ1d3B3e6EY64Xk3cArpyBC4sERbe', '09771237846', '', '6383b7a41913edbb1654b5d17beadc20', NULL, 1, 'Active', NULL, NULL, NULL, 1, 1, 1, 0, '2026-07-20 16:36:52'),
(8, 1, 7, 'Admin', 'admin@findmypet.com', '$2y$10$9Pd9QuV2mWx0CD2pRErVr.jBnCB7A6z.rx.h5gjryfysoNdrwNT9u', '09123456789', 'FindMyPet HQ, Myanmar', NULL, NULL, 1, 'Active', NULL, NULL, NULL, 1, 1, 1, 1, '2026-07-20 21:54:09'),
(9, 2, 9, 'David', 'david@gmail.com', '$2y$10$qOG.lhoWivFYYnxHb6pE2.IS5B9eYpKGiMDS2inn.iEkGfM8lPm1S', '09959004423', 'Mandalay', NULL, NULL, 1, 'Suspended', '2026-07-25 14:49:36', NULL, NULL, 1, 1, 1, 1, '2026-07-20 22:08:42'),
(11, 2, 9, 'Kaung', 'kaung@gmail.com', '$2y$10$val4klBDsJGg.UYjesHz5upuReYwKHXukqjKdQsbukA4I8P7nysFa', '09959004423', '', NULL, NULL, 1, 'Banned', NULL, NULL, NULL, 1, 1, 1, 1, '2026-07-25 15:00:50'),
(42, 2, 7, 'Chan Myae Tharaphi Kyaw', 'chanmyaetharaphikyaw2020@gmail.com', '$2y$10$gsZH17jp7Pyeqo8Zx79y7uU1DbQhwntxfi13Ffi3pXhKGCMLKDPCK', '09123456789', '110 street, 62 Street', 'daa8c129d1158729ac34c327a2893888', NULL, 1, 'Active', NULL, '3187ccc2ed22cc2bb644d5b65309baf2d4136776d5c514150d3596c1a7814136', '2026-08-14 08:00:36', 0, 1, 1, 1, '2026-07-31 06:39:04'),
(43, 2, 8, 'Tharaphi', 'tharaphikyaw7@gmail.com', '$2y$10$7rMjKmudBOvLiNgUkZPUWuGHJWvlqD2P1Er7vUuWGeAIPewDErZbG', '09771337796', 'U Bein Bridge', '7084782b7605cb4cedfa18630f8ad16b', '2026-08-14 20:41:19', 0, 'Active', NULL, NULL, NULL, 1, 1, 1, 1, '2026-07-31 06:57:52'),
(44, 2, 8, 'Min Min', 'minmin24@gmail.com', '$2y$10$wsjJzRRkb97xOr9HsA.NZe6dENeo3et78qcGNwwpyw8Is6dUo.loC', '09959674987', 'Near U Bein Bridge', '729c5e7379def322b79503bce06c74ad', NULL, 1, 'Suspended', '2026-08-07 15:45:20', NULL, NULL, 1, 1, 1, 1, '2026-07-31 15:31:33'),
(45, 2, 8, 'Aung Aung', 'aungaung23@gmail.com', '$2y$10$CrQp9YQCV/XygoLpNRu9W.MYGN2JPn96O7IetBzDra3LIOUvpPmea', '09778958639', 'U Bein', '0cdb72161e093cd3537866bbee57c14e', '2026-08-14 17:41:20', 0, 'Active', NULL, NULL, NULL, 1, 1, 1, 1, '2026-08-13 17:41:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `breed`
--
ALTER TABLE `breed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_type_id` (`pet_type_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `content_report`
--
ALTER TABLE `content_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reporter_id` (`reporter_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `breed_id` (`breed_id`);

--
-- Indexes for table `pet_type`
--
ALTER TABLE `pet_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pet_id` (`pet_id`),
  ADD KEY `township_id` (`township_id`);

--
-- Indexes for table `post_match`
--
ALTER TABLE `post_match`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_match` (`post_id`,`matched_post_id`),
  ADD KEY `matched_post_id` (`matched_post_id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `township`
--
ALTER TABLE `township`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `township_id` (`township_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `breed`
--
ALTER TABLE `breed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `content_report`
--
ALTER TABLE `content_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `pet`
--
ALTER TABLE `pet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `pet_type`
--
ALTER TABLE `pet_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `post_match`
--
ALTER TABLE `post_match`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `township`
--
ALTER TABLE `township`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `breed`
--
ALTER TABLE `breed`
  ADD CONSTRAINT `breed_ibfk_1` FOREIGN KEY (`pet_type_id`) REFERENCES `pet_type` (`id`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD CONSTRAINT `contact_message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `content_report`
--
ALTER TABLE `content_report`
  ADD CONSTRAINT `content_report_ibfk_1` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_report_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_report_ibfk_3` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_1` FOREIGN KEY (`breed_id`) REFERENCES `breed` (`id`);

--
-- Constraints for table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`pet_id`) REFERENCES `pet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_ibfk_3` FOREIGN KEY (`township_id`) REFERENCES `township` (`id`);

--
-- Constraints for table `post_match`
--
ALTER TABLE `post_match`
  ADD CONSTRAINT `post_match_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_match_ibfk_2` FOREIGN KEY (`matched_post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `township`
--
ALTER TABLE `township`
  ADD CONSTRAINT `township_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `city` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`township_id`) REFERENCES `township` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
