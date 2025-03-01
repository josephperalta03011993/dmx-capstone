<?php
$page_title = "Add Student";
include_once('database/conn.php');
include_once('layouts/header.php');

$success = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enroll-now"])) {
    // Sanitize and validate input
    $first_name = sanitize_input($conn, $_POST['first_name']);
    $last_name = sanitize_input($conn, $_POST['last_name']);
    $middle_name = sanitize_input($conn, $_POST['middle_name']);
    $date_of_birth = sanitize_input($conn, $_POST['date_of_birth']);
    $gender = sanitize_input($conn, $_POST['gender']);
    $email = sanitize_input($conn, $_POST['email']);
    // Check for duplicate email
    $sql_check_email = "SELECT email FROM students WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);

    if ($stmt->num_rows > 0) {
        echo "Email already exists.";
    }

    $phone_number = sanitize_input($conn, $_POST['phone_number']);
    $address_line1 = sanitize_input($conn, $_POST['address_line1']);
    $address_line2 = sanitize_input($conn, $_POST['address_line2']);
    $city = sanitize_input($conn, $_POST['city']);
    $state = sanitize_input($conn, $_POST['state']);
    $zip_code = sanitize_input($conn, $_POST['zip_code']);
    $country = sanitize_input($conn, $_POST['country']);
    $enrollment_date = date("Y-m-d");
    $parent_guardian_name = sanitize_input($conn, $_POST['parent_guardian_name']);
    $parent_guardian_phone = sanitize_input($conn, $_POST['parent_guardian_phone']);
    $parent_guardian_email = sanitize_input($conn, $_POST['parent_guardian_email']);
    $emergency_contact_name = sanitize_input($conn, $_POST['emergency_contact_name']);
    $emergency_contact_phone = sanitize_input($conn, $_POST['emergency_contact_phone']);
    $status = "reserved";

    $sql = "INSERT INTO students (first_name, last_name, middle_name, date_of_birth, gender, email, phone_number, address_line1, address_line2, city, state, zip_code, country, enrollment_date, parent_guardian_name, parent_guardian_phone, parent_guardian_email, emergency_contact_name, emergency_contact_phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssssssssssss", $first_name, $last_name, $middle_name, $date_of_birth, $gender, $email, $phone_number, $address_line1, $address_line2, $city, $state, $zip_code, $country, $enrollment_date, $parent_guardian_name, $parent_guardian_phone, $parent_guardian_email, $emergency_contact_name, $emergency_contact_phone, $status);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Student added successfully!";
    } else {
        $error = "Error adding student: " . mysqli_error($conn);
    }
}
?>

<h2>Reservation Form</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="post" action="enroll-now.php">
    <div class="form-container-reservation">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br>
        </div>

        <div class="form-group">
            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name"><br>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required><br>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select><br>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br>
        </div>

        <div class="form-group">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required><br>
        </div>

        <div class="form-group">
            <label for="address_line1">Address Line 1:</label>
            <input type="text" id="address_line1" name="address_line1" required><br>
        </div>

        <div class="form-group">
            <label for="address_line2">Address Line 2:</label>
            <input type="text" id="address_line2" name="address_line2"><br>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" id="city" name="city"><br>
        </div>

        <div class="form-group">
            <label for="state">Barangay:</label>
            <input type="text" id="state" name="state"><br>
        </div>

        <div class="form-group">
            <label for="zip_code">Zip Code:</label>
            <input type="text" id="zip_code" name="zip_code"><br>
        </div>

        <div class="form-group">
            <label for="country">Country:</label>
            <input type="text" id="country" name="country"><br>
        </div>

        <div class="form-group">
            <label for="parent_guardian_name">Parent/Guardian Name:</label>
            <input type="text" id="parent_guardian_name" name="parent_guardian_name"><br>
        </div>

        <div class="form-group">
            <label for="parent_guardian_phone">Parent/Guardian Phone:</label>
            <input type="text" id="parent_guardian_phone" name="parent_guardian_phone"><br>
        </div>

        <div class="form-group">
            <label for="parent_guardian_email">Parent/Guardian Email:</label>
            <input type="email" id="parent_guardian_email" name="parent_guardian_email"><br>
        </div>

        <div class="form-group">
            <label for="emergency_contact_name">Emergency Contact Name:</label>
            <input type="text" id="emergency_contact_name" name="emergency_contact_name"><br>
        </div>

        <div class="form-group">
            <label for="emergency_contact_phone">Emergency Contact Phone:</label>
            <input type="text" id="emergency_contact_phone" name="emergency_contact_phone"><br>
        </div>

        <input type="submit" name="enroll-now" value="RESERVE SLOT">
    </div>
</form>

<?php include('layouts/footer.php'); ?>