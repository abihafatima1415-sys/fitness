<?php
// -----------------------------------------------------------
// Start session & verify trainer login
// -----------------------------------------------------------
session_start();
include('../config/db_connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Trainer') {
    header("Location: ../login.php");
    exit;
}

// Trainer info (from session)
$trainer_id = $_SESSION['user_id'];
$trainer_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Trainer';

// Fetch shift from DB
$result = mysqli_query($conn, "SELECT shift FROM trainers WHERE id='$trainer_id'");
$trainer_data = mysqli_fetch_assoc($result);
$trainer_shift = $trainer_data['shift'] ?? 'Not Assigned';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainer Dashboard | Fitness Management System</title>
    <style>
        /* =====================================================
           GLOBAL STYLE - (Single Theme Color: var(--primary-color);)
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
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .dashboard-container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        h2 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 30px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
        }

        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            padding: 25px;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.15);
        }

        .card h3 {
            margin-top: 10px;
            color: var(--primary-color);
        }

        .card p {
            margin: 10px 0 20px;
            color: #666;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #019e8f;
        }

        .logout-btn {
            position: absolute;
            right: 25px;
            top: 20px;
            background-color: #e74c3c;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        footer {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 15px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 15px;
            font-weight: 500;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header>
        Trainer Dashboard
        <a href="../logout.php" class="btn logout-btn">Logout</a>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="dashboard-container">
<h2>Welcome, <?= htmlspecialchars($trainer_name); ?> (Shift: <?= htmlspecialchars($trainer_shift); ?>)</h2>

<div class="card-grid">
    <!-- Existing cards... -->
    <div class="card">
        <h3>Workout Plans</h3>
        <p>Create, assign, or modify members’ workout plans.</p>
        <a href="manage_workouts.php" class="btn">Manage Workouts</a>
    </div>

    <div class="card">
        <h3>Dietary Recommendations</h3>
        <p>Provide and update personalized meal & nutrition advice.</p>
        <a href="diet_plans.php" class="btn">Manage Diet Plans</a>
    </div>

    <div class="card">
        <h3>Attendance</h3>
        <p>Mark member attendance and track daily participation.</p>
        <a href="mark_attendance.php" class="btn">Mark Attendance</a>
    </div>

    <div class="card">
        <h3>Bookings</h3>
        <p>View and manage member training sessions or class bookings.</p>
        <a href="view_bookings.php" class="btn">View Bookings</a>
    </div>

    <div class="card">
        <h3>Progress Reports</h3>
        <p>View and update members’ fitness and performance progress.</p>
        <a href="progress_reports.php" class="btn">View Progress</a>
    </div>

    <div class="card">
        <h3>Member Payments</h3>
        <p>View all membership payments made by members and track their status.</p>
        <a href="payments_view.php" class="btn">View Payments</a>
    </div>

    <div class="card">
        <h3>Trainer Reviews</h3>
        <p>View all reviews submitted by members for your training sessions.</p>
        <a href="view_reviews.php" class="btn">View Reviews</a>
    </div>

    <div class="card">
        <h3>My Profile</h3>
        <p>Update personal information, shift, and credentials.</p>
        <a href="edit_profile.php" class="btn">Edit Profile</a>
    </div>

</div>

    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        &copy; <?php echo date("Y"); ?> Fitness Management System | Trainer Panel
    </footer>

</body>
</html>
