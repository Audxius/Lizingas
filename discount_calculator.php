<?php
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to calculate discount based on no-claim years, number of services, and referrals
function calculateDiscount($yearsNoClaims, $numServices, $referrals) {
    $discountByYears = [1 => 0.02, 2 => 0.05, 3 => 0.10];
    $discount = 0;

    // Apply discount based on years without a claim
    if (array_key_exists($yearsNoClaims, $discountByYears)) {
        $discount += $discountByYears[$yearsNoClaims];
    } elseif ($yearsNoClaims > 3) {
        $discount += $discountByYears[3]; // Max discount for more than 3 years
    }

    // Apply discount based on the number of services
    if ($numServices >= 2 && $numServices <= 3) {
        $discount += 0.02; // 2% discount for selecting 2-3 services
    } elseif ($numServices > 3) {
        $discount += 0.05; // 5% discount for selecting more than 3 services
    }

    // Apply referral discount: 2% for each referral
    $discount += $referrals * 0.02;

    // Cap the total discount at 20%
    return min($discount, 0.20) * 100; // Return as a percentage
}

// Retrieve user data
$userId = $_SESSION['user_id'];

// Fetch years_no_claims and num_services for the current user
$stmt = $pdo->prepare("SELECT years_no_claims, num_services FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Calculate the number of referrals for this user using a subquery
$referralStmt = $pdo->prepare("SELECT COUNT(*) AS referral_count FROM Users WHERE referrer_id = ?");
$referralStmt->execute([$userId]);
$referralCount = $referralStmt->fetchColumn();

if ($user) {
    $yearsNoClaims = $user['years_no_claims'];
    $numServices = $user['num_services'];
    $referrals = $referralCount;

    // Calculate the total discount and store it in the session
    $totalDiscount = calculateDiscount($yearsNoClaims, $numServices, $referrals);
    $_SESSION['user_discount'] = $totalDiscount;

    // Display the discount details
    echo "<h2>Nuolaidos skaičiuotuvas</h2>";
    echo "Metai be nuostolių: $yearsNoClaims <br>";
    echo "Užsiprenumeruotų paslaugų kiekis: $numServices <br>";
    echo "Atvestų žmonių skaičius: $referrals <br>";
    echo "<strong>Galutinė nuolaida: " . number_format($totalDiscount, 2) . "%</strong>";
} else {
    echo "Vartotojas nerastas.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discount Calculation</title>
    
    <style>
hr {
  display: block;
  height: 1px;
  border: 0;
  border-top: 1px solid #white;
  margin: 1em 0;
  padding: 0;
}
        </style>
</head>
<body>
<hr>
<h4>Nuostoliai</h4>
<ul>
    <li>1 metai be nuostolio: Vartotojas gauna 2% nuolaidą.</li>
    <li>2 metai be nuostolio: Vartotojas gauna 5% nuolaidą.</li>
    <li>3 ar daugiau metų be nuostolio: Vartotojas gauna 10% nuolaidą.</li>
</ul>
<h4>Paslaugų Kiekis</h4>
<ul>
    <li>Jei vartotojas pasirenka 2–3 paslaugas, jis gauna papildomą 2% nuolaidą.</li>
    <li>Jei vartotojas pasirenka daugiau nei 3 paslaugas, jis gauna maksimalią 5% papildomą nuolaidą.</li>
</ul>
<h4>Atvesti Žmones</h4>
<ul>
    <li>Už kiekvieną atvestą žmogų vartotojas gauna papildomą 2% nuolaidą.</li>
</ul>

</body>
</html>
<?php include 'footer.php'; ?>