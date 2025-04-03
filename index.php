<?php
// Session is already started in conn.php, no need to start it again here

$page_title = "Login - Datamex College of Saint Adeline";
include_once('database/conn.php');

// Define BASE_URL based on environment
$is_local = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');
define('BASE_URL', $is_local ? 'http://localhost/dmx-capstone/' : 'https://datamexadelinesucat.com/');

// Initialize user_type to avoid undefined variable warnings
$user_type = null;

// Function to check if the user is logged in
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

// Function to get the current user's type
function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

// Function to get the username
function get_username() {
    return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
}

// Function to get the full name
function get_fullname() {
    $first_name = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : null;
    $last_name = isset($_SESSION["last_name"]) ? $_SESSION["last_name"] : null;
    return $first_name . " " . $last_name;
}

$user_type = get_user_type();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($conn, $_POST["username"]);
    $password = sanitize_input($conn, $_POST["password"]);

    // Query the database for user
    $sql = "SELECT user_id, user_type, first_name, last_name, password 
            FROM users 
            WHERE username = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["user_type"] = $row["user_type"];
            $_SESSION["username"] = $username;
            $_SESSION["first_name"] = $row["first_name"];
            $_SESSION["last_name"] = $row["last_name"];

            // Get student_num if user is a student
            if ($row["user_type"] === "student") {
                $student_sql = "SELECT student_num FROM students WHERE user_id = ?";
                $student_stmt = $conn->prepare($student_sql);
                if ($student_stmt === false) {
                    die("Prepare failed: " . $conn->error);
                }
                $student_stmt->bind_param("i", $row["user_id"]);
                $student_stmt->execute();
                $student_result = $student_stmt->get_result();
                if ($student_result->num_rows == 1) {
                    $student_row = $student_result->fetch_assoc();
                    $_SESSION["student_num"] = $student_row["student_num"];
                }
                $student_stmt->close();
            }

            // Redirect based on user type
            switch ($row["user_type"]) {
                case "admin":
                    header("Location: " . BASE_URL . "admin/dashboard/index.php");
                    break;
                case "registrar":
                    header("Location: " . BASE_URL . "registrar/dashboard/index.php");
                    break;
                case "teacher":
                    header("Location: " . BASE_URL . "teacher/dashboard/classes.php");
                    break;
                case "student":
                    header("Location: " . BASE_URL . "student/dashboard/index.php");
                    break;
                default:
                    $error_message = "Unknown user type.";
                    break;
            }
            exit();
        } else {
            $error_message = "Invalid credentials.";
        }
    } else {
        $error_message = "Invalid credentials.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Default Title"; ?></title>
    
    <?php if($user_type != null) { ?>
        <script src="<?php echo BASE_URL; ?>scripts/script.js"></script>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/style.css">
    <?php } else { ?>
        <script src="<?php echo BASE_URL; ?>scripts/script.js"></script>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>styles/style.css">
    <?php } ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- DataTables CSS (using latest version only) -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
</head>
<body>
    <header class="bg-primary-color">
        <div class="header-school-name">
            <?php if($user_type != null) { ?>
                <img src="<?php echo BASE_URL; ?>images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <?php } else { ?>
                <img src="<?php echo BASE_URL; ?>images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <?php } ?>
            <h1 class="text-white">Datamex College of Saint Adeline</h1>
        </div>
        
        <div class="user-info">
            <?php if($user_type != null) { ?>
                <a href="<?php echo BASE_URL; ?>logout.php" class="bg-white p-8 btn">Logout</a>
            <?php } ?>
        </div>

        <nav class="main-nav">
            <ul class="nav-list">
                <li><a href="<?php echo BASE_URL; ?>about.php" class="nav-link">About</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php" class="nav-link">Contact</a></li>
                <li><a href="<?php echo BASE_URL; ?>gallery.php" class="nav-link">Gallery</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <?php $user_type = strtolower(get_user_type()); ?>
        <div class="content">
            <div class="login-container">
                <div class="login-announcements">
                    <h2 class="p-05 pl-1 pr-1">Announcements</h2><hr>
                    <?php 
                    $sql = "SELECT * FROM announcements 
                            WHERE end_date >= CURDATE() 
                            ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $createdDate = new DateTime($row["created_at"]);
                            $content = $row["content"];
                            echo "<h3 class='p-1'>" . $row["title"] . "</h3>";
                            echo "<p class='p-1'>" . $content . "</p>";
                            echo "<p class='p-1' style='color:gray;'>Posted: " . $createdDate->format('l, F j, Y h:i A') . "</p><hr>";
                        }
                    } else {
                        echo "<p class='p-1'>No announcements.</p>";
                    }
                    ?>
                </div>
                <div class="login-div">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="card">
                        <div class="login-form-container">
                            <img src="<?php echo BASE_URL; ?>images/dcsa.webp" alt="School logo" width="100" height="100">
                            <h2>Login</h2>
                            <?php if (isset($error_message)) { echo "<p style='color: red;padding-bottom: 1rem;'>$error_message</p>"; } ?>
                        </div>
                        Username: <input type="text" name="username" required><br>
                        Password: <input type="password" name="password" required><br>
                        <div style="text-align: center; width: 100%;">
                            <button type="submit" class="btn_submit" style="width:100%"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
                        </div>
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <a href="<?php echo BASE_URL; ?>forgot-password.php" style="font-size: 0.9rem; color: #0066cc;">Forgot Password?</a><br>
                        </div>
                    </form>
                    <a href="<?php echo BASE_URL; ?>enroll-now.php" target="_blank" rel="noopener noreferrer" class="btn-enroll-now">ENROLL NOW !!!</a>
                </div>
            </div>

            <!-- Floating Chatbot -->
            <div class="chatbot-float">
                <div id="chatbotWindow" class="chatbot-window" style="display: none;">
                    <div class="chatbot-header">
                        <span>Datamex Chatbot</span>
                        <button onclick="toggleChatbot()" class="chatbot-close">×</button>
                    </div>
                    <div id="chatbox" class="chatbot-body"></div>
                    <div class="chatbot-footer">
                        <input type="text" id="chatInput" placeholder="Ask me something..." onkeypress="if(event.key === 'Enter') sendMessage()">
                        <button onclick="sendMessage()">Send</button>
                    </div>
                </div>
                <button id="chatbotToggle" onclick="toggleChatbot()" class="chatbot-toggle">💬</button>
            </div>
        </div>
    </main>

    <?php 
    $conn->close();
    include('layouts/footer.php'); 
    ?>

    <script>
    // Chatbot Functions
    function toggleChatbot() {
        const window = document.getElementById('chatbotWindow');
        const toggleBtn = document.getElementById('chatbotToggle');
        if (window.style.display === 'none' || window.style.display === '') {
            window.style.display = 'block';
            toggleBtn.style.display = 'none';
            const chatbox = document.getElementById('chatbox');
            if (!chatbox.innerHTML) {
                chatbox.innerHTML = `<p><strong>Bot:</strong> Hi! How can I assist you today?</p>`;
            }
        } else {
            window.style.display = 'none';
            toggleBtn.style.display = 'block';
        }
    }

    function sendMessage() {
        const input = document.getElementById('chatInput').value;
        if (!input) return;

        const chatbox = document.getElementById('chatbox');
        chatbox.innerHTML += `<p><strong>You:</strong> ${input}</p>`;
        
        fetch('<?php echo BASE_URL; ?>chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `message=${encodeURIComponent(input)}`
        })
        .then(response => response.json())
        .then(data => {
            chatbox.innerHTML += `<p><strong>Bot:</strong> ${data.reply}</p>`;
            chatbox.scrollTop = chatbox.scrollHeight;
        })
        .catch(error => {
            chatbox.innerHTML += `<p><strong>Bot:</strong> Oops, something went wrong!</p>`;
            chatbox.scrollTop = chatbox.scrollHeight;
        });

        document.getElementById('chatInput').value = '';
    }
    </script>
</body>
</html>