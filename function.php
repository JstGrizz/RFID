<?php
include 'db.php';
session_start(); // Ensure session is started at the top of the script

function insertPohon($rfid, $status, $latitude, $longitude, $blok)
{
    global $conn;

    // Ensure that latitude and longitude are correctly formatted as floats
    $latitude = floatval($latitude);
    $longitude = floatval($longitude);

    // Prepare to insert new data
    $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, status, latitude, longitude, blok) VALUES (?, ?, ?, ?, ?)");
    if ($insert_query === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters and execute
    $insert_query->bind_param("ssdds", $rfid, $status, $latitude, $longitude, $blok);
    if ($insert_query->execute()) {
        $_SESSION['message'] = 'New record created successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to create new record.';
        $_SESSION['message_type'] = 'error';
    }

    $insert_query->close();
    $conn->close(); // Consider keeping the connection open if it's used elsewhere

    // Redirect to a clean URL to avoid form resubmission on refresh
    header("Location: pohon.php");
    exit;
}





function insertOrUpdateTimbangan($rfid, $berat)
{
    global $conn;

    // Check if RFID exists and retrieve the highest id (most recent entry)
    $check_query = $conn->prepare("SELECT id FROM tree_data WHERE rfid = ? ORDER BY id DESC LIMIT 1");
    $check_query->bind_param("s", $rfid);
    $check_query->execute();
    $check_query->store_result();
    $check_query->bind_result($id);
    $check_query->fetch();

    if ($check_query->num_rows > 0) {
        // RFID exists, update the berat of the latest (highest id) entry
        $update_query = $conn->prepare("UPDATE tree_data SET berat = ? WHERE id = ?");
        $update_query->bind_param("di", $berat, $id);
        if ($update_query->execute()) {
            // Set success message in session
            $_SESSION['message'] = 'Berat updated successfully for the latest record.';
            $_SESSION['message_type'] = 'success';
        } else {
            // Set error message in session
            $_SESSION['message'] = 'Failed to update berat for the latest record.';
            $_SESSION['message_type'] = 'error';
        }
        $update_query->close();
    } else {
        // RFID does not exist, insert new data with initial berat
        $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, berat) VALUES (?, ?)");
        $insert_query->bind_param("sd", $rfid, $berat);
        if ($insert_query->execute()) {
            // Set success message in session
            $_SESSION['message'] = 'New record created successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            // Set error message in session
            $_SESSION['message'] = 'Failed to create new record.';
            $_SESSION['message_type'] = 'error';
        }
        $insert_query->close();
    }

    $check_query->close();
    $conn->close(); // Consider not closing here if using connection elsewhere

    // Redirect to a clean URL to prevent form resubmission
    header("Location: timbangan.php");
    exit;
}




