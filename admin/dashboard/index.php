<?php
$page_title = "Admin Dashboard";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Query to get the total number of students
$sql_students = "SELECT COUNT(*) AS total_students FROM students"; 
$result_students = $conn->query($sql_students);
$row_students = $result_students->fetch_assoc();
$total_students = $row_students['total_students'];

// Query to get the total number of teachers
$sql_teachers = "SELECT COUNT(*) AS total_teachers FROM teachers"; 
$result_teachers = $conn->query($sql_teachers);
$row_teachers = $result_teachers->fetch_assoc();
$total_teachers = $row_teachers['total_teachers'];

// Query to get the total number of registrars
$sql_registrars = "SELECT COUNT(*) AS total_registrars FROM registrars"; 
$result_registrars = $conn->query($sql_registrars);
$row_registrars = $result_registrars->fetch_assoc();
$total_registrars = $row_registrars['total_registrars'];

// Query to get the total number of courses
$sql_courses = "SELECT COUNT(*) AS total_courses FROM courses"; 
$result_courses = $conn->query($sql_courses);
$row_courses = $result_courses->fetch_assoc();
$total_courses = $row_courses['total_courses'];

// Query to get the total number of sections
$sql_sections = "SELECT COUNT(section_id) AS total_sections FROM sections";
$result_sections = $conn->query($sql_sections);
$row_sections = $result_sections->fetch_assoc();
$total_sections = $row_sections['total_sections'];

// Query to get the total number of rooms
$sql_rooms = "SELECT COUNT(room_id) AS total_rooms FROM rooms";
$result_registrars = $conn->query($sql_rooms);
$row_rooms = $result_registrars->fetch_assoc();
$total_rooms = $row_rooms['total_rooms'];

// Query to get the total depertments
$sql_department = "SELECT COUNT(department_id) AS total_department FROM departments";
$result_department = $conn->query($sql_department);
$row_department = $result_department->fetch_assoc();
$total_department = $row_department['total_department'];

// Query to get the total enrollment
$sql_enrollment = "SELECT COUNT(enrollment_id) FROM enrollments";
$result_enrollment = $conn->query($sql_enrollment);
$row_enrollment = $result_enrollment->fetch_assoc();
$total_enrollment = $row_enrollment['COUNT(enrollment_id)'];

// Query to get the total schedule
$sql_schedule = "SELECT COUNT(schedule_id) FROM schedules";
$result_schedule = $conn->query($sql_schedule);
$row_schedule = $result_schedule->fetch_assoc();
$total_schedule = $row_schedule['COUNT(schedule_id)'];
?>

<h2>Admin Dashboard</h2>

<div id="container">
    <div>
        <a href="students-list.php">
            <h3><i class="fa-solid fa-graduation-cap"></i> Total Students: <?php echo $total_students; ?></h3>
        </a>
    </div>
    <div>
        <a href="teacher-list.php">
            <h3><i class="fa-solid fa-chalkboard-user"></i> Total Teachers: <?php echo $total_teachers; ?></h3>
        </a>
    </div>
    <div>
        <a href="registrar-list.php">
            <h3><i class="fa-solid fa-building"></i> Total Registrars: <?php echo $total_registrars; ?></h3>
        </a>
    </div>
    <div>
        <a href="../courses/manage_courses.php">
            <h3><i class="fa-solid fa-book"></i> Total Courses: <?php echo $total_courses; ?></h3>
        </a>
    </div>
    <div>
        <a href="../sections/manage_sections.php" class="admin-dashboard-link">
            <h3><i class="fa-solid fa-people-line"></i> Total Sections: <?php echo $total_sections; ?></h3>
        </a>
    </div>
    <div>
        <a href="../rooms/manage_rooms.php" class="admin-dashboard-link">
            <h3><i class="fa-solid fa-door-open"></i> Total Rooms: <?php echo $total_rooms; ?></h3>
        </a>
    </div>
    <div>
        <a href="../departments/manage_departments.php" class="admin-dashboard-link">
            <h3><i class="fa-solid fa-door-open"></i> Total Department: <?php echo $total_department; ?></h3>
        </a>
    </div>
    <div>
        <a href="enrollment-list.php">
            <h3><i class="fa-solid fa-school"></i> Total Enrollment: <?php echo $total_enrollment; ?></h3>
        </a>
    </div>
    <div>
        <a href="../courses/course_scheduling.php">
            <h3><i class="fa-solid fa-calendar"></i> Total Schedule: <?php echo $total_schedule; ?></h3>
        </a>
    </div>
</div>

<?php include('../../layouts/footer.php'); ?>