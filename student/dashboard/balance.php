<?php 
$page_title = "Payment History - Datamex College of Saint Adeline";
include_once('../../database/conn.php'); // Database connection (includes sanitize_input)
include('../../layouts/header.php'); // Header layout

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Verify the user is a student and get their student_id and tuition_fee
$sql = "SELECT student_id, first_name, last_name, tuition_fee FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<p class='error-message'>No student profile found for this user (User ID: " . htmlspecialchars($user_id) . ").</p>";
    $conn->close();
    include('../../layouts/footer.php');
    exit();
}

// Use the student_id to fetch payment history
$student_id = $student['student_num'];
$sql = "SELECT payment_id, amount, payment_date, payment_method, transaction_id, receipt_number, description, payment_status, created_at 
        FROM payments 
        WHERE student_id = ? 
        ORDER BY payment_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$payments = $stmt->get_result();

// Calculate total paid (only Completed payments)
$sql = "SELECT SUM(amount) as total_paid 
        FROM payments 
        WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$total_paid_row = $result->fetch_assoc();
$total_paid = $total_paid_row['total_paid'] ?? 0.00;

// Calculate balance
$total_due = $student['tuition_fee'] ?? 0.00; // From students table
$balance = $total_due - $total_paid;

$stmt->close();
?>

<br><br>
<div class="content">
    <div class="header-container">
        <!-- Balance Info (Top Left) -->
        <div class="balance-info">
            <h3>Balance Summary</h3>
            <p>Total Amount Paid: <?php echo number_format($total_paid, 2); ?></p>
            <p>Total Balance: <span style="color:red"><?php echo number_format($balance, 2); ?></span></p>
        </div>

        <!-- QR Code Placeholder (Top Right) -->
        <div class="qr-code-placeholder">
            <p><img src="../../images/gcash-qr-code.png" alt="Gcash QR Code" width="200" height="200"></p>
            <p>To pay your remaining balance, please scan the QR code provided.</p>
        </div>
    </div>

    <h2 class="page-header-title">Payment History</h2>

    <!-- Message/Error Display -->
    <?php if (isset($_GET['message'])): ?>
        <div class="message success-message">
            <?php echo sanitize_input($conn, $_GET['message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="message error-message">
            <?php echo sanitize_input($conn, $_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <?php if ($payments->num_rows > 0): ?>
            <table id="myTable" class="data-table">
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Transaction ID</th>
                        <th>Receipt Number</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
                            <td><?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($row['transaction_id'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['receipt_number'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['description'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No payment history found for Student ID: <?php echo htmlspecialchars($student_num); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php 
$conn->close();
include('../../layouts/footer.php'); // Footer layout
?>