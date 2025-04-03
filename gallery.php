<?php
$page_title = "Gallery - Datamex College of Saint Adeline";
include_once('database/conn.php');

// HEADER FUNCTIONS (same as your login page)
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

$user_type = get_user_type();

// Fetch gallery items from the database
$sql = "SELECT * FROM gallery ORDER BY id DESC";
$result = $conn->query($sql);
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
    <style>
        .card {
            border: 1px solid #ccc;
            margin: 10px;
            border-radius: 8px;
            overflow: hidden;
            width: 300px;
            display: inline-block;
            vertical-align: top;
        }

        .card-img {
            width: 100%;
            height: auto;
        }

        .card-body {
            padding: 10px;
        }

        .card-title {
            font-size: 1.2em;
            margin-bottom: 8px;
        }

        .card-description {
            font-size: 1em;
            color: #555;
        }
    </style>
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
            <div class="gallery-container">
                <h2 class="p-05">Gallery</h2>
                <hr>
                
                <div class="gallery-cards">
                    <?php
                    // Check if there are any gallery items in the database
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='card'>
                                    <img src='{$row['image_url']}' alt='{$row['title']}' class='card-img'>
                                    <div class='card-body'>
                                        <h3 class='card-title'>{$row['title']}</h3>
                                        <p class='card-description'>{$row['description']}</p>
                                    </div>
                                  </div>";
                        }
                    } else {
                        echo "<p>No gallery items found.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <?php 
    $conn->close();
    include('layouts/footer.php'); 
    ?>
</body>
</html>
