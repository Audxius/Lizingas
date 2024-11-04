<?php
session_start();
require_once 'db.php';

// Fetch all existing users to populate the referrer dropdown
$stmt = $pdo->query("SELECT user_id, name FROM Users ORDER BY name ASC");
$existingUsers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $referrerId = !empty($_POST['referrer_id']) ? intval($_POST['referrer_id']) : null; // Set to null if no referral is selected

    // Insert new user with optional referrer_id
    $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, referrer_id) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $password, $referrerId])) {
        echo "Registration successful! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="post">
        <label for="name">Name:</label>
        <input type="text" name="name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="referrer_id">Referrer (optional):</label>
        <select name="referrer_id" id="referrer_id">
            <option value="">-- No Referrer --</option>
            <?php foreach ($existingUsers as $user): ?>
                <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
            <?php endforeach; ?>
        </select><br>
        <small>If someone referred you, select their name from the list.</small><br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
