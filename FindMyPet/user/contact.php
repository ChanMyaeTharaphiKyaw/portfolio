<?php
$pageTitle = "Contact Us";
include '../includes/header.php';
require_once '../database/functions.php';

// Check User Authentication 
$isLoggedIn = isset($_SESSION['authenticated']) && isset($_SESSION['auth_user']);

// Prefill Form Data for Logged-in Users
$prefillName = $isLoggedIn ? htmlspecialchars($_SESSION['auth_user']['name'] ?? '') : '';
$prefillEmail = $isLoggedIn ? htmlspecialchars($_SESSION['auth_user']['email'] ?? '') : '';

$readonlyAttr = $isLoggedIn ? 'readonly' : 'required';
$readonlyClass = $isLoggedIn ? 'bg-gray-50 text-gray-500 cursor-not-allowed border-gray-200' : 'border-gray-300';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>
    <link rel="stylesheet" href="../assets/css/style.css" />
    <link rel="stylesheet" href="../assets/css/output.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 
                        primary: '#1f7a5f', 
                        secondary: '#f36f1c', 
                        background: '#f8fafc' 
                    },
                    fontFamily: { 
                        poppins: ['Poppins', 'sans-serif'] 
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background font-poppins text-gray-800">
    <main class="min-h-screen pt-6 pb-12">
        <div class="container mx-auto px-4">

            <!-- Page Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-4">
                    <i class="ri-customer-service-2-line text-3xl text-primary"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-3">Get in Touch</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Have questions or need help with a lost pet listing? Our team is here to support you.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">

                <!-- Contact Info Cards -->
                <div class="lg:col-span-1 space-y-4">
                    <!-- Email Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                            <i class="ri-mail-line text-xl text-primary"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-1">Email Us</h3>
                        <p class="text-sm text-gray-600">We'll respond within 24 hours</p>
                        <a href="mailto:findmypet.support@gmail.com" class="text-primary text-sm font-medium mt-2 inline-block hover:underline">
                            findmypet.support@gmail.com
                        </a>
                    </div>

                    <!-- Phone Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center mb-4">
                            <i class="ri-phone-line text-xl text-secondary"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-1">Call Us</h3>
                        <p class="text-sm text-gray-600">Mon-Fri, 9am to 6pm</p>
                        <a href="tel:+959778324363" class="text-secondary text-sm font-medium mt-2 inline-block hover:underline">
                            09778324363
                        </a>
                    </div>

                    <!-- Location Card -->
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                            <i class="ri-map-pin-line text-xl text-primary"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 mb-1">Location</h3>
                        <p class="text-sm text-gray-600">Myanmar</p>
                        <span class="text-primary text-sm font-medium mt-2 inline-block">
                            Mandalay
                        </span>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm p-6 md:p-8 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 mb-6">Send us a Message</h2>

                        <?php include '../includes/alert.php'; ?>

                        <form action="../code.php" method="POST" class="space-y-5">

                            <!-- Name & Email  -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Your Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name"
                                        value="<?= $prefillName ?>"
                                        placeholder="Enter your name" <?= $readonlyAttr ?>
                                        class="w-full px-4 py-2.5 border <?= $readonlyClass ?> rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition">
                                    <?php if ($isLoggedIn): ?>
                                        <p class="text-xs text-gray-500 mt-1">Name is locked to your account.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                        value="<?= $prefillEmail ?>"
                                        placeholder="Enter your email" <?= $readonlyAttr ?>
                                        class="w-full px-4 py-2.5 border <?= $readonlyClass ?> rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition">
                                    <?php if ($isLoggedIn): ?>
                                        <p class="text-xs text-gray-500 mt-1">Email is locked to your account.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select id="subject" name="subject" 
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition bg-white appearance-none">
                                        <option value="">Select a subject</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Report a Problem">Report a Problem</option>
                                        <option value="Success Story">Success Story</option>
                                        <option value="Feedback">Feedback</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <i class="ri-arrow-down-s-line absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Message <span class="text-red-500">*</span>
                                </label>
                                <textarea id="message" name="message" rows="6"
                                    placeholder="How can we help you? Please provide as much detail as possible..." 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition resize-none"></textarea>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-between pt-2">
                                <button type="submit" name="btn_send_contact"
                                    class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-opacity-90 font-medium transition shadow-sm">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>