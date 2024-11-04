<?php
include 'header.php';
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the discount from the session, default to 0 if not set
$discountPercent = isset($_SESSION['user_discount']) ? $_SESSION['user_discount'] : 0;

// If the form is submitted, perform the calculation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemCost = floatval($_POST['item_cost']);
    $downPayment = floatval($_POST['down_payment']);
    $leaseTerm = intval($_POST['lease_term']);
    $annualInterestRate = floatval($_POST['interest_rate']);

    // Calculate the amount to finance
    $amountToFinance = $itemCost - $downPayment;

    // Convert annual interest rate to monthly
    $monthlyInterestRate = $annualInterestRate / 12 / 100;

    // Monthly payment calculation
    if ($monthlyInterestRate > 0) {
        $monthlyPayment = $amountToFinance * ($monthlyInterestRate * pow(1 + $monthlyInterestRate, $leaseTerm)) / (pow(1 + $monthlyInterestRate, $leaseTerm) - 1);
    } else {
        $monthlyPayment = $amountToFinance / $leaseTerm;
    }

    // Apply the discount to the monthly payment
    $discountAmount = $monthlyPayment * ($discountPercent / 100);
    $discountedMonthlyPayment = $monthlyPayment - $discountAmount;

    // Display the result
    echo "<h2>Leasing Calculation Result</h2>";
    echo "Item Cost: $" . number_format($itemCost, 2) . "<br>";
    echo "Down Payment: $" . number_format($downPayment, 2) . "<br>";
    echo "Lease Term: " . $leaseTerm . " months<br>";
    echo "Annual Interest Rate: " . number_format($annualInterestRate, 2) . "%<br>";
    echo "Discount Applied: " . number_format($discountPercent, 2) . "%<br>";
    echo "Original Monthly Payment: $" . number_format($monthlyPayment, 2) . "<br>";
    echo "<strong>Discounted Monthly Payment: $" . number_format($discountedMonthlyPayment, 2) . "</strong>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leasing Calculator</title>
    <style>
        
        </style>
</head>
<body>
    <h2>Leasing Calculator</h2>
    <form action="calculator.php" method="post">
        <label for="item_cost">Item Cost ($):</label>
        <input type="number" name="item_cost" id="item_cost" required><br><br>

        <label for="down_payment">Down Payment ($):</label>
        <input type="number" name="down_payment" id="down_payment" required><br><br>

        <label for="lease_term">Lease Term (months):</label>
        <input type="number" name="lease_term" id="lease_term" required><br><br>

        <label for="interest_rate">Interest Rate (annual %):</label>
        <input type="number" name="interest_rate" id="interest_rate" step="0.01" required><br><br>

        <button type="submit">Calculate Monthly Payment</button>
    </form>
</body>
</html>
<?php include 'footer.php'; ?>