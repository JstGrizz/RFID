<?php
include 'db.php';
session_start(); // Ensure session is started at the top of the script

function insertPohon($rfid, $latitude, $longitude, $blokName)
{
    global $conn;

    // Ensure that latitude and longitude are correctly formatted as floats
    $latitude = floatval($latitude);
    $longitude = floatval($longitude);

    // Fetch the blok_id and status_id from the blok table
    $blok_query = $conn->prepare("SELECT blok_id, status_id FROM blok WHERE blok_name = ?");
    if ($blok_query === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $blok_query->bind_param("s", $blokName);
    $blok_query->execute();
    $blok_query->bind_result($blok_id, $status_id);
    $found_blok = $blok_query->fetch();
    $blok_query->close();

    // Check if a valid blok_id and status_id was found
    if (!$found_blok) {
        $_SESSION['message'] = 'Blok not found or no status associated.';
        $_SESSION['message_type'] = 'error';
        header("Location: pohon.php");
        exit;
    }

    // Prepare to insert new data
    $insert_query = $conn->prepare("INSERT INTO tree_data (rfid, status_id, latitude, longitude, blok_id) VALUES (?, ?, ?, ?, ?)");
    if ($insert_query === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Bind parameters and execute
    $insert_query->bind_param("siddi", $rfid, $status_id, $latitude, $longitude, $blok_id);
    if ($insert_query->execute()) {
        $_SESSION['message'] = 'New record created successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to create new record. Error: ' . $insert_query->error;
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
            // Set success message in    session
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

function updateTreeData($id, $rfid, $status_id, $blok_id, $latitude, $longitude, $berat)
{
    global $conn;
    echo "rfid :  $rfid, status_id: $status_id,blok_id: $blok_id, latitude: $latitude , longitude: $longitude , blok_id: $blok_id,berat: $berat    ";

    // Fetch status_id based on blok_id
    $status_id = fetchBlokStatus($blok_id);
    if ($status_id === null) {
        $_SESSION['message'] = 'Invalid blok ID, status not found.';
        $_SESSION['message_type'] = 'error';
        return;
    }

    $query = "UPDATE tree_data SET rfid = ?, latitude = ?, longitude = ?, blok_id = ?, status_id = ?, berat = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("sddiidi", $rfid, $latitude, $longitude, $blok_id, $status_id, $berat, $id);
    if (!$stmt->execute()) {
        $_SESSION['message'] = 'Failed to update data: ' . $stmt->error;
        $_SESSION['message_type'] = 'error';
        echo "Error in executing update: " . $stmt->error;
    } else {
        $_SESSION['message'] = 'Data updated successfully.';
        $_SESSION['message_type'] = 'success';
    }

    $stmt->close();
    $conn->close();

    header("Location: edit.php?id=" . $id);
    exit;
}

function updateStatusData($id, $status_name)
{
    global $conn;

    $query = "UPDATE status SET status_name = ? WHERE status_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $stmt->bind_param("si", $status_name, $id);
    if (!$stmt->execute()) {
        $_SESSION['message'] = 'Failed to update data: ' . $stmt->error;
        $_SESSION['message_type'] = 'error';
        echo "Error in executing update: " . $stmt->error;
    } else {
        $_SESSION['message'] = 'Data updated successfully.';
        $_SESSION['message_type'] = 'success';
    }

    $stmt->close();
    $conn->close();

    header("Location: edit-Status.php?status_id=" . $id);
    exit;
}

function updateBlokData($blok_id, $blok_name, $tanggal_tanam, $luas_tanah, $jumlah_pohon, $status_id)
{
    global $conn; // Ensure that $conn is accessible

    $query = "UPDATE blok SET 
              blok_name = ?, 
              tanggal_tanam = ?, 
              luas_tanah = ?, 
              jumlah_pohon = ?, 
              status_id = ? 
              WHERE blok_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return "Prepare failed: (" . $conn->errno . ") " . $conn->error;
    }

    if (!$stmt->bind_param("ssdiii", $blok_name, $tanggal_tanam, $luas_tanah, $jumlah_pohon, $status_id, $blok_id)) {
        return "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    if (!$stmt->execute()) {
        return "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    $stmt->close();
    return true;
}



// Method to delete a record based on ID
function deleteTreeData($id)
{
    global $conn;

    // Prepare the SQL statement to delete the record
    $query = "DELETE FROM tree_data WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); // Use "i" for integer type

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Record deleted successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to delete the record.';
        $_SESSION['message_type'] = 'error';
    }

    $stmt->close();
    $conn->close(); // Comment out if using the connection elsewhere
}

function deleteStatusData($id)
{
    global $conn;

    // Prepare the SQL statement to delete the record
    $query = "DELETE FROM status WHERE status_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); // Use "i" for integer type

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Record deleted successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to delete the record.';
        $_SESSION['message_type'] = 'error';
    }

    $stmt->close();
    $conn->close(); // Comment out if using the connection elsewhere
}

function deleteBlokData($id)
{
    global $conn;

    // Prepare the SQL statement to delete the record
    $query = "DELETE FROM blok WHERE blok_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id); // Use "i" for integer type

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Record deleted successfully.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Failed to delete the record.';
        $_SESSION['message_type'] = 'error';
    }

    $stmt->close();
    $conn->close(); // Comment out if using the connection elsewhere
}


function fetchBlokNames()
{
    global $conn; // Assuming $conn is your database connection variable

    // Query to fetch blok names
    $query = "SELECT blok_name FROM blok ORDER BY blok_name";
    $result = $conn->query($query);

    $blokNames = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $blokNames[] = $row['blok_name'];
        }
    }
    return $blokNames;
}

function fetchBlokStatus($blok_id)
{
    global $conn;
    $stmt = $conn->prepare("SELECT status_id FROM blok WHERE blok_id = ?");
    $stmt->bind_param("i", $blok_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['status_id'];
    } else {
        return null; // Return null if no status_id is found
    }
}
