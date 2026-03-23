<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magazine System - Statistics</title>
    <link rel="stylesheet" href="../assests/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assests/css/layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assests/css/report.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="main-container">
        
        <?php include 'sidebar.php'; ?>
        
        <div class="main">
            <div class="stats-header">
                <h1>System-Wide Statistics</h1>
                <p>Comprehensive breakdown of university contributions and technical analytics.</p>
            </div>

            <h2 class="stats-section-title">Academic Engagement</h2>
            <div class="stats-grid">
                
                <div class="stat-card">
                    <h3>Total Contributions</h3>
                    <div class="stat-number">142</div>
                    <div class="stat-breakdown">
                        <strong>Breakdown by Faculty:</strong><br>
                        Faculty of Computing & IT: 85<br>
                        Faculty of Engineering: 57
                    </div>
                </div>

                <div class="stat-card">
                    <h3>Highest Distribution</h3>
                    <div class="stat-number">60% <span>IT</span></div>
                    <div class="stat-breakdown">
                        <strong>Other Faculties:</strong><br>
                        Faculty of Engineering: 40%<br>
                        Faculty of Business: 0%
                    </div>
                </div>

                <div class="stat-card">
                    <h3>Unique Contributors</h3>
                    <div class="stat-number">89</div>
                    <div class="stat-breakdown">
                        <strong>Breakdown by Faculty:</strong><br>
                        Faculty of Computing & IT: 50 students<br>
                        Faculty of Engineering: 39 students
                    </div>
                </div>
            </div>

            <h2 class="stats-section-title">Technical Analytics</h2>
            <div class="stats-grid">
                
                <div class="stat-card">
                    <h3>Most Active Users</h3>
                    <p class="card-desc">Users ranked by total system interactions.</p>
                    <table class="analytics-table">
                        <tbody>
                            <tr>
                                <td><strong>Alex Johnson</strong> (Student)</td>
                                <td class="metric-val highlight">142 Logins</td>
                            </tr>
                            <tr>
                                <td><strong>Sarah Smith</strong> (Coordinator)</td>
                                <td class="metric-val highlight">98 Logins</td>
                            </tr>
                            <tr>
                                <td><strong>David Lee</strong> (Student)</td>
                                <td class="metric-val highlight">76 Logins</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="stat-card">
                    <h3>Client Browser Usage</h3>
                    <p class="card-desc">Analytics on how users access the system.</p>
                    <table class="analytics-table">
                        <tbody>
                            <tr>
                                <td>Google Chrome</td>
                                <td class="metric-val">65%</td>
                            </tr>
                            <tr>
                                <td>Apple Safari</td>
                                <td class="metric-val">20%</td>
                            </tr>
                            <tr>
                                <td>Mozilla Firefox / Edge</td>
                                <td class="metric-val">15%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="stat-card">
                    <h3>Most Viewed Pages</h3>
                    <p class="card-desc">Highest traffic routes across the platform.</p>
                    <table class="analytics-table">
                        <tbody>
                            <tr>
                                <td>/student/student-panel.php</td>
                                <td class="metric-val">1,245 Views</td>
                            </tr>
                            <tr>
                                <td>/marketing-coordinator/review.php</td>
                                <td class="metric-val">890 Views</td>
                            </tr>
                            <tr>
                                <td>/auth/login.html</td>
                                <td class="metric-val">850 Views</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="../assests/js/script.js"></script>

</body>
</html>