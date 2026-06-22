<?php

require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireLogin();
requireRole("admin");

$message = "";

/* DELETE FUNDER */
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM funders WHERE funder_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: funders.php?deleted=1");
    exit;
}

/* ADD FUNDER */
if (isset($_POST["add_funder"])) {

    $stmt = $conn->prepare("
        INSERT INTO funders
        (
            funder_name,
            funder_type,
            min_loan,
            max_loan,
            min_years,
            min_revenue,
            min_score,
            description,
            website
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssddidiss",
        $_POST["funder_name"],
        $_POST["funder_type"],
        $_POST["min_loan"],
        $_POST["max_loan"],
        $_POST["min_years"],
        $_POST["min_revenue"],
        $_POST["min_score"],
        $_POST["description"],
        $_POST["website"]
    );

    $stmt->execute();

    header("Location: funders.php?added=1");
    exit;
}

/* EDIT FUNDER */
if (isset($_POST["update_funder"])) {

    $stmt = $conn->prepare("
        UPDATE funders
        SET
            funder_name = ?,
            funder_type = ?,
            min_loan = ?,
            max_loan = ?,
            min_years = ?,
            min_revenue = ?,
            min_score = ?,
            description = ?,
            website = ?
        WHERE funder_id = ?
    ");

    $stmt->bind_param(
        "ssddidissi",
        $_POST["funder_name"],
        $_POST["funder_type"],
        $_POST["min_loan"],
        $_POST["max_loan"],
        $_POST["min_years"],
        $_POST["min_revenue"],
        $_POST["min_score"],
        $_POST["description"],
        $_POST["website"],
        $_POST["funder_id"]
    );

    $stmt->execute();

    header("Location: funders.php?updated=1");
    exit;
}

$edit_funder = null;

if (isset($_GET["edit"])) {
    $id = (int) $_GET["edit"];

    $stmt = $conn->prepare("SELECT * FROM funders WHERE funder_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $edit_funder = $stmt->get_result()->fetch_assoc();
}

$funders = $conn->query("SELECT * FROM funders ORDER BY funder_id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Funders - KHULISA</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<nav>
    <h2>KHULISA Admin</h2>

    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="funders.php">Funders</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-title">
        <h1>Manage Funders</h1>
        <p>Add, edit, and delete funder profiles.</p>
    </div>

    <?php if (isset($_GET["added"])): ?>
        <div class="alert success">Funder added successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET["updated"])): ?>
        <div class="alert success">Funder updated successfully.</div>
    <?php endif; ?>

    <?php if (isset($_GET["deleted"])): ?>
        <div class="alert success">Funder deleted successfully.</div>
    <?php endif; ?>

    <div class="card">

        <h2><?php echo $edit_funder ? "Edit Funder" : "Add New Funder"; ?></h2>

        <form method="POST">

            <?php if ($edit_funder): ?>
                <input type="hidden" name="funder_id" value="<?php echo $edit_funder["funder_id"]; ?>">
            <?php endif; ?>

            <label>Funder Name</label>
            <input type="text" name="funder_name" required
                   value="<?php echo clean($edit_funder["funder_name"] ?? ""); ?>">

            <label>Funder Type</label>
            <input type="text" name="funder_type" required
                   value="<?php echo clean($edit_funder["funder_type"] ?? ""); ?>">

            <label>Minimum Loan</label>
            <input type="number" step="0.01" name="min_loan" required
                   value="<?php echo $edit_funder["min_loan"] ?? 0; ?>">

            <label>Maximum Loan</label>
            <input type="number" step="0.01" name="max_loan" required
                   value="<?php echo $edit_funder["max_loan"] ?? 0; ?>">

            <label>Minimum Years Operating</label>
            <input type="number" name="min_years" required
                   value="<?php echo $edit_funder["min_years"] ?? 0; ?>">

            <label>Minimum Revenue</label>
            <input type="number" step="0.01" name="min_revenue" required
                   value="<?php echo $edit_funder["min_revenue"] ?? 0; ?>">

            <label>Minimum Readiness Score</label>
            <input type="number" name="min_score" min="0" max="100" required
                   value="<?php echo $edit_funder["min_score"] ?? 0; ?>">

            <label>Description</label>
            <textarea name="description" rows="4"><?php echo clean($edit_funder["description"] ?? ""); ?></textarea>

            <label>Website</label>
            <input type="text" name="website"
                   value="<?php echo clean($edit_funder["website"] ?? "#"); ?>">

            <?php if ($edit_funder): ?>
                <button name="update_funder">Update Funder</button>
                <a class="btn-link" href="funders.php">Cancel Edit</a>
            <?php else: ?>
                <button name="add_funder">Add Funder</button>
            <?php endif; ?>

        </form>

    </div>

    <div class="card">

        <h2>Existing Funders</h2>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Loan Range</th>
                    <th>Min Score</th>
                    <th>Min Revenue</th>
                    <th>Min Years</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($funder = $funders->fetch_assoc()): ?>

                    <tr>
                        <td><?php echo clean($funder["funder_name"]); ?></td>
                        <td><?php echo clean($funder["funder_type"]); ?></td>
                        <td>
                            R <?php echo number_format($funder["min_loan"], 0); ?>
                            -
                            R <?php echo number_format($funder["max_loan"], 0); ?>
                        </td>
                        <td><?php echo $funder["min_score"]; ?></td>
                        <td>R <?php echo number_format($funder["min_revenue"], 0); ?></td>
                        <td><?php echo $funder["min_years"]; ?></td>
                        <td>
                            <a class="btn-link" href="funders.php?edit=<?php echo $funder["funder_id"]; ?>">
                                Edit
                            </a>

                            <a class="btn-link danger"
                               href="funders.php?delete=<?php echo $funder["funder_id"]; ?>"
                               onclick="return confirm('Delete this funder?');">
                                Delete
                            </a>
                        </td>
                    </tr>

                <?php endwhile; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>