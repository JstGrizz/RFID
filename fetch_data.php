<?php
// Include the database connection
include 'db.php'; // Ensure this file has the correct path to your database connection setup

header('Content-Type: application/json'); // Set the correct header for JSON output

// Check if the RFID parameter is provided
if (isset($_GET['rfid'])) {
    $rfid = $_GET['rfid'];

    // Prepare and execute a database query to fetch data based on RFID
    $stmt = $conn->prepare("SELECT td.rfid, td.blok, td.created_at, s.status_name, td.latitude, td.longitude, td.berat
                            FROM tree_data td
                            JOIN status s ON td.status_id = s.status_id
                            WHERE td.rfid = ?
                            ORDER BY td.created_at DESC
                            LIMIT 1");
    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    if ($result->num_rows > 0) {
        // Fetch the result into an associative array
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
    } else {
        // No records found
        $response = ['error' => 'No data found for RFID: ' . $rfid];
    }

    $stmt->close();
    $conn->close();

    // Encode the data in JSON format and output it
    echo json_encode($response);
} else {
    // If no RFID is provided, return an error message
    echo json_encode(['error' => 'RFID parameter is missing.']);
}
?>