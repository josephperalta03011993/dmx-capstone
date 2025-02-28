<?php
$page_title = "Manage Payments";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;

// Update or Insert Payment (unchanged)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_payment"])) {
    if (isset($_POST["student_id"]) && isset($_POST["amount"]) && isset($_POST["payment_date"]) && 
        isset($_POST["payment_method"]) && isset($_POST["payment_status"])) {
        $payment_id = isset($_POST["payment_id"]) ? sanitize_input($conn, $_POST["payment_id"]) : null;
        $student_id = sanitize_input($conn, $_POST["student_id"]);
        $amount = sanitize_input($conn, $_POST["amount"]);
        $payment_date = sanitize_input($conn, $_POST["payment_date"]);
        $payment_method = sanitize_input($conn, $_POST["payment_method"]);
        $transaction_id = isset($_POST["transaction_id"]) ? sanitize_input($conn, $_POST["transaction_id"]) : '';
        $receipt_number = isset($_POST["receipt_number"]) ? sanitize_input($conn, $_POST["receipt_number"]) : '';
        $description = isset($_POST["description"]) ? sanitize_input($conn, $_POST["description"]) : '';
        $payment_status = sanitize_input($conn, $_POST["payment_status"]);
        $created_by = $_SESSION['user_id'];

        $check_student = "SELECT student_id FROM students WHERE student_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_student);
        mysqli_stmt_bind_param($check_stmt, "i", $student_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) == 0) {
            $error = "Error: Student ID does not exist.";
        } else {
            if (empty($payment_id)) {
                $insert_sql = "INSERT INTO payments (student_id, amount, payment_date, payment_method, transaction_id, receipt_number, description, payment_status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($insert_stmt, "idssssssi", $student_id, $amount, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by);
                if (mysqli_stmt_execute($insert_stmt)) {
                    $success = "Payment recorded successfully!";
                } else {
                    $error = "Error recording payment: " . mysqli_error($conn);
                }
            } else {
                $update_sql = "UPDATE payments SET amount = ?, payment_date = ?, payment_method = ?, transaction_id = ?, receipt_number = ?, description = ?, payment_status = ?, created_by = ? WHERE payment_id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "dsssssiii", $amount, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by, $payment_id);
                if (mysqli_stmt_execute($update_stmt)) {
                    $success = "Payment updated successfully!";
                } else {
                    $error = "Error updating payment: " . mysqli_error($conn);
                }
            }
        }
    } else {
        $error = "Missing required fields for payment update";
    }
}

// Get Student Payment Data
$payments_sql = "SELECT s.student_id AS student_id, s.first_name, s.last_name, p.payment_id, p.amount, p.payment_date, 
                p.payment_method, p.transaction_id, p.receipt_number, p.description, p.payment_status
                FROM students s
                LEFT JOIN payments p ON s.student_id = p.student_id";
$payments_result = $conn->query($payments_sql);

// Get Payment Status and Method Options
$payment_status_sql = "SHOW COLUMNS FROM payments LIKE 'payment_status'";
$payment_status_result = $conn->query($payment_status_sql);
$payment_status_row = $payment_status_result->fetch_assoc();
preg_match("/^enum\(\'(.*)\'\)$/", $payment_status_row['Type'], $matches);
$payment_status_options = explode("','", $matches[1]);

$payment_method_sql = "SHOW COLUMNS FROM payments LIKE 'payment_method'";
$payment_method_result = $conn->query($payment_method_sql);
$payment_method_row = $payment_method_result->fetch_assoc();
preg_match("/^enum\(\'(.*)\'\)$/", $payment_method_row['Type'], $matches);
$payment_method_options = explode("','", $matches[1]);
?>

<h2>Manage Payments</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<table id="myTable" class="display">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Payment Method</th>
            <th>Transaction ID</th>
            <th>Receipt Number</th>
            <th>Description</th>
            <th>Payment Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($payments_result && $payments_result->num_rows > 0) {
            while ($row = $payments_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td data-value='" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "'>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                echo "<td data-value='" . (isset($row['amount']) && !empty($row['amount']) ? htmlspecialchars($row['amount']) : 'N/A') . "'>";
                echo "<form method='POST'>";
                echo "<input type='hidden' name='payment_id' value='" . (isset($row['payment_id']) ? htmlspecialchars($row['payment_id']) : '') . "'>";
                echo "<input type='hidden' name='student_id' value='" . htmlspecialchars($row['student_id']) . "'>";
                echo "<input type='number' name='amount' value='" . (isset($row['amount']) ? htmlspecialchars($row['amount']) : '') . "'>";
                echo "</td>";
                echo "<td data-value='" . (isset($row['payment_date']) && !empty($row['payment_date']) ? htmlspecialchars($row['payment_date']) : 'N/A') . "'>
                    <input type='date' name='payment_date' value='" . (isset($row['payment_date']) ? htmlspecialchars($row['payment_date']) : date('Y-m-d')) . "'></td>";
                echo "<td data-value='" . (isset($row['payment_method']) && !empty($row['payment_method']) ? htmlspecialchars($row['payment_method']) : 'N/A') . "'>
                    <select name='payment_method'>";
                foreach ($payment_method_options as $option) {
                    echo "<option value='" . htmlspecialchars($option) . "'" . (isset($row['payment_method']) && $row['payment_method'] == $option ? " selected" : "") . ">" . htmlspecialchars($option) . "</option>";
                }
                echo "</select></td>";
                echo "<td data-value='" . (isset($row['transaction_id']) && !empty($row['transaction_id']) ? htmlspecialchars($row['transaction_id']) : 'N/A') . "'>
                    <input type='text' name='transaction_id' value='" . (isset($row['transaction_id']) ? htmlspecialchars($row['transaction_id']) : '') . "'></td>";
                echo "<td data-value='" . (isset($row['receipt_number']) && !empty($row['receipt_number']) ? htmlspecialchars($row['receipt_number']) : 'N/A') . "'>
                    <input type='text' name='receipt_number' value='" . (isset($row['receipt_number']) ? htmlspecialchars($row['receipt_number']) : '') . "'></td>";
                echo "<td data-value='" . (isset($row['description']) && !empty($row['description']) ? htmlspecialchars($row['description']) : 'N/A') . "'>
                    <input type='text' name='description' value='" . (isset($row['description']) ? htmlspecialchars($row['description']) : '') . "'></td>";
                echo "<td data-value='" . (isset($row['payment_status']) && !empty($row['payment_status']) ? htmlspecialchars($row['payment_status']) : 'N/A') . "'>
                    <select name='payment_status'>";
                foreach ($payment_status_options as $option) {
                    echo "<option value='" . htmlspecialchars($option) . "'" . (isset($row['payment_status']) && $row['payment_status'] == $option ? " selected" : "") . ">" . htmlspecialchars($option) . "</option>";
                }
                echo "</select></td>";
                echo "<td><button type='submit' name='update_payment' id='btn_edit' class='btn'>Update</button></form></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No payment data found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Pass page title to JS -->
<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>
<?php include('../../layouts/footer.php'); ?>