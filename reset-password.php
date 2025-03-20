<?php
$page_title = "Reset Password - Datamex College of Saint Adeline";
include_once('database/conn.php');
include('layouts/header.php');

$success_message = '';
$error_message = '';
$show_form = false;

if (isset($_GET['token'])) {
    $token = sanitize_input($conn, $_GET['token']);
    
    // Check if token is valid and not expired
    $sql = "SELECT user_id FROM users WHERE reset_token = ? AND token_expiry > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['user_id'];
        $show_form = true; // Show the form if token is valid

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = password_hash(sanitize_input($conn, $_POST["password"]), PASSWORD_DEFAULT);
            
            // Update password and clear reset token
            $sql = "UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_password, $user_id);
            
            if ($stmt->execute()) {
                $success_message = "Password reset successfully. <a href='index.php'>Login here</a>";
                $show_form = false; // Hide form after success
            } else {
                $error_message = "Error resetting password. Please try again.";
            }
        }
    } else {
        $error_message = "Invalid or expired reset token.";
    }
    $stmt->close();
} else {
    $error_message = "No reset token provided.";
}
?>

<div class="login-container">
    <div class="login-div">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?token=' . htmlspecialchars($token); ?>" class="card">
            <div class="login-form-container">
                <img src="images/dcsa.webp" alt="School logo" width="100" height="100">
                <h2>Reset Password</h2>
                <?php 
                    if ($success_message) { echo "<p style='color: green; padding-bottom: 1rem;'>$success_message</p>"; }
                    if ($error_message) { echo "<p style='color: red; padding-bottom: 1rem;'>$error_message</p>"; }
                ?>
                <?php if ($show_form) { ?>
                    <p>Enter your new password.</p>
                    New Password: <input type="password" name="password" required><br>
                    <button type="submit" class="btn_submit">Reset Password</button>
                <?php } ?>
            </div>
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