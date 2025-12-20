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
// Initialize variables and handle form submission
// -----------------------------------------------------------
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $shift = mysqli_real_escape_string($conn, $_POST['shift']);

    // Hash password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert trainer into database
    $insert_query = "INSERT INTO trainers (full_name, email, password, gender, shift) 
                     VALUES ('$full_name', '$email', '$hashed_password', '$gender', '$shift')";

    if (mysqli_query($conn, $insert_query)) {
        $message = "Trainer added successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Trainer | Admin Panel</title>

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
            margin-bottom: 20px;
        }

        form label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            outline: none;
            transition: 0.3s;
        }

        form input:focus, form select:focus {
            border-color: var(--primary-color);
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #019e8f;
        }

        .message {
            text-align: center;
            font-weight: 500;
            margin-bottom: 15px;
        }

        .back-link {
            display: block;
            text-align: center;
            text-decoration: none;
            color: var(--primary-color);
            margin-top: 10px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

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
            letter-spacing: 0.5px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
    </style>
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header><a href="<?php echo $base_url;?>/admin/dashboard.php">Dashboard</a>Add New Trainer</header>

    <!-- ================= FORM SECTION ================= -->
    <div class="container">
        <h2>Trainer Registration</h2>

        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>

        <form method="POST" action="">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <label>Gender:</label>
            <select name="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <label>Shift:</label>
            <select name="shift" required>
                <option value="">-- Select Shift --</option>
                <option value="Morning">Morning</option>
                <option value="Evening">Evening</option>
                <option value="Night">Night</option>
            </select>

            <button type="submit" class="btn-submit">Add Trainer</button>
        </form>

        <a href="manage_trainers.php" class="back-link">← Back to Manage Trainers</a>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        &copy; <?php echo date("Y"); ?> Fitness Management System | Admin Panel
    </footer>

</body>
</html>
