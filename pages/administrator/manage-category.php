<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

$required_role = 1;
require_once "../../pages/auth/check-role.php";
require_once "../../config/db_connection.php";



$ay_stmt = $pdo->query("SELECT * FROM tblacademicyear ORDER BY yearname DESC");
$academic_years = $ay_stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['save_category'])) {
    $categoryname = trim($_POST['categoryname']);
    $academicyearid = $_POST['academicyearid'];
    $start_date = $_POST['start_date'];
    $closure_date = $_POST['closure_date'];

    $sql = "INSERT INTO tblcategory (academicyearid, categoryname, categorystartdate, categoryclosuredate)
            VALUES (:academicyearid, :categoryname, :start_date, :closure_date)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':academicyearid' => $academicyearid,
        ':categoryname' => $categoryname,
        ':start_date' => $start_date,
        ':closure_date' => $closure_date
    ]);

    header("Location: manage-category.php?msg=added");
    exit();
}

// 3. Fetch Categories to display in the table, joining with Academic Year to get the year name
$sql = "SELECT c.*, a.yearname FROM tblcategory c 
        LEFT JOIN tblacademicyear a ON c.academicyearid = a.academicyearid 
        ORDER BY c.categoryid DESC";
$stmt = $pdo->query($sql);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Coordinator</title>

    <link rel="stylesheet" href="../../assests/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/adm_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>

<?php include '../../components/header.php'; ?>

<div class="main-container">

    <?php include '../../components/sidebar.php'; ?>

    <div class="main">

        <?php if (isset($_GET['msg']) && $_GET['msg'] == "added") { ?>
            <div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px; margin-top:50px; font-weight: bold; border: 1px solid #c3e6cb;">
                ✅ Submission Category created successfully.
            </div>
        <?php } ?>

        <div class="dashboard-grid" style="grid-template-columns:1fr;">

            <div class="card">
                <h2>Open a New Submission Category</h2>

                <form method="POST" class="form-grid">

                    <div class="form-group">
                        <label>Category Name (e.g., Spring Issue)</label>
                        <input type="text" name="categoryname" class="form-control" placeholder="Enter Category Name" required>
                    </div>

                    <div class="form-group">
                        <label>Link to Academic Year</label>
                        <select name="academicyearid" class="form-control" required>
                            <option value="">Choose Academic Year...</option>
                            <?php foreach ($academic_years as $ay) { ?>
                                <option value="<?php echo $ay['academicyearid']; ?>">
                                    <?php echo htmlspecialchars($ay['yearname']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Start Date (Submissions Open)</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Closure Date (Submissions Close)</label>
                        <input type="date" name="closure_date" class="form-control" required>
                    </div>

                    <div class="form-group full-width">
                        <button type="submit" name="save_category" class="cta-btn" style="width: 200px;">
                            Launch Category
                        </button>
                    </div>

                </form>
            </div>

            <div class="card">
                <h2>Active & Past Categories</h2>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Academic Year</th>
                                <th>Start Date</th>
                                <th>Closure Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat) { 
                                $today = date("Y-m-d");
                                $status = "Open";
                                $badge_class = "badge-open";
                                
                                if ($today > $cat['categoryclosuredate']) {
                                    $status = "Closed";
                                    $badge_class = "badge-closed";
                                } elseif ($today < $cat['categorystartdate']) {
                                    $status = "Upcoming";
                                    $badge_class = "badge-warning";
                                }
                            ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($cat['categoryname']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($cat['yearname'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($cat['categorystartdate']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['categoryclosuredate']); ?></td>
                                    <td>
                                        <span class="role-badge <?php echo $badge_class; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../../components/footer.php'; ?>

<script src="../../assests/js/script.js"></script>
</body>
</html>