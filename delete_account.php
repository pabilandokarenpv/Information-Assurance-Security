<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in and has 'admin' role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header('Location: account_management.php');
    exit;
} else {
    echo "Error: " . $stmt->error;
}
?>