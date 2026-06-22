<?php

function clean($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, "UTF-8");
}

function calculateScores($data) {
    $total = 0;

    if ($data["revenue"] >= 100000) $total += 25;
    elseif ($data["revenue"] >= 50000) $total += 20;
    elseif ($data["revenue"] >= 10000) $total += 10;

    if ($data["separate_account"] === "yes") $total += 15;

    if ($data["record_keeping"] === "software") $total += 15;
    elseif ($data["record_keeping"] === "spreadsheet") $total += 10;
    elseif ($data["record_keeping"] === "manual") $total += 5;

    if ($data["years_operating"] >= 3) $total += 10;
    elseif ($data["years_operating"] >= 1) $total += 5;

    if ($data["business_plan"] === "yes") $total += 10;
    if ($data["cashflow_forecast"] === "yes") $total += 10;
    if ($data["financial_statements"] === "yes") $total += 5;
    if ($data["credit_score"] >= 650) $total += 5;
    if ($data["tax_compliance"] === "yes") $total += 5;

    $financial = 0;
    if ($data["revenue"] >= 100000) $financial += 40;
    elseif ($data["revenue"] >= 50000) $financial += 30;
    elseif ($data["revenue"] >= 10000) $financial += 15;
    if ($data["separate_account"] === "yes") $financial += 30;
    if ($data["record_keeping"] === "software") $financial += 30;
    elseif ($data["record_keeping"] === "spreadsheet") $financial += 20;
    elseif ($data["record_keeping"] === "manual") $financial += 10;

    $documentation = 0;
    if ($data["business_plan"] === "yes") $documentation += 35;
    if ($data["cashflow_forecast"] === "yes") $documentation += 35;
    if ($data["financial_statements"] === "yes") $documentation += 30;

    $credit = ($data["credit_score"] >= 650) ? 100 : (($data["credit_score"] >= 550) ? 60 : 30);

    $governance = 0;
    if ($data["tax_compliance"] === "yes") $governance += 60;
    if ($data["years_operating"] >= 3) $governance += 40;
    elseif ($data["years_operating"] >= 1) $governance += 25;

    $viability = ($data["profitability"] === "yes") ? 100 : 40;

    return [
        "total" => min($total, 100),
        "financial" => min($financial, 100),
        "documentation" => min($documentation, 100),
        "credit" => min($credit, 100),
        "governance" => min($governance, 100),
        "viability" => min($viability, 100)
    ];
}

function createGaps($conn, $user_id, $assessment_id, $data) {
    $gaps = [];

    if ($data["cashflow_forecast"] === "no") {
        $gaps[] = ["Documentation", "Missing cash flow forecast", "High"];
    }

    if ($data["business_plan"] === "no") {
        $gaps[] = ["Documentation", "Missing business plan", "Medium"];
    }

    if ($data["separate_account"] === "no") {
        $gaps[] = ["Financial", "No separate business account", "Low"];
    }

    if ($data["tax_compliance"] === "no") {
        $gaps[] = ["Governance", "Tax compliance needed", "High"];
    }

    if ($data["financial_statements"] === "no") {
        $gaps[] = ["Documentation", "Financial statements needed", "Medium"];
    }

    $stmt = $conn->prepare("
        INSERT INTO gaps (user_id, assessment_id, category, description, severity)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($gaps as $gap) {
        $stmt->bind_param("iisss", $user_id, $assessment_id, $gap[0], $gap[1], $gap[2]);
        $stmt->execute();
    }
}
?>