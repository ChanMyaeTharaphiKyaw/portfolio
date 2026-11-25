<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'pdo.php';

$user_image = 'images/default-profile.jpg';

if (isset($_SESSION['user'])) {
    $email = $_SESSION['user'];
    $stmt = $pdo->prepare("SELECT profile FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user_data = $stmt->fetch();

    if ($user_data && !empty($user_data['profile'])) {
        $user_image = $user_data['profile'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Fusion</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.7.0/fonts/remixicon.css"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <header>
        <nav class="nav-bar">
            <div class="nav-container">
                <div class="nav-left">
                    <div class="menu-toggle" id="toggle-menu">
                        <i class="ri-menu-line"></i>
                    </div>
                    <div class="logo">
                        <img src="images/logo.png" alt="Logo">
                        <a href="index.php">FoodFusion</a>
                    </div>
                </div>

                <div class="header-search-bar" id="search-bar">
                    <form action="search_results.php" method="GET">
                        <input type="text" name="query" placeholder="Find a recipe" required>
                        <button type="submit"><i class="ri-search-line"></i></button>
                    </form>
                </div>

                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="recipes.php">Recipes</a></li>
                    <li><a href="community.php">Community</a></li>
                    <li class="dropdown">
                        <a href="#">Resources <i class="ri-arrow-down-s-line nav-chevron"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="culinary.php">Culinary Resources</a></li>
                            <li><a href="educational.php">Educational Resources</a></li>
                        </ul>
                    </li>
                    <li><a href="about.php">About</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>

                <div class="nav-right">
                    <div class="search-toggle" id="toggle-search">
                        <i class="ri-search-line"></i>
                    </div>
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="dropdown user-profile">
                            <a href="javascript:void(0)" class="user-profile-trigger">
                                <img src="<?php echo htmlspecialchars($user_image); ?>" alt="Profile" class="nav-profile-img">
                            </a>
                            <ul class="dropdown-menu profile-menu">
                                <li class="user-info-header">
                                    <strong>Account</strong>
                                    <span><?php echo htmlspecialchars($_SESSION['user']); ?></span>
                                </li>
                                <li class="divider"></li>
                                <li>
                                    <a href="#" onclick="openResetPopup(); return false">
                                        <i class="ri-lock-2-line"></i> Reset Password
                                    </a>
                                </li>
                                <li><a href="logout.php" class="logout-link"><i class="ri-logout-box-r-line"></i> Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                        if (!empty($_SERVER['QUERY_STRING'])) {
                            $currentPage .= '?' . $_SERVER['QUERY_STRING'];
                        }

                        $currentPage = str_replace(["\r", "\n"], '', $currentPage);

                        if (str_contains($currentPage, 'login.php') || empty($currentPage)) {
                            $currentPage = 'community.php';
                        }
                        ?>
                        <a href="login.php?redirect=<?= urlencode($currentPage) ?>" class="login-btn">Login</a>

                        <button type="button" class="register-btn" onclick="openRegisterPopup(); return false">Register</button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="register-popup-container" id="registerPopupForm">
        <div class="register-form">
            <span class="close-btn" onclick="closeRegisterPopup()"><i class="ri-close-circle-fill"></i></span>
            <h2>Register Your Account</h2>
            <p>Create an account to share your recipes!</p>

            <?php if (isset($_SESSION['register_errors'])): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($_SESSION['register_errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentPage) ?>">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first-name" value="<?= htmlspecialchars($_SESSION['register_data']['first-name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last-name" value="<?= htmlspecialchars($_SESSION['register_data']['last-name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['register_data']['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>

                <div class="form-group">
                    <label>Profile Picture</label>
                    <input type="file" name="profile" id="profile">
                </div>

                <div class="popup-btn">
                    <button type="button" class="cancel-btn" onclick="closeRegisterPopup()">Cancel</button>
                    <button type="submit" class="register-form-btn">Register</button>
                </div>
                <p class="login-option">Already have an account? <a href="login.php?redirect=<?= urlencode($currentPage) ?>">Login</a></p>

                <input type="hidden" name="form_source" value="header_register">
            </form>
        </div>
    </div>

    <div class="reset-password-container" id="resetPopup">
        <form action="reset_password.php" id="resetForm" class="reset-form" method="POST">
            <span onclick="closeResetPopup()" class="close-btn"><i class="ri-close-circle-fill"></i></span>
            <h2>Reset Password</h2>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($currentPage) ?>">

            <?php if (isset($_SESSION['reset_errors'])): ?>
                <div class="error-box">
                    <ul>
                        <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <?php if (isset($_SESSION['user'])): ?>
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['user']) ?>">
                    <p class="reset-email">
                        Resetting password for <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>
                    </p>
                <?php else: ?>
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your registered email" required>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" placeholder="Enter your new password" required>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your new password" required>
            </div>

            <button type="submit" class="reset-form-btn">Update Password</button>
        </form>
    </div>

    <script>
        document.getElementById('toggle-menu').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            const menuIcon = this.querySelector('i');

            navLinks.classList.toggle('active');

            if (navLinks.classList.contains('active')) {
                menuIcon.classList.replace('ri-menu-line', 'ri-close-line');
            } else {
                menuIcon.classList.replace('ri-close-line', 'ri-menu-line');
            }
        });

        const dropdowns = document.querySelectorAll('.dropdown');

        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (window.innerWidth <= 1200) {
                    if (e.target.tagName === 'A' && e.target.getAttribute('href') !== '#') {}

                    if (this.querySelector('a').getAttribute('href') === '#') {
                        e.preventDefault();
                    }
                    this.classList.toggle('open');
                }
            });
        });

        const searchToggle = document.getElementById('toggle-search');
        const searchBar = document.getElementById('search-bar');

        searchToggle.addEventListener('click', () => {
            searchBar.classList.toggle('active');

            if (searchBar.classList.contains('active')) {
                searchBar.querySelector('input').focus();
            }
        });

        window.addEventListener('click', (e) => {
            if (
                !searchBar.contains(e.target) &&
                !searchToggle.contains(e.target) &&
                searchBar.classList.contains('active')
            ) {
                searchBar.classList.remove('active');
            }
        });

        function openRegisterPopup() {
            const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.getElementById("registerPopupForm").style.display = "flex";
            document.body.style.overflow = "hidden";

            document.body.style.paddingRight = scrollBarWidth + "px";
        }

        function closeRegisterPopup() {
            document.getElementById("registerPopupForm").style.display = "none";
            document.body.style.overflow = "auto";
            document.body.style.paddingRight = "0px";
        }

        function openResetPopup() {
            const scrollBarWidth = window.innerWidth - document.documentElement.clientWidth;
            document.getElementById("resetPopup").style.display = "flex";
            document.body.style.overflow = "hidden";
            document.body.style.paddingRight = scrollBarWidth + "px";
        }

        function closeResetPopup() {
            document.getElementById("resetPopup").style.display = "none";
            document.body.style.overflow = "auto";
            document.body.style.paddingRight = "0px";
        }

        window.onclick = function(event) {
            const regModal = document.getElementById("registerPopupForm");
            const resetModal = document.getElementById("resetPopup");

            if (event.target == regModal) {
                closeRegisterPopup();
            }
            if (event.target == resetModal) {
                closeResetPopup();
            }
        }

        window.addEventListener('load', () => {
            <?php if (isset($_SESSION['register_errors']) && ($_SESSION['register_source'] ?? '') === 'header_register'): ?>
                openRegisterPopup();
            <?php endif; ?>

            <?php if (isset($_SESSION['reset_errors'])): ?>
                openResetPopup();
            <?php endif; ?>
        })
    </script>
</body>

</html>
<?php unset($_SESSION['reset_errors']); ?>