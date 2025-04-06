<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "link2qr";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $n = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password']; // In real applications, hash the password

    // Insert data into the users table
    $sql = "INSERT INTO register (name, email, password)
            VALUES ('$n', '$email', '$pass')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully!";
        header("Location: login.php"); // Redirect to login page after successful registration
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
