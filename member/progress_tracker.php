<?php
session_start();
include('../config/db_connect.php');

// Check if member is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Member') {
    header("Location: ../login.php");
    exit;
}

// Fetch progress reports for the logged-in member
$member_id = $_SESSION['user_id'];
$query = "SELECT p.*, t.full_name AS trainer_name 
          FROM progress_reports p
          JOIN trainers t ON p.trainer_id = t.id
          WHERE p.member_id = '$member_id'
          ORDER BY p.report_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Progress Tracker | Member Panel</title>
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
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
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
   Footer
------------------------------- */
footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: var(--primary-color);;
    color: #fff;
    text-align: center;
    padding: 12px 0;
    font-size: 15px;
    font-weight: 500;
    letter-spacing: 0.5px;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
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

<header>Progress Tracker</header>

<div class="container">
    <h2>Your Fitness Progress</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Trainer</th>
                <th>Weight (kg)</th>
                <th>Body Fat (%)</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(mysqli_num_rows($result) > 0){
                $count = 1;
                while($row = mysqli_fetch_assoc($result)){
                    echo "<tr>
                            <td>{$count}</td>
                            <td>{$row['report_date']}</td>
                            <td>".htmlspecialchars($row['trainer_name'])."</td>
                            <td>".htmlspecialchars($row['weight'])."</td>
                            <td>".htmlspecialchars($row['body_fat'])."</td>
                            <td>".htmlspecialchars($row['notes'])."</td>
                          </tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='6'>No progress reports found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <!-- Back to Dashboard -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</div>

<footer>&copy; <?php echo date("Y"); ?> Fitness Management System | Member Panel</footer>

</body>
</html>
