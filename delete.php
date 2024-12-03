<?php
include 'dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT name FROM applicants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deleteStmt = $conn->prepare("DELETE FROM applicants WHERE id = ?");
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        $userId = $_SESSION['user_id'];
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
        $action = "Deleted applicant: " . $applicant['name'];
        $logStmt->bind_param("is", $userId, $action);
        $logStmt->execute();
        $logStmt->close();

        header("Location: index.php");
        exit;
    } else {
        $error = "Error deleting applicant. Please try again.";
    }
    $deleteStmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Applicant</title>
    <link rel="stylesheet" href="Art.css">
</head>
<body>
    <h1>Delete Applicant</h1>

    <form method="POST" action="delete.php?id=<?= $id ?>">
        <p>Are you sure you want to delete the applicant: <?= htmlspecialchars($applicant['name']) ?>?</p>
        <button type="submit">Yes, Delete</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
