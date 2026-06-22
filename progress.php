<?php

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

$stmt = $conn->prepare("
    SELECT score, assessment_date
    FROM progress_tracking
    WHERE user_id = ?
    ORDER BY assessment_date ASC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$labels = [];
$scores = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = date("d M", strtotime($row["assessment_date"]));
    $scores[] = (int) $row["score"];
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Progress - KHULISA</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<nav>
    <h2>KHULISA</h2>

    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="assessment.php">Assessment</a>
        <a href="business-plan.php">Business Plan</a>
        <a href="cashflow.php">Cashflow</a>
        <a href="matches.php">Matches</a>
        <a href="documents.php">Documents</a>
        <a href="report.php">Report</a>
        <a href="progress.php">Progress</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-title">
        <h1>Progress Tracking</h1>
        <p>Track your funding readiness score over time.</p>
    </div>

    <div class="card">

        <?php if (count($scores) === 0): ?>

            <p>No progress data yet. Complete an assessment first.</p>
            <a class="btn-link" href="assessment.php">Complete Assessment</a>

        <?php else: ?>

            <canvas id="progressChart"></canvas>

        <?php endif; ?>
        <h2>Assessment History</h2>

<table class="data-table">
    <tr>
        <th>Date</th>
        <th>Score</th>
    </tr>

    <?php

    $history = $conn->prepare("
        SELECT *
        FROM progress_tracking
        WHERE user_id = ?
        ORDER BY assessment_date DESC
    ");

    $history->bind_param("i", $user_id);
    $history->execute();

    $history_result = $history->get_result();

    while ($item = $history_result->fetch_assoc()):
    ?>

    <tr>
        <td><?php echo $item["assessment_date"]; ?></td>
        <td><?php echo $item["score"]; ?>%</td>
    </tr>

    <?php endwhile; ?>

</table>

    </div>

</div>

<script>
const labels = <?php echo json_encode($labels); ?>;
const scores = <?php echo json_encode($scores); ?>;

if (document.getElementById("progressChart")) {
    new Chart(document.getElementById("progressChart"), {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Readiness Score",
                data: scores,
                borderColor: "#2E7D32",
                backgroundColor: "rgba(46,125,50,0.1)",
                borderWidth: 3,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    min: 0,
                    max: 100
                }
            }
        }
    });
}
</script>

</body>
</html>