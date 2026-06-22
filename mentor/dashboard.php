<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireLogin();
requireRole("mentor");

$mentor_id = currentUserId();

$clients = $conn->prepare("
    SELECT 
        u.user_id,
        u.full_name,
        u.business_name,
        u.email,
        u.readiness_score,
        mc.assigned_date
    FROM mentor_clients mc
    JOIN users u ON mc.client_id = u.user_id
    WHERE mc.mentor_id = ?
    ORDER BY mc.assigned_date DESC
");

$clients->bind_param("i", $mentor_id);
$clients->execute();
$result = $clients->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mentor Dashboard - KHULISA</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<nav>
    <h2>KHULISA Mentor</h2>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-title">
        <h1>Mentor Dashboard</h1>
        <p>View and support your assigned SME clients.</p>
    </div>

    <div class="card">
        <h2>Assigned Clients</h2>

        <?php if ($result->num_rows === 0): ?>
            <p>No clients assigned yet.</p>
        <?php else: ?>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Owner</th>
                        <th>Business</th>
                        <th>Email</th>
                        <th>Score</th>
                        <th>Assigned</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($client = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo clean($client["full_name"]); ?></td>
                            <td><?php echo clean($client["business_name"]); ?></td>
                            <td><?php echo clean($client["email"]); ?></td>
                            <td><?php echo $client["readiness_score"]; ?>%</td>
                            <td><?php echo $client["assigned_date"]; ?></td>
                            <td>
                                <a class="btn-link" href="client.php?id=<?php echo $client["user_id"]; ?>">
                                    View Client
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

        <?php endif; ?>
    </div>

</div>

</body>
</html>