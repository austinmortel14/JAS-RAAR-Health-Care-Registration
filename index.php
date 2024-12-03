<?php
include 'dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare(
        "SELECT * FROM applicants 
         WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR address LIKE ? OR position_applied LIKE ?"
    );
    $searchTerm = '%' . $search . '%';
    $stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $applicants = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $userId = $_SESSION['user_id'];
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $action = "Searched for: " . $search;
    $logStmt->bind_param("is", $userId, $action);
    $logStmt->execute();
    $logStmt->close();
} else {
    $result = $conn->query("SELECT * FROM applicants");
    $applicants = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Applicant Management System</title>
    <link rel="stylesheet" href="Art.css">
</head>
<body>
    <h1>Applicant Management System</h1>
    <p>Welcome! <a href="logout.php">Logout</a></p>

    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Search applicants" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <h2>Applicant Records</h2>
    <a href="insert.php">Add New Applicant</a>
    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Position Applied</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($applicants)): ?>
                <?php foreach ($applicants as $applicant): ?>
                    <tr>
                        <td><?= htmlspecialchars($applicant['name']) ?></td>
                        <td><?= htmlspecialchars($applicant['email']) ?></td>
                        <td><?= htmlspecialchars($applicant['phone']) ?></td>
                        <td><?= htmlspecialchars($applicant['address']) ?></td>
                        <td><?= htmlspecialchars($applicant['position_applied']) ?></td>
                        <td>
                            <!-- Fixing the link for edit action -->
                            <a href="edit.php?id=<?= $applicant['id'] ?>">Edit</a>
                            <a href="delete.php?id=<?= $applicant['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Activity Logs</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $logs = $conn->query("SELECT * FROM activity_logs ORDER BY timestamp DESC");
            while ($log = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
