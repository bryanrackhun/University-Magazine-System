<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);


$required_role = 3;
require_once "../../pages/auth/check-role.php";
require_once "../../config/db_connection.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $contributionid = intval($_POST['contributionid']);
    $status = $_POST['status']; 
    
   
    if (!in_array($status, ['selected', 'rejected'])) {
        die("Invalid status selection.");
    }

    try {
        // Update the contribution status in the database
        $sql = "UPDATE tblcontribution SET status = ? WHERE contributionid = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $contributionid]);

        // Redirect back to dashboard with success message
        header("Location: manage-contribution.php?msg=reviewed");
        exit();
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}


if (!isset($_GET['id'])) {
    header("Location: manage-contribution.php");
    exit();
}

$contributionid = intval($_GET['id']);
$coordinator_id = $_SESSION['userid'];

// Fetch Coordinator's Faculty
$stmt_fac = $pdo->prepare("SELECT facultyid FROM tbluser WHERE userid = ?");
$stmt_fac->execute([$coordinator_id]);
$facultyid = $stmt_fac->fetchColumn();

// Fetch the specific submission (Ensure it belongs to this coordinator's faculty)
$sql = "SELECT c.*, u.username, u.email, cat.categoryname 
        FROM tblcontribution c
        JOIN tbluser u ON c.studentid = u.userid
        JOIN tblcategory cat ON c.categoryid = cat.categoryid
        WHERE c.contributionid = ? AND u.facultyid = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$contributionid, $facultyid]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    die("Error: Article not found or you do not have permission to review it.");
}

$bg = "#fff3cd"; $color = "#856404"; 
if(strtolower($article['status']) == 'selected') { $bg = "#d4edda"; $color = "#155724"; }
if(strtolower($article['status']) == 'rejected') { $bg = "#f8d7da"; $color = "#721c24"; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Article - Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/marketing_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

    <?php include '../../components/header.php'; ?>

    <div class="main-container">
        <?php include '../../components/sidebar.php'; ?>
        <div class="main">
            
            <div style="margin-bottom: 25px;">
                <a href="manage-contribution.php" style="color: var(--text-light); text-decoration: none; display: inline-flex; align-items: center; gap: 5px; font-size: 14px; font-weight: 500;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                    Back to Faculty Contributions
                </a>
            </div>

            <div class="review-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px;">
                
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h1 style="margin: 0 0 10px 0; color: var(--primary-navy);"><?php echo htmlspecialchars($article['title']); ?></h1>
                            <div style="display: flex; gap: 15px; color: #666; font-size: 14px;">
                                <span><strong style="color: #333;">Author:</strong> <?php echo htmlspecialchars($article['username']); ?></span>
                                <span><strong style="color: #333;">Category:</strong> <?php echo htmlspecialchars($article['categoryname']); ?></span>
                                <span><strong style="color: #333;">Submitted:</strong> <?php echo date("M d, Y", strtotime($article['submission_date'])); ?></span>
                            </div>
                        </div>
                        <span class="role-badge" style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>; padding: 8px 15px; font-size: 14px;">
                            Status: <?php echo strtoupper(htmlspecialchars($article['status'])); ?>
                        </span>
                    </div>

                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px; border-left: 4px solid var(--secondary-light-blue);">
                        <h3 style="margin: 0 0 10px 0; font-size: 16px;">Student's Description:</h3>
                        <p style="margin: 0; color: #555; line-height: 1.5; font-size: 14px;">
                            <?php echo nl2br(htmlspecialchars($article['description'])); ?>
                        </p>
                    </div>

                    <h3 style="margin-bottom: 15px;">Attached Files</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: white;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="material-symbols-outlined" style="color: #004080; font-size: 32px;">description</span>
                                <div>
                                    <strong style="display: block; font-size: 14px;">Primary Document (Word)</strong>
                                    <span style="font-size: 12px; color: #888;"><?php echo htmlspecialchars($article['filepath1']); ?></span>
                                </div>
                            </div>
                            <a href="../../uploads/articles/<?php echo htmlspecialchars($article['filepath1']); ?>" download class="cta-btn" style="padding: 6px 15px; text-decoration: none; font-size: 13px;">
                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">download</span> Download
                            </a>
                        </div>

                        <?php if(!empty($article['filepath2'])): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: white;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="material-symbols-outlined" style="color: #28a745; font-size: 32px;">image</span>
                                <div>
                                    <strong style="display: block; font-size: 14px;">Supporting Image 1</strong>
                                    <span style="font-size: 12px; color: #888;"><?php echo htmlspecialchars($article['filepath2']); ?></span>
                                </div>
                            </div>
                            <a href="../../uploads/images/<?php echo htmlspecialchars($article['filepath2']); ?>" target="_blank" download class="cta-btn" style="padding: 6px 15px; text-decoration: none; font-size: 13px; background: #6c757d;">
                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">download</span> Download
                            </a>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty($article['filepath3'])): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 6px; background: white;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="material-symbols-outlined" style="color: #28a745; font-size: 32px;">image</span>
                                <div>
                                    <strong style="display: block; font-size: 14px;">Supporting Image 2</strong>
                                    <span style="font-size: 12px; color: #888;"><?php echo htmlspecialchars($article['filepath3']); ?></span>
                                </div>
                            </div>
                            <a href="../../uploads/images/<?php echo htmlspecialchars($article['filepath3']); ?>" target="_blank" download class="cta-btn" style="padding: 6px 15px; text-decoration: none; font-size: 13px; background: #6c757d;">
                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">download</span> Download
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="card" style="height: fit-content;">
                    <h2 style="margin-top: 0; font-size: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;">Coordinator Review</h2>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="contributionid" value="<?php echo $article['contributionid']; ?>">
                        
                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444; font-size: 14px;">Final Decision</label>
                            <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                                <option value="">Select a status...</option>
                                <option value="selected" <?php if($article['status'] == 'selected') echo 'selected'; ?>>✅ Selected (Approve)</option>
                                <option value="rejected" <?php if($article['status'] == 'rejected') echo 'selected'; ?>>❌ Rejected</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display: block; font-weight: bold; margin-bottom: 8px; color: #444; font-size: 14px;">Private Feedback Notes</label>
                            <textarea name="comment" rows="5" placeholder="Write feedback here..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-family: inherit; resize: vertical; box-sizing: border-box;"></textarea>
                        </div>

                        <button type="submit" name="submit_review" class="cta-btn" style="width: 100%; font-size: 16px; padding: 12px; background: #28a745;">
                            <span class="material-symbols-outlined" style="vertical-align: middle;">check_circle</span> Confirm Decision
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../assests/js/script.js"></script>
    <?php include '../../components/footer.php'; ?>
</body>
</html>