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
    ORDER BY FIELD(severity, 'High', 'Medium', 'Low')
");
$gap_stmt->bind_param("i", $user_id);
$gap_stmt->execute();
$gaps = $gap_stmt->get_result();

$total_score = $score["total_score"] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - KHULISA</title>
    <link rel="stylesheet" href="css/style.css">
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
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-title">
        <h1>Welcome, <?php echo clean($user["full_name"]); ?></h1>
        <p><?php echo clean($user["business_name"]); ?></p>
    </div>

    <div class="dashboard-grid">

        <div class="card center">
            <div class="score-circle">
                <?php echo $total_score; ?>
            </div>

            <h3>Readiness Score</h3>
        </div>

        <div class="card">
            <h3>Quick Actions</h3>

            <a class="btn-link" href="assessment.php">
                Complete Assessment
            </a>

            <a class="btn-link" href="business-plan.php">
                Business Plan Builder
            </a>

            <a class="btn-link" href="cashflow.php">
                Cashflow Builder
            </a>
        </div>

    </div>

    <div class="card">
        <h3>Category Scores</h3>

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
    </div>

    <div class="card">
        <h3>Funding Gaps</h3>

        <?php if ($gaps->num_rows === 0): ?>

            <p>No gaps found yet. Complete an assessment to generate funding gaps.</p>

        <?php else: ?>

            <?php while ($gap = $gaps->fetch_assoc()): ?>

                <div class="gap <?php echo strtolower($gap["severity"]); ?>">
                    <strong><?php echo $gap["severity"]; ?>:</strong>
                    <?php echo clean($gap["description"]); ?>

                    <small>
                        <?php echo clean($gap["category"]); ?>
                    </small>
                </div>

            <?php endwhile; ?>

        <?php endif; ?>
    </div>

</div>

</body>
</html>