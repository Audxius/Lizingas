<!-- footer.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Query the current user's profit/loss information
$stmt = $pdo->prepare("
    SELECT U.name AS username, U.years_no_claims, U.num_services, 
           COUNT(R.user_id) AS referral_count,
           (U.years_no_claims * 5 + U.num_services * 3 + COUNT(R.user_id) * 2) AS profit_loss_score
    FROM Users U
    LEFT JOIN Users R ON U.user_id = R.referrer_id
    WHERE U.user_id = ?
    GROUP BY U.user_id
");
$stmt->execute([$userId]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
?>

</div> <!-- end of #content -->

<div id="right-sidebar">
    <!-- Display user information in the right sidebar for the current user -->
    <?php if ($userInfo): ?>
        <p><strong>Vartotojas:</strong> <?= htmlspecialchars($userInfo['username']); ?></p>
        <p><strong>Metai be paraiškų:</strong> <?= htmlspecialchars($userInfo['years_no_claims']); ?></p>
        <p><strong>Paslaugų kiekis:</strong> <?= htmlspecialchars($userInfo['num_services']); ?></p>
        <p><strong>Nukreiptų žmonių kiekis:</strong> <?= htmlspecialchars($userInfo['referral_count']); ?></p>
        <p><strong>Pelno/žalos vertė:</strong> <?= htmlspecialchars($userInfo['profit_loss_score']); ?></p>
    <?php else: ?>
        <p>No additional information available.</p>
    <?php endif; ?>
</div> <!-- end of #right-sidebar -->

</div> <!-- end of .container -->

<footer>
    <p>Audrius Barauskis IFB-2 T120B145 Kompiuterių tinklai ir internetinės technologijos</p>
</footer>
</body>
</html>
