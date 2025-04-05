<?php 
    $page_title = "Admin Dashboard - Add Gallery Item";
    include_once('../../database/conn.php');
    include_once('../../layouts/header.php');

    $success = null;
    $error = null;

    // Handle gallery item insertion request
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_gallery"])) {
        $title = sanitize_input($conn, $_POST["gallery_title"]);
        $description = sanitize_input($conn, $_POST["gallery_description"]);
        
        // Handle image upload
        if (isset($_FILES["gallery_image"]) && $_FILES["gallery_image"]["error"] == 0) {
            $image_tmp = $_FILES["gallery_image"]["tmp_name"];
            $image_name = $_FILES["gallery_image"]["name"];
            $image_size = $_FILES["gallery_image"]["size"];
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);

            // Validate image file type and size
            $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
            if (!in_array(strtolower($image_ext), $allowed_extensions)) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($image_size > 5000000) { // 5MB size limit
                $error = "Image size must be less than 5MB.";
            } else {
                // Create a unique filename to avoid conflicts
                $new_image_name = uniqid() . "." . $image_ext;
                $upload_dir = "../../images/gallery/";

                // Move the uploaded image to the desired directory
                if (move_uploaded_file($image_tmp, $upload_dir . $new_image_name)) {
                    $image_url = "images/gallery/" . $new_image_name;

                    // Prepare data for insertion
                    $createdAt = date("Y-m-d H:i:s");
                    $updatedAt = $createdAt; // initially the same value

                    // SQL Query to insert new gallery item
                    $sql = "INSERT INTO gallery (image_url, title, description, created_at) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);

                    if ($stmt) {
                        // Bind parameters
                        mysqli_stmt_bind_param($stmt, "ssss", $image_url, $title, $description, $createdAt);

                        // Execute the statement
                        if (mysqli_stmt_execute($stmt)) {
                            $success = "Gallery item created successfully! <a href='gallery_list.php'>View all gallery items</a>";
                        } else {
                            $error = "Error creating gallery item: " . mysqli_error($conn);
                        }
                    } else {
                        $error = "Error preparing the SQL statement.";
                    }
                } else {
                    $error = "There was an error uploading the image.";
                }
            }
        } else {
            $error = "Please upload an image.";
        }
    }
?>

<h2>Add New Gallery Item</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
    <label for="gallery_title">Title:</label>
    <input type="text" id="gallery_title" name="gallery_title" required><br><br>

    <label for="gallery_description">Description:</label>
    <textarea id="gallery_description" name="gallery_description" rows="4" cols="50" required></textarea><br><br>

    <label for="gallery_image">Image:</label>
    <input type="file" id="gallery_image" name="gallery_image" accept="image/*" required><br><br>

    <button type="submit" name="create_gallery" id="create_gallery" class="btn_submit">
        <i class="fa-solid fa-floppy-disk"></i> Add Gallery Item
    </button>
</form>

<!-- Add navigation link to gallery list -->
<div style="margin-top: 20px;">
    <a href="gallery_list.php" class="btn_submit" style="background-color: #007bff; text-decoration: none; padding: 10px 15px; color: white;">
        <i class="fa-solid fa-list"></i> View Gallery List
    </a>
</div>

<?php include('../../layouts/footer.php'); ?>