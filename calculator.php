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
    echo "<h2>Lizingo skaičiuotuvo rezultatai</h2>";
    echo "Kaina: $" . number_format($itemCost, 2) . "<br>";
    echo "Pradine imoka: $" . number_format($downPayment, 2) . "<br>";
    echo "Terminas: " . $leaseTerm . " mėnesių<br>";
    echo "Metines palukanos: " . number_format($annualInterestRate, 2) . "%<br>";
    echo "Pritaikyta nuolaida: " . number_format($discountPercent, 2) . "%<br>";
    echo "Orginalus menesinis mokestis: $" . number_format($monthlyPayment, 2) . "<br>";
    echo "<strong>Menesinis mokestis po nuolaidos: $" . number_format($discountedMonthlyPayment, 2) . "</strong>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lizingo skaiciuokle</title>
     <style>
        /* Style the form labels */
        label {
            display: block;
            font-size: 20px;
            margin-bottom: 8px;
        }

        /* Style the input fields */
        input[type="number"] {
            width: 100%; /* Make all input fields the same width */
            padding: 8px;
           
            box-sizing: border-box;
        }

        /* Style the form container */
        form {
            max-width: 400px; /* Set a max-width for the form */
            margin: 0 auto; /* Center the form on the page */
        }
    </style>
</head>
<body>
    <h2>Lizingo skaičiuotuvas</h2>
    <form action="calculator.php" method="post">
        <label for="item_cost">Prekės kaina (€):</label>
        <input type="number" name="item_cost" id="item_cost" required><br><br>

        <label for="down_payment">Pradinis įnašas (€):</label>
        <input type="number" name="down_payment" id="down_payment" required><br><br>

        <label for="lease_term">Terminas (mėnesiais):</label>
        <input type="number" name="lease_term" id="lease_term" required><br><br>

        <label for="interest_rate">Palūkanos (metinės %):</label>
        <input type="number" name="interest_rate" id="interest_rate" step="0.01" required><br><br>

        <button type="submit">Skaičiuoti mėnėsinį mokėsti</button>
    </form>
</body>
</html>
<?php include 'footer.php'; ?>