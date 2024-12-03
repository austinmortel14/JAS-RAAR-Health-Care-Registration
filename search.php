<?php
include 'dbconfig.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM applicants WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR position_applied LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $userId = $_SESSION['user_id'];
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $action = "Searched for applicants with keyword: $search";
    $logStmt->bind_param("is", $userId, $action);
    $logStmt->execute();
    $logStmt->close();
} else {
    $result = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Applicants</title>
    <link rel="stylesheet" href="Art.css">
</head>
<body>
    <h1>Search Applicants</h1>

    <form method="GET" action="search.php">
        <input type="text" name="search" placeholder="Search by name, email, phone, or position" value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($result && $result->num_rows > 0): ?>
        <h2>Search Results</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position Applied</th>
                    <th>Resume</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($applicant = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($applicant['name']) ?></td>
                        <td><?= htmlspecialchars($applicant['email']) ?></td>
                        <td><?= htmlspecialchars($applicant['phone']) ?></td>
                        <td><?= htmlspecialchars($applicant['position_applied']) ?></td>
                        <td><?= htmlspecialchars($applicant['resume']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif (isset($search) && empty($result)): ?>
        <p>No results found for your search.</p>
    <?php endif; ?>

    <p><a href="index.php">Back to Applicants List</a></p>
</body>
</html>
