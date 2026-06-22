<?php

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$score_stmt = $conn->prepare("
    SELECT * FROM scores 
    WHERE user_id = ? 
    ORDER BY score_id DESC 
    LIMIT 1
");
$score_stmt->bind_param("i", $user_id);
$score_stmt->execute();
$score = $score_stmt->get_result()->fetch_assoc();

$gap_stmt = $conn->prepare("
    SELECT * FROM gaps 
    WHERE user_id = ? AND status = 'Open'
");
$gap_stmt->bind_param("i", $user_id);
$gap_stmt->execute();
$gaps = $gap_stmt->get_result();

$funders = $conn->query("SELECT * FROM funders ORDER BY min_score ASC LIMIT 3");

$total_score = $score["total_score"] ?? 0;

?>

<!DOCTYPE html>
<html>
<head>
    <title>Readiness Report - KHULISA</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<nav class="no-print">
    <h2>KHULISA</h2>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="assessment.php">Assessment</a>
        <a href="business-plan.php">Business Plan</a>
        <a href="cashflow.php">Cashflow</a>
        <a href="matches.php">Matches</a>
        <a href="documents.php">Documents</a>
        <a href="report.php">Report</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="card report-card">

        <h1>KHULISA Funding Readiness Report</h1>

        <p><strong>Owner:</strong> <?php echo clean($user["full_name"]); ?></p>
        <p><strong>Business:</strong> <?php echo clean($user["business_name"]); ?></p>
        <p><strong>Date:</strong> <?php echo date("d M Y"); ?></p>

        <hr>

        <h2>Readiness Score</h2>

        <div class="score-circle">
            <?php echo $total_score; ?>
        </div>

        <h2>Category Scores</h2>

        <?php
        $categories = [
            "Financial" => $score["financial_score"] ?? 0,
            "Documentation" => $score["documentation_score"] ?? 0,
            "Credit" => $score["credit_score"] ?? 0,
            "Governance" => $score["governance_score"] ?? 0,
            "Viability" => $score["viability_score"] ?? 0
        ];
        ?>

        <?php foreach ($categories as $name => $value): ?>
            <div class="bar-row">
                <span><?php echo $name; ?></span>
                <div class="bar">
                    <div style="width: <?php echo $value; ?>%">
                        <?php echo $value; ?>%
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <h2>Funding Gaps</h2>

        <?php if ($gaps->num_rows === 0): ?>
            <p>No major funding gaps identified.</p>
        <?php else: ?>
            <?php while ($gap = $gaps->fetch_assoc()): ?>
                <div class="gap <?php echo strtolower($gap["severity"]); ?>">
                    <strong><?php echo $gap["severity"]; ?>:</strong>
                    <?php echo clean($gap["description"]); ?>
                    <small><?php echo clean($gap["category"]); ?></small>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>

        <h2>Recommended Funders</h2>

        <?php while ($funder = $funders->fetch_assoc()): ?>
            <div class="document-card">
                <h3><?php echo clean($funder["funder_name"]); ?></h3>
                <p><strong>Type:</strong> <?php echo clean($funder["funder_type"]); ?></p>
                <p><strong>Minimum Score:</strong> <?php echo $funder["min_score"]; ?></p>
                <p><?php echo clean($funder["description"]); ?></p>
            </div>
        <?php endwhile; ?>

        <h2>Next Steps</h2>

        <ul>
            <li>Complete or update your business plan.</li>
            <li>Prepare a 6-month cashflow forecast.</li>
            <li>Upload funding documents.</li>
            <li>Review funder matches and prepare applications.</li>
        </ul>

        <button class="no-print" onclick="window.print()">
            Download / Print Report
        </button>

    </div>

</div>

</body>
</html>