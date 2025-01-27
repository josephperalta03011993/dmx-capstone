<?php

// Function to check if the user is logged in
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

// Function to get the current user's type
function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

// Function to get the username
function get_username(){
    return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
}


function get_fullname(){
    $first_name = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : null;
    $last_name = isset($_SESSION["last_name"]) ? $_SESSION["last_name"] : null;
    return $first_name . " " . $last_name;
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

    <!-- data tables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
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
    </header>
    <main>
        <?php 
            $user_type = get_user_type();
            if(strtolower($user_type) === 'admin') 
            {
                include('nav.php'); 
            }
        ?>
        <div class="content">

