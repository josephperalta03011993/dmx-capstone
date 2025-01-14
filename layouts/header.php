<?php

// Function to check if the user is logged in
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

// Function to get the current user's type
function get_user_type() {
    return isset($_SESSION["user_type"]) ? $_SESSION["user_type"] : null;
}

// Function to get the username
function get_username(){
    return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : "Default Title"; ?></title>
    <script src="../../scripts/script.js"></script>
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital@0;1&display=swap" rel="stylesheet">
</head>
<body>
    <header class="bg-primary-color">
        <div class="header-school-name">
            <img src="../../images/dcsa.webp" alt="logo" width="45" height="40" id="img-logo">
            <h1 class="text-white">
                Datamex College of Saint Adeline
            </h1>
        </div>
        
        <div class="user-info">
            <a href="../../logout.php" class="bg-white p-8 btn">Logout</a>
        </div>
    </header>
    <main>
        <nav class="sidebar">
            <ul>
                <li>
                    <div class="user-info">
                        <?php if(is_logged_in()): ?>
                            <strong><span>Welcome, <?php echo get_username(); ?>!</span></strong>
                        <?php endif; ?>
                    </div>
                    <hr>
                </li>
                <li><a href="../dashboard/index.php">Dashboard</a></li>
                <li><a href="#">Users</a>
                    <ul>
                        <li><a href="../users/register.php">Create New User</a></li> 
                        <li><a href="manage_admins.php">Manage Admins</a></li>
                        <li><a href="manage_registrars.php">Manage Registrars</a></li>
                        <li><a href="manage_teachers.php">Manage Teachers</a></li>
                        <li><a href="manage_students.php">Manage Students</a></li>
                    </ul>
                </li>

                <li><a href="#">Courses</a>
                    <ul> <li><a href="manage_courses.php">Manage Courses</a></li>
                        <li><a href="course_scheduling.php">Course Scheduling</a></li>
                    </ul>
                </li>

                <li><a href="#">Enrollment</a>
                    <ul>
                        <li><a href="manage_enrollments.php">Manage Enrollments</a></li>
                        <li><a href="enrollment_reports.php">Enrollment Reports</a></li>
                    </ul>
                </li>

                <li><a href="#">Academic Records</a>
                    <ul>
                        <li><a href="grades_management.php">Grades Management</a></li>
                        <li><a href="transcript_generation.php">Transcript Generation</a></li>
                    </ul>
                </li>

                <li><a href="#">Settings</a>
                    <ul>
                        <li><a href="general_settings.php">General Settings</a></li>
                        <li><a href="academic_terms.php">Academic Terms</a></li>
                        <li><a href="email_settings.php">Email Settings</a></li>
                    </ul>
                </li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="logs.php">Logs</a></li>
            </ul>
        </nav>
        <div class="content">

