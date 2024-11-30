<?php
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Calculate profit/loss score for each user, including num_services and referral count
$stmt = $pdo->query("
    SELECT U.user_id, U.name, U.years_no_claims, U.num_services, 
           COUNT(R.user_id) AS referral_count,
           (U.years_no_claims * 5 + U.num_services * 3 + COUNT(R.user_id) * 2) AS profit_loss_score
    FROM Users U
    LEFT JOIN Users R ON U.user_id = R.referrer_id
    GROUP BY U.user_id
    ORDER BY profit_loss_score DESC
");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vartotoju reitingas</title>
</head>
<body>
    <h2>Vartotojo pelno/nuostoliu vertinimas</h2>
    <table border="1">
        <tr>
            <th>Vieta</th>
            <th>Vardas</th>
            <th>Metai be nuostolių</th>
            <th>Paslaugų kiekis</th>
            <th>Atvesti žmonės</th>
            <th>Pelno/nuostolių įvertis</th>
        </tr>
        <?php foreach ($users as $index => $user): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['years_no_claims']); ?></td>
                <td><?php echo htmlspecialchars($user['num_services']); ?></td>
                <td><?php echo htmlspecialchars($user['referral_count']); ?></td>
                <td><?php echo htmlspecialchars($user['profit_loss_score']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <hr>
    <h4>Pelnas/Nuostolis Balų Skaičiavimas</h4>
<ul>
    <li><strong>Metai be nuostolio:</strong> 5 taškai už kiekvienus metus</li>
    <li><strong>Paslaugų skaičius:</strong> 3 taškai už kiekvieną paslaugą</li>
    <li><strong>Atvestų žmonių skaičius:</strong> 2 taškai už kiekvieną atvestą žmogų</li>
</ul>

</body>
</html>
<?php include 'footer.php'; ?>