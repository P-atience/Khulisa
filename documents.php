<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/includes/auth.php";
require_once __DIR__ . "/includes/functions.php";

requireLogin();

$user_id = currentUserId();

$message = "";
$error = "";

$upload_dir = __DIR__ . "/uploads/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/* DELETE DOCUMENT */
if (isset($_GET["delete"])) {

    $doc_id = (int) $_GET["delete"];

    $stmt = $conn->prepare("
        SELECT *
        FROM documents
        WHERE doc_id = ? AND user_id = ?
    ");

    $stmt->bind_param("ii", $doc_id, $user_id);
    $stmt->execute();

    $doc = $stmt->get_result()->fetch_assoc();

    if ($doc) {

        $full_path = __DIR__ . "/" . $doc["file_path"];

        if (file_exists($full_path)) {
            unlink($full_path);
        }

        $delete = $conn->prepare("
            DELETE FROM documents
            WHERE doc_id = ? AND user_id = ?
        ");

        $delete->bind_param("ii", $doc_id, $user_id);
        $delete->execute();

        header("Location: documents.php?deleted=1");
        exit;
    }
}

/* UPLOAD DOCUMENT */
if (isset($_POST["upload_document"])) {

    $document_type = clean($_POST["document_type"]);

    if (!isset($_FILES["document"]) || $_FILES["document"]["error"] !== 0) {

        $error = "Please select a valid file.";

    } else {

        $file = $_FILES["document"];

        $allowed_extensions = ["pdf", "jpg", "jpeg", "png"];
        $max_size = 5 * 1024 * 1024;

        $original_name = basename($file["name"]);
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_extensions)) {

            $error = "Only PDF, JPG, JPEG and PNG files are allowed.";

        } elseif ($file["size"] > $max_size) {

            $error = "File is too large. Maximum size is 5MB.";

        } else {

            $safe_name =
                "user_" . $user_id . "_" .
                time() . "_" .
                preg_replace("/[^a-zA-Z0-9._-]/", "_", $original_name);

            $target_path = $upload_dir . $safe_name;
            $db_path = "uploads/" . $safe_name;

            if (move_uploaded_file($file["tmp_name"], $target_path)) {

                $stmt = $conn->prepare("
                    INSERT INTO documents
                    (user_id, document_type, file_name, file_path)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->bind_param(
                    "isss",
                    $user_id,
                    $document_type,
                    $original_name,
                    $db_path
                );

                $stmt->execute();

                header("Location: documents.php?uploaded=1");
                exit;

            } else {

                $error = "Upload failed. Please check folder permissions.";
            }
        }
    }
}

/* FETCH USER DOCUMENTS */
$stmt = $conn->prepare("
    SELECT *
    FROM documents
    WHERE user_id = ?
    ORDER BY upload_date DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$documents = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Documents - KHULISA</title>
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
        <h1>Document Manager</h1>
        <p>Upload and manage documents required for funding applications.</p>
    </div>

    <?php if (isset($_GET["uploaded"])): ?>
        <div class="alert success">
            Document uploaded successfully.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert success">
            Document deleted successfully.
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <h2>Upload Document</h2>

        <form method="POST" enctype="multipart/form-data">

            <label>Document Type</label>

            <select name="document_type" required>
                <option value="Bank Statement">Bank Statement</option>
                <option value="Tax Certificate">Tax Certificate</option>
                <option value="Financial Statement">Financial Statement</option>
                <option value="Business Registration">Business Registration</option>
                <option value="Other">Other</option>
            </select>

            <label>Select File</label>

            <input
                type="file"
                name="document"
                accept=".pdf,.jpg,.jpeg,.png"
                required>

            <button name="upload_document">
                Upload Document
            </button>

        </form>

        <p class="hint">
            Allowed file types: PDF, JPG, JPEG, PNG. Maximum size: 5MB.
        </p>

    </div>

    <div class="card">

        <h2>Your Uploaded Documents</h2>

        <?php if ($documents->num_rows === 0): ?>

            <p>No documents uploaded yet.</p>

        <?php else: ?>

            <div class="document-grid">

                <?php while ($doc = $documents->fetch_assoc()): ?>

                    <div class="document-card">

                        <h3>
                            <?php echo clean($doc["document_type"]); ?>
                        </h3>

                        <p>
                            <?php echo clean($doc["file_name"]); ?>
                        </p>

                        <small>
                            Uploaded:
                            <?php echo $doc["upload_date"]; ?>
                        </small>

                        <div class="document-actions">

                            <a
                                class="btn-link"
                                href="<?php echo clean($doc["file_path"]); ?>"
                                target="_blank">
                                View
                            </a>

                            <a
                                class="btn-link danger"
                                href="documents.php?delete=<?php echo $doc["doc_id"]; ?>"
                                onclick="return confirm('Delete this document?');">
                                Delete
                            </a>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>