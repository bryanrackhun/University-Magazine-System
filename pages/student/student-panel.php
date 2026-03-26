<?php
session_start();
require_once "../../config/db_connection.php";
$required_role = 4;
require_once "../auth/check-role.php";

$studentid = $_SESSION['userid'];

// 1. Fetch Categories (Only active ones, sorted by closest deadline first)
$category = "SELECT * FROM tblcategory WHERE categoryclosuredate >= CURDATE() ORDER BY categoryclosuredate ASC";
$stmt_category = $pdo->prepare($category);
$stmt_category->execute();
$categories = $stmt_category->fetchAll(PDO::FETCH_ASSOC);

// 2. Calculate the Nearest Deadline dynamically
$days_left = 0;
$closest_closure_date = "No Active Deadlines";
$upcoming_category_name = "System Closed";

if (count($categories) > 0) {
    $nearest_cat = $categories[0]; 
    $upcoming_category_name = $nearest_cat['categoryname'];
    $closest_closure_date = date("M d, Y", strtotime($nearest_cat['categoryclosuredate']));

    $today_dt = new DateTime();
    $today_dt->setTime(0, 0, 0); 
    $closure_dt = new DateTime($nearest_cat['categoryclosuredate']);
    $closure_dt->setTime(0, 0, 0);
    
    $days_left = $today_dt->diff($closure_dt)->days;
}

// 3. Fetch Student's Previous Submissions (Joined with Category to get the closure date)
$sql_subs = "SELECT c.*, cat.categoryname, cat.categoryclosuredate 
             FROM tblcontribution c
             LEFT JOIN tblcategory cat ON c.categoryid = cat.categoryid
             WHERE c.studentid = :studentid 
             ORDER BY c.submission_date DESC";
$stmt_subs = $pdo->prepare($sql_subs);
$stmt_subs->execute([':studentid' => $studentid]);
$my_submissions = $stmt_subs->fetchAll(PDO::FETCH_ASSOC);

// 4. Calculate Stats for Track Record
$selected_count = 0;
foreach($my_submissions as $sub) {
    if(strtolower($sub['status']) === 'selected') {
        $selected_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/stud_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

    <?php include '../../components/header.php'; ?>

        <div class="main-container">
            <?php include '../../components/sidebar.php'; ?>
            <div class="main">
                
                <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                    <div id="status-banner" style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px; font-weight: bold; border: 1px solid #c3e6cb; transition: opacity 0.5s ease;">
                        ✅ Action completed successfully!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div id="status-banner" style="background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px; font-weight: bold; border: 1px solid #f5c6cb; transition: opacity 0.5s ease;">
                        ❌ Error: <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="feed-grid">
                    
                    <div class="feed-column">
                        
                        <div class="card submit-card">
                            <div>
                                <h2 class="widget-title" style="margin-bottom: 5px;">Ready to submit?</h2>
                                <p class="widget-subtitle" style="margin: 0;">Upload your article and images for the upcoming issue.</p>
                            </div>
                            <button class="cta-btn" id="openModalBtn">
                                <span class="material-symbols-outlined">cloud_upload</span>
                                Upload Contribution
                            </button>
                        </div>
            
                        <h2 class="widget-title" style="margin-top: 35px;">My Previous Submissions</h2>
                        
                        <?php if (count($my_submissions) > 0): ?>
                            <?php foreach($my_submissions as $sub): 
                                $bg_color = "#fff3cd"; $text_color = "#856404"; $border_color = "#ffeeba"; 
                                if($sub['status'] === 'selected') { $bg_color = "#d4edda"; $text_color = "#155724"; $border_color = "#c3e6cb"; }
                                if($sub['status'] === 'rejected') { $bg_color = "#f8d7da"; $text_color = "#721c24"; $border_color = "#f5c6cb"; }
                            ?>
                                <div class="card">
                                    <div class="post-header">
                                        <div>
                                            <div class="post-title"><?php echo htmlspecialchars($sub['title']); ?></div>
                                            <span class="status-badge" style="background-color: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>; border: 1px solid <?php echo $border_color; ?>; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase;">
                                                <?php echo htmlspecialchars($sub['status']); ?>
                                            </span>
                                            <span style="font-size: 12px; color: #666; margin-left: 10px;">
                                                <strong>Category:</strong> <?php echo htmlspecialchars($sub['categoryname']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="post-content">
                                        <p class="post-text">
                                            Submitted on: <?php echo date("M d, Y", strtotime($sub['submission_date'])); ?>.<br>
                                            <span style="color: #666; font-size: 13px;"><?php echo htmlspecialchars($sub['description']); ?></span>
                                        </p>
                                    </div>
                                    
                                    <div class="post-actions" style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 15px;">
                                        <?php 
                                        // 🚨 SEALED SHUT LOGIC
                                        $today = date("Y-m-d");
                                        if ($today <= $sub['categoryclosuredate']): 
                                        ?>
                                            <a href="../../backend/edit-submission.php?id=<?php echo $sub['contributionid']; ?>" class="cta-btn btn-sm" style="text-decoration: none; display: inline-block; padding: 8px 15px;">
                                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">edit</span> Edit Submission
                                            </a>
                                        <?php else: ?>
                                            <div style="background-color: #f8f9fa; color: #6c757d; padding: 8px 12px; border-radius: 4px; display: inline-block; font-size: 13px; font-weight: bold; cursor: not-allowed; border: 1px solid #dee2e6;">
                                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">lock</span> Sealed (Deadline Passed)
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="card" style="text-align: center; padding: 40px; color: #666;">
                                <p>You haven't submitted any articles yet.</p>
                            </div>
                        <?php endif; ?>

                    </div>

                    <div class="sidebar-column">
                        
                        <div class="card">
                            <h2 class="widget-title" style="margin-bottom: 0;">Next Deadline</h2>
                            <p class="widget-subtitle"><?php echo htmlspecialchars($upcoming_category_name); ?></p>
                            
                            <div class="dark-stat-box">
                                <p>New Submissions Close In:</p>
                                <h1><?php echo $days_left; ?></h1>
                                <p>Days Left</p>
                            </div>
                            
                            <p class="deadline-note">Closure Date: <strong><?php echo $closest_closure_date; ?></strong></p>
                        </div>

                        <div class="card">
                            <h2 class="widget-title">My Track Record</h2>
                            <div class="contribution-item">
                                <strong><?php echo $selected_count; ?></strong> Selected Articles
                            </div>
                            <div class="contribution-item">
                                <strong><?php echo count($my_submissions); ?></strong> Total Submissions
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        <div id="uploadModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="widget-title" style="margin: 0;">Submit New Article</h2>
                    <button class="close-btn" id="closeModalBtn">&times;</button>
                </div>
                
                <form id="uploadForm" action="../../actions/upload-article.php" method="POST" enctype="multipart/form-data">
                    <input name="title" type="text" class="submit-input" placeholder="Article Title" required>
                    <textarea name="description" class="submit-textarea" rows="2" placeholder="Brief Description (Optional)"></textarea>

                    <select name="categoryid" class="submit-input" required>
                        <option value="">Select Category...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['categoryid']; ?>">
                                <?php echo htmlspecialchars($cat['categoryname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                        
                    <div class="file-upload-container">
                        <div class="file-upload-group">
                            <label>File 1 (Required)</label>
                            <p>Word Document (.doc, .docx)</p>
                            <input type="file" name="filepath1" accept=".doc, .docx" required>
                        </div>
                                
                        <div class="file-upload-group">
                            <label>File 2 (Optional)</label>
                            <p>Supporting Image or Doc</p>
                            <input type="file" name="filepath2" accept=".doc, .docx, image/*">
                        </div>                           

                        <div class="file-upload-group">
                            <label>File 3 (Optional)</label>
                            <p>Supporting Image or Doc</p>
                            <input type="file" name="filepath3" accept=".doc, .docx, image/*">
                        </div>
                    </div>

                    <div class="form-footer">
                        <label class="terms-label">
                            <input type="checkbox" name="terms" required> I agree to the <a href="#">Terms & Conditions</a>
                        </label>
                        <button type="submit" class="cta-btn">
                            <span class="material-symbols-outlined">send</span>
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>    
    </div>

    <?php include '../../components/footer.php'; ?>
</body>
<script src="../../assests/js/script.js"></script>
<script>
        document.addEventListener("DOMContentLoaded", function() {
            if (window.history.replaceState) {
                const url = new URL(window.location);
                if (url.searchParams.has('success') || url.searchParams.has('error')) {
                    url.searchParams.delete('success');
                    url.searchParams.delete('error');
                    window.history.replaceState({path: url.href}, '', url.href);
                }
            }
            const banner = document.getElementById('status-banner');
            if (banner) {
                setTimeout(() => {
                    banner.style.opacity = '0';
                    setTimeout(() => banner.style.display = 'none', 500); 
                }, 5000);
            }
        });
</script>
</html>