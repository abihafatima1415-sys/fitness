<?php
// -----------------------------------------------------------
// Start session and check member authentication
// -----------------------------------------------------------
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Member') {
    header("Location: ../login.php");
    exit;
}

// -----------------------------------------------------------
// Include database connection
// -----------------------------------------------------------
include('../config/connection.php');

// Get member ID from session
$member_id = $_SESSION['user_id'];

// Fetch workout plans for this member
$query = "
    SELECT w.id, w.plan_name, w.exercises, w.duration, w.created_at, t.full_name AS trainer_name
    FROM workouts w
    JOIN trainers t ON w.trainer_id = t.id
    WHERE w.member_id = '$member_id'
    ORDER BY w.created_at DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Workout Plans | Member Dashboard</title>

<style>
/* ------------------------------
   GLOBAL STYLE (Single color #00c8b3)
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
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
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
    z-index: 1000;
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

</style>
</head>

<body>

<header>My Workout Plans</header>

<div class="container">
    <h2>Assigned Workout Plans</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Plan Name</th>
                <th>Exercises</th>
                <th>Duration</th>
                <th>Trainer</th>
                <th>Assigned On</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['plan_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['exercises']); ?></td>
                        <td><?php echo htmlspecialchars($row['duration']); ?></td>
                        <td><?php echo htmlspecialchars($row['trainer_name']); ?></td>
                        <td><?php echo date("d M Y", strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="6">No workout plans assigned yet.</td></tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- Back to Dashboard -->
    <div class="back-btn-container">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

</div>

<footer>
    &copy; <?php echo date("Y"); ?> Fitness Management System | Member Panel
</footer>

</body>
</html>
