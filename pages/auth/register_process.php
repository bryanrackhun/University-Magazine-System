<?php
session_start();
require "../../config/db_connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']); 
    $gender = trim($_POST['gender']);
    $faculty = trim($_POST['faculty']);
    $role = trim($_POST['role']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $_SESSION['error_msg'] = "Passwords do not match.";
        header("Location: register.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $check_stmt = $pdo->prepare("SELECT * FROM tbluser WHERE email = ? OR username = ?");
        $check_stmt->execute([$email, $username]);

        if($check_stmt->rowCount() > 0) {
            $_SESSION['error_msg'] = "Email or username already exists.";
            header("Location: register.php");
            exit();
        }

        $pf_pic_name = "default_pf.jpg";

        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_picture'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if(in_array($ext, $allowed)) {
                $new_pf_name = $username . "_pf." . $ext;
                $dest_path = "../../uploads/profile_pics/" . $new_pf_name;
                
                if (move_uploaded_file($file['tmp_name'], $dest_path)) {
                    $pf_pic_name = $new_pf_name;
                } else {
                    $_SESSION['error_msg'] = "Failed to upload profile picture.";
                    header("Location: register.php");
                    exit();
                }
            }
        }
        
        $sql = "INSERT INTO tbluser (facultyid, roleid, username, email, password_hash, gender, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if($stmt->execute([$facultyid, $roleid, $username, $email, $password_hash, $gender, $pf_pic_name])) {
            $_SESSION['success_msg'] = "Registration successful. Please log in.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error_msg'] = "Registration failed. Please try again.";
            header("Location: register.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Database Error: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
?>