<?php
include 'db.php';

function insertOrUpdatePohon($rfid, $ratoon, $latitude, $longitude)
{
    global $conn;

    $latitude = floatval($latitude);
    $longitude = floatval($longitude);

    $check_query = $update_query = $insert_query = null; // Initialize variables

    // Check if RFID exists
    $check_query = $conn->prepare("SELECT id FROM tree_data WHERE rfid = ?");
    $check_query->bind_param("s", $rfid);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        // RFID exists, update the data
        $update_query = $conn->prepare("UPDATE tree_data SET ratoon = ?, latitude = ?, longitude = ? WHERE rfid = ?");
        $update_query->bind_param("idds", $ratoon, $latitude, $longitude, $rfid);
        if ($update_query->execute()) {
            echo "<script>alert('Data updated successfully.');</script>";
        } else {
            echo "<script>alert('Failed to update data.');</script>";
        }
    } else {
        // RFID does not exist, insert new data
        $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, ratoon, latitude, longitude) VALUES (?, ?, ?, ?)");
        $insert_query->bind_param("sidd", $rfid, $ratoon, $latitude, $longitude);
        if ($insert_query->execute()) {
            echo "<script>alert('New record created successfully.');</script>";
        } else {
            echo "<script>alert('Failed to create new record.');</script>";
        }
    }

    // Close queries and connection
    if ($check_query) $check_query->close();
    if ($update_query) $update_query->close();
    if ($insert_query) $insert_query->close();
    $conn->close();
}

function insertOrUpdateTimbangan($rfid, $berat)
{
    global $conn;
    $check_query = $update_query = $insert_query = null; // Initialize variables

    // Check if RFID exists
    $check_query = $conn->prepare("SELECT id FROM tree_data WHERE rfid = ?");
    $check_query->bind_param("s", $rfid);
    $check_query->execute();
    $check_query->store_result();

    if ($check_query->num_rows > 0) {
        // RFID exists, update the berat
        $update_query = $conn->prepare("UPDATE tree_data SET berat = ? WHERE rfid = ?");
        $update_query->bind_param("ds", $berat, $rfid);
        if ($update_query->execute()) {
            echo "<script>alert('Berat data updated successfully.');</script>";
        } else {
            echo "<script>alert('Failed to update berat data.');</script>";
        }
    } else {
        // RFID does not exist, insert new data
        $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, berat) VALUES (?, ?)");
        $insert_query->bind_param("sd", $rfid, $berat);
        if ($insert_query->execute()) {
            echo "<script>alert('New record created successfully.');</script>";
        } else {
            echo "<script>alert('Failed to create new record.');</script>";
        }
    }

    // Close queries and connection
    if ($check_query) $check_query->close();
    if ($update_query) $update_query->close();
    if ($insert_query) $insert_query->close();
    $conn->close();
}

?>
