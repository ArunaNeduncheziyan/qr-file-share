<?php
// Include the PHP QR Code library
include 'phpqrcode/qrlib.php';

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

// Handle File Upload
if (isset($_POST['submit'])) {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name']; // This is the original file name
        $fileSize = $_FILES['file']['size'];
        $fileType = $_FILES['file']['type'];

        // Set file path and move uploaded file
        $uploadDir = 'uploads/';
        $uploadFilePath = $uploadDir . $fileName;

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
          $stmt = $pdo->prepare("INSERT INTO files (file_name, file_path) VALUES (?, ?)");
          $stmt->execute([$fileName, $uploadFilePath]);
          $fileId = $pdo->lastInsertId(); // Get the auto-increment ID
      
          echo "<div class='alert alert-success'>File uploaded successfully! File ID: $fileId</div>";
      }
}
}
// Handle QR code generation when retrieving file URL
if (isset($_POST['get_url'])) {
    $fileId = $_POST['file_id'];
    $stmt = $pdo->prepare("SELECT file_path FROM files WHERE id = ?");
    $stmt->execute([$fileId]);
    $file = $stmt->fetch();

    if ($file) {
        $fileUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/file_upload_system/' . $file['file_path'];

        // File URL for QR code generation
        $fileName = 'images/qr_' . $fileId . '.png';
        $ecc = 'H'; // Error correction level
        $pixelSize = 20; // Size of the QR code
        $frameSize = 5; // Frame size around the QR code

        // Generate the QR code and save as PNG
        QRcode::png($fileUrl, $fileName, $ecc, $pixelSize, $frameSize);

        // Display the QR code
        echo "<br><br><br><br><div><center><h3><b>Generated QR Code for File ID {$fileId}</b></h3><img src='{$fileName}' width='150'></center></div>";
    } else {
        echo "<div class='alert alert-danger'>File not found.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title> Link To QR</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/animate.css/animate.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
<style>
    #header{
        height:60px;
        background-attachment:scroll;
        background-color: rgb(22, 21, 21);
    }
    .panel-body{
        margin-top: 66px;
        text-align: center;
        margin-left: 300px;
        margin-bottom: 50px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgb(24, 21, 21) (0, 0, 0.2, 0.3);
        width: 500px;
    }
</style>

</head>
<body>
<header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.php" class="logo d-flex align-items-center">
        
        <h1 class="sitename">Link2QR</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="index.php">About</a></li>
          <li><a href="index.php">Services</a></li>
          <li class="dropdown"><a href="#"><span>User</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            
                <ul>
                  <li><a href="register.php">Register</a></li>
                  <li><a href="login.php">Login</a></li>
                </ul>
              </li>
              
          <li><a href="#footer">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>
<div class="container">
                <div class="panel-body">

                    <!-- Form to Upload File -->
                    <form method="post" enctype="multipart/form-data">
                        
                        <div class="form-group">
                            
                            <b><label for="file">Upload your files to generate QR link:</label></b><br><br>
                            <input type="file" name="file" class="form-control" required />
                        </div><br>
                        <button type="submit" name="submit" class="btn btn-primary">Upload File</button>
                    </form>

                    <!-- Form to Retrieve File URL and Generate QR Code -->
                    <form method="post" class="mt-4">
                        <div class="form-group">
                            <b><label for="file_id">File ID to Retrieve and Generate QR Code:</label></b><br><br>
                            <input type="text" name="file_id" class="form-control" value="<?php echo isset($fileId) ? htmlspecialchars($fileId) : ''; ?>" readonly />
                        </div><br>
                        <button type="submit" name="get_url" class="btn btn-success">Get File URL and Generate QR Code</button>
                    </form>

                </div>
            
    
</div>
<footer id="footer" class="footer dark-background">
    <div class="container">
      <h3 class="sitename">Link2QR</h3>
      <p>Et aut eum quis fuga eos sunt ipsa nihil. Labore corporis magni eligendi fuga maxime saepe commodi placeat.</p>
      <div class="social-links d-flex justify-content-center">
        <a href=""><i class="bi bi-twitter-x"></i></a>
        <a href=""><i class="bi bi-facebook"></i></a>
        <a href=""><i class="bi bi-instagram"></i></a>
        <a href=""><i class="bi bi-skype"></i></a>
        <a href=""><i class="bi bi-linkedin"></i></a>
      </div>
      <div class="container">
        <div class="copyright">
          <span>Copyright</span> <strong class="px-1 sitename">Link2QR</strong> <span>All Rights Reserved</span>
        </div>
        
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>

