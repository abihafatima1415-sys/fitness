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
include('../config/connection.php');

// -----------------------------------------------------------
// Fetch trainer data by ID
// -----------------------------------------------------------
if (isset($_GET['id'])) {
    $trainer_id = $_GET['id'];
    $query = "SELECT * FROM trainers WHERE id = '$trainer_id'";
    $result = mysqli_query($conn, $query);
    $trainer = mysqli_fetch_assoc($result);
} else {
    header("Location: manage_trainers.php");
    exit;
}

// -----------------------------------------------------------
// Update trainer data when form is submitted
// -----------------------------------------------------------
if (isset($_POST['update'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Only update password if not empty
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "UPDATE trainers 
                         SET full_name='$full_name', email='$email', gender='$gender', shift='$shift', password='$hashed_password'
                         WHERE id='$trainer_id'";
    } else {
        $update_query = "UPDATE trainers 
                         SET full_name='$full_name', email='$email', gender='$gender', shift='$shift'
                         WHERE id='$trainer_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        header("Location: manage_trainers.php");
        exit;
    } else {
        echo "<script>alert('Error updating trainer');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Trainer | Admin Panel</title>

    <style>
        /* =====================================================
           GLOBAL STYLE (Single color theme #00c8b3)
        ====================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            background-color: var(--bgcolor);
            color: #333;
        }

        header {
            background-color: var(--primary-color);
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
            color: var(--primary-color);
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        button {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 10px 20px;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
        }

        button:hover {
            background-color: #019e8f;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: var(--primary-color);
            font-weight: 500;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* =====================================================
           FOOTER STYLE
        ====================================================== */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 12px 0;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header><a href="<?php echo $base_url;?>/admin/dashboard.php">Dashboard</a>Edit Trainer</header>

    <!-- ================= CONTENT ================= -->
    <div class="container">
        <h2>Update Trainer Details</h2>
        <form method="POST" action="">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($trainer['full_name']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($trainer['email']); ?>" required>

            <label>Gender</label>
            <select name="gender" required>
                <option value="Male" <?php echo ($trainer['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($trainer['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>

            <label>Shift</label>
            <select name="shift" required>
                <option value="Morning" <?php echo ($trainer['shift'] == 'Morning') ? 'selected' : ''; ?>>Morning</option>
                <option value="Evening" <?php echo ($trainer['shift'] == 'Evening') ? 'selected' : ''; ?>>Evening</option>
                <option value="Night" <?php echo ($trainer['shift'] == 'Night') ? 'selected' : ''; ?>>Night</option>
            </select>

            <label>New Password (optional)</label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">

            <button type="submit" name="update">Update Trainer</button>
        </form>

        <a href="manage_trainers.php" class="back-link">← Back to Trainers List</a>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        &copy; <?php echo date("Y"); ?> Fitness Management System | Admin Panel
    </footer>

</body>
</html>
