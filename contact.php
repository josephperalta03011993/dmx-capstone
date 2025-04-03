<?php
$page_title = "Contact Us - Datamex College of Saint Adeline";
include_once('database/conn.php');

// HEADER FUNCTIONS (same as your login page)
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

function get_username() {
    return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
}

function get_fullname() {
    $first_name = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : null;
    $last_name = isset($_SESSION["last_name"]) ? $_SESSION["last_name"] : null;
    return $first_name . " " . $last_name;
}

$user_type = get_user_type();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and capture form data
    $name = sanitize_input($conn, $_POST["name"]);
    $email = sanitize_input($conn, $_POST["email"]);
    $message = sanitize_input($conn, $_POST["message"]);

    // SQL query to insert form data into the contact_us table
    $sql = "INSERT INTO contact_us (name, email, message) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Thank you for your message! We'll get back to you soon.";
        } else {
            $error_message = "Error saving your message: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        $error_message = "Error preparing the query: " . mysqli_error($conn);
    }
}
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
            <div class="contact-container">
                <h2 class="p-05">Contact Us</h2>
                <hr>
                <?php if (isset($success_message)) { ?>
                    <p class="success-message"><?php echo $success_message; ?></p>
                <?php } ?>
                <?php if (isset($error_message)) { ?>
                    <p class="error-message"><?php echo $error_message; ?></p>
                <?php } ?>
                
                <div class="contact-content">
                    <div class="contact-form">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" name="name" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" id="email" required>
                            </div>
                            <div class="form-group full-width">
                                <label for="message">Message:</label>
                                <textarea name="message" id="message" rows="5" required></textarea>
                            </div>
                            <div class="form-group full-width">
                                <input type="submit" value="Send Message" class="btn_submit">
                            </div>
                        </form>
                    </div>
                    
                    <div class="contact-info">
                        <h3>Contact Information</h3>
                        <p><i class="fa-solid fa-location-dot"></i> 123 Datamex Street, Saint Adeline City</p>
                        <p><i class="fa-solid fa-phone"></i> (123) 456-7890</p>
                        <p><i class="fa-solid fa-envelope"></i> info@datamexcollege.edu</p>
                        <p><i class="fa-solid fa-clock"></i> Mon-Fri: 8:00 AM - 5:00 PM</p>
                    </div>
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
