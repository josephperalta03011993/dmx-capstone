<?php
$page_title = "Add Student";
include_once('database/conn.php');

// HEADER
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

function get_username(){
    return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
}

function get_fullname(){
    $first_name = isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : null;
    $last_name = isset($_SESSION["last_name"]) ? $_SESSION["last_name"] : null;
    return $first_name . " " . $last_name;
}

$user_type = get_user_type();

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
    $level = sanitize_input($conn, $_POST['school-level']);
    $strand = (isset($_POST['strand']) && !empty($_POST['strand'])) ? sanitize_input($conn, $_POST['strand']) : NULL;

    // Check for duplicate email
    $sql_check_email = "SELECT email FROM students WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $error = "Email already exists.";
    } else {
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

        $sql = "INSERT INTO students (first_name, last_name, middle_name, date_of_birth, gender, email, phone_number, address_line1, address_line2, city, state, zip_code, country, enrollment_date, parent_guardian_name, parent_guardian_phone, parent_guardian_email, emergency_contact_name, emergency_contact_phone, status, level, strand) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssssssssssssssss", $first_name, $last_name, $middle_name, $date_of_birth, $gender, $email, $phone_number, $address_line1, $address_line2, $city, $state, $zip_code, $country, $enrollment_date, $parent_guardian_name, $parent_guardian_phone, $parent_guardian_email, $emergency_contact_name, $emergency_contact_phone, $status, $level, $strand);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Student added successfully!";
        } else {
            $error = "Error adding student: " . mysqli_error($conn);
        }
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Default Title"; ?></title>
    <?php if($user_type != null) { ?>
        <script src="../../scripts/script.js"></script>
        <link rel="stylesheet" href="../../styles/style.css">
    <?php } else { ?>
        <script src="scripts/script.js"></script>
        <link rel="stylesheet" href="styles/style.css">
    <?php } ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital@0;1&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- data tables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
</head>
<body>
    <header class="bg-primary-color">
        <div class="header-school-name">
            <?php if($user_type != null) { ?>
                <img src="../../images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <?php } else { ?>
                <img src="images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <?php } ?>
            <h1 class="text-white">
                Datamex College of Saint Adeline
            </h1>
        </div>
        
        <div class="user-info">
            <?php if($user_type != null) { ?>
                <a href="../../logout.php" class="bg-white p-8 btn">Logout</a>
            <?php } ?>
        </div>
    </header>
    <main>
        <?php 
            $user_type = strtolower(get_user_type());
        ?>
        <div class="content">
        <h2>Reservation Form</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="post" action="enroll-now.php">
    <div class="form-container-reservation">
        <div class="form-group">
            <label for="school-level"><span style='color:red'>*</span> School Level:</label>
            <select id="school-level" name="school-level" required onchange="toggleStrands()">
                <option value="Kinder 1">Kinder 1</option>
                <option value="Kinder 2">Kinder 2</option>
                <option value="Grade 1">Grade 1</option>
                <option value="Grade 2">Grade 2</option>
                <option value="Grade 3">Grade 3</option>
                <option value="Grade 4">Grade 4</option>
                <option value="Grade 5">Grade 5</option>
                <option value="Grade 6">Grade 6</option>
                <option value="Grade 7">Grade 7</option>
                <option value="Grade 8">Grade 8</option>
                <option value="Grade 9">Grade 9</option>
                <option value="Grade 10">Grade 10</option>
                <option value="Grade 11">Grade 11</option>
                <option value="Grade 12">Grade 12</option>
                <option value="1st yr college">1st yr college</option>
                <option value="2nd yr college">2nd yr college</option>
                <option value="3rd yr college">3rd yr college</option>
                <option value="4th yr college">4th yr college</option>
            </select><br>
        </div>

        <div class="form-group" id="strands-group" style="display: none;">
            <label for="strand"><span style='color:red'>*</span> Strand:</label>
            <select id="strand" name="strand">
                <option value="N/A" disabled selected>N/A</option>
                <option value="STEM">Science, Technology, Engineering, and Mathematics (STEM)</option>
                <option value="HUMSS">Humanities and Social Sciences (HUMSS)</option>
                <option value="GAS">General Academic Strand (GAS)</option>
                <option value="ABM">Accountancy, Business and Management (ABM)</option>
                <option value="ICT">Information and Communication Technology (ICT)</option>
                <option value="HE">Home Economics</option>
            </select><br>
        </div>

        <div class="form-group">
            <label for="first_name"><span style='color:red'>*</span> First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br>
        </div>

        <div class="form-group">
            <label for="last_name"><span style='color:red'>*</span> Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br>
        </div>

        <div class="form-group">
            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name"><br>
        </div>

        <div class="form-group">
            <label for="date_of_birth"><span style='color:red'>*</span> Date of Birth:</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required><br>
        </div>

        <div class="form-group">
            <label for="gender"><span style='color:red'>*</span> Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select><br>
        </div>

        <div class="form-group">
            <label for="email"><span style='color:red'>*</span> Email:</label>
            <input type="email" id="email" name="email" required><br>
        </div>

        <div class="form-group">
            <label for="phone_number"><span style='color:red'>*</span> Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required><br>
        </div>

        <div class="form-group">
            <label for="address_line1"><span style='color:red'>*</span> Address Line 1:</label>
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
    </div>
    <input type="submit" name="enroll-now" value="RESERVE SLOT">
</form>

<script>
function toggleStrands() {
    const schoolLevel = document.getElementById('school-level').value;
    const strandsGroup = document.getElementById('strands-group');
    
    // Show strands only for Grade 11 and Grade 12
    if (schoolLevel === 'Grade 11' || schoolLevel === 'Grade 12') {
        strandsGroup.style.display = 'block';
        document.getElementById('strand').setAttribute('required', 'required');
    } else {
        strandsGroup.style.display = 'none';
        document.getElementById('strand').removeAttribute('required');
    }
}

// Call the function on page load to set initial state
document.addEventListener('DOMContentLoaded', function() {
    toggleStrands();
});
</script>

<?php include('layouts/footer.php'); ?>

</body>
</html>