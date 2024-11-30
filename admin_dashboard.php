<?php
include 'header.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the logged-in user is an admin
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || !$user['is_admin']) {
    die("Access denied: Admins only.");
}

// Handle form submission to update user data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $targetUserId = intval($_POST['user_id']);
    $yearsNoClaims = intval($_POST['years_no_claims']);
    $numServices = intval($_POST['num_services']);
    $balance = floatval($_POST['balance']);

    // Update user data in the database
    $stmt = $pdo->prepare("UPDATE Users SET years_no_claims = ?, num_services = ?, balance = ? WHERE user_id = ?");
    $stmt->execute([$yearsNoClaims, $numServices, $balance, $targetUserId]);
    echo "Vartotojo $targetUserId duomenys sėkmingai atnaujinti!";
}

// Fetch all users to display in the dashboard
$stmt = $pdo->query("SELECT user_id, name, years_no_claims, num_services, balance FROM Users ORDER BY user_id");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tvarkyti naudotojus</title>
    <style>
        /* Style the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            text-align: left;
            border: 1px solid #ddd;
        }

        /* Make input fields and buttons fill their cells */
        input[type="number"], button {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
        }

        /* Button styling */
        button {
            background-color: #3d2486; /* Custom color */
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #291a61; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <h2>Admin sąsaja</h2>
    <table border="1">
        <tr>
            <th>Vartotojo ID</th>
            <th>Vardas</th>
            <th>Metai be nuostolių</th>
            <th>Užsiprenumeruotų paslaugų skaičius</th>
            <th>Turima suma (€)</th>
            <th>Atnaujinti</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <form method="post" action="admin_dashboard.php">
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td>
                        <input type="number" name="years_no_claims" value="<?php echo htmlspecialchars($user['years_no_claims']); ?>" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="num_services" value="<?php echo htmlspecialchars($user['num_services']); ?>" min="0" required>
                    </td>
                    <td>
                        <input type="number" name="balance" value="<?php echo htmlspecialchars($user['balance']); ?>" step="0.01" required>
                    </td>
                    <td>
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                        <button type="submit" name="update_user">Atnaujinti</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php include 'footer.php'; ?>
