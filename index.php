<?php 
    $page_title = "Login - Datamex College of Saint Adeline";
    include_once('database/conn.php');
    include('layouts/header.php');

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
                        header("Location: registrar/dashboard/index.php");
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
                    echo "<p class='p-1' style='color:gray;'>Posted: " . $createdDate->format('l, F j, Y h:i A') . "</p><hr>";
                }
            } else {
                echo "<p class='p1'>No announcements.</p>";
            }
        ?>
    </div>
    <div class="login-div">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="card">
            <div class="login-form-container">
                <img src="images/dcsa.webp" alt="School logo" width="100" height="100">
                <h2>Login</h2>
                <?php if (isset($error_message)) { echo "<p style='color: red;padding-bottom: 1rem;'>$error_message</p>"; } ?>
            </div>
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="forgot-password.php" style="font-size: 0.9rem; color: #0066cc;">Forgot Password?</a>
            </div>
            <button type="submit" class="btn_submit"><i class="fa-solid fa-right-to-bracket"></i> Login</button>
        </form>
        <a href="enroll-now.php" target="_blank" rel="noopener noreferrer" class="btn-enroll-now">ENROLL NOW !!!</a>
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

<?php 
    $conn->close();
    include('layouts/footer.php'); 
?>

<script>
    const element = $0;
    element.innerHTML = element.innerHTML.replace(/\r?\n/g, '<br>');

    // Chatbot Functions
    function toggleChatbot() {
        const window = document.getElementById('chatbotWindow');
        const toggleBtn = document.getElementById('chatbotToggle');
        if (window.style.display === 'none' || window.style.display === '') {
            window.style.display = 'block';
            toggleBtn.style.display = 'none';
            // Optional: Show welcome message on first open
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
        
        fetch('chatbot.php', {
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
        });

        document.getElementById('chatInput').value = '';
    }
</script>