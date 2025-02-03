<?php
session_start(); // Start the session

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dmx-capstone";

// Database Server Credentials:
// $servername = "localhost";
// $username = "u105832525_dmx_username";
// $password = "=2[!OwpR";
// $dbname = "u105832525_dmx_capstone";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input to prevent SQL injection
function sanitize_input($conn, $input) {
    return mysqli_real_escape_string($conn, trim($input));
}