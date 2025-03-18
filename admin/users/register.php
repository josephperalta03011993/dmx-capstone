<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../../database/conn.php');
$page_title = "User Registration - Datamex College of Saint Adeline";
$registration_success = null;
$registration_error = null;

// Initialize form variables
$reg_user_type = isset($_POST['reg_user_type']) ? $_POST['reg_user_type'] : 'admin'; // Default to admin
$reg_first_name = isset($_POST['reg_first_name']) ? $_POST['reg_first_name'] : '';
$reg_last_name = isset($_POST['reg_last_name']) ? $_POST['reg_last_name'] : '';
$reg_username = isset($_POST['reg_username']) ? $_POST['reg_username'] : '';
$selected_student_id = isset($_POST['selected_student_id']) ? $_POST['selected_student_id'] : '';
$enrolled_student = isset($_POST['enrolled_student']) ? $_POST['enrolled_student'] : '';

// Debugging: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_error());
}

// Helper function to insert into user-type-specific tables
function insert_user_type($conn, $table, $user_id, $first_name, $last_name) {
    $sql = "INSERT INTO $table (user_id, first_name, last_name) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $first_name, $last_name);
    $success = mysqli_stmt_execute($stmt);
    $error = $success ? null : mysqli_error($conn);
    mysqli_stmt_close($stmt);
    return ['success' => $success, 'error' => $error];
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {

    $user_type = sanitize_input($conn, $reg_user_type);
    $username = sanitize_input($conn, $reg_username);
    $password = $_POST["reg_password"];
    $confirm_password = $_POST["reg_confirm_password"];

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $registration_error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $registration_error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $registration_error = "Password must be at least 8 characters long.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $check_username_sql = "SELECT username FROM users WHERE username = ?";
        $check_stmt = mysqli_prepare($conn, $check_username_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        if (mysqli_num_rows($check_result) > 0) {
            $registration_error = "Username already exists.";
        } else {
            $first_name = '';
            $last_name = '';

            $effective_student_id = !empty($selected_student_id) ? $selected_student_id : $enrolled_student;

            if ($user_type === "student" && !empty($effective_student_id)) {
                $fetch_student_sql = "SELECT first_name, last_name FROM students WHERE student_id = ?";
                $fetch_stmt = mysqli_prepare($conn, $fetch_student_sql);
                mysqli_stmt_bind_param($fetch_stmt, "i", $effective_student_id);
                mysqli_stmt_execute($fetch_stmt);
                $fetch_result = mysqli_stmt_get_result($fetch_stmt);
                if ($student = mysqli_fetch_assoc($fetch_result)) {
                    $first_name = $student['first_name'];
                    $last_name = $student['last_name'];
                    if (empty($first_name) || empty($last_name)) {
                        $registration_error = "Student record missing first or last name.";
                    }
                } else {
                    $registration_error = "Selected student not found.";
                }
                mysqli_stmt_close($fetch_stmt);
            } elseif ($user_type !== "student") {
                $first_name = sanitize_input($conn, $reg_first_name);
                $last_name = sanitize_input($conn, $reg_last_name);
                if (empty($first_name) || empty($last_name)) {
                    $registration_error = "First and last names are required for $user_type.";
                } else {
                    echo "Non-student: First Name = $first_name, Last Name = $last_name<br>";
                }
            } else {
                $registration_error = "Please select an enrolled student.";
            }

            if (!$registration_error) {
                $sql = "INSERT INTO users (username, password, user_type, first_name, last_name) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssss", $username, $hashed_password, $user_type, $first_name, $last_name);

                if (mysqli_stmt_execute($stmt)) {
                    $user_id = mysqli_insert_id($conn);

                    if ($user_type === "student" && !empty($effective_student_id)) {
                        $update_sql = "UPDATE students SET user_id = ? WHERE student_id = ?";
                        $update_stmt = mysqli_prepare($conn, $update_sql);
                        mysqli_stmt_bind_param($update_stmt, "ii", $user_id, $effective_student_id);
                        if (!mysqli_stmt_execute($update_stmt)) {
                            $registration_error = "Error updating student: " . mysqli_error($conn);
                        }
                        mysqli_stmt_close($update_stmt);
                    } elseif ($user_type !== "student") {
                        $tables = ['admin' => 'admins', 'registrar' => 'registrars', 'teacher' => 'teachers'];
                        $table = $tables[$user_type];
                        $result = insert_user_type($conn, $table, $user_id, $first_name, $last_name);
                        if (!$result['success']) {
                            $registration_error = "Error adding $user_type: " . $result['error'];
                        } else {
                            echo "Inserted into $table successfully<br>";
                        }
                    }

                    if (!$registration_error) {
                        $registration_success = "Registration successful!";
                    }
                } else {
                    $registration_error = "Error inserting into users: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_stmt_close($check_stmt);
    }
}
?>

<?php 
include('../../layouts/header.php'); 
?>

<h2>User Registration</h2><hr><br>

<?php 
if ($registration_success) { echo "<p style='color: green;'>$registration_success</p>"; }
if ($registration_error) { echo "<p style='color: red;'>$registration_error</p>"; }
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-container">
        <div class="form-group">
            <label for="reg_user_type">User Type:</label>
            <select name="reg_user_type" id="reg_user_type" required>
                <option value="admin" <?php echo $reg_user_type === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="registrar" <?php echo $reg_user_type === 'registrar' ? 'selected' : ''; ?>>Registrar</option>
                <option value="teacher" <?php echo $reg_user_type === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                <option value="student" <?php echo $reg_user_type === 'student' ? 'selected' : ''; ?>>Student</option>
            </select>
        </div>

        <div id="enrolled_student_section" style="display: <?php echo $reg_user_type === 'student' ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label for="enrolled_student">Select Enrolled Student:</label>
                <select name="enrolled_student" id="enrolled_student">
                    <option value="">-- Select a Student --</option>
                    <?php
                    $enrolled_students_sql = "SELECT student_id, first_name, last_name FROM students WHERE status = 'enrolled'";
                    $enrolled_students_result = mysqli_query($conn, $enrolled_students_sql);
                    if ($enrolled_students_result === false) {
                        echo "Query Error: " . mysqli_error($conn);
                    } elseif (mysqli_num_rows($enrolled_students_result) > 0) {
                        while ($row = mysqli_fetch_assoc($enrolled_students_result)) {
                            $selected = ($enrolled_student == $row['student_id']) ? 'selected' : '';
                            echo "<option value='{$row['student_id']}' data-first-name='{$row['first_name']}' data-last-name='{$row['last_name']}' $selected>{$row['first_name']} {$row['last_name']}</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No enrolled students found</option>";
                    }
                    ?>
                </select>
            </div>
            <input type="hidden" name="selected_student_id" id="selected_student_id" value="<?php echo htmlspecialchars($selected_student_id); ?>">
        </div>

        <div id="name_section" style="display: <?php echo $reg_user_type === 'student' ? 'none' : 'block'; ?>;">
            <div class="form-group">
                <label for="reg_first_name">First Name:</label>
                <input type="text" name="reg_first_name" id="reg_first_name" value="<?php echo htmlspecialchars($reg_first_name); ?>">
            </div>
            <div class="form-group">
                <label for="reg_last_name">Last Name:</label>
                <input type="text" name="reg_last_name" id="reg_last_name" value="<?php echo htmlspecialchars($reg_last_name); ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="reg_username">Username:</label>
            <input type="text" name="reg_username" id="reg_username" value="<?php echo htmlspecialchars($reg_username); ?>" required>
        </div>
        <div class="form-group">
            <label for="reg_password">Password:</label>
            <input type="password" name="reg_password" id="reg_password" required>
        </div>
        <div class="form-group">
            <label for="reg_confirm_password">Confirm Password:</label>
            <input type="password" name="reg_confirm_password" id="reg_confirm_password" required>
        </div>
        <div class="form-group full-width">
            <button type="submit" name="register" class="btn_submit">
                <i class="fa-solid fa-floppy-disk"></i> Register User
            </button>
        </div>
    </div>
</form>

<script>
    document.getElementById('reg_user_type').addEventListener('change', function() {
        const isStudent = this.value === 'student';
        document.getElementById('enrolled_student_section').style.display = isStudent ? 'block' : 'none';
        document.getElementById('name_section').style.display = isStudent ? 'none' : 'block';
        if (!isStudent) {
            document.getElementById('selected_student_id').value = '';
            document.getElementById('enrolled_student').selectedIndex = 0;
        }
    });

    document.getElementById('enrolled_student').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        document.getElementById('selected_student_id').value = this.value;
        console.log("Selected student_id: " + this.value);
    });
</script>

<?php 
include('../../layouts/footer.php'); 
?>