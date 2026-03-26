<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/db_connection.php";

if (!isset($_SESSION['userid'])) {
    header("Location: ../pages/auth/login.php");
    exit();
}

$userid = $_SESSION['userid'];


$success_msg = $_SESSION['success_msg'] ?? "";
$error_msg = $_SESSION['error_msg'] ?? "";
unset($_SESSION['success_msg'], $_SESSION['error_msg']); 

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (!empty($new_password) && $new_password !== $confirm_password) {
        $_SESSION['error_msg'] = "New passwords do not match.";
        header("Location: edit-profile.php");
        exit();
    } else {
        try {
            $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM tbluser WHERE (email = ? OR username = ?) AND userid != ?");
            $check_stmt->execute([$email, $username, $userid]);
            
            if ($check_stmt->fetchColumn() > 0) {
                $_SESSION['error_msg'] = "Email or Username is already taken by another account.";
                header("Location: edit-profile.php");
                exit();
            } else {
                $update_sql = "UPDATE tbluser SET username = ?, email = ?, gender = ?";
                $params = [$username, $email, $gender];

                if (!empty($new_password)) {
                    $update_sql .= ", password_hash = ?";
                    $params[] = password_hash($new_password, PASSWORD_DEFAULT);
                }

                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['profile_picture'];
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png'];

                    if (in_array($ext, $allowed) && $file['size'] <= 2097152) { 
                        $new_filename = $username . "_pf_" . time() . "." . $ext;
                        $dest_path = __DIR__ . "/../uploads/profile_pics/" . $new_filename;

                        if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                            $old_pic_stmt = $pdo->prepare("SELECT profile_picture FROM tbluser WHERE userid = ?");
                            $old_pic_stmt->execute([$userid]);
                            $old_pic = $old_pic_stmt->fetchColumn();

                            if (!empty($old_pic) && $old_pic !== 'default_pf.jpg') {
                                $old_pic_path = __DIR__ . "/../uploads/profile_pics/" . $old_pic;
                                if (file_exists($old_pic_path)) {
                                    unlink($old_pic_path);
                                }
                            }

                            $update_sql .= ", profile_picture = ?";
                            $params[] = $new_filename;
                            $_SESSION['profile_picture'] = $new_filename; 
                        }
                    } else {
                        $_SESSION['error_msg'] = "Invalid image format or file too large (Max 2MB).";
                        header("Location: edit-profile.php");
                        exit();
                    }
                }

                $update_sql .= " WHERE userid = ?";
                $params[] = $userid;
                
                $stmt = $pdo->prepare($update_sql);
                if ($stmt->execute($params)) {
                    $_SESSION['success_msg'] = "Profile updated successfully!";
                    $_SESSION['username'] = $username;
                    header("Location: edit-profile.php");
                    exit();
                } else {
                    $_SESSION['error_msg'] = "System error during profile update.";
                    header("Location: edit-profile.php");
                    exit();
                }
            }
        } catch (PDOException $e) {
            $_SESSION['error_msg'] = "Database Error: " . $e->getMessage();
            header("Location: edit-profile.php");
            exit();
        }
    }
}

// Determine Home Button link based on Role ID
$roleid = $_SESSION['roleid'] ?? 0;
$home_url = "../pages/auth/login.php"; // Fallback safety URL
if ($roleid == 1) {
    $home_url = "../pages/administrator/admin-dashboard.php";
} elseif ($roleid == 2) {
    $home_url = "../pages/marketing-manager/manager-dashboard.php";
} elseif ($roleid == 3) {
    $home_url = "../pages/marketing-coordinator/manage-contribution.php";
} elseif ($roleid == 4) {
    $home_url = "../pages/student/student-panel.php";
}

$stmt = $pdo->prepare("SELECT * FROM tbluser WHERE userid = ?");
$stmt->execute([$userid]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$current_pic = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_pf.jpg'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            max-height: 95vh;
            overflow-y: auto;
            box-sizing: border-box;
        }
        .form-title {
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .alert {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .profile-pic-section {
            text-align: center;
            margin-bottom: 25px;
        }
        .profile-pic-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #4a90e2;
            margin-bottom: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .profile-pic-label {
            cursor: pointer;
            color: #4a90e2;
            font-weight: bold;
            text-decoration: underline;
        }
        .profile-pic-input {
            display: none;
        }
        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            flex: 1;
            margin-bottom: 0;
        }
        .form-group-full {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .radio-group {
            display: flex;
            gap: 15px;
            align-items: center;
            padding-top: 5px;
        }
        .radio-label {
            cursor: pointer;
        }
        .divider {
            border: 0;
            border-top: 1px solid #eee;
            margin: 25px 0;
        }
        .section-title {
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
        }
        .password-row {
            display: flex;
            gap: 15px;
            margin-bottom: 5px;
        }
        .password-input-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #fff;
            padding-right: 10px;
            box-sizing: border-box;
        }
        .password-input {
            width: 100%;
            padding: 10px;
            border: none;
            outline: none;
            box-sizing: border-box;
            background: transparent;
        }
        .toggle-icon {
            cursor: pointer;
            color: #666;
            user-select: none;
        }
        .password-msg {
            display: none;
            margin-bottom: 20px;
            font-weight: bold;
            width: 100%;
        }
        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
        }
        .btn-cancel {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            color: #555;
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .btn-submit {
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4a90e2;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="form-title">Edit My Profile</h2>

        <?php if(!empty($error_msg)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success_msg)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            
            <div class="profile-pic-section">
                <img id="pf_preview" class="profile-pic-preview" src="../uploads/profile_pics/<?php echo htmlspecialchars($current_pic); ?>" alt="Profile Preview">
                <div>
                    <label for="profile_picture" class="profile-pic-label">Change Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="profile-pic-input" onchange="previewImage(event)">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
            </div>

            <div class="form-group-full">
                <label class="form-label">Gender</label>
                <div class="radio-group">
                    <label class="radio-label"><input type="radio" name="gender" value="male" <?php echo ($user['gender'] === 'male') ? 'checked' : ''; ?> required> Male</label>
                    <label class="radio-label"><input type="radio" name="gender" value="female" <?php echo ($user['gender'] === 'female') ? 'checked' : ''; ?> required> Female</label>
                    <label class="radio-label"><input type="radio" name="gender" value="other" <?php echo ($user['gender'] === 'other') ? 'checked' : ''; ?> required> Other</label>
                </div>
            </div>

            <hr class="divider">
            <h3 class="section-title">Change Password (Leave blank to keep current)</h3>

            <div class="password-row">
                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="new_password" name="new_password" class="password-input">
                        <span class="material-symbols-outlined toggle-icon" onclick="toggleVisibility('new_password', this)">visibility_off</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="password-input">
                        <span class="material-symbols-outlined toggle-icon" onclick="toggleVisibility('confirm_password', this)">visibility_off</span>
                    </div>
                </div>
            </div>
            <small id="password_match_msg" class="password-msg"></small>

            <div class="button-container">
                <a href="<?php echo htmlspecialchars($home_url); ?>" class="btn-cancel">Go to Home</a>
                <button type="submit" id="submitBtn" name="update_profile" class="btn-submit">Save Changes</button>
            </div>
        </form>
    </div> 

<script>
    function toggleVisibility(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.textContent = "visibility";
        } else {
            input.type = "password";
            icon.textContent = "visibility_off";
        }
    }

    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('pf_preview');
            output.src = reader.result;
        };
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    const submitBtn = document.getElementById('submitBtn');
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    const matchMsg = document.getElementById('password_match_msg');

    function validatePasswordMatch() {
        if (newPassword.value === "" && confirmPassword.value === "") {
            matchMsg.style.display = "none";
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            return;
        }

        matchMsg.style.display = "block";
        if (newPassword.value === confirmPassword.value) {
            matchMsg.textContent = "Passwords match!";
            matchMsg.style.color = "#28a745";
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            matchMsg.textContent = "Passwords do not match.";
            matchMsg.style.color = "#dc3545";
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    newPassword.addEventListener('input', validatePasswordMatch);
    confirmPassword.addEventListener('input', validatePasswordMatch);
</script>
</body>
</html>