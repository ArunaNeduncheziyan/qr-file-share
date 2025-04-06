<?php
// Database connection details
$host = 'localhost';
$dbname = 'file_db';
$username = 'root';
$password = ''; // Default password for XAMPP MySQL

// Establish PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=UTF8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if file ID is passed
if (isset($_GET['file_id'])) {
    $fileId = (int) $_GET['file_id'];

    // Fetch the file URL from the database
    $stmt = $pdo->prepare("SELECT file_path FROM files WHERE id = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch();

    if ($file) {
        // Return file URL in the response
        $fileUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/file_upload_system/' . $file['file_path'];
        echo json_encode(['success' => true, 'file_url' => $fileUrl]);
    } else {
        echo json_encode(['success' => false, 'message' => 'File not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No file ID provided']);
}
?>
