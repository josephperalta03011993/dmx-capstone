<?php
$page_title = "Forgot Password - Datamex College of Saint Adeline";
include_once('database/conn.php');
include('layouts/header.php');

// Load PHPMailer files manually
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Use the PHPMailer namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($conn, $_POST["username"]);
    
    // Check if username exists in the database and get user_type
    $sql = "SELECT user_id, user_type, username FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];
        $user_type = $user['user_type'];
        $username = $user['username'];

        // Get email if user_type is student
        $email = null;
        if ($user_type === 'student') {
            $sql = "SELECT email FROM students WHERE user_id = ?"; // Adjust if linking by username
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $student_result = $stmt->get_result();
            
            if ($student_result->num_rows == 1) {
                $student = $student_result->fetch_assoc();
                $email = $student['email'];
            }
        }

        if ($user_type === 'student' && !$email) {
            $error_message = "No email address found for this student account.";
        } elseif ($user_type !== 'student') {
            $error_message = "Password reset is only available for student accounts at this time.";
        } else {
            // Generate a unique reset token
            $reset_token = bin2hex(random_bytes(16));
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

            // Store the token in the users table
            $sql = "UPDATE users SET reset_token = ?, token_expiry = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $reset_token, $token_expiry, $user_id);
            
            if ($stmt->execute()) {
                // Send reset email using PHPMailer
                $reset_link = "https://datamexadelinesucat.com/reset-password.php?token=" . $reset_token; // Adjust for production
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dmxcapstone@gmail.com'; // Your Gmail address
                    $mail->Password = 'nwsoyafhwfzbqjyk'; // Your Gmail App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('your-email@gmail.com', 'Datamex College');
                    $mail->addAddress($email); // Student's email

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body    = "Hello $username,<br><br>Click this link to reset your password: <a href='$reset_link'>$reset_link</a><br><br>This link will expire in 1 hour.";
                    $mail->AltBody = "Hello $username,\n\nClick this link to reset your password: $reset_link\n\nThis link will expire in 1 hour.";

                    $mail->send();
                    $success_message = "A password reset link has been sent to your email.";
                } catch (Exception $e) {
                    $error_message = "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error_message = "Error processing request. Please try again.";
            }
        }
    } else {
        $error_message = "No account found with that username.";
    }
    $stmt->close();
}
?>

<div class="login-container">
    <div class="login-div">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="card">
            <div class="login-form-container">
                <img src="images/dcsa.webp" alt="School logo" width="100" height="100">
                <h2>Forgot Password</h2>
                <?php 
                    if ($success_message) { echo "<p style='color: green; padding-bottom: 1rem;'>$success_message</p>"; }
                    if ($error_message) { echo "<p style='color: red; padding-bottom: 1rem;'>$error_message</p>"; }
                ?>
                <p>Enter your username to receive a password reset link (students only).</p>
            </div>
            Username: <input type="text" name="username" required><br>
            <button type="submit" class="btn_submit">Send Reset Link</button>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="index.php" style="font-size: 0.9rem; color: #0066cc;">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<?php 
$conn->close();
include('layouts/footer.php'); 
?>