<?php
$page_title = "Manage Students";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;
$status = isset($_GET['status']) ? $_GET['status'] : ''; // Check if 'status' exists, otherwise default to an empty string.

// Update Student
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_student"])) {
    $student_id = sanitize_input($conn, $_POST["student_id"]);
    $first_name = sanitize_input($conn, $_POST["first_name"]);
    $last_name = sanitize_input($conn, $_POST["last_name"]);
    $middle_name = sanitize_input($conn, $_POST["middle_name"]);
    $date_of_birth = sanitize_input($conn, $_POST["date_of_birth"]);
    $gender = sanitize_input($conn, $_POST["gender"]);
    $email = sanitize_input($conn, $_POST["email"]);
    $phone_number = sanitize_input($conn, $_POST["phone_number"]);
    $address_line1 = sanitize_input($conn, $_POST["address_line1"]);
    $address_line2 = sanitize_input($conn, $_POST["address_line2"]);
    $city = sanitize_input($conn, $_POST["city"]);
    $state = sanitize_input($conn, $_POST["state"]);
    $zip_code = sanitize_input($conn, $_POST["zip_code"]);
    $country = sanitize_input($conn, $_POST["country"]);
    $parent_guardian_name = sanitize_input($conn, $_POST["parent_guardian_name"]);
    $parent_guardian_phone = sanitize_input($conn, $_POST["parent_guardian_phone"]);
    $parent_guardian_email = sanitize_input($conn, $_POST["parent_guardian_email"]);
    $emergency_contact_name = sanitize_input($conn, $_POST["emergency_contact_name"]);
    $emergency_contact_phone = sanitize_input($conn, $_POST["emergency_contact_phone"]);

    $update_sql = "UPDATE students SET first_name=?, last_name=?, middle_name=?, date_of_birth=?, gender=?, email=?, phone_number=?, address_line1=?, address_line2=?, city=?, state=?, zip_code=?, country=?, parent_guardian_name=?, parent_guardian_phone=?, parent_guardian_email=?, emergency_contact_name=?, emergency_contact_phone=? WHERE student_id=?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ssssssssssssssssssi", $first_name, $last_name, $middle_name, $date_of_birth, $gender, $email, $phone_number, $address_line1, $address_line2, $city, $state, $zip_code, $country, $parent_guardian_name, $parent_guardian_phone, $parent_guardian_email, $emergency_contact_name, $emergency_contact_phone, $student_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $success = "Student updated successfully!";
    } else {
        $error = "Error updating student: " . mysqli_error($conn);
    }
}

// Admit Student
if (isset($_GET['admit_id'])) {
    $admit_id = sanitize_input($conn, $_GET['admit_id']);
    
    // Set timezone to Philippine time (Asia/Manila)
    date_default_timezone_set('Asia/Manila');
    
    // Get the current date
    $current_date = date('Ymd'); // e.g., 20250402
    
    // Find the next sequence number for today
    $seq_sql = "SELECT COUNT(*) + 1 AS next_seq FROM students WHERE student_num LIKE ?";
    $seq_stmt = mysqli_prepare($conn, $seq_sql);
    $like_pattern = $current_date . '%';
    mysqli_stmt_bind_param($seq_stmt, "s", $like_pattern);
    mysqli_stmt_execute($seq_stmt);
    mysqli_stmt_bind_result($seq_stmt, $next_seq);
    mysqli_stmt_fetch($seq_stmt);
    mysqli_stmt_close($seq_stmt);

    // Generate the student number
    $student_num = $current_date . str_pad($next_seq, 3, '0', STR_PAD_LEFT); // e.g., 20250402001
    
    // Update both status and student_num
    $admit_sql = "UPDATE students SET status = 'enrolled', student_num = ? WHERE student_id = ?";
    $admit_stmt = mysqli_prepare($conn, $admit_sql);
    mysqli_stmt_bind_param($admit_stmt, "si", $student_num, $admit_id);

    if (mysqli_stmt_execute($admit_stmt)) {
        $success = "Student admitted successfully with student number: " . htmlspecialchars($student_num);
    } else {
        $error = "Error admitting student: " . mysqli_error($conn);
        if (mysqli_errno($conn) == 1062) {
            $error = "Student number $student_num is already in use. Please try again.";
        }
    }
}

// Get Student Data
if($status == 'all' || $status == '') {
    $students_sql = "SELECT * FROM students";
    $students_result = $conn->query($students_sql);
} else {
    $students_sql = "SELECT * FROM students WHERE status = 'reserved'";
    $students_result = $conn->query($students_sql);
}

?>

<div class="page-header-title">
    <h2>Manage Students</h2>
    <a href="add_student.php" id="btn_add" name="btn_add"><i class="fa-solid fa-plus"></i> Add New Student</a>
</div>
<hr><br>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<div style="max-width: 1100px; overflow-x: auto;">
    <table id="myTable" class="display">
        <thead>
            <?php if ($students_result && $students_result->num_rows > 0) { ?>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Address Line 1</th>
                <th>Address Line 2</th>
                <th>City</th>
                <th>Barangay</th>
                <th>Zip Code</th>
                <th>Country</th>
                <th>Parent/Guardian Name</th>
                <th>Parent/Guardian Phone</th>
                <th>Parent/Guardian Email</th>
                <th>Emergency Contact Name</th>
                <th>Emergency Contact Phone</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php } ?>
        </thead>
        <tbody>
            <?php
            if ($students_result && $students_result->num_rows > 0) {
                while ($row = $students_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['middle_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date_of_birth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address_line1']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['address_line2']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['city']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['state']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['zip_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['parent_guardian_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['parent_guardian_phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['parent_guardian_email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['emergency_contact_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['emergency_contact_phone']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_student.php?id=" . $row['student_id'] . "' id='btn_edit'><i class='fa-solid fa-pen-to-square'></i> Edit</a>";
                    if($row['status'] == 'reserved'){
                        echo "<br><br><a href='?admit_id=" . $row['student_id'] . "' id='btn_admit'><i class='fa-solid fa-check'></i> Admit</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "No students found.";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>

<?php include('../../layouts/footer.php'); ?>