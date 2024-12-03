<?php
include 'dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id) {
    die("Applicant ID is required.");
}

$stmt = $conn->prepare("SELECT * FROM applicants WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$applicant = $result->fetch_assoc();
$stmt->close();

if (!$applicant) {
    die("Applicant not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $position = trim($_POST['position']);
    $resume = trim($_POST['resume']);
    $username = $_SESSION['username']; 

    $updateStmt = $conn->prepare("UPDATE applicants SET name = ?, email = ?, phone = ?, address = ?, position_applied = ?, resume = ? WHERE id = ?");
    $updateStmt->bind_param("ssssssi", $name, $email, $phone, $address, $position, $resume, $id);

    if ($updateStmt->execute()) {
        
        $userId = $_SESSION['user_id'];
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, username, action) VALUES (?, ?, ?)");
        $action = "Updated applicant details: $name";
        $logStmt->bind_param("iss", $userId, $username, $action);
        $logStmt->execute();
        $logStmt->close();

        header("Location: index.php");
        exit;
    } else {
        $error = "Error updating applicant. Please try again.";
    }
    $updateStmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Applicant</title>
    <link rel="stylesheet" href="Art.css">
</head>
<body>
    <h1>Edit Applicant</h1>

    <?php if (!empty($error)): ?>
        <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $applicant['id'] ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($applicant['name']) ?>" required><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($applicant['email']) ?>" required><br>

        <label>Phone:</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($applicant['phone']) ?>" required><br>

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($applicant['address']) ?>" required><br>

        <label>Position Applied:</label>
        <input type="text" name="position" value="<?= htmlspecialchars($applicant['position_applied']) ?>" required><br>

        <label>Resume:</label>
        <textarea name="resume" rows="5" required><?= htmlspecialchars($applicant['resume']) ?></textarea><br>

        <button type="submit">Update Applicant</button>
    </form>

    <p><a href="index.php">Back to Applicants List</a></p>
</body>
</html>
