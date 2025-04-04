<nav class="sidebar">
    <ul>
        <li>
            <div class="user-info">
                <?php if(is_logged_in()): ?>
                    <strong><span>Welcome, <?php echo get_fullname(); ?>!</span></strong>
                <?php endif; ?>
            </div>
            <div>
            <?php 
                $user_type = get_user_type();
                if($user_type == 'student') {
                    ?>
                        <p><?php echo 'Student Num: '. get_student_num(); ?></p>
                    <?php
                }
            ?>
            </div>
            <hr>
        </li>
        <?php
            $user_type = get_user_type();
            if($user_type == 'admin') { 
        ?>
        <li><a href="../dashboard/index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>

        <li><a href="../announcement/manage_announcement.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
        
        <li><a href="../gallery/add_gallery.php"><i class="fa-solid fa-image"></i> Gallery</a></li>

        <li><a href="#"><i class="fa-solid fa-user"></i> Users</a>
            <ul>
                <li><a href="../users/register.php">Create New User</a></li> 
                <li><a href="../users/manage_admins.php">Manage Users</a></li>
            </ul>
        </li>

        <li><a href="#"><i class="fa-solid fa-book"></i> Courses</a>
            <ul> 
                <li><a href="../courses/manage_courses.php">Manage Courses</a></li>
                <li><a href="../courses/course_scheduling.php">Course Scheduling</a></li>
            </ul>
        </li>

        <li><a href="../departments/manage_departments.php"><i class="fa-solid fa-building-user"></i> Departments</a>
        </li>

        <li><a href="#"><i class="fa-solid fa-people-line"></i> Sections</a>
            <ul>
                <li>
                    <a href="../sections/manage_sections.php"><i class="fa-solid fa-people-line"></i> Manage Sections</a>
                </li>
                <li>
                    <a href="../sections/teacher_assignments.php"><i class="fa-solid fa-chalkboard-user"></i> Assign Teacher</a>
                </li>
                <li><a href="../sections/assign_course_section.php"><i class="fa-solid fa-book-open-reader"></i> Assign Course</a></li>
            </ul>
        </li>

        <li><a href="../rooms/manage_rooms.php"><i class="fa-solid fa-door-open"></i> Rooms</a>
        </li>
        
        <li><a href="../contact/index.php"><i class="fa-solid fa-phone"></i> Contact</a></li>

        <li><a href="../profile/index.php"><i class="fa-solid fa-user"></i> Profile</a></li>

        <?php } else if($user_type == 'registrar') { ?>
            <li><a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>

            <li><a href="#"><i class="fa-solid fa-person"></i> Students</a>
                <ul>
                    <li><a href="manage_students.php?status=all">Manage Students</a></li>
                </ul>
            </li>

            <li><a href="#"><i class="fa-solid fa-school"></i> Enrollment</a>
                <ul>
                    <li><a href="manage_enrollments.php">Manage Enrollments</a></li>
                    <li><a href="enrollment_reports.php">Enrollment Reports</a></li>
                </ul>
            </li>

            <li><a href="manage_payments.php"><i class="fa-solid fa-file"></i> Manage Payments</a></li>
        
        <?php } else if($user_type == 'teacher') {?>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="classes.php"><i class="fa-solid fa-list"></i> View Class List</a></li>
            <li><a href="class_schedule.php"><i class="fa-solid fa-calendar-days"></i> Class Schedule</a></li>
            <!-- <li>
                <a href="#"><i class="fa-solid fa-file"></i> Manage Grades</a>
            </li> -->
        
        <?php } else if($user_type == 'student') { ?>
            <li><a href="index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
            <li><a href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
            <li><a href="announcement.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
            <li><a href="grades.php"><i class="fa-solid fa-file"></i> View Grades</a></li>
            <li><a href="schedule.php"><i class="fa-solid fa-calendar"></i> View Schedules</a></li>
            <li><a href="balance.php"><i class="fa-solid fa-money-bill"></i> Balance</a></li>
            <li><a href="enroll.php"><i class="fa-solid fa-school"></i> Enroll</a></li>
        <?php } else {
            echo "Unknown user type.";
        }
    ?>

    <!-- <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
        <ul>
            <li><a href="general_settings.php">General Settings</a></li>
            <li><a href="academic_terms.php">Academic Terms</a></li>
            <li><a href="email_settings.php">Email Settings</a></li>
        </ul>
    </li>

    <li><a href="reports.php"><i class="fa-solid fa-chart-simple"></i> Reports</a></li>
    <li><a href="logs.php">Logs</a></li> -->
    </ul>
</nav>