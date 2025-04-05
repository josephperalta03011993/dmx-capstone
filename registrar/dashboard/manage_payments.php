<?php
$page_title = "Manage Payments";
include_once('../../database/conn.php');
include_once('../../layouts/header.php');

$success = null;
$error = null;

// Get Payment Status and Method Options
$payment_status_options = [];
$payment_status_sql = "SHOW COLUMNS FROM payments LIKE 'payment_status'";
$payment_status_result = $conn->query($payment_status_sql);
if ($payment_status_result && $payment_status_row = $payment_status_result->fetch_assoc()) {
    if (preg_match("/^enum\(\'(.*)\'\)$/", $payment_status_row['Type'], $matches)) {
        $payment_status_options = array_map('trim', explode("','", $matches[1]));
    } else {
        $error = "Error: Could not parse payment_status ENUM values.";
    }
} else {
    $error = "Error: Failed to retrieve payment_status column info.";
}

$payment_method_options = [];
$payment_method_sql = "SHOW COLUMNS FROM payments LIKE 'payment_method'";
$payment_method_result = $conn->query($payment_method_sql);
if ($payment_method_result && $payment_method_row = $payment_method_result->fetch_assoc()) {
    if (preg_match("/^enum\(\'(.*)\'\)$/", $payment_method_row['Type'], $matches)) {
        $payment_method_options = array_map('trim', explode("','", $matches[1]));
    } else {
        $error = "Error: Could not parse payment_method ENUM values.";
    }
} else {
    $error = "Error: Failed to retrieve payment_method column info.";
}

// Update both Tuition Fee and Payment with one submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_all"])) {
    $student_id = sanitize_input($conn, $_POST["student_id"]);
    $tuition_fee = sanitize_input($conn, $_POST["tuition_fee"]);
    $payment_id = isset($_POST["payment_id"]) && !empty($_POST["payment_id"]) ? sanitize_input($conn, $_POST["payment_id"]) : null;
    $amount_paid = sanitize_input($conn, $_POST["amount_paid"]);
    $payment_date = sanitize_input($conn, $_POST["payment_date"]);
    $payment_method = sanitize_input($conn, $_POST["payment_method"]);
    $transaction_id = isset($_POST["transaction_id"]) ? sanitize_input($conn, $_POST["transaction_id"]) : '';
    $receipt_number = isset($_POST["receipt_number"]) ? sanitize_input($conn, $_POST["receipt_number"]) : '';
    $description = isset($_POST["description"]) ? sanitize_input($conn, $_POST["description"]) : '';
    $payment_status = trim(sanitize_input($conn, $_POST["payment_status"]));
    $created_by = $_SESSION['user_id'];

    // Check if student exists
    $check_student = "SELECT student_id FROM students WHERE student_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_student);
    mysqli_stmt_bind_param($check_stmt, "i", $student_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        $error = "Error: Student ID does not exist.";
    } else {
        // Update tuition fee in students table
        $update_tuition_sql = "UPDATE students SET tuition_fee = ? WHERE student_id = ?";
        $tuition_stmt = mysqli_prepare($conn, $update_tuition_sql);
        mysqli_stmt_bind_param($tuition_stmt, "di", $tuition_fee, $student_id);
        $tuition_success = mysqli_stmt_execute($tuition_stmt);
        
        // Update or insert payment
        if ($payment_id) {
            $update_payment_sql = "UPDATE payments SET amount = ?, payment_date = ?, payment_method = ?, transaction_id = ?, receipt_number = ?, description = ?, payment_status = ?, created_by = ?, tuition_fee = ? WHERE payment_id = ?";
            $payment_stmt = mysqli_prepare($conn, $update_payment_sql);
            mysqli_stmt_bind_param($payment_stmt, "dssssssidi", $amount_paid, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by, $tuition_fee, $payment_id);
            $payment_success = mysqli_stmt_execute($payment_stmt);
            mysqli_stmt_close($payment_stmt);
        } else if (!empty($amount_paid)) {
            $insert_payment_sql = "INSERT INTO payments (student_id, amount, payment_date, payment_method, transaction_id, receipt_number, description, payment_status, created_by, tuition_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $payment_stmt = mysqli_prepare($conn, $insert_payment_sql);
            mysqli_stmt_bind_param($payment_stmt, "idssssssdi", $student_id, $amount_paid, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by, $tuition_fee);
            $payment_success = mysqli_stmt_execute($payment_stmt);
            mysqli_stmt_close($payment_stmt);
        }

        if ($tuition_success && (isset($payment_success) ? $payment_success : true)) {
            $success = "Record updated successfully!";
        } else {
            $error = "Error updating record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($tuition_stmt);
    }
    mysqli_stmt_close($check_stmt);
}

// Update or Insert Payment (for new payment form)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_payment"])) {
    $student_id = sanitize_input($conn, $_POST["student_id"]);
    $amount_paid = sanitize_input($conn, $_POST["amount_paid"]);
    $payment_date = sanitize_input($conn, $_POST["payment_date"]);
    $payment_method = sanitize_input($conn, $_POST["payment_method"]);
    $transaction_id = isset($_POST["transaction_id"]) ? sanitize_input($conn, $_POST["transaction_id"]) : '';
    $receipt_number = isset($_POST["receipt_number"]) ? sanitize_input($conn, $_POST["receipt_number"]) : '';
    $description = isset($_POST["description"]) ? sanitize_input($conn, $_POST["description"]) : '';
    $payment_status = trim(sanitize_input($conn, $_POST["payment_status"]));
    $created_by = $_SESSION['user_id'];
    $tuition_fee = sanitize_input($conn, $_POST["tuition_fee"]);

    $check_student = "SELECT student_id FROM students WHERE student_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_student);
    mysqli_stmt_bind_param($check_stmt, "i", $student_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) == 0) {
        $error = "Error: Student ID does not exist.";
    } elseif (empty($payment_status_options) || !in_array($payment_status, $payment_status_options, true)) {
        $error = "Error: Invalid payment status value submitted.";
    } else {
        $insert_sql = "INSERT INTO payments (student_id, amount, payment_date, payment_method, transaction_id, receipt_number, description, payment_status, created_by, tuition_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_sql);
        mysqli_stmt_bind_param($insert_stmt, "idssssssid", $student_id, $amount_paid, $payment_date, $payment_method, $transaction_id, $receipt_number, $description, $payment_status, $created_by, $tuition_fee);
        if (mysqli_stmt_execute($insert_stmt)) {
            $success = "Payment recorded successfully!";
        } else {
            $error = "Error recording payment: " . mysqli_error($conn);
        }
        mysqli_stmt_close($insert_stmt);
    }
    mysqli_stmt_close($check_stmt);
}

// Get Student Payment Data
$payments_sql = "SELECT s.tuition_fee AS student_tuition_fee, s.student_num, s.student_id AS student_id, s.first_name, s.last_name,
                    p.payment_id, p.amount AS amount_paid, p.payment_date, p.payment_method, p.transaction_id, p.receipt_number, p.description,
                    p.payment_status, p.tuition_fee AS payment_tuition_fee
                    FROM students s
                    LEFT JOIN payments p ON s.student_id = p.student_id";
$payments_result = $conn->query($payments_sql);
?>

<h2>Manage Payments</h2>

<?php if ($success) { echo "<p style='color: green;'>$success</p>"; } ?>
<?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>

<table id="myTable" class="display">
    <thead>
        <tr>
            <th>Student Number</th>
            <th>Student Name</th>
            <th>Tuition Fee (Needs to Pay)</th>
            <th>Amount Paid</th>
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
                echo "<td data-value='" . htmlspecialchars($row['student_num']) ."'>" . htmlspecialchars($row['student_num']) . "</td>";
                echo "<td data-value='" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "'>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                
                // Single form for both tuition and payment updates
                echo "<form method='POST'>";
                echo "<input type='hidden' name='update_all'>";
                echo "<input type='hidden' name='payment_id' value='" . (isset($row['payment_id']) ? htmlspecialchars($row['payment_id']) : '') . "'>";
                echo "<input type='hidden' name='student_id' value='" . htmlspecialchars($row['student_id']) . "'>";
                
                // Tuition Fee
                echo "<td data-value='" . htmlspecialchars($row['student_tuition_fee']) . "'>";
                echo "<input type='number' name='tuition_fee' value='" . htmlspecialchars($row['student_tuition_fee']) . "' step='0.01'>";
                echo "</td>";
                
                // Amount Paid
                echo "<td data-value='" . (isset($row['amount_paid']) && !empty($row['amount_paid']) ? htmlspecialchars($row['amount_paid']) : 'N/A') . "'>";
                echo "<input type='number' name='amount_paid' value='" . (isset($row['amount_paid']) ? htmlspecialchars($row['amount_paid']) : '') . "' step='0.01'>";
                echo "</td>";
                
                // Payment Date
                echo "<td data-value='" . (isset($row['payment_date']) && !empty($row['payment_date']) ? htmlspecialchars($row['payment_date']) : 'N/A') . "'>";
                echo "<input type='date' name='payment_date' value='" . (isset($row['payment_date']) ? htmlspecialchars($row['payment_date']) : date('Y-m-d')) . "' max='" . date('Y-m-d') ."'></td>";
                
                // Payment Method
                echo "<td data-value='" . (isset($row['payment_method']) && !empty($row['payment_method']) ? htmlspecialchars($row['payment_method']) : 'N/A') . "'>";
                echo "<select name='payment_method'>";
                foreach ($payment_method_options as $option) {
                    echo "<option value='" . htmlspecialchars($option) . "'" . (isset($row['payment_method']) && $row['payment_method'] === $option ? " selected" : "") . ">" . htmlspecialchars($option) . "</option>";
                }
                echo "</select></td>";
                
                // Transaction ID
                echo "<td data-value='" . (isset($row['transaction_id']) && !empty($row['transaction_id']) ? htmlspecialchars($row['transaction_id']) : 'N/A') . "'>";
                echo "<input type='text' name='transaction_id' value='" . (isset($row['transaction_id']) ? htmlspecialchars($row['transaction_id']) : '') . "'></td>";
                
                // Receipt Number
                echo "<td data-value='" . (isset($row['receipt_number']) && !empty($row['receipt_number']) ? htmlspecialchars($row['receipt_number']) : 'N/A') . "'>";
                echo "<input type='text' name='receipt_number' value='" . (isset($row['receipt_number']) ? htmlspecialchars($row['receipt_number']) : '') . "'></td>";
                
                // Description
                echo "<td data-value='" . (isset($row['description']) && !empty($row['description']) ? htmlspecialchars($row['description']) : 'N/A') . "'>";
                echo "<input type='text' name='description' value='" . (isset($row['description']) ? htmlspecialchars($row['description']) : '') . "'></td>";
                
                // Payment Status
                echo "<td data-value='" . (isset($row['payment_status']) && !empty($row['payment_status']) ? htmlspecialchars($row['payment_status']) : 'N/A') . "'>";
                echo "<select name='payment_status'>";
                foreach ($payment_status_options as $option) {
                    $option = trim($option);
                    $selected = (isset($row['payment_status']) && trim($row['payment_status']) === $option) ? " selected" : "";
                    echo "<option value='" . htmlspecialchars($option) . "'$selected>" . htmlspecialchars($option) . "</option>";
                }
                echo "</select></td>";
                
                // Single Update Button
                echo "<td><button type='submit' class='btn btn-sm btn-success'>Update</button></form></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No payment data found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- <h3>Record New Payment</h3>
<form method="POST">
    <input type="hidden" name="new_payment">
    <div class="form-group">
        <label for="student_id">Student ID:</label>
        <input type="number" class="form-control" id="student_id" name="student_id" required>
    </div>
    <div class="form-group">
        <label for="tuition_fee">Tuition Fee (Needs to Pay):</label>
        <input type="number" class="form-control" id="tuition_fee" name="tuition_fee" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="amount_paid">Amount Paid:</label>
        <input type="number" class="form-control" id="amount_paid" name="amount_paid" step="0.01" required>
    </div>
    <div class="form-group">
        <label for="payment_date">Payment Date:</label>
        <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>" required>
    </div>
    <div class="form-group">
        <label for="payment_method">Payment Method:</label>
        <select class="form-control" id="payment_method" name="payment_method" required>
            <?php
            foreach ($payment_method_options as $option) {
                echo "<option value='" . htmlspecialchars($option) . "'>" . htmlspecialchars($option) . "</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="transaction_id">Transaction ID:</label>
        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
    </div>
    <div class="form-group">
        <label for="receipt_number">Receipt Number:</label>
        <input type="text" class="form-control" id="receipt_number" name="receipt_number">
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <input type="text" class="form-control" id="description" name="description">
    </div>
    <div class="form-group">
        <label for="payment_status">Payment Status:</label>
        <select class="form-control" id="payment_status" name="payment_status" required>
            <?php
            foreach ($payment_status_options as $option) {
                echo "<option value='" . htmlspecialchars($option) . "'>" . htmlspecialchars($option) . "</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Record Payment</button>
</form> -->

<script>
    var pageTitle = '<?php echo $page_title; ?>';
</script>
<?php include('../../layouts/footer.php'); ?>