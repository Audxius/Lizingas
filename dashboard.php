

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

// Check if the user is an admin or moderator
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || ($user['role'] !== 'admin' && $user['role'] !== 'moderator')) {
    die("Access denied: Admins and Moderators only.");
}


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suvestine</title>

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
    <tr>
    <td><strong>Pataisymai</strong></td>
        <td>
            <ul>
                <li>Trys registruotų naudotojų tipai</li>
                <li>Visi sistemos užrašai LT kalba</li>
                <li>Du tipai lizingo. Prekės ir automobiliai</li>
                <li>-Nepavyko įkelti nuotraukų</li>
                <li>-Kai patvirtinamas lizingas - nuskaičiuojama pradinio įnašo pinigų suma iš profilio, kai daromas nuostolių grąžinimas - įkrenta pinigai į profilį. </li>
            </ul>
        </td>
</tr>
</table>
</body>
</html>
<?php include 'footer.php'; ?>