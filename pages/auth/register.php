<?php
    require_once '../../config/db_connection.php';

    $facultselect = "SELECT * FROM tblfaculty";
    $stmt_faculty = $pdo->prepare($facultselect);
    $stmt_faculty->execute();
    $faculties = $stmt_faculty->fetchAll(PDO::FETCH_ASSOC);

    $roleselect = "SELECT * FROM tblrole WHERE roleid IN (4,5) ORDER BY roleid ASC";
    $stmt_role = $pdo->prepare($roleselect);
    $stmt_role->execute();  
    $roles = $stmt_role->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assests/css/form_style.css">
    <link rel="stylesheet" href="../../assests/css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <title>Magazine System</title>
</head>
<body>

    <div class="form_container">
        <h1>Register for an Account</h1>
        <form action="register_process.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <input type="radio" id="male" name="gender" value="male" required class="inline-input">
                <label for="male" class="inline-label">Male</label>
                <input type="radio" id="female" name="gender" value="female" required>
                <label for="female" class="inline-label">Female</label>
                <input type="radio" id="other" name="gender" value="other" required>
                <label for="other" class="inline-label">Other</label>
            </div>

            <div class="form-group">
                <label for="faculty">Faculty:</label>
                <select id="faculty" name="faculty" required>
                    <option value="">--Select Faculty--</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?php echo $faculty['facultyid']; ?>"><?php echo $faculty['facultyname']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="role">Register as:</label>
                <select id="role" name="role" required>
                    <option value="">--Select Role--</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['roleid']; ?>"><?php echo $role['rolename']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required style="width: 100%; padding-right: 40px; box-sizing: border-box;">
                    <span class="material-symbols-outlined" onclick="toggleVisibility('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; user-select: none;">visibility_off</span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" required style="width: 100%; padding-right: 40px; box-sizing: border-box;">
                    <span class="material-symbols-outlined" onclick="toggleVisibility('confirm_password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; user-select: none;">visibility_off</span>
                </div>
                <small id="password_match_msg" style="display: none; margin-top: 5px; font-weight: bold;"></small>
            </div>

            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>

            <div class="form-group">
                <input type="submit" name="btnReg" value="Register">
                <input type="reset" value="Cancel">
            </div>
            <div class="form-group">
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </div>
        </form>
    </div> 
</body>
<script>
        function toggleVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.textContent = "visibility";
            } else {
                input.type = "password";
                icon.textContent = "visibility_off";
            }
        }

        const submitBtn = document.getElementById('submitBtn');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const matchMsg = document.getElementById('password_match_msg');

        function validateForm() {
            let isMatch = false;

            if (confirmPassword.value === "") {
                matchMsg.style.display = "none";
            } else if (password.value === confirmPassword.value) {
                matchMsg.textContent = "Passwords match!";
                matchMsg.style.color = "var(--status-success, #28a745)";
                matchMsg.style.display = "block";
                isMatch = true;
            } else {
                matchMsg.textContent = "Passwords do not match.";
                matchMsg.style.color = "var(--status-overdue, #dc3545)";
                matchMsg.style.display = "block";
            }

            if (isMatch) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                submitBtn.style.cursor = 'pointer';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.5';
                submitBtn.style.cursor = 'not-allowed';
            }
        }

        password.addEventListener('input', validateForm);
        confirmPassword.addEventListener('input', validateForm);
    </script>
</html>