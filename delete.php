<?php
include 'function.php'; // Include your function file

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer

    // Call the function to delete the record
    deleteTreeData($id);

    // Redirect to the page after deletion
    header("Location: Laporan.php");
    exit();
}
?>