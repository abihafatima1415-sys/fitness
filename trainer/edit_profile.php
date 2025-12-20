<?php
session_start();
include('../config/db_connect.php');

// Ensure only logged-in trainers can access
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Trainer') {
    header("Location: ../login.php");
    exit;
}

$trainer_id = $_SESSION['user_id'];
$message = "";

// Fetch trainer details
$result = mysqli_query($conn, "SELECT * FROM trainers WHERE id='$trainer_id'");
$trainer = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query = "UPDATE trainers SET full_name='$full_name', password='$password' WHERE id='$trainer_id'";
    } else {
        $update_query = "UPDATE trainers SET full_name='$full_name' WHERE id='$trainer_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        $message = "<p style='color:green;text-align:center;'>Profile updated successfully!</p>";
        $result = mysqli_query($conn, "SELECT * FROM trainers WHERE id='$trainer_id'");
        $trainer = mysqli_fetch_assoc($result);
    } else {
        $message = "<p style='color:red;text-align:center;'>Error updating profile. Try again.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Profile | Trainer Panel</title>
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
    max-width: 600px;
    margin: 60px auto;
    background: #fff;
    padding: 30px;
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

/* Input fields */
input[type="text"],
input[type="password"],
input[readonly] {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
    background: #e9ecef;
}

/* ------------------------------
   Buttons
------------------------------- */
button {
    background: var(--primary-color);
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
   Messages
------------------------------- */
.message {
    text-align: center;
    margin-bottom: 15px;
}

</style>
</head>
<body>

<div class="container">
    <h2>Edit Profile</h2>
    <?php if($message) echo $message; ?>
    <form method="POST">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($trainer['full_name']); ?>">

        <label for="password">New Password (leave blank to keep current)</label>
        <input type="password" id="password" name="password">

        <label for="email">Email</label>
        <input type="text" id="email" value="<?= htmlspecialchars($trainer['email']); ?>" readonly>

        <label for="gender">Gender</label>
        <input type="text" id="gender" value="<?= htmlspecialchars($trainer['gender']); ?>" readonly>

        <label for="shift">Shift</label>
        <input type="text" id="shift" value="<?= htmlspecialchars($trainer['shift']); ?>" readonly>

        <button type="submit">Update Profile</button>
    </form>
    <p style="text-align:center; margin-top:20px;"><a href="dashboard.php">⬅ Back to Dashboard</a></p>
</div>

</body>
</html>
