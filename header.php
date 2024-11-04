<!-- header.php -->
<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$isAdmin = $user && $user['is_admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div id="left-sidebar">
            <h3>Dashboard Navigation</h3>
            <button onclick="location.href='calculator.php'" title="Calculate monthly leasing payments.">Leasing Calculator</button>
            <button onclick="location.href='discount_calculator.php'" title="View your discount based on eligibility.">Discount Calculator</button>
            <button onclick="location.href='claim_submission.php'" title="Submit a claim with photo uploads.">Submit a Claim</button>
            <button onclick="location.href='user_rankings.php'" title="View the rankings of users based on profit/loss.">User Rankings</button>
            <button onclick="location.href='profile.php'" title="Edit your user profile">Profile Editor</button>
            
            <?php if ($isAdmin): ?>
                <button onclick="location.href='admin_dashboard.php'" title=" Manage user no-claim years and services.">Admin Dashboard</button>
                <button onclick="location.href='admin_claims.php'" title="Manage user claims.">Manage Claims</button>
            <?php endif; ?>

            <button onclick="location.href='logout.php'" title="Logout from the portal">Logout</button>
        </div>
        
        <div id="content">
