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
                    <h2>Configure Academic Year Details</h2>
                    <form class="form-grid">
                        <div class="form-group full-width">
                            <label>Academic Year Name</label>
                            <input type="text" class="form-control" placeholder="e.g. 2025/2026">
                        </div>
                        <div class="form-group">
                            <label>Submission Closure Date</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Final Closure Date</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="form-group full-width">
                            <button type="submit" class="cta-btn" style="width: 200px;">Save Configuration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>