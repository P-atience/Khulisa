<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireLogin();
requireRole("mentor");

$mentor_id = currentUserId();
$client_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$check = $conn->prepare("
    SELECT *
    FROM mentor_clients
    WHERE mentor_id = ? AND client_id = ?
");

$check->bind_param("ii", $mentor_id, $client_id);
$check->execute();

if ($check->get_result()->num_rows === 0) {
    die("Client not assigned to this mentor.");
}

if (isset($_POST["add_comment"])) {
    $comment = clean($_POST["comment"]);

    $stmt = $conn->prepare("
        INSERT INTO mentor_comments
        (mentor_id, client_id, comment)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("iis", $mentor_id, $client_id, $comment);
    $stmt->execute();

    header("Location: client.php?id=" . $client_id);
    exit;
}

$client_stmt = $conn->prepare("
    SELECT *
    FROM users
    WHERE user_id = ?
");

$client_stmt->bind_param("i", $client_id);
$client_stmt->execute();
$client = $client_stmt->get_result()->fetch_assoc();

$score_stmt = $conn->prepare("
    SELECT *
    FROM scores
    WHERE user_id = ?
    ORDER BY score_id DESC
    LIMIT 1
");

$score_stmt->bind_param("i", $client_id);
$score_stmt->execute();
$score = $score_stmt->get_result()->fetch_assoc();

$gap_stmt = $conn->prepare("
    SELECT *
    FROM gaps
    WHERE user_id = ? AND status = 'Open'
");

$gap_stmt->bind_param("i", $client_id);
$gap_stmt->execute();
$gaps = $gap_stmt->get_result();

$comments = $conn->prepare("
    SELECT *
    FROM mentor_comments
    WHERE mentor_id = ? AND client_id = ?
    ORDER BY created_at DESC
");

$comments->bind_param("ii", $mentor_id, $client_id);
$comments->execute();
$comment_result = $comments->get_result();

$total_score = $score["total_score"] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Client View - KHULISA</title>
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
        <h1><?php echo clean($client["business_name"]); ?></h1>
        <p>Owner: <?php echo clean($client["full_name"]); ?></p>
    </div>

    <div class="dashboard-grid">
        <div class="card center">
            <div class="score-circle">
                <?php echo $total_score; ?>
            </div>
            <h3>Readiness Score</h3>
        </div>

        <div class="card">
            <h3>Client Details</h3>
            <p><strong>Email:</strong> <?php echo clean($client["email"]); ?></p>
            <p><strong>Role:</strong> <?php echo clean($client["user_type"]); ?></p>
            <p><strong>Joined:</strong> <?php echo $client["created_at"]; ?></p>
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
            <p>No open gaps.</p>
        <?php else: ?>
            <?php while ($gap = $gaps->fetch_assoc()): ?>
                <div class="gap <?php echo strtolower($gap["severity"]); ?>">
                    <strong><?php echo $gap["severity"]; ?>:</strong>
                    <?php echo clean($gap["description"]); ?>
                    <small><?php echo clean($gap["category"]); ?></small>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Add Mentor Feedback</h3>

        <form method="POST">
            <textarea name="comment" rows="5" required></textarea>

            <button name="add_comment">
                Save Comment
            </button>
        </form>
    </div>

    <div class="card">
        <h3>Previous Comments</h3>

        <?php if ($comment_result->num_rows === 0): ?>
            <p>No comments yet.</p>
        <?php else: ?>
            <?php while ($comment = $comment_result->fetch_assoc()): ?>
                <div class="document-card">
                    <p><?php echo clean($comment["comment"]); ?></p>
                    <small><?php echo $comment["created_at"]; ?></small>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

</div>

</body>
</html>