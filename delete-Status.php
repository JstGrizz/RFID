<?php
include 'function.php'; // Include your function file

if (isset($_GET['status_id'])) {
    $id = intval($_GET['status_id']); // Convert to integer

    // Call the function to delete the record
    deleteStatusData($id);

    if (isset($_SESSION['message'])) {
        echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message'], $_SESSION['message_type']);
    }

    // Redirect to the page after deletion
    header("Location: data-Status.php");
    exit();
}
?>