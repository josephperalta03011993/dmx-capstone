<?php
include('../../database/conn.php');
$page_title = "Edit User - Datamex College of Saint Adeline";

$update_success = null;
$update_error = null;

// Check if user_id is provided in the URL
if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Fetch user details for the provided user_id
    $sql = "SELECT * FROM users WHERE user_id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch user data
    } else {
        die("User not found.");
    }
} else {
    die("Invalid request. User ID is missing.");
}

// Handle form submission for updating user data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $first_name = sanitize_input($conn, $_POST["reg_first_name"]);
    $last_name = sanitize_input($conn, $_POST["reg_last_name"]);
    $username = sanitize_input($conn, $_POST["reg_username"]);
    $user_type = sanitize_input($conn, $_POST["reg_user_type"]);

    $password = $_POST["reg_password"];
    $confirm_password = $_POST["reg_confirm_password"];

    $password_clause = ""; // Default: no password update

    // If password fields are filled, validate and hash the new password
    if (!empty($password) || !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $update_error = "Passwords do not match.";
        } elseif (strlen($password) < 8) {
            $update_error = "Password must be at least 8 characters long.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $password_clause = ", password = '$hashed_password'"; // Update password in SQL
        }
    }

    if (!$update_error) {
        // Update query
        $sql = "UPDATE users 
                SET first_name = '$first_name', 
                    last_name = '$last_name', 
                    username = '$username', 
                    user_type = '$user_type'
                    $password_clause 
                WHERE user_id = $user_id";

        if ($conn->query($sql) === TRUE) {
            $update_success = "User updated successfully!";
        } else {
            $update_error = "Error updating user: " . $conn->error;
        }
    }
}

?>

<?php include('../../layouts/header.php'); ?>

<h2>Edit User</h2><hr><br>

<?php if ($update_success) { echo "<p style='color: green;'>$update_success</p>"; } ?>
<?php if ($update_error) { echo "<p style='color: red;'>$update_error</p>"; } ?>

<form method="post" action="">
    <div class="form-container">
        <div class="form-group">
            <label for="reg_first_name">First Name:</label>
            <input type="text" name="reg_first_name" id="reg_first_name" value="<?php echo $user['first_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_last_name">Last Name:</label>
            <input type="text" name="reg_last_name" id="reg_last_name" value="<?php echo $user['last_name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_username">Username:</label>
            <input type="text" name="reg_username" id="reg_username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_user_type">User Type:</label>
            <select name="reg_user_type" id="reg_user_type" required>
                <option value="admin" <?php if ($user['user_type'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="registrar" <?php if ($user['user_type'] == 'registrar') echo 'selected'; ?>>Registrar</option>
                <option value="teacher" <?php if ($user['user_type'] == 'teacher') echo 'selected'; ?>>Teacher</option>
                <option value="student" <?php if ($user['user_type'] == 'student') echo 'selected'; ?>>Student</option>
            </select>
        </div>
        <div class="form-group">
                <label for="reg_password">New Password:</label>
                <input type="password" name="reg_password" id="reg_password" placeholder="Leave blank to keep current password">
            </div>
            <div class="form-group">
                <label for="reg_confirm_password">Confirm New Password:</label>
                <input type="password" name="reg_confirm_password" id="reg_confirm_password" placeholder="Leave blank to keep current password">
            </div>
        <div class="form-group full-width">
            <button type="submit" name="update" class="btn_submit">
                <i class="fa-solid fa-save"></i> Update User
            </button>
        </div>
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>
