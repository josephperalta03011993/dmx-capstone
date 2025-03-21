<?php 
    $page_title = "Announcement - Datamex College of Saint Adeline";
    include_once('../../database/conn.php');
    include('../../layouts/header.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = sanitize_input($conn, $_POST["username"]);
        $password = sanitize_input($conn, $_POST["password"]);

        // Query the database based on username
        $sql = "SELECT user_id, user_type, first_name, last_name, password FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Verify password using password_verify() if you are hashing passwords
            if (password_verify($password, $row["password"])) { // Use password_hash() for registration
            //if ($password == $row["password"]){ // For plain text password for testing only NEVER USE IN PRODUCTION
                $_SESSION["user_id"] = $row["user_id"];
                $_SESSION["user_type"] = $row["user_type"];
                $_SESSION["username"] = $username; // Store username in session
                $_SESSION["first_name"] = $row["first_name"]; 
                $_SESSION["last_name"] = $row["last_name"]; 

                // Redirect based on user type
                switch ($row["user_type"]) {
                    case "admin":
                        header("Location: admin/dashboard/index.php");
                        break;
                    case "registrar":
                        header("Location: registrar/dashboard/manage_students.php");
                        break;
                    case "teacher":
                        header("Location: teacher/dashboard/classes.php");
                        break;
                    case "student":
                        header("Location: student/dashboard/index.php");
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
        <div>
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
    </div>

<?php 
    $conn->close();
    include('../../layouts/footer.php'); 
?>

<script>
    const element = $0;
    element.innerHTML = element.innerHTML.replace(/\r?\n/g, '<br>');
</script>