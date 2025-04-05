<?php
$page_title = "Manage Payments";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

// Helper function
function sanitize_input($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

$success = null;
$error = null;

// Get ENUM values from database for payment_status
$payment_status_options = [];
$payment_status_sql = "SHOW COLUMNS FROM payments LIKE 'payment_status'";
$payment_status_result = $conn->query($payment_status_sql);
if ($payment_status_result && $payment_status_row = $payment_status_result->fetch_assoc()) {
    if (preg_match("/^enum\(\'(.*)\'\)$/", $payment_status_row['Type'], $matches)) {
        $payment_status_options = explode("','", $matches[1]);
    } else {
        $error = "Error: Could not parse payment_status ENUM values.";
    }
} else {
    $error = "Error: Failed to retrieve payment_status column info.";
}

// Get ENUM values for payment_method
$payment_method_options = [];
$payment_method_sql = "SHOW COLUMNS FROM payments LIKE 'payment_method'";
$payment_method_result = $conn->query($payment_method_sql);
if ($payment_method_result && $payment_method_row = $payment_method_result->fetch_assoc()) {
    if (preg_match("/^enum\(\'(.*)\'\)$/", $payment_method_row['Type'], $matches)) {
        $payment_method_options = explode("','", $matches[1]);
    } else {
        $error = "Error: Could not parse payment_method ENUM values.";
    }
} else {
    $error = "Error: Failed to retrieve payment_method column info.";
}

// Update Tuition Fee
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_tuition"])) {
    $student_id = sanitize_input($conn, $_POST["student_id"]);
    $tuition_fee = sanitize_input($conn, $_POST["tuition_fee"]);

    $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $check_stmt->bind_param("i", $student_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows === 0) {
        $error = "Error: Student ID does not exist.";
    } else {
        $update_stmt = $conn->prepare("UPDATE students SET tuition_fee = ? WHERE student_id = ?");
        $update_stmt->bind_param("di", $tuition_fee, $student_id);
        if ($update_stmt->execute()) {
            $success = "Tuition fee updated successfully!";
        } else {
            $error = "Error updating tuition fee: " . $conn->error;
        }
        $update_stmt->close();
    }
    $check_stmt->close();
}

// Update or Insert Payment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_payment"])) {
    if (
        isset($_POST["student_id"], $_POST["amount"], $_POST["payment_date"], $_POST["payment_method"],
        $_POST["payment_status"], $_POST["tuition_fee"])
    ) {
        $payment_id = isset($_POST["payment_id"]) ? sanitize_input($conn, $_POST["payment_id"]) : null;
        $student_id = sanitize_input($conn, $_POST["student_id"]);
        $amount = sanitize_input($conn, $_POST["amount"]);
        $payment_date = sanitize_input($conn, $_POST["payment_date"]);
        $payment_method = sanitize_input($conn, $_POST["payment_method"]);
        $transaction_id = isset($_POST["transaction_id"]) ? sanitize_input($conn, $_POST["transaction_id"]) : '';
        $receipt_number = isset($_POST["receipt_number"]) ? sanitize_input($conn, $_POST["receipt_number"]) : '';
        $description = isset($_POST["description"]) ? sanitize_input($conn, $_POST["description"]) : '';
        $payment_status = sanitize_input($conn, $_POST["payment_status"]);
        $tuition_fee = sanitize_input($conn, $_POST["tuition_fee"]);
        $created_by = $_SESSION['user_id'] ?? 0;

        $check_stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
        $check_stmt->bind_param("i", $student_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows === 0) {
            $error = "Error: Student ID does not exist.";
        } elseif (!in_array($payment_status, $payment_status_options)) {
            $error = "Error: Invalid payment status.";
        } else {
            if (empty($payment_id)) {
                $insert_stmt = $conn->prepare(
                    "INSERT INTO payments (student_id, amount, payment_date, payment_method, transaction_id, receipt_number, description, payment_status, created_by, tuition_fee) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $insert_stmt->bind_param("idssssssid", $student_id, $amount, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by, $tuition_fee);
                if ($insert_stmt->execute()) {
                    $success = "Payment recorded successfully!";
                } else {
                    $error = "Error recording payment: " . $conn->error;
                }
                $insert_stmt->close();
            } else {
                $update_stmt = $conn->prepare(
                    "UPDATE payments SET amount = ?, payment_date = ?, payment_method = ?, transaction_id = ?, receipt_number = ?, description = ?, payment_status = ?, created_by = ?, tuition_fee = ? 
                     WHERE payment_id = ?"
                );
                $update_stmt->bind_param("dssssssidi", $amount, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by, $tuition_fee, $payment_id);
                if ($update_stmt->execute()) {
                    $success = "Payment updated successfully!";
                } else {
                    $error = "Error updating payment: " . $conn->error;
                }
                $update_stmt->close();
            }
        }
        $check_stmt->close();
    } else {
        $error = "Missing required fields for payment update.";
    }
}

// Fetch student + payment records
$payments_sql = "
SELECT s.tuition_fee AS student_tuition_fee, s.student_num, s.student_id, s.first_name, s.last_name,
       p.payment_id, p.amount, p.payment_date, p.payment_method, p.transaction_id, p.receipt_number,
       p.description, p.payment_status, p.tuition_fee AS payment_tuition_fee
FROM students s
LEFT JOIN payments p ON s.student_id = p.student_id
";
$payments_result = $conn->query($payments_sql);
?>

<h2>Manage Payments</h2>

<?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
<?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>

<table id="myTable" class="display">
    <thead>
        <tr>
            <th>Student Number</th>
            <th>Name</th>
            <th>Tuition Fee (Current)</th>
            <th>Tuition Fee (Payment)</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Method</th>
            <th>Transaction ID</th>
            <th>Receipt #</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($payments_result && $payments_result->num_rows > 0): ?>
            <?php while ($row = $payments_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_num']) ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>

                    <!-- Tuition Fee Current -->
                    <td>
                        <form method="POST">
                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                            <input type="number" name="tuition_fee" value="<?= htmlspecialchars($row['student_tuition_fee']) ?>" step="0.01">
                            <button type="submit" name="update_tuition">Update</button>
                        </form>
                    </td>

                    <!-- Tuition Fee Payment -->
                    <td>
                        <form method="POST">
                            <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?? '' ?>">
                            <input type="hidden" name="student_id" value="<?= $row['student_id'] ?>">
                            <input type="number" name="tuition_fee" value="<?= $row['payment_tuition_fee'] ?? $row['student_tuition_fee'] ?>" step="0.01">
                    </td>

                    <td><input type="number" name="amount" value="<?= $row['amount'] ?? '' ?>" step="0.01"></td>
                    <td><input type="date" name="payment_date" value="<?= $row['payment_date'] ?? date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"></td>
                    <td>
                        <select name="payment_method">
                            <?php foreach ($payment_method_options as $method): ?>
                                <option value="<?= $method ?>" <?= ($row['payment_method'] ?? '') === $method ? 'selected' : '' ?>><?= $method ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input type="text" name="transaction_id" value="<?= $row['transaction_id'] ?? '' ?>"></td>
                    <td><input type="text" name="receipt_number" value="<?= $row['receipt_number'] ?? '' ?>"></td>
                    <td><input type="text" name="description" value="<?= $row['description'] ?? '' ?>"></td>
                    <td>
                        <select name="payment_status">
                            <?php foreach ($payment_status_options as $status): ?>
                                <option value="<?= $status ?>" <?= ($row['payment_status'] ?? '') === $status ? 'selected' : '' ?>><?= $status ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><button type="submit" name="update_payment">Save</button></form></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="12">No data available.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>

<?php include('../../layouts/footer.php'); ?>
