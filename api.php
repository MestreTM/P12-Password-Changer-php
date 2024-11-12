<?php
header('Content-Type: text/html'); // Define o tipo de conteúdo como texto/html

// Limpa arquivos com mais de 25 minutos
cleanOldFiles('changed_certificates/', 25);

$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? null;

if ($requestMethod !== 'POST') {
    echo "<pre>";
    echo json_encode(["error" => "Invalid request method. Use POST."]);
    echo "</pre>";
    http_response_code(405);
    exit;
}

$currentPassword = $_POST['currentPassword'] ?? null;
$newPassword = $_POST['newPassword'] ?? null;

if (!isset($_FILES['p12File'])) {
    echo "<pre>";
    echo json_encode(["error" => "File not uploaded."]);
    echo "</pre>";
    http_response_code(400);
    exit;
}

$file = $_FILES['p12File'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo "<pre>";
    echo json_encode(["error" => "Error uploading file."]);
    echo "</pre>";
    http_response_code(500);
    exit;
}

$p12Path = $file['tmp_name'];

if ($action === 'change-password') {
    if (empty($currentPassword) || empty($newPassword)) {
        echo json_encode(["error" => "Missing required parameters for changing password."]);
        http_response_code(400);
        exit;
    }

    $outputDir = 'changed_certificates/';
    $outputFileName = 'changed_' . uniqid() . '.p12';
    $outputPath = $outputDir . $outputFileName;

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    $result = changeP12Password($p12Path, $currentPassword, $newPassword, $outputPath);

    if ($result['success']) {
        echo json_encode([
            "message" => "Password changed successfully. The certificate will deleted in 25 minutes.",
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

function changeP12Password($p12Path, $currentPassword, $newPassword, $outputPath) {
    $p12Content = file_get_contents($p12Path);
    $certs = [];

    if (!openssl_pkcs12_read($p12Content, $certs, $currentPassword)) {
        return ["success" => false, "error" => "Incorrect password or issue with the file."];
    }

    if (!openssl_pkcs12_export_to_file($certs['cert'], $outputPath, $certs['pkey'], $newPassword)) {
        return ["success" => false, "error" => "Could not save certificate with new password."];
    }

    return ["success" => true, "file_path" => $outputPath];
}

function cleanOldFiles($dir, $expirationTime) {
    if (!is_dir($dir)) return;

    $files = scandir($dir);
    $currentTime = time();

    foreach ($files as $file) {
        $filePath = $dir . $file;

        if (is_file($filePath) && ($currentTime - filemtime($filePath)) > $expirationTime) {
            unlink($filePath);
        }
    }
}
?>
