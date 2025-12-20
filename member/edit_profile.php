<?php
session_start();
include('../config/db_connect.php');

// Ensure member is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Member') {
    header("Location: ../login.php");
    exit;
}

$member_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if (isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    
    // Optional password update
    $password_sql = "";
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_sql = ", password='$password'";
    }

    $update_query = "UPDATE members 
                     SET full_name='$full_name', email='$email', phone='$phone', gender='$gender' $password_sql
                     WHERE id='$member_id'";

    if (mysqli_query($conn, $update_query)) {
        $message = "<p style='color:green; text-align:center;'>Profile updated successfully!</p>";
    } else {
        $message = "<p style='color:red; text-align:center;'>Error: ".mysqli_error($conn)."</p>";
    }
}

// Fetch current member info
$result = mysqli_query($conn, "SELECT * FROM members WHERE id='$member_id'");
$member = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile</title>
<style>
/* ------------------------------
   GLOBAL STYLE
------------------------------- */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: #f4f7fa;
    color: #333;
}

/* ------------------------------
   Header
------------------------------- */
header {
    background: #00c8b3;
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
    max-width: 500px;
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
    color: #00c8b3;
    margin-bottom: 20px;
}

/* ------------------------------
   Form Styling
------------------------------- */
form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

input,
select {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}

button {
    background: #00c8b3;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #019e8f;
}

/* ------------------------------
   Back Button
------------------------------- */
.back-btn {
    display: inline-block;
    margin-bottom: 15px;
    padding: 8px 15px;
    background: #00c8b3;
    color: #fff;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.3s;
}

.back-btn:hover {
    background: #019e8f;
}

/* ------------------------------
   Message Styling
------------------------------- */
.message {
    text-align: center;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<header>Edit Profile</header>

<div class="container">
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

    <h2>Update Your Profile</h2>

    <div class="message"><?php echo $message; ?></div>

    <form method="POST" action="">
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($member['full_name']); ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>

        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" required>

        <label>Gender:</label>
        <select name="gender" required>
            <option value="Male" <?php if($member['gender']=='Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if($member['gender']=='Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if($member['gender']=='Other') echo 'selected'; ?>>Other</option>
        </select>

        <label>New Password (leave blank to keep current):</label>
        <input type="password" name="password" placeholder="Enter new password">

        <button type="submit" name="update_profile">Update Profile</button>
    </form>
</div>

</body>
</html>
