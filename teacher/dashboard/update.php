<?php
include_once('../../database/conn.php'); // Database connection (includes sanitize_input)

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['user_id'];
    
    // Verify current password
    $current_password = sanitize_input($conn, $_POST['current_password']);
    $sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!password_verify($current_password, $user['password'])) {
        header("Location: profile.php?error=Incorrect current password");
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Fields that can be updated (excluding name fields)
    $fields = [
        'date_of_birth' => sanitize_input($conn, $_POST['date_of_birth']),
        'gender' => sanitize_input($conn, $_POST['gender']),
        'email' => sanitize_input($conn, $_POST['email']),
        'phone_number' => sanitize_input($conn, $_POST['phone_number']),
        'address_line1' => sanitize_input($conn, $_POST['address_line1']),
        'address_line2' => sanitize_input($conn, $_POST['address_line2']),
        'city' => sanitize_input($conn, $_POST['city']),
        'state' => sanitize_input($conn, $_POST['state']),
        'zip_code' => sanitize_input($conn, $_POST['zip_code']),
        'country' => sanitize_input($conn, $_POST['country']),
        'parent_guardian_name' => sanitize_input($conn, $_POST['parent_guardian_name']),
        'parent_guardian_phone' => sanitize_input($conn, $_POST['parent_guardian_phone']),
        'parent_guardian_email' => sanitize_input($conn, $_POST['parent_guardian_email']),
        'emergency_contact_name' => sanitize_input($conn, $_POST['emergency_contact_name']),
        'emergency_contact_phone' => sanitize_input($conn, $_POST['emergency_contact_phone']),
        'enrollment_date' => sanitize_input($conn, $_POST['enrollment_date'])
    ];

    // Build the SQL UPDATE query for teachers table
    $set_clause = [];
    $params = [];
    $types = '';

    foreach ($fields as $field => $value) {
        if ($value !== '') { // Only update non-empty fields
            $set_clause[] = "$field = ?";
            $params[] = $value;
            $types .= 's'; // Assuming all fields are strings; adjust if needed
        }
    }

    $updates_made = false;

    // Update teachers table if there are changes
    if (!empty($set_clause)) {
        $sql = "UPDATE teachers SET " . implode(', ', $set_clause) . " WHERE user_id = ?";
        $params[] = $user_id;
        $types .= 'i'; // user_id is an integer

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $updates_made = true;
        }
        $stmt->close();
    }

    // Handle username and password updates (users table)
    $new_username = sanitize_input($conn, $_POST['new_username']);
    $new_password = sanitize_input($conn, $_POST['new_password']);

    if (!empty($new_username) || !empty($new_password)) {
        $user_set_clause = [];
        $user_params = [];
        $user_types = '';

        if (!empty($new_username)) {
            $user_set_clause[] = "username = ?";
            $user_params[] = $new_username;
            $user_types .= 's';
        }
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $user_set_clause[] = "password = ?";
            $user_params[] = $hashed_password;
            $user_types .= 's';
        }

        $user_params[] = $user_id;
        $user_types .= 'i';

        $sql = "UPDATE users SET " . implode(', ', $user_set_clause) . " WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($user_types, ...$user_params);
        
        if ($stmt->execute()) {
            $updates_made = true;
        }
        $stmt->close();
    }

    if ($updates_made) {
        header("Location: profile.php?message=Profile updated successfully");
    } else {
        header("Location: profile.php?message=No changes made");
    }
}

$conn->close();
?>