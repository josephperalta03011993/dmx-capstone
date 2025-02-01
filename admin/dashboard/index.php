<?php
$page_title = "Admin Dashboard";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Query to get the total number of students
$sql_students = "SELECT COUNT(*) AS total_students FROM users WHERE user_type = 'student'"; 
$result_students = $conn->query($sql_students);
$row_students = $result_students->fetch_assoc();
$total_students = $row_students['total_students'];

// Query to get the total number of teachers
$sql_teachers = "SELECT COUNT(*) AS total_teachers FROM users WHERE user_type = 'teacher'"; 
$result_teachers = $conn->query($sql_teachers);
$row_teachers = $result_teachers->fetch_assoc();
$total_teachers = $row_teachers['total_teachers'];

// Query to get the total number of registrars
$sql_registrars = "SELECT COUNT(*) AS total_registrars FROM users WHERE user_type = 'registrar'"; 
$result_registrars = $conn->query($sql_registrars);
$row_registrars = $result_registrars->fetch_assoc();
$total_registrars = $row_registrars['total_registrars'];

// Query to get the total number of courses
$sql_courses = "SELECT COUNT(*) AS total_courses FROM courses"; 
$result_courses = $conn->query($sql_courses);
$row_courses = $result_courses->fetch_assoc();
$total_courses = $row_courses['total_courses'];

?>

<h2>Admin Dashboard</h2>

<div id="container">
    <div>
        <h3><i class="fa-solid fa-graduation-cap"></i> Total Students: <?php echo $total_students; ?></h3>
    </div>
    <div>
        <h3><i class="fa-solid fa-chalkboard-user"></i> Total Teachers: <?php echo $total_teachers; ?></h3>
    </div>
    <div>
        <h3><i class="fa-solid fa-building"></i> Total Registrars: <?php echo $total_registrars; ?></h3>
    </div>
    <div>
        <h3><i class="fa-solid fa-book"></i> Total Courses: <?php echo $total_courses; ?></h3>
    </div>
</div>

<?php include('../../layouts/footer.php'); ?>