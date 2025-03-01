<?php
    $page_title = "Registrar Dashboard";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    // Query to get the total number of students
    $sql_students = "SELECT COUNT(*) AS total_students FROM students WHERE status = 'reserved'"; 
    $result_students = $conn->query($sql_students);
    $row_students = $result_students->fetch_assoc();
    $total_students = $row_students['total_students'];
?>

<h2>Registrar Dashboard</h2>

<div id="container">
    <div id="total-reservation-container">
        <h3><i class="fa-solid fa-graduation-cap"></i> <a href="manage_students.php?status=reserved" id="total-reservation-link">Total Reservation: </a><?php echo $total_students; ?></h3>
    </div>
</div>

<?php include('../../layouts/footer.php'); ?>