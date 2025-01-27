<nav class="sidebar">
            <ul>
                <li>
                    <div class="user-info">
                        <?php if(is_logged_in()): ?>
                            <strong><span>Welcome, <?php echo get_fullname(); ?>!</span></strong>
                        <?php endif; ?>
                    </div>
                    <hr>
                </li>
                <li><a href="../dashboard/index.php"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
                <li><a href="../announcement/index.php"><i class="fa-solid fa-bullhorn"></i> Announcements</a></li>
                <li><a href="#"><i class="fa-solid fa-user"></i> Users</a>
                    <ul>
                        <li><a href="../users/register.php">Create New User</a></li> 
                        <li><a href="../users/manage_admins.php">Manage Users</a></li>
                    </ul>
                </li>

                <li><a href="#"><i class="fa-solid fa-book"></i> Courses</a>
                    <ul> 
                        <li><a href="manage_courses.php">Manage Courses</a></li>
                        <li><a href="../departments/manage_departments.php">Manage Derpartments</a></li>
                        <li><a href="course_scheduling.php">Course Scheduling</a></li>
                    </ul>
                </li>

                <li><a href="#"><i class="fa-solid fa-school"></i> Enrollment</a>
                    <ul>
                        <li><a href="manage_enrollments.php">Manage Enrollments</a></li>
                        <li><a href="enrollment_reports.php">Enrollment Reports</a></li>
                    </ul>
                </li>

                <li><a href="#"><i class="fa-solid fa-file"></i> Academic Records</a>
                    <ul>
                        <li><a href="grades_management.php">Grades Management</a></li>
                        <li><a href="transcript_generation.php">Transcript Generation</a></li>
                    </ul>
                </li>

                <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
                    <ul>
                        <li><a href="general_settings.php">General Settings</a></li>
                        <li><a href="academic_terms.php">Academic Terms</a></li>
                        <li><a href="email_settings.php">Email Settings</a></li>
                    </ul>
                </li>
                <li><a href="reports.php"><i class="fa-solid fa-chart-simple"></i> Reports</a></li>
                <!-- <li><a href="logs.php">Logs</a></li> -->
            </ul>
        </nav>