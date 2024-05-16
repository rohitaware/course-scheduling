<?php
// dashboard.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <h2>Manage Content</h2>
            <ul>
                <li><a href="instructors.php">View Instructors</a></li>
                <li><a href="add_course.php">Add Course</a></li>
                <li><a href="courses.php">View Courses</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Welcome to the Admin Dashboard</h1>
                <a class="logout-button" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
