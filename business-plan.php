<?php

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

$message = "";

$check = $conn->prepare("
    SELECT *
    FROM business_plans
    WHERE user_id = ?
");

$check->bind_param("i", $user_id);
$check->execute();

$plan = $check->get_result()->fetch_assoc();

if(isset($_POST["save_plan"])){

    $executive_summary = $_POST["executive_summary"];
    $company_description = $_POST["company_description"];
    $products_services = $_POST["products_services"];
    $market_analysis = $_POST["market_analysis"];
    $marketing_strategy = $_POST["marketing_strategy"];
    $financial_plan = $_POST["financial_plan"];

    if($plan){

        $stmt = $conn->prepare("
            UPDATE business_plans
            SET
                executive_summary=?,
                company_description=?,
                products_services=?,
                market_analysis=?,
                marketing_strategy=?,
                financial_plan=?
            WHERE user_id=?
        ");

        $stmt->bind_param(
            "ssssssi",
            $executive_summary,
            $company_description,
            $products_services,
            $market_analysis,
            $marketing_strategy,
            $financial_plan,
            $user_id
        );

    } else {

        $stmt = $conn->prepare("
            INSERT INTO business_plans
            (
                user_id,
                executive_summary,
                company_description,
                products_services,
                market_analysis,
                marketing_strategy,
                financial_plan
            )
            VALUES (?,?,?,?,?,?,?)
        ");

        $stmt->bind_param(
            "issssss",
            $user_id,
            $executive_summary,
            $company_description,
            $products_services,
            $market_analysis,
            $marketing_strategy,
            $financial_plan
        );
    }

    $stmt->execute();

    $message = "Business Plan Saved Successfully";

    header("Location: business-plan.php");
    exit;
}

$check->execute();
$plan = $check->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Business Plan Builder</title>
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
        <a href="report.php">Report</a>
        <a href="documents.php">Documents</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="card">

        <h1>Business Plan Builder</h1>

        <p>
            Complete each section to build your funding-ready business plan.
        </p>

        <form method="POST">

            <label>Executive Summary</label>

            <textarea
                name="executive_summary"
                rows="5"><?php echo $plan["executive_summary"] ?? ""; ?></textarea>

            <label>Company Description</label>

            <textarea
                name="company_description"
                rows="5"><?php echo $plan["company_description"] ?? ""; ?></textarea>

            <label>Products & Services</label>

            <textarea
                name="products_services"
                rows="5"><?php echo $plan["products_services"] ?? ""; ?></textarea>

            <label>Market Analysis</label>

            <textarea
                name="market_analysis"
                rows="5"><?php echo $plan["market_analysis"] ?? ""; ?></textarea>

            <label>Marketing Strategy</label>

            <textarea
                name="marketing_strategy"
                rows="5"><?php echo $plan["marketing_strategy"] ?? ""; ?></textarea>

            <label>Financial Plan</label>

            <textarea
                name="financial_plan"
                rows="5"><?php echo $plan["financial_plan"] ?? ""; ?></textarea>

            <button name="save_plan">
                Save Business Plan
            </button>

        </form>

    </div>

</div>

</body>
</html>