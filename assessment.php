<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

if (isset($_POST["submit_assessment"])) {

    $data = [
        "revenue" => (float) $_POST["revenue"],
        "separate_account" => clean($_POST["separate_account"]),
        "record_keeping" => clean($_POST["record_keeping"]),
        "years_operating" => (int) $_POST["years_operating"],
        "business_plan" => clean($_POST["business_plan"]),
        "cashflow_forecast" => clean($_POST["cashflow_forecast"]),
        "financial_statements" => clean($_POST["financial_statements"]),
        "credit_score" => (int) $_POST["credit_score"],
        "tax_compliance" => clean($_POST["tax_compliance"]),
        "profitability" => clean($_POST["profitability"])
    ];

    $scores = calculateScores($data);

    $stmt = $conn->prepare("
        INSERT INTO assessments (
            user_id,
            revenue,
            separate_account,
            record_keeping,
            years_operating,
            business_plan,
            cashflow_forecast,
            financial_statements,
            credit_score,
            tax_compliance,
            profitability,
            total_score
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Assessment prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "idssisssissi",
        $user_id,
        $data["revenue"],
        $data["separate_account"],
        $data["record_keeping"],
        $data["years_operating"],
        $data["business_plan"],
        $data["cashflow_forecast"],
        $data["financial_statements"],
        $data["credit_score"],
        $data["tax_compliance"],
        $data["profitability"],
        $scores["total"]
    );

    if (!$stmt->execute()) {
        die("Assessment insert failed: " . $stmt->error);
    }

    $assessment_id = $stmt->insert_id;

    $score_stmt = $conn->prepare("
        INSERT INTO scores (
            user_id,
            assessment_id,
            financial_score,
            documentation_score,
            credit_score,
            governance_score,
            viability_score,
            total_score
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$score_stmt) {
        die("Score prepare failed: " . $conn->error);
    }

    $score_stmt->bind_param(
        "iiiiiiii",
        $user_id,
        $assessment_id,
        $scores["financial"],
        $scores["documentation"],
        $scores["credit"],
        $scores["governance"],
        $scores["viability"],
        $scores["total"]
    );

    if (!$score_stmt->execute()) {
        die("Score insert failed: " . $score_stmt->error);
    }

    $update = $conn->prepare("
        UPDATE users 
        SET readiness_score = ? 
        WHERE user_id = ?
    ");

    $update->bind_param(
        "ii",
        $scores["total"],
        $user_id
    );

    $update->execute();

    $delete_old = $conn->prepare("
        DELETE FROM gaps 
        WHERE user_id = ?
    ");

    $delete_old->bind_param("i", $user_id);
    $delete_old->execute();

    createGaps($conn, $user_id, $assessment_id, $data);

    $progress_stmt = $conn->prepare("
        INSERT INTO progress_tracking
        (user_id, score)
        VALUES (?, ?)
    ");

    if (!$progress_stmt) {
        die("Progress prepare failed: " . $conn->error);
    }

    $progress_stmt->bind_param(
        "ii",
        $user_id,
        $scores["total"]
    );

    if (!$progress_stmt->execute()) {
        die("Progress insert failed: " . $progress_stmt->error);
    }

    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Assessment - KHULISA</title>
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
        <a href="progress.php">Progress</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container narrow">

    <div class="card">

        <h1>Funding Readiness Assessment</h1>

        <form method="POST">

            <label>1. Monthly Revenue</label>
            <input type="number" name="revenue" required>

            <label>2. Separate business bank account?</label>
            <select name="separate_account" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label>3. Record keeping method</label>
            <select name="record_keeping" required>
                <option value="none">No formal records</option>
                <option value="manual">Manual book</option>
                <option value="spreadsheet">Spreadsheet</option>
                <option value="software">Accounting software</option>
            </select>

            <label>4. Years operating</label>
            <input type="number" name="years_operating" required>

            <label>5. Business plan?</label>
            <select name="business_plan" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label>6. Cashflow forecast?</label>
            <select name="cashflow_forecast" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label>7. Financial statements?</label>
            <select name="financial_statements" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label>8. Credit score</label>
            <input type="number" name="credit_score" min="0" max="850" required>

            <label>9. Tax compliant?</label>
            <select name="tax_compliance" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <label>10. Profitable?</label>
            <select name="profitability" required>
                <option value="yes">Yes</option>
                <option value="no">No</option>
            </select>

            <button type="submit" name="submit_assessment">
                Submit Assessment
            </button>

        </form>

    </div>

</div>

</body>
</html>