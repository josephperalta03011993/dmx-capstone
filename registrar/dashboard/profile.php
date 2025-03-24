<?php 
$page_title = "Registrar Profile - Datamex College of Saint Adeline";
include_once('../../database/conn.php'); // Database connection (includes sanitize_input)
include('../../layouts/header.php'); // Header layout

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Get teacher data based on user_id
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM teachers WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo "<p class='error-message'>No profile found for this user.</p>";
    $conn->close();
    include('../../layouts/footer.php');
    exit();
}

// Get current username from the users table (assuming there's a users table)
$sql = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_username = $user['username'];

$stmt->close();
?>

<div class="content">
    <h2 class="page-header-title">Registrar Profile</h2>

    <!-- Message/Error Display -->
    <?php if (isset($_GET['message'])): ?>
        <div class="message success-message">
            <?php echo sanitize_input($conn, $_GET['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="message error-message">
            <?php echo sanitize_input($conn, $_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="profile-grid">
        <form action="update.php" method="POST">
            <!-- Hidden user_id field to identify the teacher -->
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <!-- Column 1: Personal Information -->
            <div class="profile-section">
                <h3>Personal Information</h3>
                <div class="form-container">
                    <div class="form-group">
                        <label>First Name:</label>
                        <input style="background-color: #f0f0f0; color: #6c757d; cursor: not-allowed" type="text" name="first_name" value="<?php echo sanitize_input($conn, $teacher['first_name']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Last Name:</label>
                        <input style="background-color: #f0f0f0; color: #6c757d; cursor: not-allowed" type="text" name="last_name" value="<?php echo sanitize_input($conn, $teacher['last_name']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Middle Name:</label>
                        <input style="background-color: #f0f0f0; color: #6c757d; cursor: not-allowed" type="text" name="middle_name" value="<?php echo sanitize_input($conn, $teacher['middle_name']) ?: 'N/A'; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth:</label>
                        <input type="date" name="date_of_birth" value="<?php echo sanitize_input($conn, $teacher['date_of_birth']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Gender:</label>
                        <select name="gender">
                            <option value="Male" <?php echo $teacher['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                            <option value="Female" <?php echo $teacher['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                            <option value="Other" <?php echo $teacher['gender'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Column 2: Contact Information -->
            <div class="profile-section">
                <h3>Contact Information</h3>
                <div class="form-container">
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" value="<?php echo sanitize_input($conn, $teacher['email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Phone Number:</label>
                        <input type="text" name="phone_number" value="<?php echo sanitize_input($conn, $teacher['phone_number']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Address Line 1:</label>
                        <input type="text" name="address_line1" value="<?php echo sanitize_input($conn, $teacher['address_line1']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Address Line 2:</label>
                        <input type="text" name="address_line2" value="<?php echo sanitize_input($conn, $teacher['address_line2']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>City:</label>
                        <input type="text" name="city" value="<?php echo sanitize_input($conn, $teacher['city']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>State:</label>
                        <input type="text" name="state" value="<?php echo sanitize_input($conn, $teacher['state']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Zip Code:</label>
                        <input type="text" name="zip_code" value="<?php echo sanitize_input($conn, $teacher['zip_code']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Country:</label>
                        <input type="text" name="country" value="<?php echo sanitize_input($conn, $teacher['country']) ?: ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Column 3: Additional Information -->
            <div class="profile-section">
                <h3>Emergency Contact</h3>
                <div class="form-container">
                    <div class="form-group">
                        <label>Emergency Contact Name:</label>
                        <input type="text" name="emergency_contact_name" value="<?php echo sanitize_input($conn, $teacher['emergency_contact_name']) ?: ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Emergency Contact Phone:</label>
                        <input type="text" name="emergency_contact_phone" value="<?php echo sanitize_input($conn, $teacher['emergency_contact_phone']) ?: ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Column 4: Account Settings -->
            <div class="profile-section">
                <h3>Account Settings</h3>
                <div class="form-container">
                    <div class="form-group">
                        <label>Current Username:</label>
                        <input style="cursor: not-allowed; background-color: #f0f0f0;" type="text" name="current_username" value="<?php echo sanitize_input($conn, $current_username); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label>New Username:</label>
                        <input type="text" name="new_username" placeholder="Enter new username (optional)">
                    </div>
                    <div class="form-group">
                        <label>New Password:</label>
                        <input type="password" name="new_password" placeholder="Enter new password (optional)">
                    </div>
                    <div class="form-group">
                        <label>Confirm Current Password:</label>
                        <input type="password" name="current_password" required placeholder="Enter current password">
                    </div>
                </div>

                <div class="form-group full-width">
                    <input type="submit" class="btn" value="Update Profile">
                </div>
            </div>
        </form>
    </div>
</div>

<?php 
$conn->close();
include('../../layouts/footer.php'); // Footer layout
?>