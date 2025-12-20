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
if (!isset($_GET['id'])) {
    header("Location: manage_trainers.php");
    exit;
}

$trainer_id = $_GET['id'];

// -----------------------------------------------------------
// Fetch trainer details
// -----------------------------------------------------------
$query = "SELECT * FROM trainers WHERE id = '$trainer_id'";
$result = mysqli_query($conn, $query);
$trainer = mysqli_fetch_assoc($result);

if (!$trainer) {
    header("Location: manage_trainers.php");
    exit;
}

// -----------------------------------------------------------
// Update trainer shift in SQL
// -----------------------------------------------------------
if (isset($_POST['update_shift'])) {
    $new_shift = mysqli_real_escape_string($conn, $_POST['shift']);
    $update_query = "UPDATE trainers SET shift = '$new_shift' WHERE id = '$trainer_id'";
    if (mysqli_query($conn, $update_query)) {
        $success = "Trainer shift updated successfully!";
        // Refresh data
        $result = mysqli_query($conn, "SELECT * FROM trainers WHERE id = '$trainer_id'");
        $trainer = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating shift: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Trainer Shift | Admin Panel</title>

    <style>
        /* =====================================================
           GLOBAL STYLE
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            background-color: #00c8b3;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background-color: #019e8f;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            background-color: #3498db;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .back-btn:hover {
            background-color: #217dbb;
        }

        .alert {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        .success {
            background-color: #2ecc71;
            color: #fff;
        }

        .error {
            background-color: #e74c3c;
            color: #fff;
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
    </style>
</head>
<body>

<header>Modify Trainer Shift</header>

<div class="container">
    <h2>Update Trainer Shift</h2>

    <?php if (isset($success)) echo "<div class='alert success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert error'>$error</div>"; ?>

    <form method="POST">
        <label>Trainer Name:</label>
        <input type="text" value="<?php echo htmlspecialchars($trainer['full_name']); ?>" disabled>

        <label>Email:</label>
        <input type="text" value="<?php echo htmlspecialchars($trainer['email']); ?>" disabled>

        <label>Current Shift:</label>
        <input type="text" value="<?php echo htmlspecialchars($trainer['shift']); ?>" disabled>

        <label>New Shift:</label>
        <select name="shift" required>
            <option value="">-- Select New Shift --</option>
            <option value="Morning" <?php if ($trainer['shift'] === 'Morning') echo 'selected'; ?>>Morning</option>
            <option value="Evening" <?php if ($trainer['shift'] === 'Evening') echo 'selected'; ?>>Evening</option>
            <option value="Night" <?php if ($trainer['shift'] === 'Night') echo 'selected'; ?>>Night</option>
        </select>

        <button type="submit" name="update_shift">Update Shift</button>
    </form>

    <a href="manage_trainers.php" class="back-btn">Back to Manage Trainers</a>
</div>

<footer>
    &copy; <?php echo date("Y"); ?> Fitness Management System | Admin Panel
</footer>

</body>
</html>
