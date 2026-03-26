<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function redirectWithError($message) {
    header("Location: ../pages/student/student-panel.php?error=" . urlencode($message));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectWithError("Invalid request method.");
}

if (!isset($_SESSION['userid'])) {
    header("Location: ../pages/auth/login.php");
    exit();
}


require_once "../config/db_connection.php"; 

$studentid = $_SESSION['userid'];

$stmt_user = $pdo->prepare("SELECT username, facultyid FROM tbluser WHERE userid = ?");
$stmt_user->execute([$studentid]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) { redirectWithError("User account could not be found."); }
$facultyid = $user['facultyid'];


$clean_username = preg_replace('/[^A-Za-z0-9]/', '', $user['username']); 

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$categoryid = intval($_POST['categoryid'] ?? 0);

if (empty($title) || $categoryid === 0) {
    redirectWithError("Title and Submission Category are required.");
}


$clean_title = preg_replace('/[^A-Za-z0-9]/', '', $title);

$stmt_cat = $pdo->prepare("SELECT categoryname FROM tblcategory WHERE categoryid = ?");
$stmt_cat->execute([$categoryid]);
$category = $stmt_cat->fetch(PDO::FETCH_ASSOC);

if (!$category) { redirectWithError("Invalid submission category selected."); }


$clean_category = preg_replace('/[^A-Za-z0-9]/', '', $category['categoryname']); 


$file_prefix = $clean_username . "_" . $clean_category . "_" . $categoryid . "_" . $clean_title;

$upload_dir_doc = __DIR__ . "/../uploads/articles/";
$upload_dir_img = __DIR__ . "/../uploads/images/";

if (!is_dir($upload_dir_doc)) mkdir($upload_dir_doc, 0777, true);
if (!is_dir($upload_dir_img)) mkdir($upload_dir_img, 0777, true);

function uploadFile($input_name, $target_dir, $file_prefix) {
    if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] == 4) {
        return null;
    }

    $file = $_FILES[$input_name];
    $allowed = [
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/octet-stream",
        "image/jpeg",
        "image/png"
    ];

    if (!in_array($file['type'], $allowed)) {
        redirectWithError("Invalid file type. Only Word Documents (.doc, .docx) or Images are allowed.");
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    

    $filename = $file_prefix . "_" . $input_name . "_" . time() . "." . $ext;
    $target = $target_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        redirectWithError("Server failed to save the file. Check folder permissions.");
    }

    return $filename;
}

$file1 = uploadFile("filepath1", $upload_dir_doc, $file_prefix); 
$file2 = uploadFile("filepath2", $upload_dir_img, $file_prefix); 
$file3 = uploadFile("filepath3", $upload_dir_img, $file_prefix); 

if (!$file1) {
    redirectWithError("File 1 (Word Document) is strictly required.");
}


try {
    $sql = "INSERT INTO tblcontribution
    (studentid, categoryid, title, description, submission_date, status, filepath1, filepath2, filepath3)
    VALUES (?, ?, ?, ?, CURDATE(), 'submitted', ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $studentid,
        $categoryid,
        $title,
        $description,
        $file1,
        $file2,
        $file3
    ]);
} catch (PDOException $e) {
    redirectWithError("Database Error: " . $e->getMessage());
}


try {
    $sql_email = "SELECT email FROM tbluser WHERE facultyid = :fid AND roleid = 3"; 
    $stmt_email = $pdo->prepare($sql_email);
    $stmt_email->execute([':fid' => $facultyid]);
    $coordinator = $stmt_email->fetch(PDO::FETCH_ASSOC);

    if($coordinator){
        $log_dir = __DIR__ . "/../logs/";
        if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
        
        file_put_contents($log_dir . "email_log.txt",
            "[" . date('Y-m-d H:i:s') . "] New submission for faculty {$facultyid} sent to {$coordinator['email']}\n",
            FILE_APPEND
        );
    }
} catch (Exception $e) {

}

header("Location: ../pages/student/student-panel.php?success=1");
exit();
?>