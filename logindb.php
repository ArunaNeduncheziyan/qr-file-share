<?php
// Start a session to track logged-in users
session_start();

// Database connection
$servername = "localhost";
$username = "root";  // Default username for XAMPP MySQL
$password = "";  // Default password for XAMPP MySQL
$dbname = "link2qr";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email and password from the form
    $email = $_POST['email'];
    $pass = $_POST['password']; // In real apps, hash the password

    // SQL query to check if the user exists
    $sql = "SELECT * FROM register WHERE email = '$email'";

    // Run the query
    $result = $conn->query($sql);

    // Check if a matching record is found
    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();

        // Check if the password matches
        if ($pass == $user['password']) {
            // Password matches, login success
            // Store the user's ID in session
            $_SESSION['email'] = $user['email']; // Store email in session
            header("Location: generateqr.php");  // Redirect to homepage (or dashboard)
            exit();
        } else {
            // Invalid password
            echo "Incorrect password!";
        }
    }
     else {
        // User not found
        echo "No user found with that email!";
    }
}

// Close connection
$conn->close();
?>
