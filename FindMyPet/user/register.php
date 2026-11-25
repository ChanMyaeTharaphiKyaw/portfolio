<?php
session_start();
$pageTitle = "Registration Form";

require_once '../database/pdo.php';
require_once '../database/functions.php';
include '../includes/header.php';

$errors = $_SESSION['errors'] ?? [];
$input = $_SESSION['input'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['input']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($pageTitle) ?> | FindMyPet</title>

    <link rel="stylesheet" href="../assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
</head>

<body>
    <main class="register-page">
        <section class="register-container">
            <div class="register-wrapper">
                <div class="register-header">
                    <h1>Create Account</h1>
                    <p>Join FindMyPet to help reunite pets with their families.</p>
                </div>

                <?php include '../includes/alert.php'; ?>

                <!-- Registration Form -->
                <form action="../code.php" method="POST" class="register-form" id="main-register-form">
                    
                    <!-- Full Name -->
                    <div class="form-group">
                        <label for="full-name">Full Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="full-name" name="full_name"
                            class="<?= hasError('full_name', $errors) ? 'error-border' : '' ?>"
                            value="<?= htmlspecialchars($input['full_name'] ?? '') ?>"
                            placeholder="Enter Your Full Name" >
                        <?php if (hasError('full_name', $errors)): ?>
                            <small class="error-text"><?= getError('full_name', $errors) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <!-- Email Address -->
                        <div class="form-group">
                            <label for="email">Email Address <span style="color: #dc3545;">*</span></label>
                            <input type="email" id="email" name="email"
                                class="<?= hasError('email', $errors) ? 'error-border' : '' ?>"
                                value="<?= htmlspecialchars($input['email'] ?? '') ?>"
                                placeholder="Enter Your Email" >
                            <?php if (hasError('email', $errors)): ?>
                                <small class="error-text"><?= getError('email', $errors) ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="phone">Phone Number <span style="color: #dc3545;">*</span></label>
                            <input type="tel" id="phone" name="phone"
                                class="<?= hasError('phone', $errors) ? 'error-border' : '' ?>"
                                value="<?= htmlspecialchars($input['phone'] ?? '') ?>"
                                placeholder="Enter Your Phone Number" >
                            <?php if (hasError('phone', $errors)): ?>
                                <small class="error-text"><?= getError('phone', $errors) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-row">
                        <!-- City -->
                        <div class="form-group">
                            <label for="city">City <span style="color: #dc3545;">*</span></label>
                            <select name="city" id="city" class="<?= hasError('city', $errors) ? 'error-border' : '' ?>" >
                                <option value="">Select City</option>
                                <?php
                                $cities = getAll($pdo, 'city');
                                foreach ($cities as $city) {
                                    $selected = (isset($input['city']) && $input['city'] == $city['id']) ? 'selected' : '';
                                    echo '<option value="' . $city['id'] . '" ' . $selected . '>' . htmlspecialchars($city['city_name']) . '</option>';
                                }
                                ?>
                            </select>
                            <?php if (hasError('city', $errors)): ?>
                                <small class="error-text"><?= getError('city', $errors) ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Township -->
                        <div class="form-group">
                            <label for="township">Township <span style="color: #dc3545;">*</span></label>
                            <select id="township" name="township"
                                class="<?= hasError('township', $errors) ? 'error-border' : '' ?>"
                                data-old-value="<?= htmlspecialchars($input['township'] ?? '') ?>" >
                                <option value="">Select A City First</option>
                            </select>
                            <?php if (hasError('township', $errors)): ?>
                                <small class="error-text"><?= getError('township', $errors) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address <span style="color: #dc3545;">*</span></label>
                        <textarea id="address" name="address" rows="2"
                            class="<?= hasError('address', $errors) ? 'error-border' : '' ?>"
                            placeholder="Enter Your Current Address" ><?= htmlspecialchars($input['address'] ?? '') ?></textarea>
                        <?php if (hasError('address', $errors)): ?>
                            <small class="error-text"><?= getError('address', $errors) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-row">
                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password <span style="color: #dc3545;">*</span></label>
                            <input type="password" id="password" name="password"
                                class="<?= hasError('password', $errors) ? 'error-border' : '' ?>" >
                            <?php if (hasError('password', $errors)): ?>
                                <small class="error-text"><?= getError('password', $errors) ?></small>
                            <?php endif; ?>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="confirm-password">Confirm Password <span style="color: #dc3545;">*</span></label>
                            <input type="password" id="confirm-password" name="confirm_password"
                                class="<?= hasError('confirm_password', $errors) ? 'error-border' : '' ?>" >
                            <?php if (hasError('confirm_password', $errors)): ?>
                                <small class="error-text"><?= getError('confirm_password', $errors) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" name="btn_register" class="btn-register">
                        Create Account
                    </button>
                </form>

                <div class="register-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Township Dropdown
        document.getElementById('city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('township');
            const oldTownship = townshipSelect.getAttribute('data-old-value') || '';

            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">Select A City First</option>';
                return;
            }

            townshipSelect.innerHTML = '<option value="">Loading townships...</option>';

            fetch('../user/get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = '<option value="">Select Township</option>' + htmlOptions;

                    if (oldTownship) {
                        townshipSelect.value = oldTownship;
                    }
                })
                .catch(err => {
                    console.error('Ajax error:', err);
                    townshipSelect.innerHTML = '<option value="">Error loading data</option>';
                });
        });

        window.addEventListener('DOMContentLoaded', () => {
            const citySelect = document.getElementById('city');
            if (citySelect.value) {
                citySelect.dispatchEvent(new Event('change'));
            }
        });
    </script> 
</body>
</html>