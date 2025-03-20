<?php 
$page_title = "Student Profile - Datamex College of Saint Adeline";
include_once('../../database/conn.php'); // Database connection (includes sanitize_input)
include('../../layouts/header.php'); // Header layout

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Get student data based on user_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<p class='error-message'>No profile found for this user.</p>";
    $conn->close();
    include('../../layouts/footer.php');
    exit();
}

$stmt->close();
?>

<div class="content">
    <h2 class="page-header-title">Student Profile</h2>
    <div class="card profile-grid">
        <!-- Column 1: Personal Information -->
        <div class="profile-section">
            <h3>Personal Information</h3>
            <div class="form-container">
                <div class="form-group">
                    <label>First Name:</label>
                    <p><?php echo sanitize_input($conn, $student['first_name']); ?></p>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <p><?php echo sanitize_input($conn, $student['last_name']); ?></p>
                </div>
                <div class="form-group">
                    <label>Middle Name:</label>
                    <p><?php echo sanitize_input($conn, $student['middle_name']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <p><?php echo sanitize_input($conn, $student['date_of_birth']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <p><?php echo sanitize_input($conn, $student['gender']) ?: 'N/A'; ?></p>
                </div>
            </div>
        </div>

        <!-- Column 2: Contact Information -->
        <div class="profile-section">
            <h3>Contact Information</h3>
            <div class="form-container">
                <div class="form-group">
                    <label>Email:</label>
                    <p><?php echo sanitize_input($conn, $student['email']); ?></p>
                </div>
                <div class="form-group">
                    <label>Phone Number:</label>
                    <p><?php echo sanitize_input($conn, $student['phone_number']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Address Line 1:</label>
                    <p><?php echo sanitize_input($conn, $student['address_line1']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Address Line 2:</label>
                    <p><?php echo sanitize_input($conn, $student['address_line2']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>City:</label>
                    <p><?php echo sanitize_input($conn, $student['city']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>State:</label>
                    <p><?php echo sanitize_input($conn, $student['state']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Zip Code:</label>
                    <p><?php echo sanitize_input($conn, $student['zip_code']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Country:</label>
                    <p><?php echo sanitize_input($conn, $student['country']) ?: 'N/A'; ?></p>
                </div>
            </div>
        </div>

        <!-- Column 3: Additional Information -->
        <div class="profile-section">
            <h3>Guardian Information</h3>
            <div class="form-container">
                <div class="form-group">
                    <label>Parent/Guardian Name:</label>
                    <p><?php echo sanitize_input($conn, $student['parent_guardian_name']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Parent/Guardian Phone:</label>
                    <p><?php echo sanitize_input($conn, $student['parent_guardian_phone']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Parent/Guardian Email:</label>
                    <p><?php echo sanitize_input($conn, $student['parent_guardian_email']) ?: 'N/A'; ?></p>
                </div>
            </div>

            <h3>Emergency Contact</h3>
            <div class="form-container">
                <div class="form-group">
                    <label>Emergency Contact Name:</label>
                    <p><?php echo sanitize_input($conn, $student['emergency_contact_name']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Emergency Contact Phone:</label>
                    <p><?php echo sanitize_input($conn, $student['emergency_contact_phone']) ?: 'N/A'; ?></p>
                </div>
            </div>

            <h3>Enrollment Details</h3>
            <div class="form-container">
                <div class="form-group">
                    <label>Enrollment Date:</label>
                    <p><?php echo sanitize_input($conn, $student['enrollment_date']) ?: 'N/A'; ?></p>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <p><?php echo sanitize_input($conn, $student['status']) ?: 'N/A'; ?></p>
                </div>
            </div>

            <!-- <div class="form-group full-width">
                <a href="edit_profile.php" class="btn">Edit Profile</a>
            </div> -->
        </div>
    </div>
</div>

<?php 
$conn->close();
include('../../layouts/footer.php'); // Footer layout
?>