<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Authenticate the User
require_once __DIR__ . "/../config/db_connection.php";
$required_role = 4;
require_once __DIR__ . "/../pages/auth/check-role.php";

$studentid = $_SESSION['userid'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contributionid = intval($_POST['contributionid']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categoryid = intval($_POST['categoryid']);

    // Fetch current submission to get old file names
    $stmt = $pdo->prepare("SELECT * FROM tblcontribution WHERE contributionid = ? AND studentid = ?");
    $stmt->execute([$contributionid, $studentid]);
    $old_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$old_data) {
        header("Location: student-panel.php?error=" . urlencode("Submission not found."));
        exit();
    }

    // Prepare file naming prefix
    $stmt_user = $pdo->prepare("SELECT username FROM tbluser WHERE userid = ?");
    $stmt_user->execute([$studentid]);
    $clean_username = preg_replace('/[^A-Za-z0-9]/', '', $stmt_user->fetchColumn()); 

    $stmt_cat = $pdo->prepare("SELECT categoryname FROM tblcategory WHERE categoryid = ?");
    $stmt_cat->execute([$categoryid]);
    $clean_category = preg_replace('/[^A-Za-z0-9]/', '', $stmt_cat->fetchColumn()); 
    $clean_title = preg_replace('/[^A-Za-z0-9]/', '', $title);

    $file_prefix = $clean_username . "_" . $clean_category . "_" . $categoryid . "_" . $clean_title;

    $dir_doc = __DIR__ . "/../../uploads/articles/";
    $dir_img = __DIR__ . "/../../uploads/images/";


    function replaceFile($input_name, $target_dir, $file_prefix, $old_filename) {
        if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] == 4) {
            return $old_filename; 
        }

        $file = $_FILES[$input_name];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_filename = $file_prefix . "_EDIT_" . $input_name . "_" . time() . "." . $ext;
        $target = $target_dir . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            if (!empty($old_filename) && file_exists($target_dir . $old_filename)) {
                unlink($target_dir . $old_filename);
            }
            return $new_filename;
        }
        return $old_filename; 
    }

    $file1 = replaceFile("filepath1", $dir_doc, $file_prefix, $old_data['filepath1']); 
    $file2 = replaceFile("filepath2", $dir_img, $file_prefix, $old_data['filepath2']); 
    $file3 = replaceFile("filepath3", $dir_img, $file_prefix, $old_data['filepath3']); 

    try {
        $sql = "UPDATE tblcontribution 
                SET title = ?, description = ?, categoryid = ?, filepath1 = ?, filepath2 = ?, filepath3 = ? 
                WHERE contributionid = ? AND studentid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $description, $categoryid, $file1, $file2, $file3, $contributionid, $studentid]);
        
        header("Location: student-panel.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: student-panel.php?error=" . urlencode("Database Error: " . $e->getMessage()));
        exit();
    }
}


if (!isset($_GET['id'])) {
    header("Location: student-panel.php");
    exit();
}

$contributionid = intval($_GET['id']);

$sql = "SELECT * FROM tblcontribution WHERE contributionid = ? AND studentid = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$contributionid, $studentid]);
$submission = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$submission) {
    die("Error: Submission not found or you do not have permission to edit it.");
}

$category = "SELECT * FROM tblcategory WHERE categoryclosuredate >= CURDATE() ORDER BY categoryclosuredate ASC";
$stmt_category = $pdo->query($category);
$categories = $stmt_category->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Submission - Magazine System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 40px 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #555;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: color 0.2s;
        }
        .back-btn:hover {
            color: #003366;
        }
        .card {
            background: #ffffff;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #e1e5eb;
        }
        h2 {
            margin: 0 0 25px 0;
            color: #003366;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #444;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #003366;
        }
        .warning-text {
            color: #856404;
            background-color: #fff3cd;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #ffeeba;
        }
        .file-upload-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px dashed #bbb;
            margin-bottom: 15px;
        }
        .file-upload-box label {
            color: #004080;
            font-size: 15px;
            margin-bottom: 5px;
        }
        .file-upload-box p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #666;
        }
        .file-upload-box input[type="file"] {
            font-size: 14px;
        }
        .cta-btn {
            background: #004080;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: background 0.2s, transform 0.1s;
        }
        .cta-btn:hover {
            background: #002244;
        }
        .cta-btn:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="header-actions">
            <a href="../pages/student/student-panel.php" class="back-btn">
                <span class="material-symbols-outlined">arrow_back</span>
                Back to Dashboard
            </a>
        </div>

        <div class="card">
            <h2>Edit Your Contribution</h2>

            <form action="" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="contributionid" value="<?php echo $submission['contributionid']; ?>">

                <div class="form-group">
                    <label>Article Title</label>
                    <input name="title" type="text" class="form-control" value="<?php echo htmlspecialchars($submission['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Brief Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($submission['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Submission Category</label>
                    <select name="categoryid" class="form-control" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['categoryid']; ?>" <?php echo ($cat['categoryid'] == $submission['categoryid']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['categoryname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="warning-text">
                    <strong>Note:</strong> Uploading a new file below will automatically permanently delete and replace the old file.
                </div>

                <div class="file-upload-box">
                    <label>Update Word Document (.doc, .docx)</label>
                    <p>Current File: <strong><?php echo htmlspecialchars($submission['filepath1']); ?></strong></p>
                    <input type="file" name="filepath1" accept=".doc, .docx">
                </div>

                <div class="file-upload-box">
                    <label>Update Image 1 (Optional)</label>
                    <p>Current File: <strong><?php echo $submission['filepath2'] ? htmlspecialchars($submission['filepath2']) : 'None attached'; ?></strong></p>
                    <input type="file" name="filepath2" accept="image/*">
                </div>

                <div class="file-upload-box">
                    <label>Update Image 2 (Optional)</label>
                    <p>Current File: <strong><?php echo $submission['filepath3'] ? htmlspecialchars($submission['filepath3']) : 'None attached'; ?></strong></p>
                    <input type="file" name="filepath3" accept="image/*">
                </div>

                <div class="form-group" style="margin-top: 30px; margin-bottom: 0;">
                    <button type="submit" class="cta-btn">
                        <span class="material-symbols-outlined">save</span> 
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>