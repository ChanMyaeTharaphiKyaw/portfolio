<?php
require_once '../database/functions.php';
require_once __DIR__ . '/../includes/notificationHelper.php';

date_default_timezone_set('Asia/Yangon');

// Handle Admin Login
if (isset($_POST['btn_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate Required Fields
    if (!empty($email) && !empty($password)) {
        // Retrieve Admin 
        $sql = "SELECT u.*, r.role_name 
                FROM users u 
                JOIN role r ON u.role_id = r.id 
                WHERE u.email = :email AND r.role_name = 'admin'
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validate Password
        if ($row && password_verify($password, $row['password'])) {
            // Authenticated Admin
            $_SESSION['admin_authenticated'] = TRUE;
            $_SESSION['auth_admin'] = [
                'id' => $row['id'],
                'name' => $row['full_name'],
                'email' => $row['email'],
                'role' => $row['role_name']
            ];
            redirect('dashboard.php');
        } else {
            setMessage('Invalid Email or Password.', 'error');
            redirect('login.php');
        }
    } else {
        setMessage('Please enter in all fields.', 'error');
        redirect('login.php');
    }
}

// Handle Add User
if (isset($_POST['btn_add_user'])) {

    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $township = trim($_POST['township'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $errors = [];
    $input = $_POST;

    // Validate Required Fields
    if (empty($fullName)) $errors['full_name'] = 'Full Name is required.';
    if (empty($email)) $errors['email'] = 'Email Address is required.';
    if (empty($phone)) $errors['phone'] = 'Phone Number is required.';
    if (empty($city)) $errors['city'] = 'City is required.';
    if (empty($township)) $errors['township'] = 'Township is required.';
    if (empty($password)) $errors['password'] = 'Password is required.';

    $hasEmptyFields = !empty($errors);

    // Validate Email Format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    // Check Duplicate Email
    if (!empty($email) && !isset($errors['email'])) {
        $emailSql = "SELECT email FROM users WHERE email = :email LIMIT 1";
        $emailStmt = $pdo->prepare($emailSql);
        $emailStmt->execute(['email' => $email]);
        if ($emailStmt->fetch()) {
            $errors['email'] = 'Email already exists.';
        }
    }

    // Validate Password Strength
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!empty($password) && !isset($errors['password']) && !preg_match($passwordPattern, $password)) {
        $errors['password'] = 'Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.';
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['input'] = $input;

        if ($hasEmptyFields) {
            setMessage('Please enter in all required fields.', 'error');
        } else {
            setMessage('Please fix the errors below.', 'error');
        }

        redirect("add_user.php");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $verificationToken = bin2hex(random_bytes(16));

    // Retrieve User Role ID
    $role_sql = "SELECT id FROM role WHERE role_name = 'user'";
    $role_stmt = $pdo->prepare($role_sql);
    $role_stmt->execute();
    $role_id = $role_stmt->fetchColumn();

    // Insert New User Record
    $sql = "INSERT INTO users (role_id, township_id, full_name, email, password, phone, address, verification_token, is_verified, status) 
            VALUES (:role_id, :township_id, :full_name, :email, :hashed_password, :phone, :address, :verification_token, 1, 'Active')";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'role_id' => $role_id,
        'township_id' => $township,
        'full_name' => $fullName,
        'email' => $email,
        'hashed_password' => $hashedPassword,
        'phone' => $phone,
        'address' => $address,
        'verification_token' => $verificationToken
    ]);

    if ($result) {
        setMessage('User successfully added.', 'success');
        redirect("users.php");
    } else {
        setMessage('User creation failed. Please try again.', 'error');
        redirect("add_user.php");
    }
}

// Handle User Update
if (isset($_POST['btn_update_user'])) {
    $userId = $_POST['user_id'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $city = $_POST['city'] ?? '';
    $township = $_POST['township']  ?? '';
    $address = trim($_POST['address'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $isVerified = $_POST['is_verified'] ?? '';
    $status = $_POST['status'] ?? '';

    $errors = [];
    $input = $_POST;

    $user = getById($pdo, 'users', $userId);

    // Verify User Exists
    if ($user['status'] != 200) {
        setMessage('No such id found.', 'error');
        redirect('edit_user.php?id=' . $userId);
    }

    // Validate Required Fields
    if (empty($fullName)) $errors['full_name'] = 'Full Name is required.';
    if (empty($email)) $errors['email'] = 'Email Address is required.';
    if (empty($phone)) $errors['phone'] = 'Phone Number is required.';
    if (empty($city)) $errors['city'] = 'City is required.';
    if (empty($township)) $errors['township'] = 'Township is required.';
    if (empty($address)) $errors['address'] = 'Address is required.';

    $hasEmptyFields = !empty($errors);

    // Validate Email Format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format.';
    }

    // Check Duplicate Email
    if (!empty($email) && !isset($errors['email'])) {
        $emailSql = "SELECT email FROM users WHERE email = :email AND id != :id LIMIT 1";
        $emailStmt = $pdo->prepare($emailSql);
        $emailStmt->execute([
            'email' => $email,
            'id' => $userId
        ]);

        if ($emailStmt->fetch()) {
            $errors['email'] = 'Email already exists.';
        }
    }

    // Validate Password
    if (!empty($password)) {
        $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';

        if (!preg_match($passwordPattern, $password)) {
            $errors['password'] = 'Password must be at least 8 characters long, include uppercase, lowercase, a number, and a special character.';
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['input'] = $input;
        if ($hasEmptyFields) {
            setMessage('Please enter in all required fields.', 'error');
        } else {
            setMessage('Please fix the errors below.', 'error');
        }
        redirect("edit_user.php?id=$userId");
    }

    // Update User Record With New Password
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET township_id = :township, full_name = :full_name, email = :email, password = :password, phone = :phone, address = :address, is_verified = :is_verified, status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'township' => $township,
            'full_name'     => $fullName,
            'email'    => $email,
            'password' => $hashedPassword,
            'phone'    => $phone,
            'address'  => $address,
            'is_verified' => $isVerified,
            'status' => $status,
            'id' => $userId
        ]);
    } else {

        // Update Required User Record
        $sql = "UPDATE users SET township_id = :township, full_name = :full_name, email = :email, phone = :phone, address = :address, is_verified = :is_verified, status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'township' => $township,
            'full_name'     => $fullName,
            'email'    => $email,
            'phone'    => $phone,
            'address'  => $address,
            'is_verified' => $isVerified,
            'status' => $status,
            'id' => $userId
        ]);
    }

    if ($result) {
        setMessage("User updated successfully.", 'success');
        redirect("users.php");
    } else {
        setMessage("User update failed.", 'error');
        redirect("edit_user.php?id=" . $userId);
    }
}

// Handle User Status Update
if (isset($_POST['user_id']) && isset($_POST['status'])) {
    $userId = (int)$_POST['user_id'];
    $status = ucfirst(strtolower(trim($_POST['status'])));

    $allowedStatuses = ['Active', 'Suspended', 'Banned'];
    if (!in_array($status, $allowedStatuses)) {
        setMessage("Invalid status selected.", 'error');
        redirect("users.php");
    }

    $suspendedUntil = null;

    if ($status === 'Suspended') {
        // Suspend account for 7 days
        $suspendedUntil = date('Y-m-d H:i:s', strtotime('+7 days'));
    }

    // Update User Status
    $sql = "UPDATE users SET status = :status, suspended_until = :suspended_until WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'status' => $status,
        'suspended_until' => $suspendedUntil,
        'id' => $userId
    ]);

    if ($result) {
        if ($status === 'Suspended') {
            setMessage("User has been suspended for 7 days.", 'success');
        } elseif ($status === 'Banned') {
            setMessage("User has been permanently banned.", 'success');
        } else {
            setMessage("User account has been activated.", 'success');
        }
    } else {
        setMessage("Failed to update user status.", 'error');
    }

    redirect("users.php");
}

// Handle Admin Post Update
if (isset($_POST['btn_update_post'])) {
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

    // Validate Required Fields
    if (empty($postId) || empty($petId) || empty($postType) || empty($petTypeId) || empty($breedId) || empty($contactPhone) || empty($contactEmail) || empty($townshipId) || empty($status)) {
        setMessage('Please fill in all required fields.', 'error');
        redirect("edit_post.php?id=" . $postId);
    }

    // Retrieve Existing Post Data
    $post = getPostById($pdo, $postId);

    if ($post['status'] != 200) {
        setMessage('Post not found.', 'error');
        redirect('posts.php');
    }

    $oldImage = $post['data']['image'] ?? null;
    $imagePath = $oldImage;

    if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/pets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExt = strtolower(pathinfo($_FILES['pet_image']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExt, $allowedExts, true)) {
            setMessage('Invalid image format. Allowed: jpg, jpeg, png.', 'error');
            redirect("edit_post.php?id=" . $postId);
        }

        $fileName = 'pet_' . time() . '_' . uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['pet_image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/pets/' . $fileName;
        }
        else {
            setMessage('Failed to upload image.', 'error');
            redirect("edit_post.php?id=" . $postId);
        }
    }
    try {

        // Start Database Transaction
        $pdo->beginTransaction();

        // Update Pet Table
    $petSql = "UPDATE pet SET breed_id = :breed_id, name = :name, color = :color, age = :age, gender = :gender";
    $paramsPet = [
        'breed_id' => $breedId,
        'name'     => $petName,
        'color'    => $petColor,
        'age'      => $petAge,
        'gender'   => $petGender,
        'pet_id'   => $petId
    ];
        // Update Image Only If New Image Uploaded
        if ($imagePath != $post['data']['image']) {
            $petSql .= ", image = :image";
            $paramsPet['image'] = $imagePath;
        }
        $petSql .= " WHERE id = :pet_id";
        $petStmt = $pdo->prepare($petSql);
        $petUpdated = $petStmt->execute($paramsPet);

        // Update Post Table
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
        // Commit Transaction
        if ($petUpdated && $postUpdated) {
            $pdo->commit();
            if($oldImage && $oldImage != $imagePath){

                $oldImagePath='../'.$oldImage;

                if(file_exists($oldImagePath)){
                    unlink($oldImagePath);
                }
            }
            setMessage('Post updated successfully.', 'success');
            redirect('posts.php');
        } else {
            $pdo->rollBack();
            setMessage('Failed to update post.', 'error');
            redirect("edit_post.php?id=".$postId);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Admin Post Update Error: ".$e->getMessage());
        setMessage('Something went wrong while updating post.', 'error');
        redirect("edit_post.php?id=".$postId);
    }
}

// Handle Post Status Update
if (isset($_POST['post_id']) && isset($_POST['status'])) {
    $postId = (int)$_POST['post_id'];
    $status = ucfirst(strtolower(trim($_POST['status'])));

    // Validate Allowed Statuses
    $allowedStatuses = ['Active', 'Reunited'];
    if (!in_array($status, $allowedStatuses)) {
        setMessage("Invalid status selected.", 'error');
        redirect("posts.php");
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
        redirect("posts.php");
    } else {
        setMessage("Failed to update post status.", 'error');
        redirect("posts.php");
    }
}

// Handle Content Report Action
if (isset($_POST['btn_handle_report'])) {
    $reportId = (int) $_POST['report_id'];
    $action = $_POST['admin_action'];
    $targetType = $_POST['target_type'];
    $targetId = (int) $_POST['target_id'];
    $ownerId = (int) $_POST['owner_id'];
    $adminMessage = trim($_POST['admin_message'] ?? '');

    $newStatus = ($action === 'dismiss') ? 'Dismissed' : 'Resolved';

    $notiHelper = new NotificationHelper($pdo);

    // Update Report Status
    $updateSql = "UPDATE content_report SET status = :status, updated_at = NOW() WHERE id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $reportUpdated = $updateStmt->execute([
        'status' => $newStatus,
        'id'     => $reportId
    ]);
    if (!$reportUpdated) {
        setMessage("Failed to update report.", "error");
        redirect("reports.php");
    }

    // Remove Action
    if ($action === 'remove') {
        // Determine Target Table
        $table = ($targetType === 'post') ? 'post' : 'comment';
        // Delete Reported Content
        deleteQuery($pdo, $table, $targetId);
        if (empty($adminMessage)) {
            $adminMessage = "Your $targetType was removed by our administration team because it violated our community guidelines.";
        }
        // Notify User About Removed Content
        try {
            $notiHelper->sendToUser($ownerId, null, $adminMessage, 'content_removed', null);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        setMessage("Report resolved. The $targetType has been removed and user notified.", "success");
    }
    // Send Warning to User
    elseif ($action === 'warn') {
        if (empty($adminMessage)) {
            $adminMessage = "Your $targetType was reported for violating community guidelines. Please review our rules.";
        }
        $contentLink = null;
        if ($targetType === 'post') {
            $contentLink = '../user/post_details.php?id=' . $targetId;
        } elseif ($targetType === 'comment') {
            $sql = "SELECT post_id FROM comment WHERE id = :id LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $targetId]);
            $parentPostId = $stmt->fetchColumn();
            if ($parentPostId) {
                $contentLink = '../user/post_details.php?id=' . $parentPostId;
            }
        }
            // Notify User
            try {
                $notiHelper->sendToUser($ownerId, null, $adminMessage, 'warning', $contentLink);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
            setMessage("Report resolved. Warning sent to the user.", "success");
        }
        // Dismiss Report 
        else {
            setMessage("Report dismissed. No action taken on content.", "info");
        }
    redirect("reports.php");
}

// Handle Send Announcement
if (isset($_POST['btn_send_announcement'])) {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    // Validate Required Fields
    if (empty($title) || empty($message)) {
        setMessage('Title and message are required.', 'error');
        redirect('announcements.php');
    }
    try {
        // Initialize Notification Helper
        $notiHelper = new NotificationHelper($pdo);
        $fullMessage = $title . " - " . $message;
        // Broadcast Announcement to All Users
        $notiHelper->sendToAll($fullMessage, 'announcement');
        setMessage('Announcement broadcasted successfully to all users!', 'success');
    } catch (Exception $e) {
        error_log("Announcement Error: " . $e->getMessage());
        setMessage('Failed to send announcement.', 'error');
    }
    redirect('announcements.php');
}

// Handle Add New Pet Type
if (isset($_POST['btn_add_type'])) {
    $petType = trim($_POST['type_name']);
    // Validate Required Field
    if (!empty($petType)) {
        // Check Type Name Exist
        $checkSql = "SELECT type_name FROM pet_type WHERE LOWER(type_name) = LOWER(:pettype) LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['pettype' => $petType]);

        if ($checkStmt->fetch()) {
            setMessage('That pet type already exists.', 'error');
            redirect('pet_types.php');
        } else {
            // Insert New Type Name
            $sql = "INSERT INTO pet_type (type_name) VALUES (:pettype)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(['pettype' => $petType]);

            if ($result) {
                setMessage('New pet type added successfully.', 'success');
                redirect('pet_types.php');
            } else {
                setMessage('Failed to add pet type.', 'error');
                redirect('pet_types.php');
            }
        }
    } else {
        setMessage('Enter a pet type name.', 'error');
        redirect('pet_types.php');
    }
}

// Handle Pet Type Update
if (isset($_POST['btn_update_type'])) {
    $typeId = $_POST['type_id'];
    $typeName = trim($_POST['type_name']);
    // Validate Required Field
    if (!empty($typeName)) {
        // Check Type Name Exist
        $checkSql = "SELECT type_name FROM pet_type WHERE LOWER(type_name) = LOWER(:name) AND id != :id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'name' => $typeName,
            'id'   => $typeId
        ]);

        if ($checkStmt->fetch()) {
            setMessage('That pet type already exists.', 'error');
            redirect('pet_types.php');
        } else {
            // Update Type Name
            $sql = "UPDATE pet_type SET type_name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'name' => $typeName,
                'id'   => $typeId
            ]);

            if ($result) {
                setMessage('Pet type updated successfully.', 'success');
                redirect('pet_types.php');
            } else {
                setMessage('Failed to update pet type.', 'error');
                redirect('pet_types.php');
            }
        }
    } else {
        setMessage('Pet Type cannot be empty.', 'error');
        redirect('pet_types.php');
    }
}

// Handle Add New Breed
if (isset($_POST['btn_add_breed'])) {
    $petTypeId = $_POST['pet_type_id'];
    $breedName = trim($_POST['breed_name']);
    // Validate Required Fields
    if (!empty($petTypeId) && !empty($breedName)) {
        // Check Breed Exist
        $checkSql = "SELECT id FROM breed WHERE pet_type_id = :pet_type_id AND LOWER(breed_name) = LOWER(:breed_name) LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['pet_type_id' => $petTypeId, 'breed_name' => $breedName]);

        if ($checkStmt->fetch()) {
            setMessage('This breed already exists in the selected pet type.', 'error');
            redirect('breeds.php');
        }
        // Insert New Breed Record
        $sql = "INSERT INTO breed (breed_name, pet_type_id) VALUES (:breed_name, :pet_type_id)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(['breed_name' => $breedName, 'pet_type_id' => $petTypeId]);

        if ($result) {
            setMessage('New breed added successfully.', 'success');
            redirect('breeds.php');
        } else {
            setMessage('Failed to add breed.', 'error');
            redirect('breeds.php');
        }
    } else {
        setMessage('Please select a pet type and enter a breed name.', 'error');
        redirect('breeds.php');
    }
}

// Handle Breed Update
if (isset($_POST['btn_update_breed'])) {
    $breedId = $_POST['breed_id'];
    $petTypeId = $_POST['pet_type_id'];
    $breedName = trim($_POST['breed_name']);
    // Validate Required Fields
    if (!empty($breedName) && !empty($petTypeId)) {
        // Check Breed Exist
        $checkSql = "SELECT id FROM breed WHERE pet_type_id = :pet_type_id AND LOWER(breed_name) = LOWER(:breed_name) AND id != :breed_id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'pet_type_id' => $petTypeId,
            'breed_name' => $breedName,
            'breed_id' => $breedId
        ]);

        if ($checkStmt->fetch()) {
            setMessage('This breed name already exists in this pet type.', 'error');
            redirect('breeds.php');
        }
        // Update Breed Record
        $sql = "UPDATE breed SET pet_type_id = :pet_type_id, breed_name = :breed_name WHERE id = :breed_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'pet_type_id' => $petTypeId,
            'breed_name' => $breedName,
            'breed_id' => $breedId
        ]);

        if ($result) {
            setMessage('Breed updated successfully.', 'success');
            redirect('breeds.php');
        } else {
            setMessage('Failed to update breed.', 'error');
            redirect('breeds.php');
        }
    } else {
        setMessage('All fields are required.', 'error');
        redirect('breeds.php');
    }
}

// Handle Add New City
if (isset($_POST['btn_add_city'])) {
    $cityName = trim($_POST['city_name']);

    // Validate Required Field
    if (!empty($cityName)) {
        // Check City Exist
        $checkSql = "SELECT id FROM city WHERE LOWER(city_name) = LOWER(:city) LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute(['city' => $cityName]);

        if ($checkStmt->fetch()) {
            setMessage('City already exists.', 'error');
            redirect('cities.php');
        } else {
            // Insert New City Record
            $sql = "INSERT INTO city (city_name) VALUES (:city)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute(['city' => $cityName]);

            if ($result) {
                setMessage('New city added successfully.', 'success');
                redirect('cities.php');
            } else {
                setMessage('Failed to add city.', 'error');
                redirect('cities.php');
            }
        }
    } else {
        setMessage('Enter a city name', 'error');
        redirect('cities.php');
    }
}

// Handle City Update
if (isset($_POST['btn_update_city'])) {
    $cityId = $_POST['city_id'];
    $cityName = trim($_POST['city_name']);

    if (!empty($cityName)) {
        // Check Duplicate City Name
        $checkSql = "SELECT id FROM city WHERE LOWER(city_name) = LOWER(:name) AND id != :id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'name' => $cityName,
            'id'   => $cityId
        ]);

        if ($checkStmt->fetch()) {
            setMessage('This city already exists.', 'error');
            redirect('cities.php');
        } else {
            // Update City Name
            $sql = "UPDATE city SET city_name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                'name' => $cityName,
                'id'   => $cityId
            ]);

            if ($result) {
                setMessage('This city updated successfully.', 'success');
                redirect('cities.php');
            } else {
                setMessage('Failed to update city.', 'error');
                redirect('cities.php');
            }
        }
    } else {
        setMessage('City name cannot be empty.', 'error');
        redirect('cities.php');
    }
}

// Handle Add New Township
if (isset($_POST['btn_add_township'])) {
    $cityId = $_POST['city_id'];
    $townshipName = trim($_POST['township_name']);
    // Validate Required Fields
    if (!empty($cityId) && !empty($townshipName)) {
        // Check Township Exist
        $checkSql = "SELECT id FROM township WHERE LOWER(township_name) = LOWER(:township_name) AND city_id = :city_id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'city_id' => $cityId,
            'township_name' => $townshipName
        ]);

        if ($checkStmt->fetch()) {
            setMessage('This township already exists in the selected city.', 'error');
            redirect('townships.php');
        }
        // Insert New Township Record
        $sql = "INSERT INTO township (city_id, township_name) VALUES (:city_id, :township_name)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'city_id' => $cityId,
            'township_name' => $townshipName
        ]);

        if ($result) {
            setMessage('New township added successfully.', 'success');
            redirect('townships.php');
        } else {
            setMessage('Failed to add township.', 'error');
            redirect('townships.php');
        }
    } else {
        setMessage('Please select a city and enter a township name.', 'error');
        redirect('townships.php');
    }
}

// Handle Township Update
if (isset($_POST['btn_update_township'])) {
    $townshipId = $_POST['township_id'];
    $cityId = $_POST['city_id'];
    $townshipName = trim($_POST['township_name']);
    // Validate Required Fields
    if (!empty($townshipName) && !empty($cityId)) {
        // Check Township Exist
        $checkSql = "SELECT id FROM township WHERE LOWER(township_name) = LOWER(:township_name) AND city_id = :city_id AND id != :township_id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([
            'township_name' => $townshipName,
            'city_id' => $cityId,
            'township_id' => $townshipId
        ]);

        if ($checkStmt->fetch()) {
            setMessage('This township name already exists in this city.', 'error');
            redirect("townships.php");
        }
        // Update Township Record
        $sql = "UPDATE township SET city_id = :city_id, township_name = :township_name WHERE id = :township_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'city_id' => $cityId,
            'township_name' => $townshipName,
            'township_id' => $townshipId
        ]);

        if ($result) {
            setMessage('Township updated successfully.', 'success');
            redirect('townships.php');
        } else {
            setMessage('Failed to update township.', 'error');
            redirect("townships.php");
        }
    } else {
        setMessage('All fields are required.', 'error');
        redirect("townships.php");
    }
}

// Handle Add New Blog
if (isset($_POST['btn_add_blog'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category = $_POST['category'] ?? 'Community';
    $imagePath = null;

    // Validate Required Fields
    if (empty($title) || empty($content)) {
        $_SESSION['input'] = $_POST;
        setMessage('Title and content are required.', 'error');
        redirect('add_blog.php');
    }

    // Validate Blog Category
    $allowedCategories = ['Emergency', 'Prevention', 'Health', 'Community'];

    if (!in_array($category, $allowedCategories, true)) {
        $_SESSION['input'] = $_POST;
        setMessage('Invalid blog category selected.', 'error');
        redirect('add_blog.php');
    }
    // Handle Blog Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];

        $uploadDir = '../uploads/blogs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Validate Image Extension
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExtension, $allowedExtensions, true)) {
            $_SESSION['input'] = $_POST;
            setMessage('Invalid image format. Allowed: jpg, jpeg, png.', 'error');
            redirect('blogs.php');
        }

        // Create File Name
        $fileName = 'blog_' . time() . '_' . uniqid() . '.' . $imageExtension;
        // File Paths
        $serverPath = $uploadDir . $fileName;
        $imagePath = '/uploads/blogs/' . $fileName;
        if (!move_uploaded_file($imageTmp, $serverPath)) {
            $_SESSION['input'] = $_POST;
            setMessage('Failed to upload image file.', 'error');
            redirect('add_blog.php');
        }
    }
    $adminId = (int) $_SESSION['auth_admin']['id'];
    // Insert New Blog Record
    $sql = "INSERT INTO blog (user_id, title, content, image, category) VALUES (:user_id, :title, :content, :image, :category)";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'user_id'  => $adminId,
        'title'    => $title,
        'content'  => $content,
        'image'    => $imagePath,
        'category' => $category
    ]);

    if ($result) {
        setMessage('Blog added successfully!', 'success');
    } else {
        $_SESSION['input'] = $_POST;
        setMessage('Failed to add blog.', 'error');
    }

    redirect('blogs.php');
}

// Handle Blog Update
if (isset($_POST['btn_update_blog'])) {

    $blogId = (int)($_POST['blog_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'Community';
    // Retrieve Blog Record
    $blog = getBlogById($pdo, $blogId);
    // Validate Blog Exists
    if ($blog['status'] != 200) {
        setMessage('No such blog found.', 'error');
        redirect('edit_blog.php?id=' . $blogId);
    }
    // Get Current Blog Image
    $currentImage = $blog['data']['image'] ?? null;
    $imagePath = $currentImage;

    // Validate Required Fields
    if ($blogId <= 0 || empty($title) || empty($content)) {
        setMessage('Title and content are required.', 'error');
        redirect('blogs.php');
    }
    // Validate Blog Category
    $allowedCategories = [
        'Emergency',
        'Prevention',
        'Health',
        'Community'
    ];
    if (!in_array($category, $allowedCategories, true)) {
        setMessage('Invalid blog category selected.', 'error');
        redirect('blogs.php');
    }
    // Handle New Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $uploadDir = '../uploads/blogs/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Validate Image Extension
        $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageExtension, $allowedExtensions, true)) {
            setMessage('Invalid image format. Allowed: jpg, jpeg, png.', 'error');
            redirect('blogs.php');
        }
        // Create File Name
        $fileName = 'blog_' . time() . '_' . uniqid() . '.' . $imageExtension;
        // File Paths
        $serverPath = $uploadDir . $fileName;
        $imagePath = '/uploads/blogs/' . $fileName;
        if (move_uploaded_file($imageTmp, $serverPath)) {
            if ($currentImage && file_exists('../' . $currentImage)) {
                unlink('../' . $currentImage);
            }
        } else {
            setMessage('Failed to upload image.', 'error');
            redirect('blogs.php');
        }
    }
    // Update Blog Record
    $sql = "UPDATE blog SET title = :title, content = :content, image = :image, category = :category WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        'title'    => $title,
        'content'  => $content,
        'image'    => $imagePath,
        'category' => $category,
        'id'       => $blogId
    ]);
    if ($result) {
        setMessage('Blog updated successfully!', 'success');
    } else {
        setMessage('Failed to update blog.', 'error');
    }
    redirect('blogs.php');
}
