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
$payment_message = "";

// Plan prices
$plan_prices = [
    "Basic" => 50,
    "Standard" => 140,
    "Premium" => 270
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = mysqli_real_escape_string($conn, $_POST['plan_name']);
    $duration = intval($_POST['duration']);
    $amount = $plan_prices[$plan_name] ?? 0;
    $start_date = date('Y-m-d');

    // Check for existing active membership of this plan
    $existing_membership_result = mysqli_query($conn, "
        SELECT * FROM memberships 
        WHERE member_id='$member_id' AND plan_name='$plan_name' 
        ORDER BY id DESC LIMIT 1
    ");

    if (mysqli_num_rows($existing_membership_result) > 0) {
        // Renew: extend end_date
        $membership = mysqli_fetch_assoc($existing_membership_result);
        $membership_id = $membership['id'];
        $new_end_date = date('Y-m-d', strtotime("+$duration months", strtotime($membership['end_date'])));

        mysqli_query($conn, "
            UPDATE memberships 
            SET end_date='$new_end_date', status='Active'
            WHERE id='$membership_id'
        ");

        $message = "<p style='color:green;text-align:center;'>Membership Renewed Successfully!</p>";
    } else {
        // New membership
        $end_date = date('Y-m-d', strtotime("+$duration months"));
        mysqli_query($conn, "
            INSERT INTO memberships (member_id, plan_name, start_date, end_date, status)
            VALUES ('$member_id', '$plan_name', '$start_date', '$end_date', 'Active')
        ");
        $membership_id = mysqli_insert_id($conn);
        $message = "<p style='color:green;text-align:center;'>Membership Purchased Successfully!</p>";
    }

    // Insert payment for this plan
    mysqli_query($conn, "
        INSERT INTO payments (member_id, membership_id, amount, payment_date, status)
        VALUES ('$member_id', '$membership_id', '$amount', NOW(), 'Pending')
    ");
}

// Fetch all memberships and their latest payment
$memberships_result = mysqli_query($conn, "
    SELECT ms.*, p.amount, p.payment_date, p.status AS payment_status
    FROM memberships ms
    LEFT JOIN payments p ON p.membership_id = ms.id
    AND p.id = (SELECT MAX(id) FROM payments WHERE membership_id = ms.id)
    WHERE ms.member_id='$member_id'
    ORDER BY ms.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Membership | Fitness Management System</title>
<style>
/* ------------------------------
   Global Styles
------------------------------- */
body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--bgcolor);
    color: #333;
    margin: 0;
}

/* ------------------------------
   Container
------------------------------- */
.container {
    width: 90%;
    max-width: 900px;
    background-color: #fff;
    margin: 50px auto;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* ------------------------------
   Headings
------------------------------- */
h2 {
    text-align: center;
    color: var(--primary-color);;
    margin-bottom: 20px;
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
    border-bottom: 1px solid #ccc;
    text-align: left;
}

table th {
    background-color: var(--primary-color);;
    color: white;
}

/* ------------------------------
   Forms & Inputs
------------------------------- */
form {
    text-align: center;
    margin-top: 20px;
}

select {
    padding: 10px;
    margin-right: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}

/* ------------------------------
   Buttons
------------------------------- */
button {
    background-color: var(--primary-color);;
    color: white;
    border: none;
    padding: 12px 18px;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    display: block;
    margin: 0 auto;
}

button:hover {
    background-color: #019e8f;
}

/* ------------------------------
   Messages
------------------------------- */
.message {
    text-align: center;
    margin-bottom: 20px;
}

.success {
    color: green;
}

.error {
    color: red;
}

/* ------------------------------
   Links
------------------------------- */
a {
    color: var(--primary-color);;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>

<div class="container">
<h2>My Memberships</h2>
<p class="message"><?= $message ?? ''; ?></p>

<?php if(mysqli_num_rows($memberships_result) > 0): ?>
<table>
<tr>
    <th>Plan</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Status</th>
    <th>Latest Payment</th>
</tr>
<?php while($row = mysqli_fetch_assoc($memberships_result)): ?>
<tr>
    <td><?= htmlspecialchars($row['plan_name']); ?></td>
    <td><?= htmlspecialchars($row['start_date']); ?></td>
    <td><?= htmlspecialchars($row['end_date']); ?></td>
    <td><?= htmlspecialchars($row['status']); ?></td>
    <td>
        <?= $row['amount'] ? '$' . htmlspecialchars($row['amount']) . ' on ' . date('d M Y', strtotime($row['payment_date'])) . ' (' . htmlspecialchars($row['payment_status']) . ')' : 'No Payment'; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p style="text-align:center;">No memberships found.</p>
<?php endif; ?>

<form method="POST">
<label for="plan_name">Select Plan:</label>
<select name="plan_name" id="plan_name" required onchange="autoSelectDuration(this.value)">
    <option value="">-- Select Plan --</option>
    <option value="Basic">Basic - 1 Month</option>
    <option value="Standard">Standard - 3 Months</option>
    <option value="Premium">Premium - 6 Months</option>
</select>

<select name="duration" id="duration_select" required>
    <option value="">-- Select Duration --</option>
</select>

<button type="submit">Purchase / Renew Membership</button>
</form>

<p style="text-align:center; margin-top:25px;"><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</div>

<script>
function autoSelectDuration(plan){
    const durationSelect = document.getElementById('duration_select');
    durationSelect.innerHTML = '';
    if(plan === 'Basic') durationSelect.innerHTML = '<option value="1">1 Month</option>';
    if(plan === 'Standard') durationSelect.innerHTML = '<option value="3">3 Months</option>';
    if(plan === 'Premium') durationSelect.innerHTML = '<option value="6">6 Months</option>';
}
</script>

</body>
</html>
