<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || !$user['is_admin']) {
    die("Access denied: Admins only.");
}

// Update user details if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $targetUserId = intval($_POST['user_id']);
    $yearsNoClaims = intval($_POST['years_no_claims']);
    $numServices = intval($_POST['num_services']);

    // Update years without a claim and number of services for the specified user
    $stmt = $pdo->prepare("UPDATE Users SET years_no_claims = ?, num_services = ? WHERE user_id = ?");
    $stmt->execute([$yearsNoClaims, $numServices, $targetUserId]);
    echo "User details updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Update - User Details</title>
</head>
<body>
    <h2>Admin: Update User Details</h2>
    <form action="admin_update.php" method="post">
        <label for="user_id">User ID:</label>
        <input type="number" name="user_id" required><br><br>

        <label for="years_no_claims">Years Without a Claim:</label>
        <input type="number" name="years_no_claims" required><br><br>

        <label for="num_services">Number of Services:</label>
        <input type="number" name="num_services" required><br><br>

        <button type="submit">Update User</button>
    </form>
</body>
</html>

