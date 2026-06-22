<?php

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

$months = [
    "Month 1",
    "Month 2",
    "Month 3",
    "Month 4",
    "Month 5",
    "Month 6"
];

$message = "";

if (isset($_POST["save_cashflow"])) {

    $delete = $conn->prepare("DELETE FROM cashflows WHERE user_id = ?");
    $delete->bind_param("i", $user_id);
    $delete->execute();

    $stmt = $conn->prepare("
        INSERT INTO cashflows 
        (user_id, month_name, revenue, expenses, profit)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($months as $index => $month) {

        $revenue = (float) $_POST["revenue"][$index];
        $expenses = (float) $_POST["expenses"][$index];
        $profit = $revenue - $expenses;

        $stmt->bind_param(
            "isddd",
            $user_id,
            $month,
            $revenue,
            $expenses,
            $profit
        );

        $stmt->execute();
    }

    header("Location: cashflow.php?saved=1");
    exit;
}

$saved = [];

$result = $conn->prepare("
    SELECT * FROM cashflows 
    WHERE user_id = ?
    ORDER BY cashflow_id ASC
");

$result->bind_param("i", $user_id);
$result->execute();

$rows = $result->get_result();

while ($row = $rows->fetch_assoc()) {
    $saved[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cashflow Builder - KHULISA</title>
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

    <div class="card">

        <h1>Cashflow Builder</h1>

        <p>
            Enter your expected revenue and expenses for the next 6 months.
        </p>

        <?php if (isset($_GET["saved"])): ?>
            <div class="alert success">
                Cashflow saved successfully.
            </div>
        <?php endif; ?>

        <form method="POST">

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Revenue</th>
                        <th>Expenses</th>
                        <th>Estimated Profit</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($months as $index => $month): ?>

                        <?php
                            $revenue_value = $saved[$index]["revenue"] ?? 0;
                            $expenses_value = $saved[$index]["expenses"] ?? 0;
                            $profit_value = $saved[$index]["profit"] ?? 0;
                        ?>

                        <tr>
                            <td>
                                <?php echo $month; ?>
                            </td>

                            <td>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="revenue[]" 
                                    value="<?php echo $revenue_value; ?>"
                                    required>
                            </td>

                            <td>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    name="expenses[]" 
                                    value="<?php echo $expenses_value; ?>"
                                    required>
                            </td>

                            <td>
                                R <?php echo number_format($profit_value, 2); ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

            <button name="save_cashflow">
                Save Cashflow
            </button>

        </form>

    </div>

</div>

</body>
</html>