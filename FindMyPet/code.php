<?php

require_once 'database/pdo.php';
include 'database/functions.php';
require_once __DIR__ . '/includes/NotificationHelper.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/dompdf/dompdf/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\RedirectResponse;

date_default_timezone_set('Asia/Yangon');

function sendEmailVerify($name, $email, $verificationToken)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->SMTPAuth   = true;

        $mail->Host = 'smtp.gmail.com';
        $mail->Username   = '';
        $mail->Password   = '';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom("", "FindMyPet Support");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Email Verification from FindMyPet";

        $email_template = "
        <h2>Hello {$name},</h2>
        <p>Welcome to <strong>FindMyPet</strong>.</p>
        <p>
            Thank you for creating an account. To activate your account and start using our
            Lost and Found Pet Information System, please verify your email address by
            clicking the link below.
        </p>
        <a href='http://localhost/cproj/user/verify_email.php?token={$verificationToken}'>Verify Email</a>
        <hr>
        <p>
            Thank you,<br>
            <strong>FindMyPet Team</strong><br>
            Lost and Found Pet Information System
        </p>
    ";

        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}";
    }
}

// Handle User Registration
if (isset($_POST['btn_register'])) {

    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $township = trim($_POST['township'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    $errors = [];
    $input = $_POST;

    //Validate Required Fields
    if (empty($name)) {
        $errors['full_name'] = 'Full Name is required.';
    }
    if (empty($email)) {
        $errors['email'] = 'Email Address is required.';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Phone Number is required.';
    }
    if (empty($township)) {
        $errors['township'] = 'Township is required.';
    }
    if (empty($address)) {
        $errors['address'] = 'Address is required.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }
    if (empty($confirmPassword)) {
        $errors['confirm_password'] = 'Confirm Password is required.';
    }

    $hasEmptyFields = !empty($errors);

    //Validate Email Format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    //Check Duplicate Email
    if (!empty($email) && !isset($errors['email'])) {
        $emailSql = "SELECT email FROM users WHERE email = :email LIMIT 1";
        $emailStmt = $pdo->prepare($emailSql);
        $emailStmt->execute(['email' => $email]);
        if ($emailStmt->fetch()) {
            $errors['email'] = 'Email already exists.';
        }
    }

    //Validate Password Match
    if (!empty($password) && !empty($confirmPassword) && $password !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    //Validate Password Strength
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

    if (!empty($password) && !isset($errors['password']) && !preg_match($passwordPattern, $password)) {
        $errors['password'] = 'Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.';
    }

    //Handle Errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['input'] = $input;

        if ($hasEmptyFields) {
            setMessage('Please enter in all required fields.', 'error');
        } else {
            setMessage('Please fix the errors below.', 'error');
        }
        redirect('user/register.php');
    }

    // Generate Verification Token
    $verificationToken = bin2hex(random_bytes(16));

    // Set Token Expiration 
    $tokenExpire = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Hash Password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Retrieve User Role
    $roleSql = "SELECT id FROM role WHERE role_name = 'user'";
    $roleStmt = $pdo->prepare($roleSql);
    $roleStmt->execute();
    $roleId = $roleStmt->fetchColumn();

    // Insert New User Record
    $sql = "INSERT INTO users (role_id, township_id, full_name, email, password, phone, address, verification_token, token_expire) 
            VALUES (:role_id, :township_id, :name, :email, :hashed_password, :phone, :address, :verification_token, :token_expire)";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'role_id' => $roleId,
        'township_id' => $township,
        'name' => $name,
        'email' => $email,
        'hashed_password' => $hashedPassword,
        'phone' => $phone,
        'address' => $address,
        'verification_token' => $verificationToken,
        'token_expire' => $tokenExpire
    ]);

    if ($result) {
        // Send Verification Email
        sendEmailVerify($name, $email, $verificationToken);

        setMessage('Registration successful. Please check your email to verify your account.', 'success');
        redirect('user/login.php');
    } else {
        setMessage('Registration Failed. Please try again.', 'error');
        redirect('user/register.php');
    }
}

// Handle Resend Verification
if (isset($_POST['btn_resend_verification'])) {

    $email = trim($_POST['email'] ?? '');

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setMessage("Please enter a valid email address.", "error");
        redirect("user/resend_verification.php");
    }

    // Retrieve User
    $sql = "SELECT id, full_name, email, is_verified FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if already verified
        if ($user['is_verified'] == '1') {
            setMessage("Your email address has already been verified. Please log in.", "info");
            redirect("user/login.php");
        }

        // Generate new token and expiration 
        $newVerificationToken = bin2hex(random_bytes(16));
        $newTokenExpire = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Update user with new token
        $updateSql = "UPDATE users SET verification_token = :verification_token, token_expire = :token_expire WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'verification_token' => $newVerificationToken,
            'token_expire' => $newTokenExpire,
            'id' => $user['id']
        ]);

        // Send new verification email
        sendEmailVerify($user['full_name'], $user['email'], $newVerificationToken);

        setMessage("A new verification link has been sent to your email address.", "success");
        redirect("user/login.php");
    } else {
        setMessage("If an account with that email address exists and has not yet been verified, a new verification link has been sent.", "success");
        redirect("user/login.php");
    }
}

function sendPasswordResetEmail($name, $email, $resetToken)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPAuth   = true;

        $mail->Host = 'smtp.gmail.com';
        $mail->Username   = '';
        $mail->Password   = '';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom("", "FindMyPet Support");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Password Reset Request - FindMyPet";

        $email_template = "
            <h2>Hello {$name},</h2>
            <p>We received a request to reset your password for your account.</p>
            <p>Click the button below to reset your password. This link will expire in <strong>1 hour</strong>.</p>
            <a href='http://localhost/cproj/user/reset_password.php?token={$resetToken}'>Reset Password</a>
            <p>If you did not request this, please ignore this email. Your password will remain unchanged.</p>
            <hr>
            <p>Thank you,<br><strong>FindMyPet Team</strong></p>
        ";

        $mail->Body = $email_template;
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}");
    }
}

// Handle Forgot Password Request
if (isset($_POST['btn_forgot_password'])) {

    $email = trim($_POST['email'] ?? '');

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setMessage("Please enter a valid email address.", "error");
        redirect("user/forgot_password.php");
    }

    // Check if user exists
    $sql = "SELECT id, full_name FROM users WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate reset token and expiration
        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Update User Record
        $updateSql = "UPDATE users SET reset_token = :token, reset_expire = :expire WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'token' => $token,
            'expire' => $expire,
            'id' => $user['id']
        ]);

        // Send email
        sendPasswordResetEmail($user['full_name'], $email, $token);
    }
    setMessage("If an account with that email exists, a password reset link has been sent.", "success");
    redirect('user/login.php');
}

// Handle Password Reset
if (isset($_POST['btn_reset_password'])) {

    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate Token 
    if (empty($token)) {
        setMessage("Invalid reset link.", "error");
        redirect('user/login.php');
    }

    $sql = "SELECT id, reset_expire FROM users WHERE reset_token = :token LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validate Token and Expiration
    if (!$user || strtotime($user['reset_expire']) < time()) {
        setMessage("This reset link has expired.", "error");
        redirect('user/login.php');
    }

    // Validate Passwords
    if (empty($newPassword) || empty($confirmPassword)) {
        setMessage("Please fill in all fields.", "error");
        redirect('user/reset_password.php?token=' . urlencode($token));
    }
    if ($newPassword !== $confirmPassword) {
        setMessage("Passwords do not match.", "error");
        redirect('user/reset_password.php?token=' . urlencode($token));
    }
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($passwordPattern, $newPassword)) {
        setMessage("Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.", "error");
        redirect('user/reset_password.php?token=' . urlencode($token));
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $updateSql = "UPDATE users SET password = :password, reset_token = NULL, reset_expire = NULL WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([
        'password' => $hashedPassword, 
        'id' => $user['id']
    ]);

    setMessage("Password reset successfully. Please log in.", "success");
    redirect('user/login.php');
}

// Handle User Login
if (isset($_POST['btn_login'])) {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $errors = [];
    $input = ['email' => $email];

    // Validate Required Field
    if (empty($email)) {
        $errors['email'] = 'Email Address is required.';
    }
    if (empty($password)) {
        $errors['password'] = 'Password is required.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['input'] = $input;
        redirect("user/login.php");
    }

    // Retrieve User Data
    $loginSql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN role r ON u.role_id = r.id 
                WHERE u.email = :email AND r.role_name = 'user'
                LIMIT 1";
    $loginStmt = $pdo->prepare($loginSql);
    $loginStmt->execute([':email' => $email]);
    $row = $loginStmt->fetch(PDO::FETCH_ASSOC);

    // Verify User Password
    if ($row && password_verify($password, $row['password'])) {
        $status = $row['status'] ?? 'Active';
        // Check Account Status
        if ($status === 'Banned') {
            setMessage('Your account has been permanently banned.', 'error');
            redirect("user/login.php");
        }
        if ($status === 'Suspended') {
            $suspendedUntil = $row['suspended_until'] ?? null;
            if ($suspendedUntil && strtotime($suspendedUntil) > time()) {
                $suspendDate = date('F j, Y, g:i a', strtotime($suspendedUntil));
                setMessage("Your account is temporarily suspended until {$suspendDate}. Please try logging in after this date.", 'error');
                redirect("user/login.php");
            }
            $updateSql = "UPDATE users SET status = 'Active', suspended_until = NULL WHERE id = :id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                'id' => $row['id']
            ]);
            $row['status'] = 'Active';
            $row['suspended_until'] = null;
        }
        // Check Email Verification Status
        if ($row['is_verified'] == 1) {
            $_SESSION['authenticated'] = TRUE;
            $_SESSION['auth_user'] = [
                'id' => $row['id'],
                'name' => $row['full_name'],
                'email' => $row['email']
            ];
            setMessage('Welcome Back, ' . $row['full_name'], 'success');
            $redirectUrl = $_SESSION['redirect_url'] ?? 'user/my_posts.php';
            unset($_SESSION['redirect_url']);
            redirect($redirectUrl);
        } else {
            setMessage('Please verify your email to login.', 'error');
            redirect("user/login.php");
        }
    } else {
        // Handle Invalid credentials 
        $_SESSION['input'] = $input;
        setMessage('Invalid Email or Password.', 'error');
        redirect("user/login.php");
    }
}

// Handle Post Creation and Save Draft
if (isset($_POST['btn_create_post']) || isset($_POST['btn_save_draft'])) {

    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please Login to Create a Post.', 'error');
        redirect('user/login.php');
    }

    $userId = $_SESSION['auth_user']['id'];
    $isDraft = isset($_POST['btn_save_draft']);
    $status = $isDraft ? 'draft' : 'active';

    // Collect Form Data
    $formData = [
        'petName'          => trim($_POST['pet_name'] ?? ''),
        'petType'          => trim($_POST['pet_type'] ?? ''),
        'breedId'          => $_POST['breed_name'] ?? '',
        'petAge'           => trim($_POST['pet_age'] ?? ''),
        'petColor'         => trim($_POST['pet_color'] ?? ''),
        'petGender'        => trim($_POST['pet_gender'] ?? ''),
        'postType'         => trim($_POST['post_type'] ?? ''),
        'description'      => trim($_POST['post_des'] ?? ''),
        'lastSeenDate'     => trim($_POST['last_seen_date'] ?? ''),
        'lastSeenTime'     => trim($_POST['last_seen_time'] ?? ''),
        'lastSeenLocation' => trim($_POST['last_seen_location'] ?? ''),
        'cityId'           => $_POST['city'] ?? '',
        'townshipId'       => $_POST['township'] ?? '',
        'email'            => trim($_POST['email'] ?? ''),
        'phone'            => trim($_POST['phone'] ?? ''),
    ];

    if (empty($formData['petName'])) {
        $formData['petName'] = 'Unknown';
    }

    $errors = [];

    // Validate Form Data
    if (empty($formData['petType'])) {
        $errors['pet_type'] = 'Pet type is required.';
    }
    if (empty($formData['breedId'])) {
        $errors['breed_name'] = 'Breed is required.';
    }
    if (empty($formData['postType'])) {
        $errors['post_type'] = 'Post type is required.';
    }
    if (empty($formData['cityId'])) {
        $errors['city'] = 'City is required.';
    }
    if (empty($formData['townshipId'])) {
        $errors['township'] = 'Township is required.';
    }
    if (!isset($_FILES['pet_image']) || $_FILES['pet_image']['error'] !== UPLOAD_ERR_OK) {
        $errors['pet_image'] = 'Pet image is required.';
    } else {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024;
        if (!in_array($_FILES['pet_image']['type'], $allowedTypes)) {
            $errors['pet_image'] = 'Invalid image format. Only JPG and PNG allowed.';
        } elseif ($_FILES['pet_image']['size'] > $maxSize) {
            $errors['pet_image'] = 'Image size exceeds 5MB limit.';
        }
    }

    if (!$isDraft) {
        if (empty($formData['petColor'])) {
            $errors['pet_color'] = 'Color is required.';
        }
        if (empty($formData['petGender'])) {
            $errors['pet_gender'] = 'Gender is required.';
        }
        if (empty($formData['lastSeenDate'])) {
            $errors['last_seen_date'] = 'Last seen date is required.';
        }
        if (empty($formData['lastSeenTime'])) {
            $errors['last_seen_time'] = 'Last seen time is required.';
        }
        if (empty($formData['lastSeenLocation'])) {
            $errors['last_seen_location'] = 'Last seen location is required.';
        }
        if (empty($formData['description'])) {
            $errors['post_des'] = 'Description is required.';
        }
        if (empty($formData['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }
        if (empty($formData['phone'])) {
            $errors['phone'] = 'Phone number is required.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $formData;
        setMessage('Please correct the errors below.', 'error');
        redirect('user/create_post.php');
    }

    // Upload Pet Image
    $finalImage = null;
    if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/pets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = $_FILES['pet_image']['name'];
        $imageTmp = $_FILES['pet_image']['tmp_name'];

        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        $fileName = time() . '_' . uniqid() . '.' . strtolower($imageExtension);
        $destination = $uploadDir . $fileName;
        $finalImage = 'uploads/pets/' . $fileName;

        if (!move_uploaded_file($imageTmp, $destination)) {
            $errors['pet_image'] = 'Failed to upload image.';
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $formData;
            setMessage('Failed to upload image.', 'error');
            redirect('user/create_post.php');
        }
    }

    try {
        // Start Database Transaction
        $pdo->beginTransaction();

        // Insert New Pet Record
        $petSql = "INSERT INTO pet (breed_id, name, color, age, gender, image) 
                       VALUES (:breed_id, :name, :color, :age, :gender, :image)";
        $petStmt = $pdo->prepare($petSql);
        $petStmt->execute([
            'breed_id' => $formData['breedId'],
            'name'     => $formData['petName'],
            'color'    => $formData['petColor'],
            'age'      => $formData['petAge'],
            'gender'   => $formData['petGender'],
            'image'    => $finalImage
        ]);
        $petId = $pdo->lastInsertId();

        // Insert New Post Record
        $sql = "INSERT INTO post (user_id, pet_id, township_id, post_type, description, 
                      last_seen_date, last_seen_time, last_seen_location, contact_phone, contact_email, status) 
                    VALUES (:user_id, :pet_id, :township_id, :post_type, :desc, 
                            :last_seen_date, :last_seen_time, :last_seen_loc, :phone, :email, :status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'user_id'          => $userId,
            'pet_id'           => $petId,
            'township_id'      => $formData['townshipId'],
            'post_type'        => $formData['postType'],
            'desc'             => $formData['description'],
            'last_seen_date'   => $formData['lastSeenDate'],
            'last_seen_time'   => $formData['lastSeenTime'],
            'last_seen_loc'    => $formData['lastSeenLocation'],
            'phone'            => $formData['phone'],
            'email'            => $formData['email'],
            'status'           => $status
        ]);
        $newPostId = $pdo->lastInsertId();

        // Commit All Changes
        $pdo->commit();

        if ($status === 'active') {
            $postData = [
                'user_id'        => $userId,
                'breed_id'       => $formData['breedId'],
                'pet_color'      => $formData['petColor'],
                'pet_gender'     => $formData['petGender'],
                'township_id'    => $formData['townshipId'],
                'post_type'      => $formData['postType'],
                'last_seen_date' => $formData['lastSeenDate']
            ];

            // Process Match and Notification for Active Posts
            processActivePost($pdo, $newPostId, $postData);

            setMessage('Post created successfully!', 'success');
            redirect('user/post_details.php?id=' . $newPostId);
        } else {
            setMessage('Draft saved successfully.', 'success');
            redirect('user/my_posts.php?tab=draft');
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            // Rollback for Errors
            $pdo->rollBack();
        }
        error_log("Post Creation Error: " . $e->getMessage());

        if ($finalImage && file_exists(__DIR__ . '/../' . $finalImage)) {
            // Remove uploaded image for error
            unlink(__DIR__ . '/../' . $finalImage);
        }

        setMessage('An error occurred while creating your post. Please try again.', 'error');
        redirect('user/create_post.php');
    }
}

// Handle Add Comment
if (isset($_POST['btn_add_comment'])) {
    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        $_SESSION['comment_message'] = 'Please login to add a comment.';
        $_SESSION['comment_message_type'] = 'error';
        redirect('user/login.php');
    }

    $userId = $_SESSION['auth_user']['id'];
    $userName = $_SESSION['auth_user']['name'];
    $postId = filter_var($_POST['post_id'], FILTER_VALIDATE_INT);
    $comment = trim($_POST['comment']);

    // Validate Post ID
    if (!$postId) {
        $_SESSION['comment_message'] = 'Invalid Post.';
        $_SESSION['comment_message_type'] = 'error';
        redirect('user/post_details.php');
    }

    // Validate Comment Field
    if (empty($comment)) {
        $_SESSION['comment_message'] = 'Please enter a comment.';
        $_SESSION['comment_message_type'] = 'error';
        redirect('user/post_details.php?id=' . $postId);
    }

    // Insert User Comment
    $sql = "INSERT INTO comment (user_id, post_id, comment_text) VALUES (:user_id, :post_id, :comment)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'user_id' => $userId,
        'post_id' => $postId,
        'comment' => $comment
    ]);

    if ($result) {
        // Retrieve Post Owner ID
        $stmt = $pdo->prepare("SELECT user_id FROM post WHERE id = :id");
        $stmt->execute(['id' => $postId]);
        $postOwner = $stmt->fetch(PDO::FETCH_ASSOC);
        $postOwnerId = $postOwner['user_id'];

        if ($postOwnerId && $postOwnerId != $userId) {
            // Retrieve Post Owner Notification Preference
            $preferences = getUserNotiPreferences($pdo, (int)$postOwnerId);

            // Send Notification If Comment Notification Is Enabled
            if ($preferences['comment_notifications']) {

                $notiHelper = new NotificationHelper($pdo);

                $message = "{$userName} commented on your post.";

                $link = "post_details.php?id=" . $postId;

                $notiHelper->sendToUser($postOwnerId, $postId, $message, 'comment', $link);
            }
        }
        $_SESSION['comment_message'] = 'Comment added successfully.';
        $_SESSION['comment_message_type'] = 'success';
        redirect('user/post_details.php?id=' . $postId);
    } else {
        $_SESSION['comment_message'] = 'Failed to add comment.';
        $_SESSION['comment_message_type'] = 'error';
        redirect('user/post_details.php?id=' . $postId);
    }
}

// Hanlde Download Pet Poster
if (isset($_GET['download_poster']) && $_GET['download_poster'] == '1') {

    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please login to download the poster.', 'error');
        redirect('user/login.php');
    }

    $postId = filter_var($_GET['post_id'] ?? 0, FILTER_VALIDATE_INT);

    // Validate Post ID
    if (!$postId) {
        setMessage('Invalid Post ID.', 'error');
        redirect('user/post_details.php?id=' . $postId);
    }

    // Retrieve Post Details
    $postResponse = getPostById($pdo, $postId);

    if ($postResponse['status'] != 200) {
        setMessage('Post not found.', 'error');
        redirect('user/post_details.php?id=' . $postId);
    }

    $post = $postResponse['data'];

    try {

        // Generate HTML Poster
        $html = generatePosterHtml($post);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        // Create PDF File
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Create File Name
        $fileName = strtolower(str_replace(' ', '_', $post['name'])). "_". strtolower($post['post_type']). "_pet_poster.pdf";

        //Download PDF Poster
        $dompdf->stream($fileName, ["Attachment" => true]);

        exit;
    } catch (Exception $e) {

        // Handle Error
        setMessage('Failed to generate poster. Please try again.', 'error');
        redirect('user/post_details.php?id=' . $postId);
    }
}

// Hanlde Content Report Submission
if (isset($_POST['btn_submit_report'])) {

    // Check User Authentication
    if (!isset($_SESSION['auth_user']['id'])) {
        $_SESSION['report_message'] = 'Please log in to report content.';
        $_SESSION['report_message_type'] = 'error';
        redirect('user/login.php');
    }

    $reporterId = $_SESSION['auth_user']['id'];

    $targetId   = $_POST['target_id'] ?? null;
    $targetType = $_POST['target_type'] ?? '';
    $reason      = trim($_POST['report_reason'] ?? '');
    $details     = trim($_POST['report_details'] ?? '');
    $allowedReasons = [
        'Spam',
        'False Information',
        'Inappropriate Content',
        'Harassment',
        'Other'
    ];

    // Validate Required Fields
    if (empty($targetId) || empty($targetType) || empty($reason)) {
        $_SESSION['report_message'] = 'Please select a reason for reporting.';
        $_SESSION['report_message_type'] = 'error';
        redirect($_SERVER['HTTP_REFERER'] ?? 'post_details.php');
    }

    if (!in_array($reason, $allowedReasons)) {
        $_SESSION['report_message'] = 'Invalid report reason.';
        $_SESSION['report_message_type'] = 'error';
        redirect($_SERVER['HTTP_REFERER'] ?? 'post_details.php');
    }

    if (empty($details)) {
        $_SESSION['report_message'] = 'Please enter additional details.';
        $_SESSION['report_message_type'] = 'error';
        redirect($_SERVER['HTTP_REFERER'] ?? 'post_details.php');
    }

    // Determine Content Type
    $postId = null;
    $commentId = null;

    if ($targetType === 'post') {
        $postId = (int)$targetId;
    } elseif ($targetType === 'comment') {
        $commentId = (int)$targetId;
    } else {
        $_SESSION['report_message'] = 'Invalid report target.';
        $_SESSION['report_message_type'] = 'error';
        redirect($_SERVER['HTTP_REFERER'] ?? 'post_details.php');
    }

    // Check Duplicate User Report
    if ($postId !== null) {
        $checkSql = "SELECT id
                     FROM content_report
                     WHERE reporter_id = :reporter_id
                     AND post_id = :post_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'reporter_id' => $reporterId,
            'post_id' => $postId
        ]);
    } else {
        $checkSql = "SELECT id
                     FROM content_report
                     WHERE reporter_id = :reporter_id
                     AND comment_id = :comment_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'reporter_id' => $reporterId,
            'comment_id' => $commentId
        ]);
    }

    // Display Error Message for Duplicate Report
    if ($checkStmt->fetch()) {
        $_SESSION['report_message'] = 'You have already reported this content.';
        $_SESSION['report_message_type'] = 'warning';
        redirect($_SERVER['HTTP_REFERER'] ?? 'post_details.php');
    }

    // Insert New Report Record
    $sql = "INSERT INTO content_report
            (reporter_id, post_id, comment_id, reason, details)
            VALUES
            (:reporter_id, :post_id, :comment_id, :reason, :details)";

    $stmt = $pdo->prepare($sql);

    $result = $stmt->execute([
        'reporter_id' => $reporterId,
        'post_id' => $postId,
        'comment_id' => $commentId,
        'reason' => $reason,
        'details' => $details
    ]);

    if ($result) {
        $_SESSION['report_message'] = 'Thank you! Your report has been submitted successfully.';
        $_SESSION['report_message_type'] = 'success';
    } else {
        $_SESSION['report_message'] = 'Failed to submit the report. Please try again later.';
        $_SESSION['report_message_type'] = 'error';
    }

    redirect($_SERVER['HTTP_REFERER'] ?? 'post_details.php');
}

// Handle Contact Message Submission
if (isset($_POST['btn_send_contact'])) {
    // Check if User is logged in
    $isLoggedIn = isset($_SESSION['authenticated']) && isset($_SESSION['auth_user']);
    // Retrieve Information of Logged in user
    $fullName = $isLoggedIn ? $_SESSION['auth_user']['name'] : trim($_POST['name'] ?? '');
    $email     = $isLoggedIn ? $_SESSION['auth_user']['email'] : trim($_POST['email'] ?? '');

    $subject   = trim($_POST['subject'] ?? '');
    $message   = trim($_POST['message'] ?? '');
    $userId   = $isLoggedIn ? (int)$_SESSION['auth_user']['id'] : null;

    $errors = [];

    // Validate Sender Name
    if (empty($fullName)) {
        $errors[] = "Name is required.";
    } elseif (strlen($fullName) < 2) {
        $errors[] = "Name must be at least 2 characters.";
    }

    // Validate Email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    // Validate Subject
    if (empty($subject)) {
        $errors[] = "Please select a subject.";
    }


    // Validate Message
    if (empty($message)) {
        $errors[] = "Message is required.";
    } elseif (strlen($message) < 10) {
        $errors[] = "Message must be at least 10 characters.";
    }

    if (!empty($errors)) {
        setMessage(implode("\n", $errors), 'error');
        redirect('user/contact.php');
    }

    // Insert Contact Message Record
    $sql = "INSERT INTO contact_message (user_id, full_name, email, subject, message) 
            VALUES (:user_id, :full_name, :email, :subject, :message)";
    $stmt = $pdo->prepare($sql);

    $result = $stmt->execute([
        'user_id'   => $userId,
        'full_name' => $fullName,
        'email'     => $email,
        'subject'   => $subject,
        'message'   => $message
    ]);

    if ($result) {
        setMessage("Thank you! Your message has been sent successfully. We'll get back to you soon.", 'success');
    } else {
        setMessage("Something went wrong. Please try again later.", 'error');
    }

    redirect('user/contact.php');
}

// Handle Own Post Update
if (isset($_POST['btn_update_own_post'])) {

    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please login first to update post.', 'error');
        redirect('user/login.php');
    }

    $currentUserId = $_SESSION['auth_user']['id'];
    $postId = $_POST['post_id'];
    $petId  = $_POST['pet_id'];

    $postType          = $_POST['post_type'];
    $description        = trim($_POST['description']);
    $lastSeenDate     = !empty($_POST['last_seen_date']) ? $_POST['last_seen_date'] : null;
    $lastSeenTime     = !empty($_POST['last_seen_time']) ? $_POST['last_seen_time'] : null;
    $lastSeenLocation = trim($_POST['last_seen_location']);
    $contactPhone      = trim($_POST['contact_phone']);
    $contactEmail      = trim($_POST['contact_email']);
    $townshipId        = $_POST['township_id'];
    $status             = $_POST['status'];

    $petTypeId = $_POST['pet_type_id'];
    $breedId    = $_POST['breed_id'];
    $petName    = trim($_POST['pet_name']);
    $petColor   = trim($_POST['pet_color']);
    $petAge     = !empty($_POST['pet_age']) ? (int)$_POST['pet_age'] : null;
    $petGender  = $_POST['pet_gender'];

    $post = getPostById($pdo, $postId);

    if ($post['status'] != 200) {
        setMessage("Post not found.", 'error');
        redirect("user/my_posts.php");
    }

    // Get Post Owner ID
    $postOwnerId = $post['data']['user_id'];

    // Check Post Ownership
    if ($postOwnerId != $currentUserId) {
        setMessage("You are not authorized to edit this post.", 'error');
        redirect("user/my_posts.php");
    }
    // Validate Required Fields
    if (empty($postType) || empty($petTypeId) || empty($breedId) || empty($contactPhone) || empty($contactEmail) || empty($townshipId) || empty($status)) {
        setMessage("Please fill in all required fields.", "error");
        redirect("user/edit_post.php?id=" . $postId);
    }

    $imagePath = $post['data']['image'] ?? null;
    if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/pets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Validate Uploaded Image
        $fileExt = strtolower(pathinfo($_FILES['pet_image']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExt, $allowedExts, true)) {
            setMessage('Invalid image format. Allowed: jpg, jpeg, png.', 'error');
            redirect("user/edit_post.php?id=" . $postId);
        }

        // Create File Name
        $fileName = 'pet_' . time() . '_' . uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pet_image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/pets/' . $fileName;

            // Delete Old Image
            if (!empty($post['data']['image'])) {
                $oldImagePath = '../' . $post['data']['image'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
        } else {
            setMessage('Failed to upload image.', 'error');
            redirect("user/edit_post.php?id=" . $postId);
        }
    }

    try {
        // Start Database Transaction
        $pdo->beginTransaction();

        // Update Pet Information
        $petSql = "UPDATE pet SET breed_id = :breed_id, name = :name, color = :color, age = :age, gender = :gender";
        $petParams = [
            'breed_id' => $breedId,
            'name' => $petName,
            'color' => $petColor,
            'age' => $petAge,
            'gender' => $petGender,
            'pet_id' => $petId
        ];

        // Update Image Only If New Image Exists
        if ($imagePath != $post['data']['image']) {
            $petSql .= ", image = :image";
            $petParams['image'] = $imagePath;
        }
        $petSql .= " WHERE id = :pet_id";
        $petStmt = $pdo->prepare($petSql);
        $petUpdated = $petStmt->execute($petParams);

        // Update Post Information
        $postSql = "UPDATE post SET post_type = :post_type, description = :description, last_seen_date = :last_seen_date, last_seen_time = :last_seen_time, last_seen_location = :last_seen_location, contact_phone = :contact_phone, contact_email = :contact_email, township_id = :township_id, status = :status WHERE id = :post_id";
        $postStmt = $pdo->prepare($postSql);
        $postUpdated = $postStmt->execute([
            'post_type'          => $postType,
            'description'        => $description,
            'last_seen_date'     => $lastSeenDate,
            'last_seen_time'     => $lastSeenTime,
            'last_seen_location' => $lastSeenLocation,
            'contact_phone'      => $contactPhone,
            'contact_email'      => $contactEmail,
            'township_id'        => $townshipId,
            'status'             => $status,
            'post_id'            => $postId
        ]);

        // Commit Changes 
        if ($petUpdated && $postUpdated) {
            $pdo->commit();
            setMessage('Post updated successfully.', 'success');
            redirect('user/my_posts.php');
        } else {
            $pdo->rollBack();
            setMessage('Failed to update post.', 'error');
            redirect("user/edit_post.php?id=" . $postId);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Post Update Error: " . $e->getMessage());
        setMessage('Something went wrong while updating post.', 'error');
        redirect("user/edit_post.php?id=" . $postId);
    }
}

// Handle Post Status Update
if (isset($_POST['post_id']) && isset($_POST['status'])) {
    $postId = (int)$_POST['post_id'];
    $status = ucfirst(strtolower(trim($_POST['status'])));

    $allowedStatuses = ['Active', 'Reunited'];
    // Validate Status
    if (!in_array($status, $allowedStatuses)) {
        setMessage("Invalid status selected.", 'error');
        redirect("user/my_posts.php");
    }
    // Update Post Status
    $sql = "UPDATE post SET status = :status WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'status' => $status,
        'id' => $postId
    ]);

    if ($result) {
        setMessage("Post status updated to '" . $status . "' successfully.", 'success');
    } else {
        setMessage("Failed to update post status.", 'error');
    }

    redirect("user/my_posts.php");
}

// Handle Match Status Update
if (isset($_POST['update_match_status'])) {

    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please login first to update match status.', 'error');
        redirect('user/login.php');
    }
    $currentUserId = $_SESSION['auth_user']['id'];
    $matchId = (int)$_POST['match_id'];
    $postId = (int)$_POST['post_id'];
    $formStatus = $_POST['update_match_status'];

    if ($formStatus === 'accepted') {
        $newStatus = 'Accepted';
        $successMsg = 'accepted';
    } elseif ($formStatus === 'rejected') {
        $newStatus = 'Rejected';
        $successMsg = 'rejected';
    } else {
        setMessage('Invalid status value.', 'error');
        redirect("user/potential_matches.php?id=" . $postId);
    }
    // Retrieve Post Details
    $post = getPostById($pdo, $postId);
    // Check Post Exists
    if ($post['status'] != 200) {
        setMessage('Post not found.', 'error');
        redirect('user/my_posts.php');
    }

    $postOwnerId = $post['data']['user_id'];
    // Check Authorization
    if ($postOwnerId != $currentUserId) {
        setMessage('You are not authorized to update this match.', 'error');
        redirect('user/my_posts.php');
    }
    // Verify Match is for selected Post
    $matchSql = "SELECT id, post_id FROM post_match WHERE id = :match_id AND post_id = :post_id";
    $matchStmt = $pdo->prepare($matchSql);
    $matchStmt->execute([
        'match_id' => $matchId,
        'post_id' => $postId
    ]);

    if ($matchStmt->rowCount() == 0) {
        setMessage('Match not found or does not belong to this post.', 'error');
        redirect("user/potential_matches.php?id=" . $postId);
    }

    try {
        // Update Match Status
        $sql = "UPDATE post_match SET status = :status WHERE id = :match_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'status' => $newStatus,
            'match_id' => $matchId
        ]);

        setMessage("Match successfully {$successMsg}.", 'success');
        redirect("user/potential_matches.php?id=" . $postId);
    } catch (PDOException $e) {
        setMessage('Failed to update match status. Please try again.', 'error');
        redirect("user/potential_matches.php?id=" . $postId);
    }
}

// Handle Profile Update
if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {

    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please login first to update profile.', 'error');
        redirect('user/login.php');
    }

    $userId = (int)$_POST['user_id'];

    // Validate Authorization
    if ($userId !== (int)$_SESSION['auth_user']['id']) {
        setMessage('Unauthorized action.', 'error');
        redirect('user/profile.php');
    }

    $fullName   = trim($_POST['full_name'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $address     = trim($_POST['address'] ?? '');
    $townshipId = !empty($_POST['township_id']) ? (int)$_POST['township_id'] : null;
    $cityId     = !empty($_POST['city_id']) ? (int)$_POST['city_id'] : null;

    $errors = [];
    $input = [
        'full_name'   => $fullName,
        'phone'       => $phone,
        'address'     => $address,
        'township_id' => $townshipId,
        'city_id'     => $cityId
    ];

    // Validate Required Fields
    if (empty($fullName)) {
        $errors['full_name'] = 'Full name is required.';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required.';
    }
    if (empty($townshipId)) {
        $errors['township_id'] = 'Township is required.';
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        $_SESSION['profile_data'] = $input;
        setMessage('Please correct the errors below.', 'error');
        redirect('user/profile.php');
    }

    try {
        // Update User Information
        $sql = "UPDATE users SET 
                full_name = :full_name, 
                phone = :phone, 
                address = :address, 
                township_id = :township_id 
                WHERE id = :user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'full_name'   => $fullName,
            'phone'       => $phone,
            'address'     => $address,
            'township_id' => $townshipId,
            'user_id'     => $userId
        ]);
        setMessage('Profile updated successfully.', 'success');
        redirect('user/profile.php');
    } catch (PDOException $e) {
        setMessage('Failed to update profile. Please try again.', 'error');
        redirect('user/profile.php');
    }
}

// Handle Password Change
if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please login first to change password.', 'error');
        redirect('user/login.php');
    }

    $userId = (int)$_POST['user_id'];

    // Validate Authorization
    if ($userId !== (int)$_SESSION['auth_user']['id']) {
        setMessage('Unauthorized action.', 'error');
        redirect('user/profile.php');
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate Required Fields
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        setMessage('All password fields are required.', 'error');
        redirect('user/profile.php');
    }
    // Verify Passwords Match
    if ($newPassword !== $confirmPassword) {
        setMessage('New passwords do not match.', 'error');
        redirect('user/profile.php');
    }
    // Validate Password Strength
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($passwordPattern, $newPassword)) {
        setMessage("Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.", 'error');
        redirect('user/profile.php');
    }

    try {
        // Retrieve Current Password
        $sql = "SELECT password FROM users WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validate Entered Current Password
        if (!$userData || !password_verify($currentPassword, $userData['password'])) {
            setMessage('Current password is incorrect.', 'error');
            redirect('user/profile.php');
        }
        // Prevent Reusing Current Password
        if (password_verify($newPassword, $userData['password'])) {
            setMessage('New password cannot be the same as your current password.', 'error');
            redirect('user/profile.php');
        }

        // Update User Password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $updateSql = "UPDATE users SET password = :password WHERE id = :user_id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            'password' => $hashedPassword,
            'user_id'  => $userId
        ]);

        setMessage('Password changed successfully.', 'success');
        redirect('user/profile.php');
    } catch (PDOException $e) {

        setMessage('Failed to change password. Please try again.', 'error');
        redirect('user/profile.php');
    }
}

// Handle Notification Preferences Update
if (isset($_POST['action']) && $_POST['action'] === 'update_notification_preferences') {
    // Check User Authentication
    if (!isset($_SESSION['authenticated']) || !isset($_SESSION['auth_user']['id'])) {
        setMessage('Please login first to update notification preferences.', 'error');
        redirect('user/login.php');
    }

    $userId = (int)$_POST['user_id'];

    // Validate Authorization
    if ($userId !== (int)$_SESSION['auth_user']['id']) {
        setMessage('Unauthorized action.', 'error');
        redirect('user/profile.php');
    }

    $emailNotifications    = isset($_POST['email_notifications']) ? 1 : 0;
    $matchNotifications    = isset($_POST['match_notifications']) ? 1 : 0;
    $commentNotifications  = isset($_POST['comment_notifications']) ? 1 : 0;
    $postNotifications     = isset($_POST['post_notifications']) ? 1 : 0;

    try {
        // Update User Preferences
        $sql = "UPDATE users SET 
                email_notifications = :email_notifications,
                match_notifications = :match_notifications,
                comment_notifications = :comment_notifications,
                post_notifications = :post_notifications
                WHERE id = :user_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email_notifications'    => $emailNotifications,
            'match_notifications'    => $matchNotifications,
            'comment_notifications'  => $commentNotifications,
            'post_notifications'     => $postNotifications,
            'user_id'                => $userId
        ]);

        setMessage('Notification preferences saved successfully.', 'success');
        redirect('user/profile.php');
    } catch (PDOException $e) {
        setMessage('Failed to save preferences. Please try again.', 'error');
        redirect('user/profile.php');
    }
}
