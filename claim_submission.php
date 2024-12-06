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
                $message .= "Nepavyko perkelti failo: " . htmlspecialchars($originalFileName) . "<br>";
            }
        } else {
            // Detailed error messages based on the error code
            switch ($fileError) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $message .= "Failas per didelis: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $message .= "Failas dalinai įkeltas: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $message .= "Joks failas neįkeltas: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $message .= "Nera laikino aplankalo failui: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $message .= "Nepavyko rašyti į diską: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $message .= "Failo įkelimas sustabdytas php plėtinio: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
                default:
                    $message .= "Nežinoma įkėlimo klaida: " . htmlspecialchars($originalFileName) . "<br>";
                    break;
            }
        }
    }

    // Reset user's years_no_claims to 0
    $stmt = $pdo->prepare("UPDATE Users SET years_no_claims = 0 WHERE user_id = ?");
    $stmt->execute([$userId]);

    if (empty($message)) {
        $message = "Operacija atlikta sekmingai";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit a Claim</title>
    <style>
    body {
        display: flex;
        justify-content: center;
        min-height: 100vh;
        text-align: center;
        margin: 0;
        font-family: Arial, sans-serif;
    }
    label {
        display: block;
        font-size: 20px;
        margin-bottom: 8px;
    }
    input[type="number"], textarea {
        width: 100%;
        padding: 10px;
        height:200px;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
    form {
        max-width: 400px;
        width: 100%;
        margin: 0 auto;
    }
    button, .file-upload-btn {
        display: inline-block;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
    }
    input[type="file"] {
        display: none;
    }
    .file-upload-container {
        margin-bottom: 20px;
        text-align: center;
    }
    .file-names {
        margin-top: 10px;
        font-size: 14px;
        color: #555;
        word-wrap: break-word;
    }
</style>

</head>
<body>
<h2>Pateik nuostolį</h2>
<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>
<form action="claim_submission.php" method="post" enctype="multipart/form-data">
    <label for="description">Nuostolio aprašymas:</label>
    <textarea name="description" id="description" required></textarea>
    
    <label for="photos">Įkelti nuotraukas:</label>
    <div class="file-upload-container">
        <label class="file-upload-btn" onclick="document.getElementById('photos').click()">Pasirinkti failus</label>
        <input type="file" name="photos[]" id="photos" multiple="multiple" required>
        <div class="file-names" id="fileNames">Neįkeltos jokios nuotraukos</div>
    </div>
    
    <button type="submit">Pateikti nuostolį</button>
</form>

<script>
    const fileInput = document.getElementById('photos');
    const fileNamesDisplay = document.getElementById('fileNames');
    
    fileInput.addEventListener('change', () => {
        const fileNames = Array.from(fileInput.files).map(file => file.name).join(', ');
        fileNamesDisplay.textContent = fileNames || 'Nepasirinkti jokie failai';
    });
</script>
</body>
</html>
<?php include 'footer.php'; ?>

<?php
// Refund logic
$claimApprovedForRefund = false; // Initialize as false

// Logic to determine if the claim is approved
if (isset($_POST['claim_id'])) {
    $claimId = $_POST['claim_id'];
    $stmt = $pdo->prepare("SELECT status FROM Claims WHERE claim_id = ?");
    $stmt->execute([$claimId]);
    $claimStatus = $stmt->fetchColumn();

    $claimApprovedForRefund = ($claimStatus === 'approved'); // Update condition based on status
}

if ($claimApprovedForRefund) {
    $userId = $_POST['user_id'];
    $refundAmount = floatval($_POST['refund_amount']);
    
    $query = "UPDATE Users SET balance = balance + ? WHERE user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$refundAmount, $userId]);

    if ($stmt->rowCount() > 0) {
        echo "€" . number_format($refundAmount, 2) . " buvo pridėta į jūsų balansą.";
    } else {
        echo "Klaida atnaujinant balansą.";
    }
}
?>
