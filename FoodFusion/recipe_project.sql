-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2026 at 03:54 PM
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
-- Database: `recipe_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `community_posts`
--

CREATE TABLE `community_posts` (
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_type` enum('tip','experience') NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_posts`
--

INSERT INTO `community_posts` (`post_id`, `user_id`, `post_type`, `title`, `content`, `image_url`, `created_at`) VALUES
(1, 1, 'tip', '5 Easy Ways to Keep Your Kitchen Organized', 'Keeping your kitchen tidy can save a lot of time and stress while cooking. Here are my top 5 tips:\r\n1. Use Clear Containers: Store ingredients like pasta, rice, and spices in transparent containers so you can see what you have at a glance.\r\n2. Label Everything: Labels make it easy for everyone in the household to know where things go.\r\n3. Group Similar Items: Keep baking supplies together, snacks together, etc., to make finding ingredients faster.\r\n4. Clean As You Go: Wash utensils and wipe counters while cooking — it prevents a huge mess at the end.\r\n5. Declutter Monthly: Check your pantry monthly and discard expired items or things you no longer use.', 'images/695c938e61415_clean_kitchen.jpg', '2026-01-06 04:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `full_name`, `email`, `subject`, `message`, `uploaded_at`) VALUES
(1, 'chan', 'chan@gmail.com', 'Recipe Request', 'I want burmese recipes.', '2026-01-06 10:13:16'),
(2, 'chan', 'chan@gmail.com', 'Recipe Request', 'Provide burmese recipes such as mohinga and shan noodle.', '2026-01-06 10:14:39'),
(3, 'chan', 'chan@gmail.com', 'Recipe Request', 'Useful website', '2026-01-06 10:15:29');

-- --------------------------------------------------------

--
-- Table structure for table `cuisines`
--

CREATE TABLE `cuisines` (
  `cuisine_id` int(11) NOT NULL,
  `cuisine_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cuisines`
--

INSERT INTO `cuisines` (`cuisine_id`, `cuisine_type`) VALUES
(1, 'Burmese'),
(2, 'Chinese'),
(3, 'French'),
(4, 'Italian'),
(5, 'Japanese'),
(6, 'Korean'),
(7, 'Mexican');

-- --------------------------------------------------------

--
-- Table structure for table `diet`
--

CREATE TABLE `diet` (
  `diet_id` int(11) NOT NULL,
  `diet` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diet`
--

INSERT INTO `diet` (`diet_id`, `diet`) VALUES
(1, 'Dairy-Free'),
(2, 'Gluten-Free'),
(3, 'Low Calorie'),
(4, 'Vegan'),
(5, 'Vegetarian'),
(6, 'Non-Vegetarian');

-- --------------------------------------------------------

--
-- Table structure for table `difficulty_levels`
--

CREATE TABLE `difficulty_levels` (
  `difficulty_id` int(11) NOT NULL,
  `difficulty` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `difficulty_levels`
--

INSERT INTO `difficulty_levels` (`difficulty_id`, `difficulty`) VALUES
(1, 'Easy'),
(2, 'Medium'),
(3, 'Hard');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_description` text NOT NULL,
  `event_status` enum('open','close','full') NOT NULL DEFAULT 'open',
  `event_image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_title`, `event_date`, `event_description`, `event_status`, `event_image_url`) VALUES
(1, 'Italian Pasta Masterclass', '2026-02-21', 'Learn the secrets of authentic Carbonara from expert home chefs in a fun, interactive session.', 'open', 'pasta-class.jpg'),
(2, 'Artisan Bread Workshop', '2026-02-28', 'Master the art of sourdough and rustic loaves. A hands-on morning for bread enthusiasts.', 'open', 'bread-workshop.jpg'),
(3, 'Vegan Soul Food Night', '2026-03-08', 'Discover how to create rich, flavorful plant-based comfort food that tastes just like the classics.', 'open', 'vegan-soul-food.jpg'),
(4, 'Sushi Rolling Basics', '2026-03-15', 'Learn to select the best fish and perfect your rolling technique in this fun session.', 'open', 'sushi-rolling-class.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `registration_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`registration_id`, `event_id`, `user_id`, `registered_at`) VALUES
(1, 1, 1, '2026-01-23 06:52:33'),
(2, 4, 1, '2026-01-23 06:54:04'),
(3, 3, 1, '2026-01-23 06:55:18'),
(4, 2, 7, '2026-02-03 07:45:41');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `pcomment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_comments`
--

INSERT INTO `post_comments` (`pcomment_id`, `user_id`, `post_id`, `comment_text`, `created_at`) VALUES
(1, 2, 1, 'Useful tips', '2026-01-06 07:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `plike_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_likes`
--

INSERT INTO `post_likes` (`plike_id`, `user_id`, `post_id`, `created_at`) VALUES
(3, 1, 1, '2026-01-06 05:52:52'),
(4, 2, 1, '2026-01-06 07:00:16');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cuisine_id` int(11) NOT NULL,
  `diet_id` int(11) NOT NULL,
  `difficulty_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `ingredients` text NOT NULL,
  `cook_time` int(11) NOT NULL,
  `instructions` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `user_id`, `cuisine_id`, `diet_id`, `difficulty_id`, `title`, `description`, `ingredients`, `cook_time`, `instructions`, `image_url`, `uploaded_at`) VALUES
(1, 1, 4, 6, 2, 'Creamy Garlic Chicken Pasta', 'A rich and comforting pasta dish made with tender chicken breast, a creamy garlic parmesan sauce, and perfectly cooked pasta. Ideal for a quick weeknight dinner or a cozy family meal.', '250g pasta (penne or fettuccine)\r\n2 chicken breasts, sliced\r\n3 cloves garlic, minced\r\n1 cup heavy cream\r\n½ cup grated parmesan cheese\r\n2 tablespoons olive oil\r\n1 tablespoon butter\r\nSalt to taste\r\nBlack pepper to taste\r\n½ teaspoon chili flakes (optional)\r\nFresh parsley for garnish', 30, 'Boil pasta in salted water according to package instructions. Drain and set aside.\r\nHeat olive oil and butter in a pan over medium heat.\r\nAdd chicken slices, season with salt and pepper, and cook until golden and fully cooked. Remove and set aside.\r\nIn the same pan, add minced garlic and sauté for 30 seconds until fragrant.\r\nPour in the heavy cream and bring to a gentle simmer.\r\nStir in parmesan cheese until the sauce thickens.\r\nAdd cooked chicken and pasta to the sauce. Toss well to coat evenly.\r\nSprinkle chili flakes if desired and adjust seasoning.\r\nGarnish with fresh parsley and serve hot.', 'images/695c78b7c7a03_pasta.jpg', '2026-01-06 02:51:35'),
(2, 1, 7, 5, 1, 'Vegetarian Black Bean Tacos', 'These tacos are filled with perfectly seasoned ingredients and fresh toppings for a bold, satisfying bite. A quick and flavorful taco recipe that combines warm tortillas with savory fillings and vibrant textures.', '1 can black beans, drained and rinsed\r\n1 tsp cumin\r\n1 tsp paprika\r\n½ tsp chili powder\r\n8 taco shells or tortillas\r\n1 cup shredded lettuce\r\n½ cup diced tomatoes\r\n¼ cup sour cream or yogurt\r\n1 tbsp olive oil', 20, 'Heat olive oil in a pan and add black beans.\r\nStir in cumin, paprika, and chili powder; cook for 5 minutes.\r\nWarm taco shells in a pan or oven.\r\nFill tacos with beans, lettuce, and tomatoes.\r\nTop with sour cream and serve immediately.', 'images/695c96792a659_black_bean_taco.jpg', '2026-01-06 04:58:33'),
(3, 2, 6, 3, 3, 'Korean Cold Buckwheat Noodles', 'A refreshing yet complex Korean noodle dish served in an icy, tangy broth with chewy buckwheat noodles. Light in calories but demanding in technique, this dish balances precision, timing, and flavor.', '120g buckwheat naengmyeon noodles\r\n2 cups beef or radish water kimchi broth (dongchimi)\r\n½ cup light beef stock (fat skimmed)\r\n1 boiled egg, halved\r\n¼ Korean pear, julienned\r\n½ cucumber, julienned\r\n2 slices lean beef (boiled and sliced thin)\r\n1 tbsp rice vinegar\r\n1 tsp sugar or monk fruit sweetener\r\n1 tsp mustard paste\r\nSalt to taste\r\nIce cubes', 60, 'Combine dongchimi broth with light beef stock, vinegar, sugar, and salt; chill until partially frozen.\r\nCook noodles in rapidly boiling water, stirring constantly, then rinse thoroughly under ice-cold water until very chewy.\r\nDrain noodles well and portion into serving bowls.\r\nPour icy broth over noodles and add ice cubes.\r\nTop with beef slices, cucumber, pear, and boiled egg.\r\nServe immediately with mustard on the side.', 'images/69609a160dca0_buckwheat_noodle_dish.jpg', '2026-01-09 06:03:02'),
(4, 2, 2, 4, 1, 'Cantonese Garlic Stir-Fried Mixed Vegetables', 'This classic Cantonese dish focuses on freshness and light seasoning to highlight the natural flavors of the vegetables. It is commonly served as a home-style dish or as part of a shared Chinese meal.', '200 g broccoli florets\r\n\r\n1 small carrot, thinly sliced\r\n\r\n2 cloves garlic, finely minced\r\n\r\n1 tablespoon vegetable oil\r\n\r\n1–2 tablespoons water or light vegetable stock\r\n\r\n1 teaspoon light soy sauce\r\n\r\nSalt, to taste', 14, 'Bring a pot of water to a boil. Add a pinch of salt and a few drops of oil.\r\n\r\nBlanch the broccoli and carrots for 30–60 seconds. Drain and set aside.\r\n\r\nHeat a wok over medium heat and add the vegetable oil.\r\n\r\nAdd the minced garlic and stir-fry briefly until fragrant (do not brown).\r\n\r\nAdd the blanched vegetables to the wok and toss well.', 'images/698745eba2a43_Easy-Garlic-Vegetable-Stir-fry-1.jpg', '2026-02-07 14:02:19'),
(5, 2, 4, 5, 1, 'Tomato Pasta', 'Tomato Pasta is a simple yet satisfying dish that highlights the natural flavor of tomatoes. It is a popular comfort food and a great introduction to Italian-style cooking. The garlic and herbs enhance the sauce, making it rich without being heavy. This recipe shows how a few basic ingredients can create a delicious meal when cooked properly. It is ideal for lunch or dinner and can be enjoyed by all age groups.', '200 g pasta\r\n\r\n1 cup tomato sauce\r\n\r\n2 cloves garlic\r\n\r\n1 tbsp olive oil\r\n\r\nSalt and dried herbs', 30, 'Boil pasta in salted water until tender.\r\n\r\nHeat oil and sauté garlic.\r\n\r\nAdd tomato sauce, salt, and herbs.\r\n\r\nSimmer the sauce for a few minutes.\r\n\r\nMix pasta with sauce and serve warm.', 'images/6987463b7a67d_tomato_pasta.jpg', '2026-02-07 14:03:39'),
(6, 2, 7, 6, 1, 'Grilled Chicken Tacos', 'Grilled Chicken Tacos are a popular Mexican dish known for bold flavors and fresh ingredients. The grilled chicken is lightly seasoned and paired with vegetables inside warm tortillas. This recipe shows how Mexican cuisine balances spices, textures, and colors. It is filling, flavorful, and perfect for a family meal.', '250 g boneless chicken breast, sliced\r\n\r\n6 soft tortillas\r\n\r\n1 onion, sliced\r\n\r\n1 bell pepper, sliced\r\n\r\n1 tablespoon vegetable oil\r\n\r\n½ teaspoon cumin\r\n\r\n½ teaspoon paprika\r\n\r\nSalt to taste', 30, 'Heat vegetable oil in a pan over medium heat.\r\n\r\nAdd sliced chicken and cook slowly, stirring occasionally, until it turns white and cooks evenly.\r\n\r\nSprinkle salt, cumin, and paprika over the chicken and mix well.\r\n\r\nAdd onion and bell pepper to the pan and sauté until vegetables soften.\r\n\r\nContinue cooking until the chicken is fully cooked and lightly browned.', 'images/69874691a06a0_chicken_tacos.jpg', '2026-02-07 14:05:05'),
(7, 2, 5, 1, 2, 'Vegetable Sushi Rolls', 'Vegetable Sushi Rolls showcase the precision and simplicity of Japanese cuisine. The focus is on fresh vegetables, lightly seasoned rice, and careful rolling technique. Sushi is both visually appealing and nutritious.', '2 cups sushi rice\r\n\r\n3 nori sheets\r\n\r\n1 cucumber, thinly sliced\r\n\r\n1 carrot, thinly sliced\r\n\r\n2 tablespoons rice vinegar\r\n\r\n1 teaspoon sugar\r\n\r\n½ teaspoon salt', 45, 'Wash rice until the water runs clear, then cook it and allow it to cool slightly.\r\n\r\nMix vinegar, sugar, and salt and gently fold into the rice.\r\n\r\nPlace a nori sheet on a bamboo mat.\r\n\r\nSpread rice evenly, leaving space at the edges.\r\n\r\nArrange vegetables in the center.\r\n\r\nRoll tightly using the mat.', 'images/698746e1223b8_vegan_sushi.jpg', '2026-02-07 14:06:25'),
(8, 2, 3, 4, 2, 'Vegetable Ratatouille', 'Ratatouille is a traditional French vegetable dish that focuses on slow cooking and natural flavors. It is healthy, colorful, and comforting, reflecting the French emphasis on technique and patience.', '1 eggplant, diced\r\n\r\n1 zucchini, sliced\r\n\r\n1 bell pepper, chopped\r\n\r\n2 tomatoes, chopped\r\n\r\n1 onion, chopped\r\n\r\n2 tablespoons olive oil\r\n\r\nSalt and herbs', 45, 'Heat olive oil in a deep pan over medium heat.\r\n\r\nAdd onions and cook until soft and translucent.\r\n\r\nAdd eggplant and cook for a few minutes.\r\n\r\nAdd zucchini, bell pepper, and tomatoes.\r\n\r\nSeason with salt and herbs.\r\n\r\nCover the pan and cook on low heat.\r\n\r\nStir occasionally to prevent sticking.', 'images/698747249bb26_rataouille.jpg', '2026-02-07 14:07:32'),
(9, 2, 5, 6, 1, 'Japanese Chicken Teriyaki', 'Chicken Teriyaki is a classic Japanese dish featuring juicy chicken glazed in a glossy, sweet-savory sauce. “Teriyaki” refers to the cooking technique of grilling or pan-frying with soy sauce, mirin, and sugar. This dish is loved for its simplicity and bold umami flavor. It pairs beautifully with steamed rice and lightly sautéed vegetables.', '500 g boneless chicken thighs\r\n\r\n2 tbsp vegetable oil\r\n\r\n¼ cup soy sauce\r\n\r\n¼ cup mirin\r\n\r\n2 tbsp sugar\r\n\r\n1 tbsp sake (optional)\r\n\r\nSesame seeds\r\n\r\nSliced green onions', 30, 'Prepare the sauceCombine soy sauce, mirin, sugar, and sake in a bowl. Stir until sugar dissolves.\r\nHeat oil in a pan over medium heat. Place chicken skin-side down and cook for 5–6 minutes until golden.\r\nTurn chicken over, pour in the sauce, and reduce heat. Simmer for 8–10 minutes, spooning sauce over chicken.\r\nLet the sauce reduce until thick and glossy.\r\nSlice chicken, drizzle extra sauce, and garnish with sesame seeds and green onions.', 'images/698747701b75a_teriyaki.jpg', '2026-02-07 14:08:48'),
(12, 1, 4, 5, 2, 'Margherita Pizza', 'Margherita Pizza is a classic Italian dish known for its simplicity and fresh flavors. It represents traditional Italian cooking, where high-quality ingredients are used without heavy seasoning. The combination of soft dough, tangy tomato sauce, melted cheese, and fresh herbs creates a balanced and satisfying meal. This pizza is popular worldwide and is a great example of how Italian cuisine focuses on freshness and comfort.', '2 cups all-purpose flour\r\n\r\n1 tsp dry yeast\r\n\r\n1 tsp sugar\r\n\r\n¾ cup warm water\r\n\r\n1 tbsp olive oil\r\n\r\n½ tsp salt\r\n\r\n1 cup tomato sauce (plain, unsweetened)\r\n\r\n1½ cups shredded mozzarella cheese\r\n\r\nFresh basil leaves\r\n\r\nExtra olive oil for brushing', 45, 'In a bowl, mix warm water, sugar, and yeast. Let it sit for 5–10 minutes until it becomes foamy.\r\n\r\nAdd flour, salt, and olive oil to the yeast mixture. Mix until a dough forms.\r\n\r\nKnead the dough on a clean surface for about 8–10 minutes until smooth and elastic.\r\n\r\nPlace the dough in a greased bowl, cover it, and let it rise for 30 minutes.\r\n\r\nPreheat the oven to a high temperature.\r\n\r\nRoll the dough into a round shape and place it on a baking tray.\r\n\r\nSpread tomato sauce evenly over the dough, leaving the edges clear.\r\n\r\nAdd cheese and basil leaves on top.\r\n\r\nBake until the crust is golden and the cheese melts.\r\n\r\nRemove from oven, drizzle olive oil lightly, slice, and serve.', 'images/69874a320d4e2_margherita_pizza.jpg', '2026-02-07 14:20:34'),
(13, 1, 1, 5, 1, 'Tea Leaf Salad (Lahpet Thoke)', 'Tea Leaf Salad (Lahpet Thoke) is a traditional Burmese dish made with fermented tea leaves, fresh vegetables, crunchy nuts, and aromatic oils. It has a unique balance of tangy, savory, bitter, and spicy flavors and is often served as a snack or side dish in Myanmar.', '½ cup fermented tea leaves (lahpet)\r\n\r\n2 cups shredded cabbage\r\n\r\n1 medium tomato, sliced\r\n\r\n2 green chilies, finely chopped (optional)\r\n\r\n2 tablespoons roasted peanuts\r\n\r\n2 tablespoons roasted sesame seeds\r\n\r\n2 tablespoons fried garlic\r\n\r\n2 tablespoons fried dried shrimp (optional)\r\n\r\n2 tablespoons garlic oil\r\n\r\n1 tablespoon chili oil (adjust to taste)\r\n\r\n1 tablespoon fish sauce (or soy sauce for vegetarian)\r\n\r\n1 tablespoon lime juice\r\n\r\nSalt, to taste\r\n\r\nFresh cilantro (optional, for garnish)', 14, 'Rinse the fermented tea leaves lightly and drain well.\r\n\r\nPlace the tea leaves in a large mixing bowl.\r\n\r\nAdd garlic oil, chili oil, fish sauce, and lime juice to the tea leaves.\r\n\r\nMix well until the tea leaves are evenly coated.\r\n\r\nAdd shredded cabbage, tomato slices, and green chilies.\r\n\r\nSprinkle in roasted peanuts, sesame seeds, fried garlic, and dried shrimp.\r\n\r\nGently toss all ingredients together until well combined.\r\n\r\nTaste and adjust seasoning with salt, lime juice, or fish sauce as needed.\r\n\r\nTransfer the salad to a serving plate.\r\n\r\nGarnish with fresh cilantro if desired.\r\n\r\nServe immediately for best texture and flavor.', 'images/69874c1e9b1b7_lahpet-thoke.jpg', '2026-02-07 14:28:46'),
(16, 1, 1, 3, 1, 'Shan Noodles (Shan Khao Swe)', 'Shan Noodles is a popular Burmese dish made with rice noodles topped with a savory meat and tomato sauce. It has a mild, slightly spicy flavor and is often served with crunchy peanuts and fresh herbs.', '200 g rice noodles\r\n\r\n200 g minced chicken or pork\r\n\r\n2 tablespoons cooking oil\r\n\r\n2 cloves garlic, minced\r\n\r\n2 shallots, finely chopped\r\n\r\n2 medium tomatoes, chopped\r\n\r\n2 tablespoons chili paste (adjust to taste)\r\n\r\n1 tablespoon soy sauce\r\n\r\n1 tablespoon fish sauce\r\n\r\n½ teaspoon sugar\r\n\r\nSalt, to taste\r\n\r\n2 tablespoons roasted peanuts (crushed)\r\n\r\n2 tablespoons chopped spring onions\r\n\r\nFresh cilantro (optional)\r\n\r\nLime wedges (optional)', 20, 'Boil rice noodles according to package instructions and drain well.\r\n\r\nHeat oil in a pan over medium heat.\r\n\r\nAdd garlic and shallots. Sauté until fragrant.\r\n\r\nAdd minced meat and cook until fully done.\r\n\r\nStir in chopped tomatoes and chili paste.\r\n\r\nAdd soy sauce, fish sauce, sugar, and salt.\r\n\r\nSimmer until the sauce thickens slightly.\r\n\r\nPlace cooked noodles into serving bowls.\r\n\r\nSpoon the meat sauce over the noodles.\r\n\r\nTop with peanuts, spring onions, and cilantro.\r\n\r\nServe warm with lime wedges on the side.', 'images/69874daa44a2d_shan-noodles.jpg', '2026-02-07 14:35:22'),
(18, 1, 1, 6, 2, 'Mohinga (Burmese Fish Noodle Soup)', 'Mohinga is Myanmar’s national dish, made with rice noodles in a flavorful fish broth thickened with chickpea flour and scented with lemongrass.', '200 g rice noodles\r\n\r\n200 g fish fillet\r\n\r\n1 onion, chopped\r\n\r\n2 cloves garlic, minced\r\n\r\n1 tablespoon ginger, minced\r\n\r\n1 stalk lemongrass, crushed\r\n\r\n2 tablespoons oil\r\n\r\n1 teaspoon turmeric\r\n\r\n1 tablespoon paprika\r\n\r\n2 tablespoons chickpea flour\r\n\r\n4 cups water\r\n\r\n1 tablespoon fish sauce\r\n\r\nSalt, to taste\r\n\r\nBoiled eggs (optional)\r\n\r\nCilantro (optional)', 80, 'Boil fish in water until cooked. Remove and flake. Keep broth.\r\n\r\nHeat oil and sauté onion, garlic, and ginger.\r\n\r\nAdd turmeric and paprika. Stir well.\r\n\r\nPour in fish broth and add lemongrass.\r\n\r\nMix chickpea flour with water and stir into soup.\r\n\r\nAdd flaked fish and season.\r\n\r\nSimmer until slightly thick.\r\n\r\nCook noodles separately and drain.\r\n\r\nPlace noodles in bowls and pour soup over.\r\n\r\nTop with eggs and cilantro.', 'images/69874f83e8c2e_mohinga.jpg', '2026-02-07 14:43:15');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_comments`
--

CREATE TABLE `recipe_comments` (
  `rcomment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_comments`
--

INSERT INTO `recipe_comments` (`rcomment_id`, `user_id`, `recipe_id`, `comment_text`, `created_at`) VALUES
(1, 2, 2, 'I will try this recipe.', '2026-01-06 06:59:52'),
(2, 2, 1, 'Look delicious', '2026-01-06 08:03:21'),
(3, 2, 1, 'Yum Yum', '2026-01-06 08:04:01'),
(4, 2, 1, 'Wanna take a bite.', '2026-01-06 08:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_likes`
--

CREATE TABLE `recipe_likes` (
  `rlike_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_likes`
--

INSERT INTO `recipe_likes` (`rlike_id`, `user_id`, `recipe_id`, `created_at`) VALUES
(2, 1, 2, '2026-01-06 05:39:38'),
(3, 2, 2, '2026-01-06 06:59:25'),
(5, 7, 2, '2026-01-23 11:17:34');

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ratings`
--

CREATE TABLE `recipe_ratings` (
  `rating_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipe_ratings`
--

INSERT INTO `recipe_ratings` (`rating_id`, `recipe_id`, `user_id`, `rating`, `created_at`) VALUES
(1, 2, 1, 4, '2026-01-06 05:12:17'),
(2, 2, 2, 3, '2026-01-06 07:00:49'),
(3, 1, 2, 4, '2026-01-06 07:08:40'),
(4, 3, 1, 4, '2026-01-19 04:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(150) NOT NULL,
  `last_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile` varchar(255) DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `lock_time` datetime DEFAULT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `profile`, `failed_login_attempts`, `lock_time`, `registered_at`) VALUES
(1, 'chan', 'myae', 'chan@gmail.com', '$2y$10$FZMpXiopee82Yn0H7lmdLuJn7Dm5nEKB.h5JjRPHZXRsCYStesbpu', NULL, 0, NULL, '2026-01-06 01:19:29'),
(2, 'Mg', 'Mg', 'mg@gmail.com', '$2y$10$JYfQfzt5UTOo7nzWuhQEFuhgO2ivXAAJdzxmVwkT.sS78pHoZ9Y8a', NULL, 0, NULL, '2026-01-06 04:17:26'),
(3, 'Su', 'Su', 'su@gmail.com', '$2y$10$bwR8icHfZ2JZlW8cfn4Yx.PSjZN2rmnmY6mDQwVY8gBWWPcRWuNhm', NULL, 0, NULL, '2026-01-06 09:44:09'),
(4, 'Aung', 'Aung', 'aung@gmail.com', '$2y$10$RnDg1d/iTDv4b7QrU7WZFeaMedSH5pFhsOcnb86IiySBOABNda1hq', NULL, 0, NULL, '2026-01-06 09:46:21'),
(5, 'Ma', 'Ma', 'ma@gmail.com', '$2y$10$aT2adoQR9V8FW/uIGc8lF.Pb7Cgv/vVLuOy.kNBhvjSubUMgkStf6', NULL, 0, NULL, '2026-01-06 09:47:34'),
(6, 'Min', 'Min', 'minmin24@gmail.com', '$2y$10$inaSegA8Fz8hgMc6McpGW./s74kmo8czk.G/rZyjCzqng2o5.K01G', 'images/6965fc08b03ea_min_min_profile.png', 0, NULL, '2026-01-13 08:02:16'),
(7, 'Sofia', 'Alice', 'sofia@gmail.com', '$2y$10$bRimNNtyxHXeXR5AW9WY5.ZPdris6zJFpZnRFOJ518CtBp2Y3gRtW', 'images/69734fea6dd5e_sofia-profile.jpg', 0, NULL, '2026-01-23 10:39:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `cuisines`
--
ALTER TABLE `cuisines`
  ADD PRIMARY KEY (`cuisine_id`);

--
-- Indexes for table `diet`
--
ALTER TABLE `diet`
  ADD PRIMARY KEY (`diet_id`);

--
-- Indexes for table `difficulty_levels`
--
ALTER TABLE `difficulty_levels`
  ADD PRIMARY KEY (`difficulty_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `event_id` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`pcomment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`plike_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`post_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cuisine_id` (`cuisine_id`),
  ADD KEY `diet_id` (`diet_id`),
  ADD KEY `difficulty_id` (`difficulty_id`);

--
-- Indexes for table `recipe_comments`
--
ALTER TABLE `recipe_comments`
  ADD PRIMARY KEY (`rcomment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipe_likes`
--
ALTER TABLE `recipe_likes`
  ADD PRIMARY KEY (`rlike_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipe_ratings`
--
ALTER TABLE `recipe_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `recipe_id` (`recipe_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `community_posts`
--
ALTER TABLE `community_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cuisines`
--
ALTER TABLE `cuisines`
  MODIFY `cuisine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `diet`
--
ALTER TABLE `diet`
  MODIFY `diet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `difficulty_levels`
--
ALTER TABLE `difficulty_levels`
  MODIFY `difficulty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `pcomment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `plike_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `recipe_comments`
--
ALTER TABLE `recipe_comments`
  MODIFY `rcomment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `recipe_likes`
--
ALTER TABLE `recipe_likes`
  MODIFY `rlike_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recipe_ratings`
--
ALTER TABLE `recipe_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`post_id`);

--
-- Constraints for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `community_posts` (`post_id`);

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`cuisine_id`) REFERENCES `cuisines` (`cuisine_id`),
  ADD CONSTRAINT `recipes_ibfk_3` FOREIGN KEY (`diet_id`) REFERENCES `diet` (`diet_id`),
  ADD CONSTRAINT `recipes_ibfk_4` FOREIGN KEY (`difficulty_id`) REFERENCES `difficulty_levels` (`difficulty_id`);

--
-- Constraints for table `recipe_comments`
--
ALTER TABLE `recipe_comments`
  ADD CONSTRAINT `recipe_comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `recipe_comments_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`);

--
-- Constraints for table `recipe_likes`
--
ALTER TABLE `recipe_likes`
  ADD CONSTRAINT `recipe_likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `recipe_likes_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`);

--
-- Constraints for table `recipe_ratings`
--
ALTER TABLE `recipe_ratings`
  ADD CONSTRAINT `recipe_ratings_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`),
  ADD CONSTRAINT `recipe_ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
