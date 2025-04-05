<?php
$page_title = "Admin Dashboard - Edit Gallery Item";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;
$gallery_item = null;

// Get gallery item ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = "No gallery item selected.";
} else {
    $id = sanitize_input($conn, $_GET['id']);
    
    // Fetch existing gallery item
    $sql = "SELECT * FROM gallery WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $gallery_item = mysqli_fetch_assoc($result);
    
    if (!$gallery_item) {
        $error = "Gallery item not found.";
    }
}

// Handle update request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_gallery"])) {
    $id = sanitize_input($conn, $_POST["gallery_id"]);
    $title = sanitize_input($conn, $_POST["gallery_title"]);
    $description = sanitize_input($conn, $_POST["gallery_description"]);
    
    $image_url = $gallery_item['image_url']; // Keep existing image by default
    
    // Handle image upload if new image is provided
    if (isset($_FILES["gallery_image"]) && $_FILES["gallery_image"]["error"] == 0) {
        $image_tmp = $_FILES["gallery_image"]["tmp_name"];
        $image_name = $_FILES["gallery_image"]["name"];
        $image_size = $_FILES["gallery_image"]["size"];
        $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
        
        $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
        if (!in_array(strtolower($image_ext), $allowed_extensions)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($image_size > 5000000) {
            $error = "Image size must be less than 5MB.";
        } else {
            $new_image_name = uniqid() . "." . $image_ext;
            $upload_dir = "../../images/gallery/";
            
            if (move_uploaded_file($image_tmp, $upload_dir . $new_image_name)) {
                // Delete old image if it exists
                if (file_exists("../../" . $gallery_item['image_url'])) {
                    unlink("../../" . $gallery_item['image_url']);
                }
                $image_url = "images/gallery/" . $new_image_name;
            }
        }
    }
    
    if (!$error) {
        $updatedAt = date("Y-m-d H:i:s");
        $sql = "UPDATE gallery SET image_url = ?, title = ?, description = ?, updated_at = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $image_url, $title, $description, $updatedAt, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Gallery item updated successfully!";
            // Refresh gallery item data
            $sql = "SELECT * FROM gallery WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $gallery_item = mysqli_fetch_assoc($result);
        } else {
            $error = "Error updating gallery item: " . mysqli_error($conn);
        }
    }
}
?>

<h2>Edit Gallery Item</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<?php if ($gallery_item): ?>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" enctype="multipart/form-data">
    <input type="hidden" name="gallery_id" value="<?php echo $gallery_item['id']; ?>">
    
    <label for="gallery_title">Title:</label>
    <input type="text" id="gallery_title" name="gallery_title" value="<?php echo htmlspecialchars($gallery_item['title']); ?>" required><br><br>
    
    <label for="gallery_description">Description:</label>
    <textarea id="gallery_description" name="gallery_description" rows="4" cols="50" required><?php echo htmlspecialchars($gallery_item['description']); ?></textarea><br><br>
    
    <label>Current Image:</label><br>
    <img src="/<?php echo $gallery_item['image_url']; ?>" alt="Current gallery image" style="max-width: 200px;"><br><br>
    
    <label for="gallery_image">New Image (optional):</label>
    <input type="file" id="gallery_image" name="gallery_image" accept="image/*"><br><br>
    
    <button type="submit" name="update_gallery" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Update Gallery Item
    </button>
</form>

<!-- Delete button -->
<form method="POST" action="delete_gallery.php" onsubmit="return confirm('Are you sure you want to delete this gallery item?');">
    <input type="hidden" name="gallery_id" value="<?php echo $gallery_item['id']; ?>">
    <button type="submit" name="delete_gallery" class="btn_submit" style="background-color: #dc3545; margin-top: 10px;">
        <i class="fa-solid fa-trash"></i> Delete Gallery Item
    </button>
</form>
<?php endif; ?>

<?php include('../../layouts/footer.php'); ?>