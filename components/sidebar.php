<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = '/University-Magazine-System';
$role = $_SESSION['roleid'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assests/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/adm_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title>Magazine System</title>
</head>
<body>
   <div class="navcontainer">
        <nav class="nav">
            <div class="nav-upper-options">
                
                <a href="<?php echo $base_url; ?>/components/statistics.php" class="nav-option <?php echo ($current_page == 'statistics.php') ? 'option1' : ''; ?>">
                    <span class="material-symbols-outlined nav-icon">bar_chart</span>
                    <h3>Reports</h3>
                </a>

                <?php if($role == 1): ?>
                    <a href="<?php echo $base_url; ?>/pages/administrator/admin-dashboard.php" class="nav-option <?php echo ($current_page == 'admin-dashboard.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">dashboard</span>
                        <h3>Dashboard</h3>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>/pages/administrator/manage-users.php" class="nav-option <?php echo ($current_page == 'manage-users.php' || $current_page == 'edit-user.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">manage_accounts</span>
                        <h3>Manage Users</h3>
                    </a>

                    <a href="<?php echo $base_url; ?>/pages/administrator/manage-category.php" class="nav-option <?php echo ($current_page == 'manage-categories.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">category</span>
                        <h3>Categories</h3>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>/pages/administrator/assign-roles.php" class="nav-option <?php echo ($current_page == 'assign-roles.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">badge</span>
                        <h3>Assign Roles</h3>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>/pages/administrator/manage-academic-years.php" class="nav-option <?php echo ($current_page == 'manage-academic-years.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">calendar_month</span>
                        <h3>Manage Academic Years</h3>
                    </a>
                    
                    <a href="<?php echo $base_url; ?>/pages/administrator/manage-faculties.php" class="nav-option <?php echo ($current_page == 'manage-faculties.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">account_balance</span>
                        <h3>Manage Faculties</h3>
                    </a>
                <?php endif; ?>

                <?php if($role == 2): ?>
                    <a href="<?php echo $base_url; ?>/pages/marketing-manager/manager-dashboard.php" class="nav-option <?php echo ($current_page == 'manager-dashboard.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">insights</span>
                        <h3>Manager Panel</h3>
                    </a>
                <?php endif; ?>

                <?php if($role == 3): ?>
                    <a href="<?php echo $base_url; ?>/pages/marketing coordinator/manage-contribution.php" class="nav-option <?php echo ($current_page == 'manage-contribution.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">article</span>
                        <h3>Contributions</h3>
                    </a>

                    <a href="<?php echo $base_url; ?>/pages/marketing coordinator/manage-guests.php" class="nav-option <?php echo ($current_page == 'manage-guests.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">group</span>
                        <h3>Guests</h3>
                    </a>
                <?php endif; ?>

                <?php if($role == 4): ?>
                    <a href="<?php echo $base_url; ?>/pages/student/student-panel.php" class="nav-option <?php echo ($current_page == 'student-panel.php') ? 'option1' : ''; ?>">
                        <span class="material-symbols-outlined nav-icon">dashboard</span>
                        <h3>Dashboard</h3>
                    </a>
                <?php endif; ?>

            </div>
        </nav>
    </div>
</body>
<script src="../../assests/js/script.js"></script>
</html>