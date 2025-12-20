<?php
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Trainer') {
    header("Location: ../login.php");
    exit;
}

// Handle adding/updating progress
if (isset($_POST['submit'])) {
    $member_id = intval($_POST['member_id']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $body_fat = mysqli_real_escape_string($conn, $_POST['body_fat']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $check_query = "SELECT * FROM progress_reports WHERE member_id='$member_id' AND report_date='$date'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE progress_reports SET weight='$weight', body_fat='$body_fat', notes='$notes' 
                         WHERE member_id='$member_id' AND report_date='$date'";
        mysqli_query($conn, $update_query);
        $message = "<p style='color:green;text-align:center;'>Progress updated successfully.</p>";
    } else {
        $insert_query = "INSERT INTO progress_reports (member_id, trainer_id, report_date, weight, body_fat, notes)
                         VALUES ('$member_id', '{$_SESSION['user_id']}', '$date', '$weight', '$body_fat', '$notes')";
        mysqli_query($conn, $insert_query);
        $message = "<p style='color:green;text-align:center;'>Progress added successfully.</p>";
    }
}

// Fetch members
$members_result = mysqli_query($conn, "SELECT * FROM members ORDER BY full_name ASC");

// Fetch progress reports
$progress_result = mysqli_query($conn, "SELECT p.*, m.full_name FROM progress_reports p 
                   JOIN members m ON p.member_id = m.id 
                   WHERE p.trainer_id='{$_SESSION['user_id']}' 
                   ORDER BY p.report_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Progress Reports | Trainer Panel</title>
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
    background: var(--primary-color);
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
    max-width: 1000px;
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
    color: var(--primary-color);
    margin-bottom: 25px;
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
    background: var(--primary-color);
    color: #fff;
}

table tr:nth-child(even) {
    background: #f9f9f9;
}

/* ------------------------------
   Buttons
------------------------------- */
button {
    background: var(--primary-color);
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    margin: 5px;
    transition: 0.3s;
}

button:hover {
    background: #019e8f;
}

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
   Form Elements
------------------------------- */
label {
    font-weight: 500;
    margin-top: 10px;
    display: block;
}

input,
textarea,
select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}

textarea {
    resize: none;
}

/* ------------------------------
   Modal
------------------------------- */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
}

.close {
    color: #aaa;
    float: right;
    font-size: 24px;
    cursor: pointer;
}

.close:hover {
    color: #333;
}

/* ------------------------------
   Back Button
------------------------------- */
.back-btn-container {
    text-align: center;
    margin: 15px 0;
}

.back-btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: var(--primary-color);
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s;
}

.back-btn:hover {
    background-color: #019e8f;
}

/* ------------------------------
   Footer
------------------------------- */
footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: var(--primary-color);
    color: #fff;
    text-align: center;
    padding: 12px 0;
    font-size: 15px;
    font-weight: 500;
    letter-spacing: 0.5px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

</style>
</head>
<body>

<header>Progress Reports</header>

<div class="container">
    <h2>Members’ Progress</h2>

    <?php if(isset($message)) echo $message; ?>

    <!-- Add / Update Modal Button -->
    <button onclick="openModal()">Add / Update Progress</button>

    <!-- Progress Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Member Name</th>
                <th>Date</th>
                <th>Weight (kg)</th>
                <th>Body Fat (%)</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(mysqli_num_rows($progress_result) > 0){
                $count = 1;
                while($row = mysqli_fetch_assoc($progress_result)){
                    echo "<tr>
                            <td>{$count}</td>
                            <td>".htmlspecialchars($row['full_name'])."</td>
                            <td>{$row['report_date']}</td>
                            <td>{$row['weight']}</td>
                            <td>{$row['body_fat']}</td>
                            <td>".htmlspecialchars($row['notes'])."</td>
                          </tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='6'>No progress records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <!-- Back to Dashboard -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

</div>

<!-- Modal Form -->
<div id="progressModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Add / Update Progress</h3>
        <form method="POST" action="">
            <label for="member_id">Member:</label>
            <select id="member_id" name="member_id" required>
                <option value="" disabled selected>Select Member</option>
                <?php while($member = mysqli_fetch_assoc($members_result)){
                    echo "<option value='{$member['id']}'>".htmlspecialchars($member['full_name'])."</option>";
                } ?>
            </select>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">

            <label for="weight">Weight (kg):</label>
            <input type="number" step="0.1" name="weight" required>

            <label for="body_fat">Body Fat (%):</label>
            <input type="number" step="0.1" name="body_fat" required>

            <label for="notes">Notes:</label>
            <textarea name="notes" rows="3"></textarea>

            <button type="submit" name="submit">Save Progress</button>
        </form>
    </div>
</div>

<script>
function openModal() { document.getElementById('progressModal').style.display='block'; }
function closeModal() { document.getElementById('progressModal').style.display='none'; }
window.onclick = function(event){ if(event.target == document.getElementById('progressModal')){ closeModal(); } }
</script>

<footer>&copy; <?php echo date("Y"); ?> Fitness Management System | Trainer Panel</footer>

</body>
</html>
