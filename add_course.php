<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $level = $_POST['level'];
    $image = $_FILES['image']['name'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

    // Insert the course details
    $stmt = $pdo->prepare("INSERT INTO courses (title, description, level, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $level, $target_file]);
    $course_id = $pdo->lastInsertId();

    $errors = [];

    foreach ($_POST['batches'] as $batch) {
        $batch_name = $batch['name'];
        $start_date = $batch['start_date'];
        $end_date = $batch['end_date'];
        $instructor_id = $batch['instructor_id'];

        // Check if the instructor is available for the given date range
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM batches WHERE instructor_id = ? AND (start_date BETWEEN ? AND ? OR end_date BETWEEN ? AND ?)");
        $stmt->execute([$instructor_id, $start_date, $end_date, $start_date, $end_date]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors[] = "Instructor ID $instructor_id is already assigned during the period $start_date to $end_date.";
            continue;
        }

        // Insert the batch details
        $stmt = $pdo->prepare("INSERT INTO batches (course_id, batch_name, start_date, end_date, instructor_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $batch_name, $start_date, $end_date, $instructor_id]);
    }

    if (empty($errors)) {
        header("Location: courses.php");
        exit();
    } else {
        // Handle errors (e.g., display to the user)
        foreach ($errors as $error) {
            echo "<p>Error: $error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
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
            <h1>Add Course</h1>
            <form method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" name="title" required><br>
                
                <label for="description">Description:</label>
                <textarea name="description" required></textarea><br>
                
                <label for="level">Level:</label>
                <input type="text" name="level" required><br>
                
                <label for="image">Image:</label>
                <input type="file" name="image" required><br>
                
                <div id="batches">
                    <h3>Batches</h3>
                    <div class="batch">
                        <label for="batch_name">Batch Name:</label>
                        <input type="text" name="batches[0][name]" required><br>
                        
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="batches[0][start_date]" required><br>
                        
                        <label for="end_date">End Date:</label>
                        <input type="date" name="batches[0][end_date]" required><br>
                        
                        <label for="instructor_id">Instructor:</label>
                        <select name="batches[0][instructor_id]" required>
                            <?php
                            // Fetch instructors from the database
                            $stmt = $pdo->query('SELECT id, name FROM instructors');
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select><br>
                    </div>
                </div>
                <button type="button" onclick="addBatch()">Add Another Batch</button><br><br>
                <button type="submit">Add Course</button>
            </form>

            <script>
                let batchIndex = 1;

                function addBatch() {
                    const batchesDiv = document.getElementById('batches');
                    const newBatchDiv = document.createElement('div');
                    newBatchDiv.classList.add('batch');
                    
                    newBatchDiv.innerHTML = `
                        <label for="batch_name">Batch Name:</label>
                        <input type="text" name="batches[${batchIndex}][name]" required><br>
                        
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="batches[${batchIndex}][start_date]" required><br>
                        
                        <label for="end_date">End Date:</label>
                        <input type="date" name="batches[${batchIndex}][end_date]" required><br>
                        
                        <label for="instructor_id">Instructor:</label>
                        <select name="batches[${batchIndex}][instructor_id]" required>
                            <?php
                            // Fetch instructors from the database
                            $stmt = $pdo->query('SELECT id, name FROM instructors');
                            while ($row = $stmt->fetch()) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select><br>
                    `;
                    
                    batchesDiv.appendChild(newBatchDiv);
                    batchIndex++;
                }
            </script>
        </div>
    </div>
</body>
</html>
