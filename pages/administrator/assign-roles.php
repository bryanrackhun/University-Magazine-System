<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine System</title>
    <link rel="stylesheet" href="../../assests/css/adm-dash.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>

<body>
    <?php include '../../components/adm-dash-header.php'; ?>
    <div class="main-container">
        <?php include '../../components/adm-dash-sidebar.php'; ?>
        <div class="main">
            <div class="dashboard-grid" style="grid-template-columns: 1fr;">
                <div class="card">
                    <h2>Assign System Roles</h2>
                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Current Role</th>
                                    <th>Change Role</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>John Doe</td>
                                    <td>j.doe@uni.edu</td>
                                    <td><span class="role-badge badge-student">Student</span></td>
                                    <td>
                                        <select class="form-control">
                                            <option>Student</option>
                                            <option>Marketing Coordinator</option>
                                            <option>Marketing Manager</option>
                                        </select>
                                    </td>
                                    <td><button class="cta-btn">Update</button></td>
                                </tr>
                                <tr>
                                    <td>Jane Smith</td>
                                    <td>j.smith@uni.edu</td>
                                    <td><span class="role-badge badge-coordinator">Coordinator</span></td>
                                    <td>
                                        <select class="form-control">
                                            <option>Marketing Coordinator</option>
                                            <option>Student</option>
                                            <option>Marketing Manager</option>
                                        </select>
                                    </td>
                                    <td><button class="cta-btn">Update</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>