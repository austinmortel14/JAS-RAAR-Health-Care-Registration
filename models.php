<?php
function getAllApplicants($conn) {
    $sql = "SELECT * FROM applicants";
    $result = $conn->query($sql);

    if ($result) {
        return [
            "message" => "Applicants retrieved successfully.",
            "statusCode" => 200,
            "querySet" => $result->fetch_all(MYSQLI_ASSOC)
        ];
    } else {
        return [
            "message" => "Error fetching applicants: " . $conn->error,
            "statusCode" => 400
        ];
    }
}

function searchApplicants($conn, $keyword) {
    $sql = "SELECT * FROM applicants WHERE 
            name LIKE '%$keyword%' OR 
            email LIKE '%$keyword%' OR 
            phone LIKE '%$keyword%' OR 
            address LIKE '%$keyword%' OR 
            position_applied LIKE '%$keyword%'";
    $result = $conn->query($sql);

    if ($result) {
        return [
            "message" => "Search successful.",
            "statusCode" => 200,
            "querySet" => $result->fetch_all(MYSQLI_ASSOC)
        ];
    } else {
        return [
            "message" => "Error during search: " . $conn->error,
            "statusCode" => 400
        ];
    }
}

function getApplicantById($conn, $id) {
    $sql = "SELECT * FROM applicants WHERE id = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        return [
            "message" => "Applicant found.",
            "statusCode" => 200,
            "querySet" => $result->fetch_assoc()
        ];
    } else {
        return [
            "message" => "Applicant not found.",
            "statusCode" => 400
        ];
    }
}

function insertApplicant($conn, $data) {
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $address = $data['address'];
    $position = $data['position_applied'];
    $resume = $data['resume'];

    $sql = "INSERT INTO applicants (name, email, phone, address, position_applied, resume) 
            VALUES ('$name', '$email', '$phone', '$address', '$position', '$resume')";

    if ($conn->query($sql)) {
        return [
            "message" => "Applicant added successfully.",
            "statusCode" => 200
        ];
    } else {
        return [
            "message" => "Error adding applicant: " . $conn->error,
            "statusCode" => 400
        ];
    }
}

function updateApplicant($conn, $id, $data) {
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $address = $data['address'];
    $position = $data['position_applied'];
    $resume = $data['resume'];

    $sql = "UPDATE applicants SET 
            name = '$name', 
            email = '$email', 
            phone = '$phone', 
            address = '$address', 
            position_applied = '$position', 
            resume = '$resume' 
            WHERE id = $id";

    if ($conn->query($sql)) {
        return [
            "message" => "Applicant updated successfully.",
            "statusCode" => 200
        ];
    } else {
        return [
            "message" => "Error updating applicant: " . $conn->error,
            "statusCode" => 400
        ];
    }
}

function deleteApplicant($conn, $id) {
    $sql = "DELETE FROM applicants WHERE id = $id";

    if ($conn->query($sql)) {
        return [
            "message" => "Applicant deleted successfully.",
            "statusCode" => 200
        ];
    } else {
        return [
            "message" => "Error deleting applicant: " . $conn->error,
            "statusCode" => 400
        ];
    }
}
