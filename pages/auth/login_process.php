<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

require "../../config/db_connection.php";

$username_entered = trim($_POST['username']);
$password = trim($_POST['password']);


$sql = "SELECT * FROM tbluser WHERE username = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$username_entered]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {


    $now = date('Y-m-d H:i:s');
    $last_login = $user['current_login']; 

    $update_sql = "UPDATE tbluser SET previous_login = ?, current_login = ? WHERE userid = ?";
    $update_stmt = $pdo->prepare($update_sql);
    $update_stmt->execute([$last_login, $now, $user['userid']]);
    
    $_SESSION['previous_login'] = $last_login;

    $_SESSION['userid'] = $user['userid'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['roleid'] = $user['roleid'];
    $_SESSION['profile_picture'] = $user['profile_picture'];

    if ($user['roleid'] == 1) {
        header("Location: ../administrator/admin-dashboard.php");
        exit();
    }
    elseif ($user['roleid'] == 3) {
        header("Location: ../student/student-panel.php");
        exit();
    }
    else {
        echo "Role dashboard not implemented yet";
        exit();
    }
}
else {
    echo "Invalid login";
}
?>