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

// Initialize variables to store form data
$leasingType = $itemCost = $downPayment = $leaseTerm = $annualInterestRate = $carAge = $carMileage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leasingType = $_POST['leasing_type'];
    $itemCost = floatval($_POST['item_cost']);
    $downPayment = floatval($_POST['down_payment']);
    $leaseTerm = intval($_POST['lease_term']);
    $annualInterestRate = floatval($_POST['interest_rate']);
    $carAge = isset($_POST['car_age']) ? intval($_POST['car_age']) : '';
    $carMileage = isset($_POST['car_mileage']) ? intval($_POST['car_mileage']) : '';

    // Validation for leasing type
    if ($leasingType === 'automobilis') {
        // Car-specific rules
        if ($leaseTerm > 84) {
            echo "Automobilio lizingo terminas negali viršyti 7 metų (84 mėnesių).<br>";
        } elseif ($carAge + ($leaseTerm / 12) > 10) {
            echo "Automobilis negali būti senesnis nei 10 metų, kai bus įmokėta paskutinė įmoka.<br>";
        } elseif ($carMileage > 50000) {
            echo "Automobilis negali būti nuvažiavęs daugiau nei 50 000 km.<br>";
        } else {
            $valid = true;
        }
    } elseif ($leasingType === 'preke') {
        // Item-specific rules
        if ($leaseTerm > 36) {
            echo "Prekės lizingo terminas negali viršyti 3 metų (36 mėnesių).<br>";
        } else {
            $valid = true;
        }
    } else {
        echo "Netinkamas lizingo tipas.<br>";
    }

    if (isset($valid) && $valid) {
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lizingo skaiciuokle</title>
    <style>
        label {
            display: block;
            font-size: 20px;
            margin-bottom: 8px;
        }
        input[type="number"], select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h2>Lizingo skaičiuotuvas</h2>
    <form action="calculator.php" method="post">
        <label for="leasing_type">Pasirinkite lizingo tipą:</label>
        <select name="leasing_type" id="leasing_type" required>
            <option value="preke" <?= $leasingType === 'preke' ? 'selected' : '' ?>>Prekės</option>
            <option value="automobilis" <?= $leasingType === 'automobilis' ? 'selected' : '' ?>>Automobiliai</option>
        </select><br><br>

        <label for="item_cost">Prekės/Automobilio kaina (€):</label>
        <input type="number" name="item_cost" id="item_cost" value="<?= htmlspecialchars($itemCost) ?>" required><br><br>

        <label for="down_payment">Pradinis įnašas (€):</label>
        <input type="number" name="down_payment" id="down_payment" value="<?= htmlspecialchars($downPayment) ?>" required><br><br>

        <label for="lease_term">Terminas (mėnesiais):</label>
        <input type="number" name="lease_term" id="lease_term" value="<?= htmlspecialchars($leaseTerm) ?>" required><br><br>

        <label for="interest_rate">Palūkanos (metinės %):</label>
        <input type="number" name="interest_rate" id="interest_rate" step="0.01" value="<?= htmlspecialchars($annualInterestRate) ?>" required><br><br>

        <div id="car_specific_fields" style="<?= $leasingType === 'automobilis' ? '' : 'display: none;' ?>">
            <label for="car_age">Automobilio amžius (metais):</label>
            <input type="number" name="car_age" id="car_age" value="<?= htmlspecialchars($carAge) ?>"><br><br>

            <label for="car_mileage">Automobilio rida (km):</label>
            <input type="number" name="car_mileage" id="car_mileage" value="<?= htmlspecialchars($carMileage) ?>"><br><br>
        </div>

        <button type="submit">Skaičiuoti mėnėsinį mokėsti</button>
    </form>

    <script>
        const leasingTypeSelect = document.getElementById('leasing_type');
        const carSpecificFields = document.getElementById('car_specific_fields');

        leasingTypeSelect.addEventListener('change', function () {
            if (leasingTypeSelect.value === 'automobilis') {
                carSpecificFields.style.display = 'block';
            } else {
                carSpecificFields.style.display = 'none';
            }
        });
    </script>
</body>
</html>
<?php include 'footer.php'; ?>
