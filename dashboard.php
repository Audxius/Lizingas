

<?php
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <title>Dashboard</title>

</head>
<body>
    <h2>IT projektas</h2>
    <p>Uzduotis</p>
    <table class="table table-striped table-dark">
    <tr>
        <td><strong>Temos pavadinimas</strong></td>
        <td>Draudimo bendrovės portalas</td>
    </tr>
    <tr>
        <td><strong>Pagrindinės funkcijos</strong></td>
        <td>
            <ul>
                <li><strong>Lizingo skaičiuotė</strong></li>
                <li>Vartotojams nuolaidų skaičiavimo sistema, pagal nuostolių/pelno darymą. (Be išmokų praleisti vieneri (+2%), dveji (+5%) ar treji metai(+10%))</li>
                <li>Nuolaidų darymas pagal pasirinktų paslaugų kiekį.</li>
                <li>Nuostolių išmokos automatinis skaičiavimas (pagal įkeltas fotonuotraukas) – Parenkant detalių keitimą, detalių kainą, dažymo paslaugas.</li>
            </ul>
        </td>
    </tr>
    <tr>
        <td><strong>Papildomos funkcijos</strong></td>
        <td>
            <ul>
                <li>Papildomos nuolaidos „atvedus“ vartotoją (2% už kiekvieną naują vartotoją).</li>
                <li>Vartotojų reitingavimas pagal pelną/nuostolius.</li>
            </ul>
        </td>
    </tr>
</table>
</body>
</html>
<?php include 'footer.php'; ?>