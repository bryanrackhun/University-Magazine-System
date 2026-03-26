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
    $facultyname = trim($_POST['facultyname']);
    $description = trim($_POST['description']);

    try {
        // Check if the faculty name already exists (Case-Insensitive)
        $check_sql = "SELECT COUNT(*) FROM tblfaculty WHERE LOWER(facultyname) = LOWER(?)";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$facultyname]);
        $exists = $check_stmt->fetchColumn();

        if ($exists > 0) {
            // Stop the registration and throw an error
            $_SESSION['error_msg'] = "Wait! A faculty named '$facultyname' already exists in the system.";
        } else {
            $sql = "INSERT INTO tblfaculty (facultyname, description) VALUES (?, ?)";
            $stmt = $pdo->prepare($sql);
            
            if ($stmt->execute([$facultyname, $description])) {
                $_SESSION['success_msg'] = "Faculty '$facultyname' registered successfully!";
            } else {
                $_SESSION['error_msg'] = "Failed to register faculty.";
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error_msg'] = "Database Error: " . $e->getMessage();
    }

    header("Location: manage-faculties.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculties - Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/adm_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include '../../components/header.php'; ?>
    <div class="main-container">
        <?php include '../../components/sidebar.php'; ?>
        <div class="main">

            <?php if(!empty($success_msg)): ?>
                <div style="background-color: #e8f5e9; color: var(--status-success); margin-top:70px; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid var(--status-success);">
                    <strong>Success!</strong> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($error_msg)): ?>
                <div style="background-color: #fff3f3; color: var(--status-overdue); margin-top:70px; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid var(--status-overdue);">
                    <strong>Error!</strong> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-grid" style="grid-template-columns: 1fr;">
                
                <div class="card">
                    <h2>Add New Faculty</h2>
                    <form class="form-grid" method="POST" action="">
                        <div class="form-group">
                            <label>Faculty Name</label>
                            <input type="text" name="facultyname" class="form-control" placeholder="Enter faculty name" required>
                        </div>
                        <div class="form-group">
                            <label>Faculty Description</label>
                            <input type="text" name="description" class="form-control" placeholder="e.g. Department of Computer Science" required>
                        </div>
                        <div class="form-group full-width">
                            <button type="submit" name="btnsubmit" class="cta-btn" style="width: 200px;">Register Faculty</button>
                        </div>
                    </form>
                </div>
                
                <div class="card">
                    <h2>Current Faculties</h2>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Faculty ID</th>
                                    <th>Faculty Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // --- Fetch Faculties ---
                                $fetch_sql = "SELECT * FROM tblfaculty ORDER BY facultyid DESC";
                                $fetch_stmt = $pdo->query($fetch_sql);
                                $faculties = $fetch_stmt->fetchAll(PDO::FETCH_ASSOC);

                                if (count($faculties) > 0) {
                                    foreach ($faculties as $row) {
                                        echo "<tr>";
                                        echo "<td>FAC-" . str_pad($row['facultyid'], 3, '0', STR_PAD_LEFT) . "</td>";
                                        echo "<td><strong>" . htmlspecialchars($row['facultyname']) . "</strong></td>";
                                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' style='text-align: center; color: var(--text-light);'>No Faculties registered yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
        // Make sure sidebar menus toggle properly
        document.addEventListener('DOMContentLoaded', function() {
            let menuicn = document.getElementById("menuicn");
            let nav = document.querySelector(".navcontainer");
            
            if(menuicn && nav) {
                menuicn.addEventListener("click", () => {
                    nav.classList.toggle("navclose");
                });
            }
        });
    </script>
</body>
</html>