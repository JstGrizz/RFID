<?php
include 'db.php';

function insertPohon($rfid, $status, $latitude, $longitude, $blok)
{
    global $conn;

    $latitude = floatval($latitude);
    $longitude = floatval($longitude);

    // Prepare to insert new data
    $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, status, latitude, longitude, blok) VALUES (?, ?, ?, ?, ?)");
    $insert_query->bind_param("ssdds", $rfid, $status, $latitude, $longitude, $blok);

    if ($insert_query->execute()) {
        $_SESSION['message'] = 'New record created successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to create new record.';
        $_SESSION['message_type'] = 'error';
    }

    $insert_query->close();
    $conn->close(); // Consider not closing here if using connection elsewhere

    // Redirect to a clean URL
    header("Location: pohon.php");
    exit;
}

// Handling the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid = $_POST['rfid'];
    $status = $_POST['status'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $blok = $_POST['blok'];

    // Call the function to insert data
    insertPohon($rfid, $status, $latitude, $longitude, $blok);
}



session_start(); // Ensure session is started at the top of the script

function insertOrUpdateTimbangan($rfid, $berat)
{
    global $conn; // Ensure your database connection is globally accessible or pass it as a parameter

    // Check if RFID exists and retrieve current induk value
    $check_query = $conn->prepare("SELECT id, induk FROM tree_data WHERE rfid = ?");
    $check_query->bind_param("s", $rfid);
    $check_query->execute();
    $check_query->store_result();
    $check_query->bind_result($id, $current_induk);
    $check_query->fetch();

    if ($check_query->num_rows > 0) {
        // RFID exists, calculate new induk
        $new_induk = ($current_induk == 0) ? 1 : $current_induk + 1;

        // Update the berat and induk
        $update_query = $conn->prepare("UPDATE tree_data SET berat = ?, induk = ? WHERE rfid = ?");
        $update_query->bind_param("dis", $berat, $new_induk, $rfid);
        if ($update_query->execute()) {
            // Set success message in session
            $_SESSION['message'] = 'Berat and Induk data updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            // Set error message in session
            $_SESSION['message'] = 'Failed to update berat and induk data.';
            $_SESSION['message_type'] = 'error';
        }
        $update_query->close();
    } else {
        // RFID does not exist, insert new data with induk starting at 1
        $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, berat, induk) VALUES (?, ?, 1)");
        $insert_query->bind_param("sd", $rfid, $berat);
        if ($insert_query->execute()) {
            // Set success message in session
            $_SESSION['message'] = 'New record created successfully with induk initialized to 1.';
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

    // Redirect to a clean URL
    header("Location: timbangan.php");
    exit;
}
