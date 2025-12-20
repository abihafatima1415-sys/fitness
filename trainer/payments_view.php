<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['Trainer','Admin'])) {
    header("Location: ../login.php");
    exit;
}

$query = "
    SELECT p.id AS payment_id, m.full_name, ms.plan_name, p.amount, p.payment_date, p.status
    FROM payments p
    INNER JOIN members m ON p.member_id = m.id
    INNER JOIN memberships ms ON p.membership_id = ms.id
    ORDER BY p.payment_date DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Member Payments | Trainer/Admin Panel</title>
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
    width: 90%;
    max-width: 900px;
    margin: 50px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* ------------------------------
   Headings
------------------------------- */
h2 {
    text-align: center;
    color: var(--primary-color);
    margin-bottom: 20px;
}

/* ------------------------------
   Table Styling
------------------------------- */
table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 12px;
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
   Status Labels
------------------------------- */
.status-pending {
    color: red;
    font-weight: bold;
}

.status-paid {
    color: green;
    font-weight: bold;
}

/* ------------------------------
   Links
------------------------------- */
a {
    color: var(--primary-color);
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

</style>
</head>
<body>

<div class="container">
<h2>Member Payments</h2>

<table>
<thead>
<tr>
    <th>#</th>
    <th>Member Name</th>
    <th>Plan</th>
    <th>Amount ($)</th>
    <th>Payment Date</th>
    <th>Status</th>
</tr>
</thead>
<tbody>
<?php
if(mysqli_num_rows($result) > 0){
    $count = 1;
    while($row = mysqli_fetch_assoc($result)){
        $status_class = $row['status'] === 'Paid' ? 'status-paid' : 'status-pending';
        echo "<tr>
                <td>{$count}</td>
                <td>".htmlspecialchars($row['full_name'])."</td>
                <td>".htmlspecialchars($row['plan_name'])."</td>
                <td>".htmlspecialchars($row['amount'])."</td>
                <td>".date('d M Y', strtotime($row['payment_date']))."</td>
                <td class='{$status_class}'>".htmlspecialchars($row['status'])."</td>
              </tr>";
        $count++;
    }
}else{
    echo "<tr><td colspan='6'>No payments found.</td></tr>";
}
?>
</tbody>
</table>

<p style="text-align:center; margin-top:20px;"><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</div>

</body>
</html>
