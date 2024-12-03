<?php
function logActivity($conn, $userId, $actionType, $details) {
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action_type, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $actionType, $details);
    $stmt->execute();
    $stmt->close();
}