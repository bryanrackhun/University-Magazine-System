<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Protect page - Marketing Coordinator Only
$required_role = 3;
require_once "../../pages/auth/check-role.php";
require_once "../../config/db_connection.php";

$coordinator_id = $_SESSION['userid'];

// 1. Get the Coordinator's assigned Faculty ID
$stmt_fac = $pdo->prepare("SELECT facultyid FROM tbluser WHERE userid = ?");
$stmt_fac->execute([$coordinator_id]);
$facultyid = $stmt_fac->fetchColumn();

if (!$facultyid) {
    die("Error: You are not assigned to a faculty. Please contact the administrator.");
}

// 2. Fetch all contributions from students in THIS coordinator's faculty
$sql = "SELECT c.*, u.username, cat.categoryname 
        FROM tblcontribution c
        JOIN tbluser u ON c.studentid = u.userid
        JOIN tblcategory cat ON c.categoryid = cat.categoryid
        WHERE u.facultyid = :facultyid
        ORDER BY c.submission_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':facultyid' => $facultyid]);
$contributions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status filtering
$filter = $_GET['filter'] ?? 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contributions - Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/adm_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        /* Mobile Responsiveness for Header Controls */
        .header-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap; /* Allows stacking on mobile */
            gap: 15px;
        }
        .filter-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .filter-btn {
            background: #6c757d;
            padding: 8px 15px;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        .filter-btn.active {
            background: #004080 !important;
        }
    </style>
</head>
<body>

    <?php include '../../components/header.php'; ?>
    <div class="main-container">
        <?php include '../../components/sidebar.php'; ?>
        <div class="main">
            
            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'reviewed'): ?>
                <div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px; font-weight: bold; border: 1px solid #c3e6cb;">
                    ✅ Review submitted successfully! The student's status has been updated.
                </div>
            <?php endif; ?>

            <div class="header-controls" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1 style="color: var(--primary-navy); font-size: 24px; margin: 0;">Faculty Submissions</h1>
                
                <div class="filter-group" style="display: flex; gap: 10px; margin-top: 70px;">
                    <button onclick="window.location.href='?filter=all'" class="cta-btn" style="background: <?php echo $filter == 'all' ? '#004080' : '#6c757d'; ?>; padding: 8px 15px;">All</button>
                    <button onclick="window.location.href='?filter=submitted'" class="cta-btn" style="background: <?php echo $filter == 'submitted' ? '#004080' : '#6c757d'; ?>; padding: 8px 15px;">Pending</button>
                    <button onclick="window.location.href='?filter=selected'" class="cta-btn" style="background: <?php echo $filter == 'selected' ? '#004080' : '#6c757d'; ?>; padding: 8px 15px;">Selected</button>
                </div>
            </div>

            <div class="card">
                <h2 style="margin-bottom: 15px;">Submissions Needing Review</h2>
                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Article Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Date Submitted</th>
                                <th>Review Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (count($contributions) > 0): 
                                $today = new DateTime();
                                $today->setTime(0, 0, 0); // Normalize to midnight

                                foreach ($contributions as $row): 
                                    if ($filter !== 'all' && strtolower($row['status']) !== $filter) {
                                        continue;
                                    }

                                 
                                    $submission_date = new DateTime($row['submission_date']);
                                    $submission_date->setTime(0, 0, 0);
                                    
                                    $deadline = clone $submission_date;
                                    $deadline->modify('+14 days');

                                    $is_overdue = $today > $deadline;
                                    $days_left = $is_overdue ? 0 : $today->diff($deadline)->days;

                                    // Dynamic Badge Styling based on status AND deadline
                                    $bg = "#fff3cd"; $color = "#856404"; 
                                    $display_text = $days_left . " Days Left";

                                    if(strtolower($row['status']) == 'selected') { 
                                        $bg = "#d4edda"; $color = "#155724"; 
                                        $display_text = "Selected";
                                    } elseif(strtolower($row['status']) == 'rejected') { 
                                        $bg = "#f8d7da"; $color = "#721c24"; 
                                        $display_text = "Rejected";
                                    } elseif($is_overdue) {
                                        $bg = "#f8d7da"; $color = "#721c24"; 
                                        $display_text = "Overdue (Closed)";
                                    }
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td><?php echo htmlspecialchars($row['categoryname']); ?></td>
                                    <td><?php echo date("M d, Y", strtotime($row['submission_date'])); ?></td>
                                    <td>
                                        <span class="role-badge" style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>;">
                                            <?php echo htmlspecialchars($display_text); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        // Hide review button if overdue OR already graded
                                        if(!$is_overdue && strtolower($row['status']) != 'selected' && strtolower($row['status']) != 'rejected'): 
                                        ?>
                                            <a href="review_article.php?id=<?php echo $row['contributionid']; ?>" class="cta-btn" style="padding: 6px 15px; font-size: 12px; text-decoration: none; display: inline-block;">
                                                <span class="material-symbols-outlined" style="font-size: 16px; vertical-align: middle;">visibility</span> Review
                                            </a>
                                        <?php else: ?>
                                            <span style="font-size: 12px; color: #888; font-weight: bold; padding: 6px 0; display: inline-block;">
                                                <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">lock</span> Closed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px; color: #666;">No submissions found for your faculty yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    
    <?php include '../../components/footer.php'; ?>
    <script src="../../assests/js/script.js?v=<?php echo time(); ?>"></script>
</body>
</html>