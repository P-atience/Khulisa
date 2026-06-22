<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "includes/auth.php";
require_once "includes/functions.php";

$error = "";
$success = "";

if (isLoggedIn()) {

    if ($_SESSION["user_type"] === "admin") {
        header("Location: admin/dashboard.php");
        exit;
    }

    if ($_SESSION["user_type"] === "mentor") {
        header("Location: mentor/dashboard.php");
        exit;
    }

    header("Location: dashboard.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

if (isset($_POST["register"])) {

    $full_name = clean($_POST["full_name"]);
    $business_name = clean($_POST["business_name"]);
    $email = clean($_POST["email"]);
    $password = $_POST["password"];

    $check = $conn->prepare(
        "SELECT user_id FROM users WHERE email = ?"
    );

    $check->bind_param("s", $email);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $error = "Email already exists.";

    } else {

        $hashed_password =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );

        $stmt = $conn->prepare("
            INSERT INTO users
            (
                full_name,
                business_name,
                email,
                password,
                user_type
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                'owner'
            )
        ");

        $stmt->bind_param(
            "ssss",
            $full_name,
            $business_name,
            $email,
            $hashed_password
        );

        if ($stmt->execute()) {

            $success =
                "Registration successful. Please login.";

        } else {

            $error =
                "Registration failed.";

        }
    }
}

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if (isset($_POST["login"])) {

    $email = clean($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (
            password_verify(
                $password,
                $user["password"]
            )
        ) {

            $_SESSION["user_id"] =
                $user["user_id"];

            $_SESSION["full_name"] =
                $user["full_name"];

            $_SESSION["user_type"] =
                $user["user_type"];

            if ($user["user_type"] === "admin") {

                header(
                    "Location: admin/dashboard.php"
                );
                exit;
            }

            if ($user["user_type"] === "mentor") {

                header(
                    "Location: mentor/dashboard.php"
                );
                exit;
            }

            header(
                "Location: dashboard.php"
            );
            exit;

        } else {

            $error =
                "Invalid email or password.";

        }

    } else {

        $error =
            "Invalid email or password.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>KHULISA</title>

<link rel="stylesheet"
      href="css/style.css">

</head>

<body>

<div class="auth-page">

    <div class="brand">

        <h1>KHULISA</h1>

        <p>
            SME Funding Readiness Accelerator
        </p>

    </div>

    <?php if (!empty($error)): ?>

        <div class="alert error">
            <?php echo $error; ?>
        </div>

    <?php endif; ?>

    <?php if (!empty($success)): ?>

        <div class="alert success">
            <?php echo $success; ?>
        </div>

    <?php endif; ?>

    <div class="auth-grid">

        <!-- LOGIN -->

        <div class="card">

            <h2>Login</h2>

            <form method="POST">

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required>

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required>

                <button
                    type="submit"
                    name="login">

                    Login

                </button>

            </form>

            <p class="hint">

                Admin Login:<br>
                admin@khulisa.co.za<br>
                admin123

                <br><br>

                Mentor Login:<br>
                mentor@khulisa.co.za<br>
                admin123

            </p>

        </div>

        <!-- REGISTER -->

        <div class="card">

            <h2>Register</h2>

            <form method="POST">

                <input
                    type="text"
                    name="full_name"
                    placeholder="Full Name"
                    required>

                <input
                    type="text"
                    name="business_name"
                    placeholder="Business Name"
                    required>

                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    required>

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required>

                <button
                    type="submit"
                    name="register">

                    Register

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>