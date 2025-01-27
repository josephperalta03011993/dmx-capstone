<?php 
    $page_title = "Login - Datamex College of Saint Adeline";
    include_once('database/conn.php');
    include('layouts/header.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = sanitize_input($conn, $_POST["username"]);
        $password = sanitize_input($conn, $_POST["password"]);

        // Query the database based on username
        $sql = "SELECT user_id, user_type, password FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Verify password using password_verify() if you are hashing passwords
            if (password_verify($password, $row["password"])) { // Use password_hash() for registration
            //if ($password == $row["password"]){ // For plain text password for testing only NEVER USE IN PRODUCTION
                $_SESSION["user_id"] = $row["user_id"];
                $_SESSION["user_type"] = $row["user_type"];
                $_SESSION["username"] = $username; // Store username in session

                // Redirect based on user type
                switch ($row["user_type"]) {
                    case "admin":
                        header("Location: admin/dashboard/index.php");
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
?>

    <div class="login-container">
        <div class="login-announcements">
            <h2 class="p-05 pl-1 pr-1">Announcements</h2><hr>
            <?php 
          
                $sql = "SELECT * FROM announcements 
                        WHERE end_date >= CURDATE() 
                        ORDER BY created_at DESC"; 
                $result = $conn->query($sql);

                if($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $createdDate = new DateTime($row["created_at"]);
                        $content = $row["content"];

                        echo "<h3 class='p-1'>" . $row["title"] . "</h3>";
                        echo "<p class='p-1'>" . $content . "</p>";
                        echo "<p class='p-1'>" . $createdDate->format('l, F j, Y h:i A') . "</p><hr>";
                    }
                } else {
                    echo "<p class='p1'>No announcements.</p>";
                }
       
            ?>
        </div>
        <div class="login-div">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="card">
                <div class="form-container">
                    <img src="images/dcsa.webp" alt="School logo" width="100" height="100">
                    <h2>Login</h2>
                    <?php if (isset($error_message)) { echo "<p style='color: red;padding-bottom: 1rem;'>$error_message</p>"; } ?>
                </div>
                Username: <input type="text" name="username" required><br><br>
                Password: <input type="password" name="password" required><br><br>
                <input type="submit" value="Login">
            </form>
        </div>
    </div>

<?php 
    $conn->close();
    include('layouts/footer.php'); 
?>

<script>
    const element = $0;
    element.innerHTML = element.innerHTML.replace(/\r?\n/g, '<br>');
</script>