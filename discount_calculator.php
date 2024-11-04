<?php
session_start();
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
    echo "<h2>Discount Calculation</h2>";
    echo "Years without a claim: $yearsNoClaims <br>";
    echo "Number of services selected: $numServices <br>";
    echo "Number of referrals: $referrals <br>";
    echo "<strong>Total Discount: " . number_format($totalDiscount, 2) . "%</strong>";
} else {
    echo "User not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Discount Calculation</title>
</head>
<body>
    <hr>
    <h4>Claims</h4>
    <ul>
    <li>1 year without a claim: User gets a 2% discount.</li>
    <li>2 years without a claim: User gets a 5% discount.</li>
    <li>3 or more years without a claim: User gets a 10% discount.</li>
</ul>
<h4>Number of Services</h4>
<ul>
    
    <li>If a user selects 2-3 services, they get a 2% additional discount.</li>
    <li>If a user selects more than 3 services, they receive the maximum 5% additional discount.</li>
</ul>
    <h4>Referrals</h4>
    <ul>
    <li>For each referral, the user receives an additional 2% discount.</li>
</ul>
</body>
</html>
