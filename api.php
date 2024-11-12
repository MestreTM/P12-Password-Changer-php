<?php
header('Content-Type: text/html'); // Sets the content type to text/html

// Cleans up files older than 25 minutes in the specified directory
cleanOldFiles('changed_certificates/', 25);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

// Validates the request method; only POST requests are accepted
if ($requestMethod !== 'POST') {
    echo "<pre>";
    echo json_encode(["error" => "Invalid request method. Use POST."]);
    echo "</pre>";
    http_response_code(405);
    exit;
}

$currentPassword = $_POST['currentPassword'] ?? null;
$newPassword = $_POST['newPassword'] ?? null;

// Checks if the certificate file has been uploaded
if (!isset($_FILES['p12File'])) {
    echo "<pre>";
    echo json_encode(["error" => "File not uploaded."]);
    echo "</pre>";
    http_response_code(400);
    exit;
}

$file = $_FILES['p12File'];
// Verifies if there was an error in file upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo "<pre>";
    echo json_encode(["error" => "Error uploading file."]);
    echo "</pre>";
    http_response_code(500);
    exit;
}

$p12Path = $file['tmp_name'];

// Executes password change action if specified
if ($action === 'change-password') {
    // Ensures required parameters for changing password are provided
    if (empty($currentPassword) || empty($newPassword)) {
        echo json_encode(["error" => "Missing required parameters for changing password."]);
        http_response_code(400);
        exit;
    }

    $outputDir = 'changed_certificates/';
    $outputFileName = 'changed_' . uniqid() . '.p12';
    $outputPath = $outputDir . $outputFileName;

    // Creates output directory if it doesn't exist
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    // Attempts to change the password of the .p12 certificate file
    $result = changeP12Password($p12Path, $currentPassword, $newPassword, $outputPath);

    // Provides feedback based on password change success or failure
    if ($result['success']) {
        echo json_encode([
            "message" => "Password changed successfully. The certificate will be deleted in 25 minutes.",
            "file" => $outputFileName
        ]);
    } else {
        echo json_encode(["error" => $result['error']]);
        http_response_code(500);
    }
} else {
    echo json_encode(["error" => "Invalid action. Use 'change-password'."]);
    http_response_code(400);
}

// Function to change the password of a .p12 certificate file
function changeP12Password($p12Path, $currentPassword, $newPassword, $outputPath) {
    $p12Content = file_get_contents($p12Path);
    $certs = [];

    // Tries to read the .p12 file using the current password
    if (!openssl_pkcs12_read($p12Content, $certs, $currentPassword)) {
        return ["success" => false, "error" => "Incorrect password or issue with the file."];
    }

    // Exports the certificate with the new password
    if (!openssl_pkcs12_export_to_file($certs['cert'], $outputPath, $certs['pkey'], $newPassword)) {
        return ["success" => false, "error" => "Could not save certificate with new password."];
    }

    return ["success" => true, "file_path" => $outputPath];
}

// Function to delete files older than a specified expiration time
function cleanOldFiles($dir, $expirationTime) {
    if (!is_dir($dir)) return;

    $files = scandir($dir);
    $currentTime = time();

    foreach ($files as $file) {
        $filePath = $dir . $file;

        // Deletes files if they exceed the expiration time
        if (is_file($filePath) && ($currentTime - filemtime($filePath)) > $expirationTime) {
            unlink($filePath);
        }
    }
}
?>
