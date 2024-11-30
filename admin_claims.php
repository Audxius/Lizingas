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

// Check if the user is an admin
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT is_admin FROM Users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user || !$user['is_admin']) {
    die("Access denied: Admins only.");
}

// Function to fetch claims by status
function fetchClaimsByStatus($pdo, $status) {
    $stmt = $pdo->prepare("
        SELECT C.claim_id, C.user_id, U.name, C.description, C.status, C.repair_cost, C.created_at 
        FROM Claims C 
        JOIN Users U ON C.user_id = U.user_id
        WHERE C.status = ?
        ORDER BY C.created_at DESC
    ");
    $stmt->execute([$status]);
    return $stmt->fetchAll();
}

// Fetch claims by status
$pendingClaims = fetchClaimsByStatus($pdo, 'Pending');
$approvedClaims = fetchClaimsByStatus($pdo, 'Approved');
$deniedClaims = fetchClaimsByStatus($pdo, 'Denied');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tvarkyti nuostolius</title>
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
    <h2>Tvarkyti nuostolius</h2>

    <!-- Pending Claims Table -->
    <h3>Neįvertinti nuostoliai</h3>
    <table border="1">
        <tr>
            <th>Nuostolio ID</th>
            <th>Vardas</th>
            <th>Aprašymas</th>
            <th>Pridavimo data</th>
            <th>Veiksmas</th>
        </tr>
        <?php foreach ($pendingClaims as $claim): ?>
            <tr>
                <td><?php echo htmlspecialchars($claim['claim_id']); ?></td>
                <td><?php echo htmlspecialchars($claim['name']); ?></td>
                <td><?php echo htmlspecialchars($claim['description']); ?></td>
                <td><?php echo htmlspecialchars($claim['created_at']); ?></td>
                <td>
                    <form action="claim_details.php" method="get">
                        <input type="hidden" name="claim_id" value="<?php echo htmlspecialchars($claim['claim_id']); ?>">
                        <button type="submit">Įvertinti</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <!-- Approved Claims Table -->
    <h3>Patvirtinti nuostoliai</h3>
    <table border="1">
        <tr>
            <th>Nuostolio ID</th>
            <th>Vardas</th>
            <th>Aprašymas</th>
            <th>Galutinė taisymo kaina</th>
            <th>Pridavimo data</th>
        </tr>
        <?php foreach ($approvedClaims as $claim): ?>
            <tr>
                <td><?php echo htmlspecialchars($claim['claim_id']); ?></td>
                <td><?php echo htmlspecialchars($claim['name']); ?></td>
                <td><?php echo htmlspecialchars($claim['description']); ?></td>
                <td><?php echo "$" . number_format($claim['repair_cost'], 2); ?></td>
                <td><?php echo htmlspecialchars($claim['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Denied Claims Table -->
    <h3>Atmesti nuostoliai</h3>
    <table border="1">
        <tr>
            <th>Nuostolio ID</th>
            <th>Vardas</th>
            <th>Aprašymas</th>
            <th>Pridavimo data</th>
        </tr>
        <?php foreach ($deniedClaims as $claim): ?>
            <tr>
                <td><?php echo htmlspecialchars($claim['claim_id']); ?></td>
                <td><?php echo htmlspecialchars($claim['name']); ?></td>
                <td><?php echo htmlspecialchars($claim['description']); ?></td>
                <td><?php echo htmlspecialchars($claim['created_at']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php include 'footer.php'; ?>
