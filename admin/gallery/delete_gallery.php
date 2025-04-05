<?php
include_once('../../database/conn.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_gallery"])) {
    $id = sanitize_input($conn, $_POST["gallery_id"]);
    
    // First, get the image URL to delete the file
    $sql = "SELECT image_url FROM gallery WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $gallery_item = mysqli_fetch_assoc($result);
    
    if ($gallery_item) {
        // Delete the image file
        if (file_exists("../../" . $gallery_item['image_url'])) {
            unlink("../../" . $gallery_item['image_url']);
        }
        
        // Delete the database record
        $sql = "DELETE FROM gallery WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to gallery list or dashboard
            header("Location: ../gallery/gallery_list.php?success=Gallery item deleted successfully");
            exit();
        } else {
            header("Location: ../gallery/gallery_list.php?error=Error deleting gallery item");
            exit();
        }
    }
}

// If accessed directly or invalid request, redirect
header("Location: ../gallery_list.php");
exit();
?>