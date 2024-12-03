<?php
include 'dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $position = trim($_POST['position']);
    $resume = trim($_POST['resume']);

    if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($position)) {
        $error = "All fields are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO applicants (name, email, phone, address, position_applied, resume) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $address, $position, $resume);

        if ($stmt->execute()) {
            $userId = $_SESSION['user_id'];
            $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
            $action = "Added a new applicant: $name";
            $logStmt->bind_param("is", $userId, $action);
            $logStmt->execute();
            $logStmt->close();

            header("Location: index.php");
            exit;
        } else {
            $error = "Error adding applicant. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Applicant</title>
    <link rel="stylesheet" href="Art.css">
</head>
<body>
    <h1>Add New Applicant</h1>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="insert.php">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone" required>
        <input type="text" name="address" placeholder="Address" required>
        <input type="text" name="position" placeholder="Position Applied" required>
        <textarea name="resume" placeholder="Resume" rows="5" required></textarea>
        <button type="submit">Add Applicant</button>
    </form>
    <p><a href="index.php">Back to Applicants List</a></p>
</body>
</html>
