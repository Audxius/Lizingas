<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Calculate profit/loss score for each user, including num_services
$stmt = $pdo->query("
    SELECT user_id, name, years_no_claims, num_services, 
           (years_no_claims * 5) AS profit_loss_score
    FROM Users
    ORDER BY profit_loss_score DESC
");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Rankings</title>
</head>
<body>
    <h2>User Profit/Loss Rankings</h2>
    <table border="1">
        <tr>
            <th>Rank</th>
            <th>User Name</th>
            <th>Years No Claims</th>
            <th>Number of Services</th>
            <th>Profit/Loss Score</th>
        </tr>
        <?php foreach ($users as $index => $user): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['years_no_claims']); ?></td>
                <td><?php echo htmlspecialchars($user['num_services']); ?></td>
                <td><?php echo htmlspecialchars($user['profit_loss_score']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
