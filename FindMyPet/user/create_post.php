<?php
require_once '../database/pdo.php';
require_once '../database/functions.php';

// Check User Authentication
if (!isset($_SESSION['authenticated'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    setMessage('Please Login First to Post.', 'error');
    redirect('login.php');
}

$formData = $_SESSION['form_data'] ?? [];
$formErrors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errors']);

$pageTitle = "Post Lost or Found Pet";
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.8.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .error-border {
            border: 1px solid #dc3545 !important;
            outline: none;
            background-color: #fff8f8;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 4px;
            display: block;
        }
    </style>
</head>

<body>
    <div class="post-form">
        <!-- Step Indicator -->
        <div class="step-wrapper">
            <div class="step-item active" data-step="1">
                <div class="step-icon"><i class="fa-solid fa-paw"></i></div>
                <div class="step-name">Pet Details</div>
            </div>
            <div class="step-item" data-step="2">
                <div class="step-icon"><i class="fa-solid fa-camera"></i></div>
                <div class="step-name">Photo Upload</div>
            </div>
            <div class="step-item" data-step="3">
                <div class="step-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="step-name">Post Details</div>
            </div>
            <div class="step-item" data-step="4">
                <div class="step-icon"><i class="fa-solid fa-address-card"></i></div>
                <div class="step-name">Contact</div>
            </div>
        </div>

        <div class="step-indicator-text" id="step-text">Step 1 of 4: <strong>Pet Information</strong></div>

        <!-- Multi Step Form -->
        <form id="multi-step-form" action="../code.php" method="POST" enctype="multipart/form-data">
            <?php include '../includes/alert.php'; ?>

            <!-- Step 1 Pet Details -->
            <div class="form-step active" id="step-1">
                <div class="pet-details">
                    <p class="post-form-desc">Provide as much detail as possible to help the reunion process.</p>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="petname">Pet Name</label>
                            <input type="text" id="petname" name="pet_name" placeholder="Enter pet name (Optional)" value="<?= old('petName') ?>" class="<?= hasFormError('pet_name') ? 'error-border' : '' ?>" />
                            <?= errorMessage('pet_name') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="pettype">Pet Type *</label>

                            <select id="pettype" name="pet_type" data-required="true" class="form-control <?= hasFormError('pet_type') ? 'error-border' : '' ?>">
                                <option value="">Select Pet Type</option>
                                <?php foreach (getPetTypes($pdo) as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= old('petType') == $type['id'] ? 'selected' : '' ?>><?= $type['type_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= errorMessage('pet_type') ?>
                        </div>
                        <div class="form-group">
                            <label for="breed">Breed *</label>

                            <select id="breed" name="breed_name" data-required="true" class="<?= hasFormError('breed_name') ? 'error-border' : '' ?>">
                                <option value="">Select Pet Type First</option>
                            </select>
                            <?= errorMessage('breed_name') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="petage">Age</label>
                            <input type="text" id="petage" name="pet_age" placeholder="Enter pet age" value="<?= old('petAge') ?>" class="<?= hasFormError('pet_age') ? 'error-border' : '' ?>" />
                            <?= errorMessage('pet_age') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="petcolor">Color</label>

                            <select id="petcolor" name="pet_color" class="<?= hasFormError('pet_color') ? 'error-border' : '' ?>">
                                <option value="">Select Color</option>
                                <option value="Black" <?= old('petColor') == 'Black' ? 'selected' : '' ?>>Black</option>
                                <option value="White" <?= old('petColor') == 'White' ? 'selected' : '' ?>>White</option>
                                <option value="Brown" <?= old('petColor') == 'Brown' ? 'selected' : '' ?>>Brown</option>
                                <option value="Gray" <?= old('petColor') == 'Gray' ? 'selected' : '' ?>>Gray</option>
                                <option value="Cream" <?= old('petColor') == 'Cream' ? 'selected' : '' ?>>Cream</option>
                                <option value="Tan" <?= old('petColor') == 'Tan' ? 'selected' : '' ?>>Tan</option>
                                <option value="Golden" <?= old('petColor') == 'Golden' ? 'selected' : '' ?>>Golden</option>
                                <option value="Orange" <?= old('petColor') == 'Orange' ? 'selected' : '' ?>>Orange</option>
                                <option value="Red" <?= old('petColor') == 'Red' ? 'selected' : '' ?>>Red / Rust / Mahogany</option>
                                <option value="Other" <?= old('petColor') == 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?= errorMessage('pet_color') ?>
                        </div>
                        <div class="form-group">
                            <label for="petgender">Gender</label>

                            <select id="petgender" name="pet_gender" class="<?= hasFormError('pet_gender') ? 'error-border' : '' ?>">
                                <option value="">Select Gender</option>
                                <option value="male" <?= old('petGender') == 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= old('petGender') == 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="unknown" <?= old('petGender') == 'unknown' ? 'selected' : '' ?>>Unknown</option>
                            </select>
                            <?= errorMessage('pet_gender') ?>
                        </div>
                    </div>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-next">Next: Photo Upload</button>
                </div>
            </div>

            <!-- Step 2 Photo Upload -->
            <div class="form-step" id="step-2">
                <div class="photo-upload">
                    <p class="post-form-desc">Upload high-quality photos of your pet.</p>
                    <div class="custom-upload">
                        <label for="photo-upload" class="custom-upload-label">
                            <i class="fa-solid fa-camera"></i> <span>Click to upload</span>

                            <input type="file" name="pet_image" id="photo-upload" accept=".jpg, .jpeg, .png" data-required="true" class="custom-upload-input <?= hasFormError('pet_image') ? 'error-border' : '' ?>">
                        </label>
                        <?= errorMessage('pet_image') ?>
                        <div id="preview-container" class="preview-container mob-flex-column"></div>
                    </div>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-back">Back</button>
                    <button type="button" class="btn-next">Next: Post Details</button>
                </div>
            </div>

            <!-- Step 3 Post Details -->
            <div class="form-step" id="step-3">
                <div class="post-details">
                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="posttype">Post Type *</label>

                            <select id="posttype" name="post_type" data-required="true" class="<?= hasFormError('post_type') ? 'error-border' : '' ?>">
                                <option value="">Select Post Type</option>
                                <option value="lost" <?= old('postType') == 'lost' ? 'selected' : '' ?>>Lost</option>
                                <option value="found" <?= old('postType') == 'found' ? 'selected' : '' ?>>Found</option>
                            </select>
                            <?= errorMessage('post_type') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="lastseendate">Last Seen Date</label>

                            <input type="date" id="lastseendate" name="last_seen_date" value="<?= old('lastSeenDate') ?>" class="<?= hasFormError('last_seen_date') ? 'error-border' : '' ?>" />
                            <?= errorMessage('last_seen_date') ?>
                        </div>
                        <div class="form-group">
                            <label for="lastseentime">Last Seen Time</label>

                            <input type="time" id="lastseentime" name="last_seen_time" value="<?= old('lastSeenTime') ?>" class="<?= hasFormError('last_seen_time') ? 'error-border' : '' ?>" />
                            <?= errorMessage('last_seen_time') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="lastseenlocation">Last Seen Location</label>

                            <input type="text" id="lastseenlocation" name="last_seen_location" placeholder="Enter last seen location" value="<?= old('lastSeenLocation') ?>" class="<?= hasFormError('last_seen_location') ? 'error-border' : '' ?>" />
                            <?= errorMessage('last_seen_location') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="city">City *</label>

                            <select name="city" id="city" data-required="true" class="form-control <?= hasFormError('city') ? 'error-border' : '' ?>">
                                <option value="">Select City</option>
                                <?php foreach (getCities($pdo) as $city): ?>
                                    <option value="<?= $city['id'] ?>" <?= old('cityId') == $city['id'] ? 'selected' : '' ?>><?= $city['city_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?= errorMessage('city') ?>
                        </div>
                        <div class="form-group">
                            <label for="township">Township *</label>

                            <select id="township" name="township" data-required="true" class="<?= hasFormError('township') ? 'error-border' : '' ?>">
                                <option value="">Select A City First</option>
                            </select>
                            <?= errorMessage('township') ?>
                        </div>
                    </div>

                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label>Post Description</label>

                            <textarea name="post_des" placeholder="Enter detailed post description" class="<?= hasFormError('post_des') ? 'error-border' : '' ?>"><?= old('description') ?></textarea>
                            <?= errorMessage('post_des') ?>
                        </div>
                    </div>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-back">Back</button>
                    <button type="button" class="btn-next">Next: Contact Info</button>
                </div>
            </div>

            <!-- Step 4 Contact Information -->
            <div class="form-step" id="step-4">
                <div class="contact-details">
                    <p class="post-form-desc">How can people reach you? (Required only if creating an active post)</p>
                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="email">Email</label>

                            <input type="email" name="email" id="email" placeholder="Enter your email" value="<?= old('email') ?>" class="<?= hasFormError('email') ? 'error-border' : '' ?>" />
                            <?= errorMessage('email') ?>
                        </div>
                    </div>
                    <div class="post-flex mob-flex-column">
                        <div class="form-group">
                            <label for="phonenumber">Phone Number</label>

                            <input type="tel" name="phone" placeholder="Enter your phone number" value="<?= old('phone') ?>" class="<?= hasFormError('phone') ? 'error-border' : '' ?>" />
                            <?= errorMessage('phone') ?>
                        </div>
                    </div>
                </div>
                <div class="form-navigation">
                    <button type="button" class="btn-back">Back</button>
                    <button type="submit" class="btn-draft" name="btn_save_draft">Save as Draft</button>
                    <button type="submit" class="btn-primary create-post-btn" name="btn_create_post">Create Post</button>
                </div>
            </div>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Form Navigation
        const steps = Array.from(document.querySelectorAll(".form-step"));
        const nextBtns = document.querySelectorAll(".btn-next");
        const backBtns = document.querySelectorAll(".btn-back");
        const stepItems = document.querySelectorAll(".step-item");
        const stepText = document.getElementById("step-text");
        const stepTitles = ["Pet Information", "Photo Upload", "Post Information", "Contact Information"];

        // Next Button Click
        nextBtns.forEach((btn) => {
            btn.addEventListener("click", () => {
                const activeStep = document.querySelector(".form-step.active");

                activeStep.querySelectorAll('.error-text.client-error').forEach(el => el.remove());
                activeStep.querySelectorAll('.error-border').forEach(el => el.classList.remove('error-border'));

                // Validate Required Fields in Current Step
                const requiredInputs = activeStep.querySelectorAll("[data-required='true']");
                let allValid = true;

                requiredInputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.classList.add('error-border');
                        const errorMsg = document.createElement('small');
                        errorMsg.className = 'error-text client-error';
                        errorMsg.textContent = 'This field is required to proceed.';
                        input.parentNode.appendChild(errorMsg);
                        allValid = false;
                    } else if (input.type === 'email' && input.value && !input.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                        input.classList.add('error-border');
                        const errorMsg = document.createElement('small');
                        errorMsg.className = 'error-text client-error';
                        errorMsg.textContent = 'Invalid email format.';
                        input.parentNode.appendChild(errorMsg);
                        allValid = false;
                    }
                });

                if (allValid) {
                    changeStep("next");
                }
            });
        });

        // Back Button Click
        backBtns.forEach((btn) => {
            btn.addEventListener("click", () => changeStep("prev"));
        });

        function changeStep(direction) {
            let index = steps.indexOf(document.querySelector(".form-step.active"));
            steps[index].classList.remove("active");
            index = direction === "next" ? index + 1 : index - 1;
            steps[index].classList.add("active");
            updateProgress(index);
        }

        function updateProgress(index) {
            stepItems.forEach((step, i) => step.classList.toggle("active", i <= index));
            stepText.innerHTML = `Step ${index + 1} of 4: <strong>${stepTitles[index]}</strong>`;
        }

        // Image Preview
        const fileInput = document.getElementById("photo-upload");
        const previewContainer = document.getElementById("preview-container");
        fileInput.addEventListener("change", function() {
            previewContainer.innerHTML = "";
            Array.from(this.files).forEach(file => {
                if (!file.type.startsWith("image/")) return;
                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.onload = () => URL.revokeObjectURL(img.src);
                previewContainer.appendChild(img);
            });
        });

        // Dynamic Dropdown
        const oldBreedId = '<?= old('breedId') ?>';
        const oldTownshipId = '<?= old('townshipId') ?>';

        document.getElementById('city').addEventListener('change', function() {
            const cityId = this.value;
            const townshipSelect = document.getElementById('township');
            if (!cityId) {
                townshipSelect.innerHTML = '<option value="">Select A City First</option>';
                return;
            }

            fetch('get_townships.php?city_id=' + cityId)
                .then(response => response.text())
                .then(htmlOptions => {
                    townshipSelect.innerHTML = htmlOptions;
                    if (oldTownshipId) townshipSelect.value = oldTownshipId;
                });
        });

        document.getElementById('pettype').addEventListener('change', function() {
            const typeId = this.value;
            const breedSelect = document.getElementById('breed');
            if (!typeId) {
                breedSelect.innerHTML = '<option value="">Select Pet Type First</option>';
                return;
            }

            fetch('get_breeds.php?pet_type_id=' + typeId)
                .then(response => response.text())
                .then(htmlOptions => {
                    breedSelect.innerHTML = htmlOptions;
                    if (oldBreedId) breedSelect.value = oldBreedId;
                });
        });

        window.onload = function() {
            if ('<?= old('petType') ?>') document.getElementById('pettype').dispatchEvent(new Event('change'));
            if ('<?= old('cityId') ?>') document.getElementById('city').dispatchEvent(new Event('change'));
        };
    </script>
</body>

</html>