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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Default Title"; ?></title>
    <style>
        /* Basic styling for the header */
        header {
            background-color: #f0f0f0;
            padding: 0 2rem;
            margin: 0;
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Align items to left and right */
            align-items: center; /* Vertically center items */
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        nav a {
            text-decoration: none;
            color: #333;
        }
        nav.sidebar {
            width: 250px;
            background-color: #f0f0f0;
            padding: 10px;
            box-sizing: border-box;
            flex-direction: column;
        }
        nav.sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: block;
        }

        nav.sidebar li {
            margin-bottom: 10px;
        }
        nav.sidebar a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
        }
        nav.sidebar a:hover {
            background-color: #ddd;
        }
        .user-info{
            display: flex;
            align-items: center;
            color: #800000;
            font-size: large;
            padding: 10px;
        }
        .user-info span{
            margin-right: 10px;
        }
        .bg-primary-color {
            background-color: #800000;
        }
        .text-primary-color {
            color: #800000;
        }
        .bg-white {
            background-color: #FFF;
        }
        .text-white {
            color: #FFF;
        }
        .p-8 {
            padding: 8px;
        }
        .btn {
            border-radius: 5rem;
            padding: .5rem 1rem;
            text-decoration: none;
            color: #800000;
        }
        .header-school-name {
            display: flex;
            flex-direction: row;
            gap: .5rem;
            justify-content: center;
        }
        #img-logo {
            padding-top: 1.3rem;
        }
        body {
            display: flex; /* Use flexbox for the body */
            flex-direction: column; /* Stack content vertically */
            min-height: 100vh; /* Ensure body takes up at least full viewport height */
            margin: 0; /* Remove default body margins */
        }

        main {
            display: flex;
            flex: 1; /* Allow main content to expand and fill available space */
            padding: 0; /* Add some padding to the main content */
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
        }

        .footer-content {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .footer-section {
            margin-bottom: 20px;
            flex: 1 1 200px;
        }

        .footer-section h4 {
            margin-bottom: 10px;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section a {
            color: #fff;
            text-decoration: none;
        }
        .content {
            flex: 1; /* Allow content to expand horizontally */
            padding: 0 20px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <header class="bg-primary-color">
        <div class="header-school-name">
            <img src="../images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <h1 class="text-white">
                Datamex College of Saint Adeline
            </h1>
        </div>
        
        <div class="user-info">
            <a href="../logout.php" class="bg-white p-8 btn">Logout</a>
        </div>
    </header>
    <main>
        <nav class="sidebar">
            <ul>
                <li>
                    <div class="user-info">
                        <?php if(is_logged_in()): ?>
                            <strong><span>Welcome, <?php echo get_username(); ?>!</span></strong>
                        <?php endif; ?>
                    </div>
                    <hr>
                </li>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Courses</a></li>
                <li><a href="#">Students</a></li>
                <li><a href="#">Teachers</a></li>
                <li><a href="#">Settings</a></li>
            </ul>
        </nav>
        <div class="content">

