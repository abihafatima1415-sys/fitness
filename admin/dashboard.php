<?php
// --------------------------------------------
// Start Session and Validate Admin Login
// --------------------------------------------
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

// --------------------------------------------
// Include Database Connection
// --------------------------------------------
include('../config/db_connect.php');

// Admin name from session
$admin_name = $_SESSION['user_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Fitness Management System</title>

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
            display: flex; /* Layout: Sidebar + Content */
            background-color: #f5f7fa; /* Light background */
            color: #333; /* Readable dark text */
            min-height: 100vh; /* Full height */
        }

        /* =====================================================
           SIDEBAR
        ====================================================== */
        .sidebar {
            width: 260px;
            background-color: var(--primary-color);; /* Project main color */
            color: #fff;
            padding: 25px 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar h2 {
            text-align: center;
            font-size: 22px;
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .nav-links {
            list-style: none;
            padding: 0;
        }

        .nav-links li {
            margin: 10px 0;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background-color: #019e8f; /* Slight hover effect */
        }

        .logout {
            text-align: center;
            margin-bottom: 20px;
        }

        .logout a {
            text-decoration: none;
            background: #019e8f;
            padding: 10px 20px;
            border-radius: 6px;
            color: #fff;
        }

        .logout a:hover {
            background-color: #007d73;
        }

        /* =====================================================
           MAIN CONTENT AREA
        ====================================================== */
        .main-content {
            flex: 1; /* Remaining width */
            padding: 30px;
        }

        header {
            background-color: var(--primary-color);;
            color: #fff;
            padding: 15px 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 24px;
        }

        .welcome {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .welcome h2 {
            color: var(--primary-color);;
            margin-bottom: 10px;
        }

        /* =====================================================
           DASHBOARD FUNCTION CARDS
        ====================================================== */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            color: var(--primary-color);;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 14px;
            margin-bottom: 15px;
        }

        .card a {
            text-decoration: none;
            background: var(--primary-color);;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.3s;
        }

        .card a:hover {
            background-color: #019e8f;
        }

        /* =====================================================
           FOOTER
        ====================================================== */
        footer {
            text-align: center;
            margin-top: 40px;
            padding: 15px;
            background-color: var(--primary-color);;
            color: #fff;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- ===================== SIDEBAR ===================== -->
    <div class="sidebar">
        <div>
            <h2>Admin Panel</h2>
            <ul class="nav-links">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="manage_members.php">Manage Members</a></li>
                <li><a href="manage_trainers.php">Manage Trainers</a></li>
                <li><a href="payments.php">Manage Payments</a></li>
                <li><a href="trainer_reviews.php">View Reviews</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- ===================== MAIN CONTENT ===================== -->
    <div class="main-content">

        <header>
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?></h1>
        </header>

        <!-- Welcome Info -->
        <div class="welcome">
            <h2>Admin Control Center</h2>
            <p>Use the options below to manage gym members, trainers, shifts and payments.</p>
        </div>

        <!-- Function Cards -->
        <div class="cards">
            <div class="card">
                <h3>Add Member</h3>
                <p>Register a new gym member with complete details.</p>
                <a href="add_member.php">Open</a>
            </div>

            <div class="card">
                <h3>Manage Members</h3>
                <p>Update, delete, or modify member records.</p>
                <a href="manage_members.php">Open</a>
            </div>

            <div class="card">
                <h3>Add Trainer</h3>
                <p>Register a new fitness trainer in the system.</p>
                <a href="add_trainer.php">Open</a>
            </div>

            <div class="card">
                <h3>Manage Trainers</h3>
                <p>Edit or delete trainer profiles and availability.</p>
                <a href="manage_trainers.php">Open</a>
            </div>

            <div class="card">
               <h3>Membership Payments</h3>
               <p>Track all member payments and renewals.</p>
               <a href="payments_view.php" class="btn">View Payments</a>
            </div>

            <div class="card">
                <h3>Trainer Reviews</h3>
                <p>View all reviews submitted by members for trainers across the system.</p>
                <a href="trainer_reviews.php" class="btn">View Reviews</a>
            </div>
        </div>

        <!-- Footer -->
        <footer>
            &copy; <?php echo date("Y"); ?> Web-Based Fitness Management System | Admin
        </footer>

    </div>
</body>
</html>
