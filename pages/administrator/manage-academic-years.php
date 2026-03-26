<?php
session_start();
require_once "../../config/db_connection.php";
$required_role = 1; 
require_once "../auth/check-role.php";

$success_msg = "";
$error_msg = "";

if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']); 
}

if (isset($_SESSION['error_msg'])) {
    $error_msg = $_SESSION['error_msg'];
    unset($_SESSION['error_msg']);
}


if (isset($_POST['btnsubmit'])) {
    $yearname = trim($_POST['yearname']);
    $submission_date = $_POST['submission_closure_date'];
    $final_date = $_POST['final_closure_date'];

    try {
        $sql = "INSERT INTO tblacademicyear (yearname, submission_closure_date, final_closure_date) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$yearname, $submission_date, $final_date])) {
          
            $_SESSION['success_msg'] = "Academic Year '$yearname' configured successfully!";
        } else {
            $_SESSION['error_msg'] = "Failed to save configuration.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Database Error: " . $e->getMessage();
    }
    
    
    header("Location: manage-academic-years.php");
    exit(); // 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Academic Years - Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/adm_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <?php include '../../components/header.php'; ?>
    <div class="main-container">
        <?php include '../../components/sidebar.php'; ?>
        
        <div class="main">
            
            <?php if(!empty($success_msg)): ?>
                <div style="background-color: #e8f5e9; color: var(--status-success); padding: 15px; border-radius: 6px; margin-bottom: 20px; margin-top: 70px; border-left: 4px solid var(--status-success);">
                    <strong>Success!</strong> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($error_msg)): ?>
                <div style="background-color: #fff3f3; color: var(--status-overdue); padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid var(--status-overdue);">
                    <strong>Error!</strong> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-grid" style="grid-template-columns: 1fr;">
                
                <div class="card">
                    <h2>Configure Academic Year Details</h2>
                    <form class="form-grid" method="POST" action="">
                        <div class="form-group full-width">
                            <label>Academic Year Name</label>
                            <input type="text" name="yearname" class="form-control" placeholder="e.g. 2025/2026" required>
                        </div>
                        <div class="form-group">
                            <label>Submission Closure Date</label>
                            <input type="date" name="submission_closure_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Final Closure Date</label>
                            <input type="date" name="final_closure_date" class="form-control" required>
                        </div>
                        <div class="form-group full-width">
                            <button type="submit" name="btnsubmit" class="cta-btn" style="width: 200px;">Save Configuration</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h2>Current Academic Years</h2>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Academic Year</th>
                                    <th>Submission Closure</th>
                                    <th>Final Closure</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $fetch_sql = "SELECT * FROM tblacademicyear ORDER BY academicyearid DESC";
                                $fetch_stmt = $pdo->query($fetch_sql);
                                $years = $fetch_stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (count($years) > 0) {
                                    foreach ($years as $row) {
                                        $today = date("Y-m-d");
                                        $status_badge = ($today > $row['final_closure_date']) 
                                            ? '<span class="role-badge" style="background-color: #f8d7da; color: #721c24;">Closed</span>' 
                                            : '<span class="role-badge badge-coordinator">Active</span>';

                                        echo "<tr>";
                                        echo "<td>AY-" . htmlspecialchars($row['academicyearid']) . "</td>";
                                        echo "<td><strong>" . htmlspecialchars($row['yearname']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['submission_closure_date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['final_closure_date']) . "</td>";
                                        echo "<td>" . $status_badge . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align: center; color: var(--text-light);'>No Academic Years configured yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php include '../../components/footer.php'; ?>
</body>
</html>