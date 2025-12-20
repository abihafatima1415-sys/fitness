<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Trainer') {
    header("Location: ../login.php");
    exit;
}

$trainer_id = $_SESSION['user_id'];
$message = "";

// Handle attendance submission
if (isset($_POST['submit'])) {
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $attendance_data = $_POST['attendance'] ?? [];

    // Delete previous records for this date
    mysqli_query($conn, "DELETE FROM attendance WHERE trainer_id='$trainer_id' AND attendance_date='$date'");

    // Insert new records
    foreach ($attendance_data as $member_id => $status) {
        $member_id = intval($member_id);
        $status = mysqli_real_escape_string($conn, $status);
        mysqli_query($conn, "INSERT INTO attendance (trainer_id, member_id, attendance_date, status) 
                             VALUES ('$trainer_id', '$member_id', '$date', '$status')");
    }

    $message = "<p style='color:green; text-align:center;'>Attendance saved successfully for $date.</p>";
}

// Handle delete of individual record
if (isset($_GET['delete'])) {
    $record_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM attendance WHERE id='$record_id' AND trainer_id='$trainer_id'");
    header("Location: mark_attendance.php");
    exit;
}

// Fetch all members
$members_result = mysqli_query($conn, "SELECT * FROM members ORDER BY full_name ASC");

// Get selected date
$selected_date = $_POST['date'] ?? date('Y-m-d');

// Fetch existing attendance for selected date
$existing_attendance = [];
$att_result = mysqli_query($conn, "SELECT * FROM attendance WHERE trainer_id='$trainer_id' AND attendance_date='$selected_date'");
while ($row = mysqli_fetch_assoc($att_result)) {
    $existing_attendance[$row['member_id']] = ['status'=>$row['status'], 'id'=>$row['id']];
}

// Fetch all dates with attendance records for this trainer
$dates_result = mysqli_query($conn, "SELECT DISTINCT attendance_date FROM attendance WHERE trainer_id='$trainer_id' ORDER BY attendance_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mark Attendance | Trainer Panel</title>
<style>
/* ------------------------------
   Global Styles
------------------------------- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
}

body {
    background: var(--bgcolor);
    color: #333;
}

/* ------------------------------
   Header
------------------------------- */
header {
    background: var(--primary-color);;
    color: #fff;
    padding: 15px;
    text-align: center;
    font-size: 22px;
    font-weight: 600;
}

/* ------------------------------
   Container
------------------------------- */
.container {
    width: 90%;
    max-width: 900px;
    margin: 30px auto 80px auto;
    background: #fff;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

/* ------------------------------
   Headings
------------------------------- */
h2 {
    text-align: center;
    color: var(--primary-color);;
    margin-bottom: 25px;
}

/* ------------------------------
   Form
------------------------------- */
form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 5px;
    font-weight: 500;
}

input[type="date"],
select {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}

/* ------------------------------
   Buttons
------------------------------- */
button {
    background: var(--primary-color);;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 15px;
    transition: 0.3s;
}

button:hover {
    background: #019e8f;
}

/* Delete button link */
.delete-btn {
    background: #e74c3c;
    color: #fff;
    padding: 5px 10px;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
}

.delete-btn:hover {
    background: #c0392b;
}

/* ------------------------------
   Table Styling
------------------------------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

table th,
table td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
}

table th {
    background: var(--primary-color);;
    color: #fff;
}

table tr:nth-child(even) {
    background: #f9f9f9;
}

/* ------------------------------
   Back Button
------------------------------- */
.back-btn-container {
    text-align: center;
    margin-top: 20px;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: var(--primary-color);;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
}

.back-btn:hover {
    background-color: #019e8f;
}

</style>
</head>
<body>

<header>Mark Attendance</header>

<div class="container">
    <h2>Mark Attendance for Members</h2>

    <?php if(isset($message)) echo $message; ?>

    <!-- Attendance Form -->
    <form method="POST" action="">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" required value="<?= $selected_date; ?>" onchange="this.form.submit()">

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($members_result) > 0){
                    $count = 1;
                    mysqli_data_seek($members_result,0);
                    while($member = mysqli_fetch_assoc($members_result)){
                        $status = $existing_attendance[$member['id']]['status'] ?? 'Absent';
                        $record_id = $existing_attendance[$member['id']]['id'] ?? '';
                        $selected_present = $status === 'Present' ? 'selected' : '';
                        $selected_absent = $status === 'Absent' ? 'selected' : '';
                        echo "<tr>
                                <td>{$count}</td>
                                <td>".htmlspecialchars($member['full_name'])."</td>
                                <td>
                                    <select name='attendance[{$member['id']}]'>
                                        <option value='Present' $selected_present>Present</option>
                                        <option value='Absent' $selected_absent>Absent</option>
                                    </select>
                                </td>
                                <td>";
                        if($record_id){
                            echo "<a href='?delete=$record_id' class='delete-btn' onclick='return confirm(\"Delete attendance for this member?\");'>Delete</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td></tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='4'>No members found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <button type="submit" name="submit">Save Attendance</button>
    </form>

    <!-- Back Button -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

    <!-- Date-wise Attendance List -->
    <h2 style="margin-top:40px; text-align:center;">Attendance Records by Date</h2>
    <?php if(mysqli_num_rows($dates_result) > 0): ?>
        <?php while($d = mysqli_fetch_assoc($dates_result)): ?>
            <?php
            $date = $d['attendance_date'];
            $records = mysqli_query($conn, "SELECT a.*, m.full_name FROM attendance a JOIN members m ON a.member_id=m.id WHERE trainer_id='$trainer_id' AND attendance_date='$date'");
            ?>
            <h3 style="margin-top:20px;">Date: <?= date("d M Y", strtotime($date)); ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while($r = mysqli_fetch_assoc($records)){
                        echo "<tr>
                                <td>{$i}</td>
                                <td>".htmlspecialchars($r['full_name'])."</td>
                                <td>".htmlspecialchars($r['status'])."</td>
                              </tr>";
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px;">No attendance records found.</p>
    <?php endif; ?>
</div>

</body>
</html>
