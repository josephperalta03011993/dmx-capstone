<?php 

    include('database/conn.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = sanitize_input($conn, $_POST["username"]);
        $password = sanitize_input($conn, $_POST["password"]);

        // Query the database based on username
        $sql = "SELECT id, user_type, password FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Verify password using password_verify() if you are hashing passwords
            if (password_verify($password, $row["password"])) { // Use password_hash() for registration
            //if ($password == $row["password"]){ // For plain text password for testing only NEVER USE IN PRODUCTION
                $_SESSION["user_id"] = $row["id"];
                $_SESSION["user_type"] = $row["user_type"];
                $_SESSION["username"] = $username; // Store username in session

                // Redirect based on user type
                switch ($row["user_type"]) {
                    case "admin":
                        header("Location: admin/admin_dashboard.php");
                        break;
                    case "registrar":
                        header("Location: registrar/registrar_dashboard.php");
                        break;
                    case "teacher":
                        header("Location: teacher/teacher_dashboard.php");
                        break;
                    case "student":
                        header("Location: student/student_dashboard.php");
                        break;
                    default:
                        // Handle unknown user types
                        echo "Unknown user type.";
                }
                exit(); // Important: Stop further execution after redirect
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "Invalid username.";
        }
    }
    
    $conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error_message)) { echo "<p style='color: red;'>$error_message</p>"; } ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Username: <input type="text" name="username" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>