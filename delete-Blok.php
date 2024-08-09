<?php
include 'function.php'; // Include your function file

if (isset($_GET['blok_id'])) {
    $id = intval($_GET['blok_id']); // Convert to integer

    // Call the function to delete the record
    deleteBlokData($id);

    if (isset($_SESSION['message'])) {
        echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        unset($_SESSION['message'], $_SESSION['message_type']);
    }

    // Redirect to the page after deletion
    header("Location: data-Blok.php");
    exit();
}
?>