<?php
include 'dbconfig.php';
include 'models.php';

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $id = $_POST['id'] ?? null;

    if (!$action) {
        echo json_encode([
            "message" => "No action specified.",
            "statusCode" => 400
        ]);
        exit;
    }

    // Handle actions
    if ($action === 'edit' && $id) {
        // Update the applicant
        $response = updateApplicant($conn, $id, $_POST);
        echo json_encode($response);
    } elseif ($action === 'delete' && $id) {
        // Delete the applicant
        $response = deleteApplicant($conn, $id);
        echo json_encode($response);
    } else {
        echo json_encode([
            "message" => "Invalid request.",
            "statusCode" => 400
        ]);
    }
} else {
    echo json_encode([
        "message" => "Invalid HTTP request method.",
        "statusCode" => 400
    ]);
}

