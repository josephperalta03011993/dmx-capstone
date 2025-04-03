<?php
$page_title = "About Us - Datamex College of Saint Adeline";
include_once('database/conn.php');

// HEADER FUNCTIONS (same as your login page)
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

$user_type = get_user_type();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Default Title"; ?></title>
    <?php if($user_type != null) { ?>
        <script src="../../scripts/script.js"></script>
        <link rel="stylesheet" href="../../styles/style.css">
    <?php } else { ?>
        <script src="scripts/script.js"></script>
        <link rel="stylesheet" href="styles/style.css">
    <?php } ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <header class="bg-primary-color">
        <div class="header-school-name">
            <?php if($user_type != null) { ?>
                <img src="../../images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <?php } else { ?>
                <img src="images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <?php } ?>
            <h1 class="text-white">
                Datamex College of Saint Adeline
            </h1>
        </div>
        
        <div class="user-info">
            <?php if($user_type != null) { ?>
                <a href="../../logout.php" class="bg-white p-8 btn">Logout</a>
            <?php } ?>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="index.php" class="nav-link">Home</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>
                <li><a href="gallery.php" class="nav-link">Gallery</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="content">
            <div class="about-container">
                <h2 class="p-05">About Us</h2>
                <hr>
                <p>Welcome to Datamex College of Saint Adeline. We are committed to providing quality education and training for students to excel in their respective fields.</p>
                <p>Our institution is built on a foundation of excellence, innovation, and integrity, ensuring that every student receives the knowledge and skills needed to succeed.</p>
                <h3>Our Mission</h3>
                <p>To provide accessible, affordable, and high-quality education that prepares students for career success.</p>
                <h3>Our Vision</h3>
                <p>To be a leading educational institution recognized for academic excellence and industry partnerships.</p>
                <h3>Core Values</h3>
                <ul>
                    <li>Excellence</li>
                    <li>Integrity</li>
                    <li>Innovation</li>
                    <li>Student-Centered Learning</li>
                </ul>
            </div>
        </div>
    </main>

    <?php 
    $conn->close();
    include('layouts/footer.php'); 
    ?>
</body>
</html>