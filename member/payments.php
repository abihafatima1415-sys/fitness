<?php
session_start();
include('../config/db_connect.php');

// Ensure only logged-in members can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Member') {
    header("Location: ../login.php");
    exit;
}

$member_id = $_SESSION['user_id'];
$message = "";

// Handle payment simulation
if(isset($_POST['pay_now'])) {
    $payment_id = intval($_POST['payment_id']);
    $update = mysqli_query($conn, "UPDATE payments SET status='Paid', payment_date=NOW() WHERE id='$payment_id' AND member_id='$member_id'");
    if($update) {
        $message = "<p class='success'>Payment Completed Successfully!</p>";
    } else {
        $message = "<p class='error'>Error processing payment.</p>";
    }
}

// Fetch all payments of the member with the correct membership info
$payments_query = "
    SELECT p.*, ms.plan_name 
    FROM payments p
    INNER JOIN memberships ms ON ms.id = p.membership_id
    WHERE p.member_id='$member_id'
    ORDER BY p.payment_date DESC, p.id DESC
";
$payments_result = mysqli_query($conn, $payments_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Payments | Fitness Management System</title>
<style>
/* ------------------------------
   Global Styles
------------------------------- */
body {
    font-family: 'Poppins', sans-serif;
    background: var(--bgcolor);
    margin: 0;
    color: #333;
}

/* ------------------------------
   Container
------------------------------- */
.container {
    max-width: 900px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* ------------------------------
   Headings
------------------------------- */
h2 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 25px;
}

/* ------------------------------
   Messages
------------------------------- */
.message {
    text-align: center;
    font-weight: 600;
    margin-bottom: 20px;
}

.success {
    color: green;
}

.error {
    color: red;
}

/* ------------------------------
   Table Styling
------------------------------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 25px;
}

table th,
table td {
    padding: 12px 10px;
    border: 1px solid #ddd;
    text-align: center;
}

table th {
    background-color: var(--primary-color);
    color: #fff;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* ------------------------------
   Buttons
------------------------------- */
button {
    background-color: var(--primary-color);
    color: #fff;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background-color: #019e8f;
}

/* ------------------------------
   Links
------------------------------- */
a {
    text-decoration: none;
    color: var(--primary-color);
}

a:hover {
    text-decoration: underline;
}

</style>
</head>
<body>

<div class="container">
    <h2>My Payments</h2>
    <div class="message"><?= $message; ?></div>

    <?php if(mysqli_num_rows($payments_result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Plan Name</th>
                    <th>Amount ($)</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                while($payment = mysqli_fetch_assoc($payments_result)) {
                    $status_class = $payment['status'] == 'Paid' ? 'success' : 'error';
                    $payment_date = $payment['payment_date'] ? date('d M Y', strtotime($payment['payment_date'])) : '-';
                    echo "<tr>
                            <td>{$count}</td>
                            <td>".htmlspecialchars($payment['plan_name'])."</td>
                            <td>".htmlspecialchars($payment['amount'])."</td>
                            <td class='{$status_class}'>".htmlspecialchars($payment['status'])."</td>
                            <td>{$payment_date}</td>
                            <td>";
                    if($payment['status'] == 'Pending') {
                        echo "<form method='POST' style='margin:0;'>
                                <input type='hidden' name='payment_id' value='{$payment['id']}'>
                                <button type='submit' name='pay_now'>Pay Now</button>
                              </form>";
                    } else {
                        echo "-";
                    }
                    echo "</td>
                          </tr>";
                    $count++;
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="text-align:center;">No payments found.</p>
    <?php endif; ?>

    <p style="text-align:center; margin-top:20px;"><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</div>

</body>
</html>
