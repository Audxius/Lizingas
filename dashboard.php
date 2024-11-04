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
    <title>Dashboard</title>
</head>
<body>
    <h2>Insurance Company Portal - Dashboard</h2>
    <p>Welcome to the insurance company portal! Please select an option below:</p>

    <ul>
        <li><a href="calculator.php">Leasing Calculator</a> - Calculate monthly leasing payments.</li>
        <li><a href="discount_calculator.php">Discount Calculator</a> - View your discount based on eligibility.</li>
        <li><a href="claim_submission.php">Submit a Claim</a> - Submit a claim with photo uploads.</li>
        <li><a href="user_rankings.php">User Rankings</a> - View the rankings of users based on profit/loss.</li>
        <li><a href="profile.php">Profile editor</a> - Edit user profile</li>
        
        <?php if ($isAdmin): ?>
            <li><a href="admin_dashboard.php">Admin Dashboard</a> - Manage user no-claim years and services.</li>
            <li><a href="admin_claims.php">Approve/Deny claims</a> - Manage user claims.</li>
            
        <?php endif; ?>
    </ul>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
