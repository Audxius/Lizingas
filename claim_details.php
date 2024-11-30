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

// Retrieve the claim ID from the URL
$claimId = $_GET['claim_id'] ?? null;

// Fetch claim details
$stmt = $pdo->prepare("SELECT * FROM Claims WHERE claim_id = ?");
$stmt->execute([$claimId]);
$claim = $stmt->fetch();

if (!$claim) {
    die("Claim not found.");
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $parts = $_POST['parts'];

    // Update each part's repair cost
    foreach ($parts as $partName => $repairCost) {
        $stmt = $pdo->prepare("REPLACE INTO ClaimParts (claim_id, part_name, repair_cost) VALUES (?, ?, ?)");
        $stmt->execute([$claimId, $partName, $repairCost]);
    }

    // Calculate total repair cost
    $stmt = $pdo->prepare("SELECT SUM(repair_cost) FROM ClaimParts WHERE claim_id = ?");
    $stmt->execute([$claimId]);
    $totalRepairCost = $stmt->fetchColumn();

    // Update claim status and total repair cost
    $stmt = $pdo->prepare("UPDATE Claims SET status = ?, repair_cost = ? WHERE claim_id = ?");
    $stmt->execute([$status, $totalRepairCost, $claimId]);

    echo "Claim ID $claimId has been updated!";
    header("Location: admin_claims.php");
    exit();
}

// Fetch existing parts and repair costs for this claim
$partStmt = $pdo->prepare("SELECT part_name, repair_cost FROM ClaimParts WHERE claim_id = ?");
$partStmt->execute([$claimId]);
$existingParts = $partStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch images associated with the claim
$photoStmt = $pdo->prepare("SELECT photo_path FROM ClaimPhotos WHERE claim_id = ?");
$photoStmt->execute([$claimId]);
$photos = $photoStmt->fetchAll(PDO::FETCH_COLUMN);

// Define parts to be evaluated
$parts = ['Langai', 'Šviestuvai', 'Sparnai', 'Durys', 'Bamperis', 'Veidrodėliai'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Claim Details</title>
    <style>/* Style the form labels */
label {
    display: block;
    font-size: 20px;
    margin-bottom: 8px;
}

/* Style the input fields and select dropdown */
input[type="number"],
select {
    width: 100%;
    padding: 8px;
    
    box-sizing: border-box;
}

/* Center and style the button */
button {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    background-color: #3d2486; /* Button color */
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    box-sizing: border-box;
}

button:hover {
    background-color: #291a61; /* Darker shade on hover */
}

/* Style the form container */
form {
    max-width: 400px; /* Set a max-width for the form */
    margin: 0 auto;
}

    </style>
</head>
<body>
    <h2>Nuostolio <?php echo htmlspecialchars($claimId); ?> detalės</h2>
    <p><strong>Aprašymas:</strong> <?php echo htmlspecialchars($claim['description']); ?></p>

    <!-- Display uploaded images -->
    <h3>Pateikta nuotrauka</h3>
    <?php if (!empty($photos)): ?>
        <?php foreach ($photos as $photo): ?>
            <a href="<?php echo htmlspecialchars($photo); ?>" target="_blank">
                <img src="<?php echo htmlspecialchars($photo); ?>" alt="Claim Photo" width="150" style="margin: 10px;">
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Jokios nuotraukos nebuvo įkeltos šiam nuostoliui.</p>
    <?php endif; ?>

    <h3>Detalių keitimo kaina</h3>
    <form method="post">
        <?php foreach ($parts as $part): ?>
            <label for="<?php echo $part; ?>"><?php echo $part; ?>:</label>
            <input type="number" name="parts[<?php echo $part; ?>]" value="<?php echo htmlspecialchars($existingParts[$part] ?? 0); ?>" step="0.01"><br>
        <?php endforeach; ?>

        <h3>Status</h3>
        <select name="status">
            <option value="Pending" <?php echo $claim['status'] == 'Pending' ? 'selected' : ''; ?>>Neįvertintas</option>
            <option value="Approved" <?php echo $claim['status'] == 'Approved' ? 'selected' : ''; ?>>Patvirtintas</option>
            <option value="Denied" <?php echo $claim['status'] == 'Denied' ? 'selected' : ''; ?>>Atmestas</option>
        </select><br><br>

        <button type="submit">Priduoti</button>
    </form>

    <p><a href="admin_claims.php">Atgal į nuostolių sąrašą</a></p>
</body>
</html>
<?php include 'footer.php'; ?>