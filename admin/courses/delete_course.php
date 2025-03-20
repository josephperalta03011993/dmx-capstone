<?php
// Include the database connection file
require_once '../../database/conn.php'; // 
$success = null;
$error = null;

// Check if 'id' is passed in the query string
if (!isset($_GET['id'])) {
    $error = "No ID specified.";
    $_SESSION['error'] = $error;
    header("Location: manage_courses.php");
    exit;
}

// Sanitize the input to prevent SQL injection
$id = intval($_GET['id']);
if ($id <= 0) {
    $error = "Invalid ID provided.";
    $_SESSION['error'] = $error;
    header("Location: manage_courses.php");
    exit;
}

// Check for dependent enrollments
$check_query = "SELECT COUNT(*) as enrollment_count FROM enrollments WHERE course_id = ?";
if ($check_stmt = $conn->prepare($check_query)) {
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $row = $result->fetch_assoc();
    $enrollment_count = $row['enrollment_count'];
    $check_stmt->close();

    if ($enrollment_count > 0) {
        $error = "Cannot delete course with ID $id because it has $enrollment_count enrollment(s). Please remove or reassign these enrollments first.";
        $_SESSION['error'] = $error;
        header("Location: manage_courses.php");
        exit;
    }
} else {
    $error = "Error checking enrollments: " . $conn->error;
    $_SESSION['error'] = $error;
    header("Location: manage_courses.php");
    exit;
}

// Proceed with deletion if no enrollments
$query = "DELETE FROM courses WHERE course_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $success = "Course with ID $id has been deleted successfully.";
        } else {
            $error = "No course found with ID $id.";
        }
    } else {
        $error = "Error executing query: " . $stmt->error;
    }
    $stmt->close();
} else {
    $error = "Error preparing query: " . $conn->error;
}

// Close the database connection
$_SESSION['success'] = $success;
$_SESSION['error'] = $error;
$conn->close();

// Redirect back
header("Location: manage_courses.php");
exit;
?>