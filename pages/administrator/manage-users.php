<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine System - Manage Users</title>
    <link rel="stylesheet" href="../../assests/css/adm-dash.css">
    <link rel="stylesheet" href="../../assests/css/form_style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    <?php include '../../components/adm-dash-header.php'; ?>
    <div class="main-container">
        <?php include '../../components/adm-dash-sidebar.php'; ?>
        <div class="main">
            <div class="dashboard-grid" style="grid-template-columns: 1fr;">
                
                <div class="card">
                    <h2>Register University Staff</h2>
                    <form class="form-grid">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" placeholder="Enter username">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" placeholder="name@example.com">
                        </div>
                        <div class="form-group">
                            <label>Temporary Password</label>
                            <input type="password" class="form-control" placeholder="Create a password">
                        </div>
                        <div class="form-group">
                            <label>Staff Role</label>
                            <select class="form-control">
                                <option>Select a role...</option>
                                <option>Marketing Coordinator</option>
                                <option>Marketing Manager</option>
                                <option>Administrator</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label>Assign Faculty (For Coordinators)</label>
                            <select class="form-control">
                                <option>N/A - System Wide Role</option>
                                <option>Faculty of IT</option>
                                <option>Faculty of Business</option>
                                <option>Faculty of Arts</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <button type="submit" class="cta-btn" style="width: 200px;">Create Account</button>
                        </div>
                    </form>
                </div>

                

                <div class="card">
                    <h2>Manage Staff Accounts</h2>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Assigned Role</th>
                                    <th>Faculty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>USR-101</td>
                                    <td>Jane Smith</td>
                                    <td>j.smith@example.com</td>
                                    <td><span class="role-badge badge-coordinator">Coordinator</span></td>
                                    <td>Faculty of IT</td>
                                    <td><button class="cta-btn" style="padding: 5px 10px; font-size: 12px;">Edit</button></td>
                                </tr>
                                <tr>
                                    <td>USR-102</td>
                                    <td>Alan Turing</td>
                                    <td>a.turing@example.com</td>
                                    <td><span class="role-badge badge-manager">Manager</span></td>
                                    <td>System Wide</td>
                                    <td><button class="cta-btn" style="padding: 5px 10px; font-size: 12px;">Edit</button></td>
                                </tr>
                                <tr>
                                    <td>USR-103</td>
                                    <td>Admin User</td>
                                    <td>admin@example.com</td>
                                    <td><span class="role-badge badge-student">Administrator</span></td>
                                    <td>System Wide</td>
                                    <td><button class="cta-btn" style="padding: 5px 10px; font-size: 12px;">Edit</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>