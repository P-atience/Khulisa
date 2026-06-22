<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config.php";

function isLoggedIn() {
    return isset($_SESSION["user_id"]);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit;
    }
}

function requireRole($role) {
    if (!isset($_SESSION["user_type"]) || $_SESSION["user_type"] !== $role) {
        die("Access denied.");
    }
}

function currentUserId() {
    return $_SESSION["user_id"] ?? null;
}

function logoutUser() {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}
?>