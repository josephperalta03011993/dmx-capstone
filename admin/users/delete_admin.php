<?php
// Include the database connection file
require_once '../../database/conn.php'; // 
$success = null;
$error = null;

// Check if 'id' is passed in the query string
if (isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $id = intval($_GET['id']);

    if ($id > 0) {
        // Prepare the SQL query to delete the user
        $query = "DELETE FROM users WHERE user_id = ?";

        if ($stmt = $conn->prepare($query)) {
            // Bind the parameter and execute the statement
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                // Check if a row was deleted
                if ($stmt->affected_rows > 0) {
                    $success = "User has been deleted successfully.";
                } else {
                    $error = "No user found with ID $id.";
                }
            } else {
                $error = "Error executing query: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            $error = "Error preparing query: " . $conn->error;
        }
    } else {
        $error = "Invalid ID provided.";
    }
} else {
    $error = "No ID specified.";
}

// Close the database connection
$_SESSION['success'] = $success;
$_SESSION['error'] = $error;
$conn->close();

// Redirect back 
header("Location: manage_admins.php");
exit;
?>
