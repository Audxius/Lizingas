<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $photoPaths = [];
    
    // Define the upload directory
    $uploadDir = 'uploads/';
    
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Insert claim into the Claims table
    $stmt = $pdo->prepare("INSERT INTO Claims (user_id, description) VALUES (?, ?)");
    $stmt->execute([$userId, $description]);
    $claimId = $pdo->lastInsertId();

    // Process each uploaded file
    foreach ($_FILES['photos']['tmp_name'] as $key => $tmpName) {
        $fileError = $_FILES['photos']['error'][$key];
        $originalFileName = basename($_FILES['photos']['name'][$key]);

        // Check for PHP file upload errors and if the file was actually uploaded
        if ($fileError === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
            // Generate a unique filename to prevent overwriting
            $uniqueFileName = uniqid('photo_', true) . "_" . $originalFileName;
            $targetFilePath = $uploadDir . $uniqueFileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($tmpName, $targetFilePath)) {
                // Insert each photo's path into ClaimPhotos table
                $stmt = $pdo->prepare("INSERT INTO ClaimPhotos (claim_id, photo_path) VALUES (?, ?)");
                $stmt->execute([$claimId, $targetFilePath]);
                $photoPaths[] = $targetFilePath;
            } else {
                $message .= "Failed to move file: " . htmlspecialchars($originalFileName) . "<br>";
            }
        } else {
            // Detailed error messages based on the error code
            switch ($fileError) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message .= "File too large: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message .= "File only partially uploaded: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message .= "No file uploaded for: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message .= "Missing temporary folder for file: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message .= "Failed to write file to disk: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message .= "File upload stopped by a PHP extension: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                default:
                    $message .= "Unknown upload error for file: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
            }
        }
    }

    // Reset user's years_no_claims to 0
    $stmt = $pdo->prepare("UPDATE Users SET years_no_claims = 0 WHERE user_id = ?");
    $stmt->execute([$userId]);

    if (empty($message)) {
        $message = "Claim submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit a Claim</title>
</head>
<body>
    <h2>Submit a Claim</h2>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="claim_submission.php" method="post" enctype="multipart/form-data">
        <label for="description">Claim Description:</label><br>
        <textarea name="description" id="description" required></textarea><br><br>
        
        <label for="photos">Upload Photos:</label>
        <input type="file" name="photos[]" multiple="multiple" required><br><br>
        
        <button type="submit">Submit Claim</button>
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
