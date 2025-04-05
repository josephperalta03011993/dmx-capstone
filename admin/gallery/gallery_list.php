<?php
$page_title = "Admin Dashboard - Gallery List";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Fetch all gallery items
$sql = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<h2>Gallery List</h2>

<?php if (isset($_GET['success'])) { echo "<p style='color: green;'>" . $_GET['success'] . "</p>"; } ?>
<?php if (isset($_GET['error'])) { echo "<p style='color: red;'>" . $_GET['error'] . "</p>"; } ?>

<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th style="padding: 10px; border: 1px solid #ddd;">Image</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Title</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Description</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <img src="../../<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="max-width: 100px;">
            </td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($row['title']); ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo htmlspecialchars($row['description']); ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
                <a href="edit_gallery.php?id=<?php echo $row['id']; ?>" class="btn_submit" style="background-color: #ffc107; color: black; text-decoration: none; padding: 5px 10px;">
                    <i class="fa-solid fa-edit"></i> Edit
                </a>
                <form method="POST" action="delete_gallery.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this gallery item?');">
                    <input type="hidden" name="gallery_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_gallery" class="btn_submit" style="background-color: #dc3545; margin-left: 5px;">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div style="margin-top: 20px;">
    <a href="add_gallery.php" class="btn_submit" style="background-color: #28a745; text-decoration: none; padding: 10px 15px; color: white;">
        <i class="fa-solid fa-plus"></i> Add New Gallery Item
    </a>
</div>

<?php include('../../layouts/footer.php'); ?>