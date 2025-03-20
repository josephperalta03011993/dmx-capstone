<?php
include_once('database/conn.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = sanitize_input($conn, $_POST['message']);
    $input = strtolower(trim($input));

    // Check for keyword matches
    $sql = "SELECT response FROM chatbot_responses WHERE ? LIKE CONCAT('%', LOWER(keyword), '%')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response = $row['response'];
    } else {
        $response = "Sorry, I don’t understand that. Try 'enrollment', 'password', or 'contact'.";
    }

    echo json_encode(['reply' => $response]);
    $stmt->close();
}
$conn->close();
?>