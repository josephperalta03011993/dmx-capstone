<?php
include('../../database/conn.php');
$page_title = "User Registration - Datamex College of Saint Adeline";
$registration_success = null;
$registration_error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $first_name = sanitize_input($conn, $_POST["reg_first_name"]);
    $last_name = sanitize_input($conn, $_POST["reg_last_name"]);
    $username = sanitize_input($conn, $_POST["reg_username"]);
    $password = $_POST["reg_password"]; // Get the plain password for hashing
    $confirm_password = $_POST["reg_confirm_password"];
    $user_type = sanitize_input($conn, $_POST["reg_user_type"]);

    // Basic Validation (Improve this significantly in a real app)
    if (empty($username) || empty($password) || empty($confirm_password) || empty($user_type)) {
        $registration_error = "All fields are required.";
    } elseif ($password != $confirm_password) {
        $registration_error = "Passwords do not match.";
    } elseif (strlen($password) < 8){
        $registration_error = "Password must be at least 8 characters long.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        //Check if username already exist
        $check_username_sql = "SELECT username FROM users WHERE username = '$username'";
        $check_username_result = $conn->query($check_username_sql);

        if ($check_username_result->num_rows > 0){
            $registration_error = "Username already exist.";
        } else {
            $sql = "INSERT INTO users (username, password, user_type, first_name, last_name) 
                    VALUES ('$username', '$hashed_password', '$user_type', '$first_name', '$last_name')";

            if ($conn->query($sql) === TRUE) {
                $registration_success = "Registration successful!";
            } else {
                $registration_error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }

    }
}

?>

<?php include('../../layouts/header.php'); ?>

    <h2>User Registration</h2><hr><br>

    <?php if ($registration_success) { echo "<p style='color: green;'>$registration_success</p>"; } ?>
    <?php if ($registration_error) { echo "<p style='color: red;'>$registration_error</p>"; } ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-container">
            <div class="form-group">
                <label for="reg_first_name">First Name:</label>
                <input type="text" name="reg_first_name" id="reg_first_name" required>
            </div>
            <div class="form-group">
                <label for="reg_last_name">Last Name:</label>
                <input type="text" name="reg_last_name" id="reg_last_name" required>
            </div>
            <div class="form-group">
                <label for="reg_username">Username:</label>
                <input type="text" name="reg_username" id="reg_username" required>
            </div>
            <div class="form-group">
                <label for="reg_password">Password:</label>
                <input type="password" name="reg_password" id="reg_password" required>
            </div>
            <div class="form-group">
                <label for="reg_confirm_password">Confirm Password:</label>
                <input type="password" name="reg_confirm_password" id="reg_confirm_password" required>
            </div>
            <div class="form-group">
                <label for="reg_user_type">User Type:</label>
                <select name="reg_user_type" id="reg_user_type" required>
                    <option value="admin">Admin</option>
                    <option value="registrar">Registrar</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                </select>
            </div>
            <div class="form-group full-width">
                <button type="submit" name="register" class="btn_submit">
                    <i class="fa-solid fa-floppy-disk"></i> Register User
                </button>
            </div>
        </div>
    </form>

<?php include('../../layouts/footer.php'); ?>