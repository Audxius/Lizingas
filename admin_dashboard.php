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
$stmt = $pdo->prepare("SELECT role FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    die("Access denied: Admins only.");
}

// Handle form submission to update user data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $targetUserId = intval($_POST['user_id']);
    $yearsNoClaims = intval($_POST['years_no_claims']);
    $numServices = intval($_POST['num_services']);
    $balance = floatval($_POST['balance']);
    $role = $_POST['role'];

    // Update user data in the database
    $stmt = $pdo->prepare("UPDATE Users SET years_no_claims = ?, num_services = ?, balance = ?, role = ? WHERE user_id = ?");
    $stmt->execute([$yearsNoClaims, $numServices, $balance, $role, $targetUserId]);
    echo "Vartotojo $targetUserId duomenys sėkmingai atnaujinti!";
}

// Fetch all users to display in the dashboard
$stmt = $pdo->query("SELECT user_id, name, years_no_claims, num_services, balance, role FROM Users ORDER BY user_id");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tvarkyti naudotojus</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            border: 1px solid #ddd;
        }
        input[type="number"], select, button {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
        }
        button {
            background-color: #3d2486;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #291a61;
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
            <th>Vartotojo rolė</th>
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
                        <select name="role" required>
                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Vartotojas</option>
                            <option value="moderator" <?php echo $user['role'] === 'moderator' ? 'selected' : ''; ?>>Moderatorius</option>
                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administratorius</option>
                        </select>
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
