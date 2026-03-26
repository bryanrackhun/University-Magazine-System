<?php
include __DIR__ . '/../config/db_connection.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assests/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <header>
        <div class="logosec">
            <div class="logo">UniMag System</div>
            <span class="material-symbols-outlined menuicn" id="menuicn">menu</span>
        </div>

        <div class="message">
            
            <div class="last-login-container">
                <?php if (empty($_SESSION['previous_login'])): ?>
                    <span class="login-label" style="text-align: right; font-size: 13px;">Welcome!</span>
                    <span class="login-timestamp">This is your first login.</span>
                <?php else: ?>
                    <span class="login-label">Last login:</span>
                    <span class="login-timestamp">
                        <?php 
                            $prev_login = strtotime($_SESSION['previous_login']);
                            echo date('d M Y,', $prev_login) . '<br>' . date('h:i A', $prev_login); 
                        ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="notification-icon">
                <div class="circle"></div>
                <span class="material-symbols-outlined notif-icon">notifications</span>
            </div>

            <div class="user-profile-container">
                <button class="profile-btn" id="profileDropdownBtn" style="display: flex; align-items: center; gap: 8px; background: none; border: none; cursor: pointer;">
                    <?php 
                        $pf_img = !empty($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'default_pf.jpg';
                    ?>
                    <img src="../../uploads/profile_pics/<?php echo htmlspecialchars($pf_img); ?>" alt="Profile Picture" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #4a90e2;">
                    <span class="material-symbols-outlined" style="font-size: 18px;">expand_more</span>
                </button>

                <div class="profile-dropdown-menu" id="profileDropdownMenu">
                    <div class="dropdown-header">
                        <strong>My Account</strong>
                    </div>
                    <a href="../../backend/edit-profile.php" class="dropdown-item">
                        <span class="material-symbols-outlined">person</span> View Profile
                    </a>
                    <hr class="dropdown-divider">
                    <a href="../../pages/auth/logout.php" class="dropdown-item text-danger">
                        <span class="material-symbols-outlined">logout</span> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>
    
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('profileDropdownBtn');
        const dropdownMenu = document.getElementById('profileDropdownMenu');

        if (profileBtn && dropdownMenu) {
            
            profileBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            window.addEventListener('click', function(event) {
                if (!profileBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
    });
</script>
<script src="../../assests/js/script.js"></script>
</html>