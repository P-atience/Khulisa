<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

$user_stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$assessment_stmt = $conn->prepare("
    SELECT * FROM assessments
    WHERE user_id = ?
    ORDER BY assessment_id DESC
    LIMIT 1
");
$assessment_stmt->bind_param("i", $user_id);
$assessment_stmt->execute();
$assessment = $assessment_stmt->get_result()->fetch_assoc();

$score = $user["readiness_score"] ?? 0;
$revenue = $assessment["revenue"] ?? 0;
$years = $assessment["years_operating"] ?? 0;

$funders = $conn->query("SELECT * FROM funders ORDER BY min_score ASC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Funder Matches - KHULISA</title>
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
        <h1>Funder Matches</h1>
        <p>Matched funders based on your readiness score, revenue, and years operating.</p>
    </div>

    <?php if (!$assessment): ?>

        <div class="card">
            <h3>No assessment found</h3>
            <p>Please complete your assessment first to see funder matches.</p>
            <a class="btn-link" href="assessment.php">Complete Assessment</a>
        </div>

    <?php else: ?>

        <div class="dashboard-grid">

            <div class="card center">
                <div class="score-circle">
                    <?php echo $score; ?>
                </div>
                <h3>Your Readiness Score</h3>
            </div>

            <div class="card">
                <h3>Your Funding Profile</h3>
                <p><strong>Business:</strong> <?php echo clean($user["business_name"]); ?></p>
                <p><strong>Monthly Revenue:</strong> R <?php echo number_format($revenue, 2); ?></p>
                <p><strong>Years Operating:</strong> <?php echo $years; ?></p>
            </div>

        </div>

        <div class="match-grid">

            <?php while ($funder = $funders->fetch_assoc()): ?>

                <?php
                    $fit = 0;

                    if ($score >= $funder["min_score"]) {
                        $fit += 50;
                    } else {
                        $fit += max(0, round(($score / max($funder["min_score"], 1)) * 50));
                    }

                    if ($revenue >= $funder["min_revenue"]) {
                        $fit += 30;
                    } else {
                        $fit += max(0, round(($revenue / max($funder["min_revenue"], 1)) * 30));
                    }

                    if ($years >= $funder["min_years"]) {
                        $fit += 20;
                    } else {
                        $fit += max(0, round(($years / max($funder["min_years"], 1)) * 20));
                    }

                    $fit = min($fit, 100);

                    $eligible =
                        $score >= $funder["min_score"] &&
                        $revenue >= $funder["min_revenue"] &&
                        $years >= $funder["min_years"];

                    $status = $eligible ? "Eligible" : "Needs Improvement";
                    $status_class = $eligible ? "eligible" : "needs";
                ?>

                <div class="card match-card">
                    <div class="match-top">
                        <h3><?php echo clean($funder["funder_name"]); ?></h3>
                        <span class="status <?php echo $status_class; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>

                    <p>
                        <strong>Type:</strong>
                        <?php echo clean($funder["funder_type"]); ?>
                    </p>

                    <p>
                        <strong>Loan Range:</strong>
                        R <?php echo number_format($funder["min_loan"], 0); ?>
                        -
                        R <?php echo number_format($funder["max_loan"], 0); ?>
                    </p>

                    <p>
                        <strong>Minimum Score:</strong>
                        <?php echo $funder["min_score"]; ?>
                    </p>

                    <p>
                        <strong>Minimum Revenue:</strong>
                        R <?php echo number_format($funder["min_revenue"], 0); ?>
                    </p>

                    <p>
                        <strong>Minimum Years:</strong>
                        <?php echo $funder["min_years"]; ?>
                    </p>

                    <p>
                        <?php echo clean($funder["description"]); ?>
                    </p>

                    <div class="fit-label">
                        Match Fit: <?php echo $fit; ?>%
                    </div>

                    <div class="bar">
                        <div style="width: <?php echo $fit; ?>%">
                            <?php echo $fit; ?>%
                        </div>
                    </div>
                </div>

            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

</body>
</html>