<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to calculate discount based on no-claim years and number of services
function calculateDiscount($yearsNoClaims, $numServices) {
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

    // Cap the total discount at 20%
    return min($discount, 0.20) * 100; // Return as a percentage
}

// Retrieve user data
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT years_no_claims, num_services FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($user) {
    $yearsNoClaims = $user['years_no_claims'];
    $numServices = $user['num_services'];

    // Calculate the total discount and store it in the session
    $totalDiscount = calculateDiscount($yearsNoClaims, $numServices);
    $_SESSION['user_discount'] = $totalDiscount;

    // Display the discount details
    echo "<h2>Discount Calculation</h2>";
    echo "Years without a claim: $yearsNoClaims <br>";
    echo "Number of services selected: $numServices <br>";
    echo "<strong>Total Discount: " . number_format($totalDiscount, 2) . "%</strong>";
} else {
    echo "User not found.";
}
?>
<html>
    <body>
        <hr>
        <h4>Claims</h4>
    <p>1 year without a claim: User gets a 2% discount.</p>
    <p>2 years without a claim: User gets a 5% discount.</p>
    <p>3 or more years without a claim: User gets a 10% discount.</p>
    <br>
    <h4>Number of services</h4>
    <p>If a user selects 2 services, they get a 2% additional discount.</p>
    <p>If a user selects 4 services, they receive the maximum 5% additional discount.</p>
    <br>
    <h4>Referrals</h4>
    <p>For every referral its a 2% discount</p>
    </body>
</html>