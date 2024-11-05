<!-- header.php -->
<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$isAdmin = $user && $user['is_admin'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Site</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div id="left-sidebar">
            <h3>Navigacija</h3>
            <button onclick="location.href='dashboard.php'" title="IT projekto užduotis">Užduotis</button>
            <button onclick="location.href='calculator.php'" title="Apskaičiuoti lizinga su konkrečiais skaičiais">Lizingo skaičiuotuvas</button>
            <button onclick="location.href='discount_calculator.php'" title="Suskaičiuoti kokia taikoma nuolaida">Nuolaidos skaičiuotuvas</button>
            <button onclick="location.href='claim_submission.php'" title="Pateikti konkretų nuostolio aprašyma su nuotrauka">Pateikti nuostolį</button>
            <button onclick="location.href='user_rankings.php'" title="Reitinguoti vartotojus pagal jų pelno/žalos vertę">Vartotojo reitingavimas</button>
            <button onclick="location.href='profile.php'" title="Pakeisti kiek paslaugų esi užsiprenumeravęs">Profilio redegatorius</button>
            
            <?php if ($isAdmin): ?>
                <button onclick="location.href='admin_dashboard.php'" title="Redaguoti kiek metų vartotojas nepateikė nuostolio ir kiek paslaugų užsiprenumeravęs">Admin sąsaja</button>
                <button onclick="location.href='admin_claims.php'" title="Valdyti nuostolių paraiškas">Nuostolių paraiškos</button>
            <?php endif; ?>

            <button onclick="location.href='logout.php'" title="Atsijungti nuo portalo">Atsijungti</button>
        </div>
        
        <div id="content">
