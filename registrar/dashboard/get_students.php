<?php
include('../../database/conn.php');

if (isset($_POST['section_id'])) {
    $section_id = sanitize_input($conn, $_POST['section_id']);
    
    // Query to get students enrolled in the section
    $students_sql = "SELECT s.student_id, s.first_name, s.last_name
                     FROM enrollments e
                     JOIN students s ON e.student_id = s.student_id
                     WHERE e.section_id = ?";
    
    $stmt = $conn->prepare($students_sql);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>Student ID</th><th>Full Name</th></tr></thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No students enrolled in this section.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}