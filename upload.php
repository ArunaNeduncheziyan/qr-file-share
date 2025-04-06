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

if (isset($_POST['submit'])) {
    $file = $_FILES['file'];
    $itemId = $_POST['item_id'];
    
    // Check if a file was uploaded
    if ($file['error'] == 0) {
        // Define the file path to store the uploaded file
        $filePath = 'uploads/' . basename($file['name']);
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Store file information in the database
            $stmt = $pdo->prepare("INSERT INTO files (file_name, file_path, item_id) VALUES (?, ?, ?)");
            $stmt->execute([$file['name'], $filePath, $itemId]);
            
            echo "<div class='alert alert-success'>File uploaded successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error uploading the file.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please select a file to upload.</div>";
    }
}

?>

