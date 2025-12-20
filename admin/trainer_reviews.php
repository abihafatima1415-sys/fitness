<?php
session_start();
include('../config/db_connect.php');

// Ensure admin is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch all reviews with member and trainer names
$reviews_query = "
    SELECT tr.*, m.full_name AS member_name, t.full_name AS trainer_name
    FROM trainer_reviews tr
    JOIN members m ON tr.member_id = m.id
    JOIN trainers t ON tr.trainer_id = t.id
    ORDER BY tr.created_at DESC
";
$reviews_result = mysqli_query($conn, $reviews_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Trainer Reviews | Admin Panel</title>
<style>
/* ------------------------------
   Global Reset & Fonts
------------------------------- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
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
    width: 95%;
    max-width: 1200px;
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
    margin-bottom: 20px;
}

/* ------------------------------
   Tables
------------------------------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
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
   Buttons
------------------------------- */
.back-btn {
    display: inline-block;
    margin-bottom: 15px;
    padding: 8px 15px;
    background: var(--primary-color);;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.3s;
}

.back-btn:hover {
    background: #019e8f;
}
</style>
</head>
<body>

<header>All Trainer Reviews</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

    <h2>Reviews Submitted by Members</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Trainer Name</th>
                <th>Member Name</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Reviewed On</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(mysqli_num_rows($reviews_result) > 0){
                $count = 1;
                while($row = mysqli_fetch_assoc($reviews_result)){
                    echo "<tr>
                            <td>{$count}</td>
                            <td>".htmlspecialchars($row['trainer_name'])."</td>
                            <td>".htmlspecialchars($row['member_name'])."</td>
                            <td>{$row['rating']}/5</td>
                            <td>".htmlspecialchars($row['comment'])."</td>
                            <td>".date("d M Y", strtotime($row['created_at']))."</td>
                          </tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='6'>No reviews submitted yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
