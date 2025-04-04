<?php
// Include the database connection file
require_once '../../database/conn.php';
$success = null;
$error = null;

// Check if 'id' is passed in the query string
if (isset($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $id = intval($_GET['id']);

    if ($id > 0) {
        // Check if the section is being used in the 'grades' table
        $check_grades_query = "SELECT COUNT(*) FROM grades WHERE section_id = ?";
        if ($stmt_check = $conn->prepare($check_grades_query)) {
            $stmt_check->bind_param('i', $id);
            $stmt_check->execute();
            $stmt_check->bind_result($grade_count);
            $stmt_check->fetch();
            $stmt_check->close();

            if ($grade_count > 0) {
                $error = "This section cannot be deleted as it is currently being used in the Grades table.";
            } else {
                // Delete schedule related to section
                $delete_schedules = "DELETE FROM schedules WHERE section_id = ?";
                if ($stmt1 = $conn->prepare($delete_schedules)) {
                    $stmt1->bind_param('i', $id);
                    $stmt1->execute();
                    $stmt1->close();
                }

                // Prepare the SQL query to delete the section
                $query = "DELETE FROM sections WHERE section_id = ?";

                if ($stmt = $conn->prepare($query)) {
                    // Bind the parameter and execute the statement
                    $stmt->bind_param('i', $id);

                    if ($stmt->execute()) {
                        // Check if a row was deleted
                        if ($stmt->affected_rows > 0) {
                            $success = "Section has been deleted successfully.";
                        } else {
                            $error = "No Section found with ID $id.";
                        }
                    } else {
                        // Check for foreign key constraint error specifically
                        if ($conn->errno == 1451) {
                            $error = "This section cannot be deleted as it is currently being used in other tables (e.g., Grades).";
                        } else {
                            $error = "Error executing query: " . $stmt->error;
                        }
                    }

                    // Close the statement
                    $stmt->close();
                } else {
                    $error = "Error preparing query: " . $conn->error;
                }
            }
        } else {
            $error = "Error preparing query to check grades: " . $conn->error;
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
header("Location: manage_sections.php");
exit;
?>