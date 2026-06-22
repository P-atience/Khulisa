<?php

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireLogin();
requireRole("admin");

$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()["total"];
$total_owners = $conn->query("SELECT COUNT(*) AS total FROM users WHERE user_type='owner'")->fetch_assoc()["total"];
$total_assessments = $conn->query("SELECT COUNT(*) AS total FROM assessments")->fetch_assoc()["total"];
$total_documents = $conn->query("SELECT COUNT(*) AS total FROM documents")->fetch_assoc()["total"];
$total_plans = $conn->query("SELECT COUNT(*) AS total FROM business_plans")->fetch_assoc()["total"];

$avg_score = $conn->query("
    SELECT ROUND(AVG(readiness_score), 0) AS avg_score 
    FROM users 
    WHERE user_type='owner'
")->fetch_assoc()["avg_score"];

if (!$avg_score) {
    $avg_score = 0;
}

$users = $conn->query("
    SELECT 
        u.user_id,
        u.full_name,
        u.business_name,
        u.email,
        u.user_type,
        u.readiness_score,
        u.created_at
    FROM users u
    ORDER BY u.created_at DESC
");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - KHULISA</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<nav>
    <h2>KHULISA Admin</h2>

    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../dashboard.php">Owner View</a>
        <a href="funders.php">Funders</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-title">
        <h1>Admin Dashboard</h1>
        <p>System overview and user performance analytics.</p>
    </div>

    <div class="dashboard-grid">

        <div class="card center">
            <h3>Total Users</h3>
            <div class="score-circle"><?php echo $total_users; ?></div>
        </div>

        <div class="card center">
            <h3>SMB Owners</h3>
            <div class="score-circle"><?php echo $total_owners; ?></div>
        </div>

    </div>

    <div class="dashboard-grid">

        <div class="card">
            <h3>Platform Analytics</h3>

            <p><strong>Total Assessments:</strong> <?php echo $total_assessments; ?></p>
            <p><strong>Documents Uploaded:</strong> <?php echo $total_documents; ?></p>
            <p><strong>Business Plans Created:</strong> <?php echo $total_plans; ?></p>
            <p><strong>Average Readiness Score:</strong> <?php echo $avg_score; ?>%</p>
        </div>

        <div class="card">
            <h3>Admin Actions</h3>

            <a class="btn-link" href="dashboard.php">Refresh Dashboard</a>
            <a class="btn-link" href="../matches.php">View Funder Matching</a>
            <a class="btn-link" href="../report.php">View Report Template</a>
        </div>

    </div>

    <div class="card">

        <h2>Registered Users</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Business</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Score</th>
                    <th>Registered</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($user = $users->fetch_assoc()): ?>

                    <tr>
                        <td><?php echo clean($user["full_name"]); ?></td>
                        <td><?php echo clean($user["business_name"]); ?></td>
                        <td><?php echo clean($user["email"]); ?></td>
                        <td><?php echo clean($user["user_type"]); ?></td>
                        <td><?php echo $user["readiness_score"]; ?>%</td>
                        <td><?php echo $user["created_at"]; ?></td>
                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>