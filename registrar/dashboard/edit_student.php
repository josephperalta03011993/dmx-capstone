<?php
$page_title = "Edit Student";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;

// Fetch student data for editing
if (isset($_GET['id'])) {
    $student_id = sanitize_input($conn, $_GET['id']);
    $student_sql = "SELECT * FROM students WHERE student_id = ?";
    $student_stmt = mysqli_prepare($conn, $student_sql);
    mysqli_stmt_bind_param($student_stmt, "i", $student_id);
    mysqli_stmt_execute($student_stmt);
    $student_result = mysqli_stmt_get_result($student_stmt);
    if ($student_row = mysqli_fetch_assoc($student_result)) {
        // Populate variables with student data
        $first_name = $student_row['first_name'];
        $last_name = $student_row['last_name'];
        $middle_name = $student_row['middle_name'];
        $date_of_birth = $student_row['date_of_birth'];
        $gender = $student_row['gender'];
        $email = $student_row['email'];
        $phone_number = $student_row['phone_number'];
        $address_line1 = $student_row['address_line1'];
        $address_line2 = $student_row['address_line2'];
        $city = $student_row['city'];
        $state = $student_row['state'];
        $zip_code = $student_row['zip_code'];
        $country = $student_row['country'];
        $parent_guardian_name = $student_row['parent_guardian_name'];
        $parent_guardian_phone = $student_row['parent_guardian_phone'];
        $parent_guardian_email = $student_row['parent_guardian_email'];
        $emergency_contact_name = $student_row['emergency_contact_name'];
        $emergency_contact_phone = $student_row['emergency_contact_phone'];

    } else {
        $error = "Student not found.";
    }
} else {
    $error = "Student ID not provided.";
}

// Update student data
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_student"])) {
    // Sanitize and validate input
    $first_name = sanitize_input($conn, $_POST['first_name']);
    $last_name = sanitize_input($conn, $_POST['last_name']);
    $middle_name = sanitize_input($conn, $_POST['middle_name']);
    $date_of_birth = sanitize_input($conn, $_POST['date_of_birth']);
    $gender = sanitize_input($conn, $_POST['gender']);
    $email = sanitize_input($conn, $_POST['email']);
    $phone_number = sanitize_input($conn, $_POST['phone_number']);
    $address_line1 = sanitize_input($conn, $_POST['address_line1']);
    $address_line2 = sanitize_input($conn, $_POST['address_line2']);
    $city = sanitize_input($conn, $_POST['city']);
    $state = sanitize_input($conn, $_POST['state']);
    $zip_code = sanitize_input($conn, $_POST['zip_code']);
    $country = sanitize_input($conn, $_POST['country']);
    $parent_guardian_name = sanitize_input($conn, $_POST['parent_guardian_name']);
    $parent_guardian_phone = sanitize_input($conn, $_POST['parent_guardian_phone']);
    $parent_guardian_email = sanitize_input($conn, $_POST['parent_guardian_email']);
    $emergency_contact_name = sanitize_input($conn, $_POST['emergency_contact_name']);
    $emergency_contact_phone = sanitize_input($conn, $_POST['emergency_contact_phone']);

    $update_sql = "UPDATE students SET first_name = ?, last_name = ?, middle_name = ?, date_of_birth = ?, gender = ?, email = ?, phone_number = ?, address_line1 = ?, address_line2 = ?, city = ?, state = ?, zip_code = ?, country = ?, parent_guardian_name = ?, parent_guardian_phone = ?, parent_guardian_email = ?, emergency_contact_name = ?, emergency_contact_phone = ? WHERE student_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "sssssssssssssssssii", $first_name, $last_name, $middle_name, $date_of_birth, $gender, $email, $phone_number, $address_line1, $address_line2, $city, $state, $zip_code, $country, $parent_guardian_name, $parent_guardian_phone, $parent_guardian_email, $emergency_contact_name, $emergency_contact_phone, $student_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $success = "Student updated successfully!";
    } else {
        $error = "Error updating student: " . mysqli_error($conn);
    }
}
?>

<h2>Edit Student</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="post" action="edit_student.php?id=<?php echo $student_id; ?>">
    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">

    <div class="form-container">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>"><br>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>"><br>
        </div>

        <div class="form-group">
            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($middle_name); ?>"><br>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo htmlspecialchars($date_of_birth); ?>"><br>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
            </select><br>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>"><br>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>"><br>
        </div>

        <div class="form-group">
            <label for="address_line1">Address Line 1:</label>
            <input type="text" id="address_line1" name="address_line1" value="<?php echo htmlspecialchars($address_line1); ?>"><br>
        </div>

        <div class="form-group">
            <label for="address_line2">Address Line 2:</label>
            <input type="text" id="address_line2" name="address_line2" value="<?php echo htmlspecialchars($address_line2); ?>"><br>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>"><br>
        </div>

        <div class="form-group">
            <label for="state">Barangay:</label>
            <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($state); ?>"><br>
        </div>

        <div class="form-group">
            <label for="zip_code">Zip Code:</label>
            <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($zip_code); ?>"><br>
        </div>

        <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($country); ?>"><br>
        </div>

        <div class="form-group">
            <label for="parent_guardian_name">Parent/Guardian Name:</label>
            <input type="text" id="parent_guardian_name" name="parent_guardian_name" value="<?php echo htmlspecialchars($parent_guardian_name); ?>"><br>
        </div>

        <div class="form-group">
            <label for="parent_guardian_phone">Parent/Guardian Phone:</label>
            <input type="text" id="parent_guardian_phone" name="parent_guardian_phone" value="<?php echo htmlspecialchars($parent_guardian_phone); ?>"><br>
        </div>

        <div class="form-group">
            <label for="parent_guardian_email">Parent/Guardian Email:</label>
            <input type="email" id="parent_guardian_email" name="parent_guardian_email" value="<?php echo htmlspecialchars($parent_guardian_email); ?>"><br>
        </div>

        <div class="form-group">
            <label for="emergency_contact_name">Emergency Contact Name:</label>
            <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo htmlspecialchars($emergency_contact_name); ?>"><br>
        </div>

        <div class="form-group">
            <label for="emergency_contact_phone">Emergency Contact Phone:</label>
            <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo htmlspecialchars($emergency_contact_phone); ?>"><br>
        </div>

        <input type="submit" name="update_student" value="Update Student">
    </div>
</form>

<?php include('../../layouts/footer.php'); ?>