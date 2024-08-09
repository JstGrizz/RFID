<?php
include 'db.php'; // Make sure this path is correct

header('Content-Type: application/json');

if (isset($_GET['rfid'])) {
    $rfid = $_GET['rfid'];

    $stmt = $conn->prepare("SELECT td.id, td.rfid, b.blok_name, td.created_at, s.status_name, td.latitude, td.longitude, td.berat, td.updated_at
                            FROM tree_data td
                            JOIN status s ON td.status_id = s.status_id
                            JOIN blok b ON td.blok_id = b.blok_id
                            WHERE td.rfid = ?
                            ORDER BY td.created_at DESC LIMIT 1");
    if (!$stmt) {
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("s", $rfid);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
    } else {
        $response = ['error' => 'No data found for RFID: ' . $rfid];
    }

    $stmt->close();
    $conn->close();
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'RFID parameter is missing.']);
}
?>
