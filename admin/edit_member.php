<?php
// -----------------------------------------------------------
// Start session and verify admin authentication
// -----------------------------------------------------------
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// -----------------------------------------------------------
// Include database connection
// -----------------------------------------------------------
include('../config/db_connect.php');

// -----------------------------------------------------------
// Check if ID is provided
// -----------------------------------------------------------
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_members.php");
    exit;
}

$member_id = $_GET['id'];

// -----------------------------------------------------------
// Fetch existing member data
// -----------------------------------------------------------
$query = "SELECT * FROM members WHERE id = '$member_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: manage_members.php");
    exit;
}

$member = mysqli_fetch_assoc($result);

// -----------------------------------------------------------
// Update member record
// -----------------------------------------------------------
if (isset($_POST['update_member'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update = "UPDATE members SET full_name='$full_name', email='$email', password='$password', gender='$gender' WHERE id='$member_id'";
    } else {
        $update = "UPDATE members SET full_name='$full_name', email='$email', gender='$gender' WHERE id='$member_id'";
    }

    if (mysqli_query($conn, $update)) {
        header("Location: manage_members.php");
        exit;
    } else {
        $error = "Error updating member: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Member | Admin Panel</title>

    <style>
        /* =====================================================
           GLOBAL THEME (#00c8b3)
        ====================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: #f4f7fa;
            color: #333;
        }

        header {
            background-color: #00c8b3;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
        }

        .container {
            width: 90%;
            max-width: 500px;
            margin: 40px auto 80px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #00c8b3;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        form input:focus, form select:focus {
            border-color: #00c8b3;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #00c8b3;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 500;
        }

        button:hover {
            background-color: #019e8f;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #00c8b3;
            text-decoration: none;
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #00c8b3;
            color: #fff;
            text-align: center;
            padding: 12px 0;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }

        .error {
            background: #ffe5e5;
            color: #d9534f;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

    </style>
</head>
<body>

    <header>Edit Member</header>

    <div class="container">
        <h2>Update Member Information</h2>

        <?php if (isset($error)) { echo "<div class='error'>$error</div>"; } ?>

        <form method="POST" action="">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($member['full_name']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>

            <label>Password (Leave blank to keep existing)</label>
            <input type="password" name="password" placeholder="Enter new password (optional)">

            <label>Gender</label>
            <select name="gender" required>
                <option value="Male" <?php if($member['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if($member['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            </select>

            <button type="submit" name="update_member">Update Member</button>
        </form>

        <a href="manage_members.php" class="back-link">← Back to Members List</a>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Fitness Management System | Admin Panel
    </footer>

</body>
</html>
