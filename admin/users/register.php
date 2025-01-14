<?php
include('../../database/conn.php');

$registration_success = null;
$registration_error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
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
            $sql = "INSERT INTO users (username, password, user_type) VALUES ('$username', '$hashed_password', '$user_type')";

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
        Username: <input type="text" name="reg_username" required><br>
        Password: <input type="password" name="reg_password" required><br>
        Confirm Password: <input type="password" name="reg_confirm_password" required><br>
        User Type:
        <select name="reg_user_type" required>
            <option value="admin">Admin</option>
            <option value="registrar">Registrar</option>
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
        </select><br><br>
        <input type="submit" name="register" value="Register">
    </form>

    <!-- <p>Already have an account? <a href="../index.php">Login here</a>.</p> -->

<?php include('../../layouts/footer.php'); ?>