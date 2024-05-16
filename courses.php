<?php
// courses.php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php';

// Fetch courses with associated instructor names
$stmt = $pdo->query("SELECT courses.id, courses.title, courses.description, courses.level, courses.image FROM courses");
$courses = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Courses</title>
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
            <h1>Courses</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Level</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Batches</th>
                </tr>
                <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?= htmlspecialchars($course['id']) ?></td>
                    <td><?= htmlspecialchars($course['title']) ?></td>
                    <td><?= htmlspecialchars($course['level']) ?></td>
                    <td><?= htmlspecialchars($course['description']) ?></td>
                    <td><img src="<?= htmlspecialchars($course['image']) ?>" alt="Course Image" width="100"></td>
                    <td>
                        <?php
                        // Fetch batches associated with the current course
                        $stmt = $pdo->prepare("SELECT batches.batch_name, batches.start_date, batches.end_date, instructors.name AS instructor_name FROM batches JOIN instructors ON batches.instructor_id = instructors.id WHERE course_id = ?");
                        $stmt->execute([$course['id']]);
                        $batches = $stmt->fetchAll();
                        foreach ($batches as $batch):
                        ?>
                        <p>Batch: <?= htmlspecialchars($batch['batch_name']) ?> (<?= htmlspecialchars($batch['start_date']) ?> to <?= htmlspecialchars($batch['end_date']) ?>), Instructor: <?= htmlspecialchars($batch['instructor_name']) ?></p>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><a href="dashboard.php">Back to Dashboard</a></p>
        </div>
    </div>
</body>
</html>
