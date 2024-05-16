<?php
// instructors.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

$stmt = $pdo->query("SELECT * FROM instructors");
$instructors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructors</title>
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
            <h1>Instructors</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($instructors as $instructor): ?>
                <tr>
                    <td><?= htmlspecialchars($instructor['id']) ?></td>
                    <td><?= htmlspecialchars($instructor['name']) ?></td>
                    <td><?= htmlspecialchars($instructor['email']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><a href="dashboard.php">Back to Dashboard</a></p>
        </div>
    </div>
</body>
</html>
