<?php
// -----------------------------------------------------------
// Start session and verify member authentication
// -----------------------------------------------------------
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Member') {
    header("Location: ../login.php");
    exit;
}

$member_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Member';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard | Fitness Management System</title>
    <style>
        /* =====================================================
           GLOBAL STYLE (Single Theme Color: #00c8b3)
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
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .logout-btn {
            position: absolute;
            right: 25px;
            top: 20px;
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .dashboard-container {
            width: 90%;
            max-width: 1100px;
            margin: 40px auto;
        }

        h2 {
            color: #00c8b3;
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
            color: #00c8b3;
        }

        .card p {
            margin: 10px 0 20px;
            color: #666;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background-color: #00c8b3;
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

        footer {
            background-color: #00c8b3;
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
        Member Dashboard
        <a href="../logout.php" class="logout-btn">Logout</a>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($member_name); ?></h2>

        <div class="card-grid">
            <!-- Membership -->
            <div class="card">
                <h3>My Membership</h3>
                <p>Purchase, renew, or track your gym membership plan.</p>
                <a href="membership.php" class="btn">View Membership</a>
            </div>

            <!-- Book Appointments -->
            <div class="card">
                <h3>Book Appointments</h3>
                <p>Book personal training sessions and fitness classes.</p>
                <a href="book_appointment.php" class="btn">Book Now</a>
            </div>


            <!-- Workout Plans -->
            <div class="card">
                <h3>Workout Plans</h3>
                <p>View your assigned workout plans and exercises.</p>
                <a href="workout_plans.php" class="btn">View Workouts</a>
            </div>

            <!-- Diet Plans -->
            <div class="card">
                <h3>Diet Plans</h3>
                <p>View your trainer’s dietary and nutrition recommendations.</p>
                <a href="diet_plan.php" class="btn">View Diet Plan</a>
            </div>

            <!-- Progress Tracking -->
            <div class="card">
                <h3>Progress Tracker</h3>
                <p>Track your fitness progress, workouts, and performance.</p>
                <a href="progress_tracker.php" class="btn">Track Progress</a>
            </div>

            <!-- View My Attendance -->
            <div class="card">
                <h3>My Attendance</h3>
                <p>Get updates about Attendance</p>
                <a href="view_attendance.php" class="btn">View Attendance</a>
            </div>

            <!-- Payment -->
            <div class="card">
                <h3>Payments</h3>
                <p>Pay your membership or trainer fee via secure online transfer.</p>
                <a href="payments.php" class="btn">Make Payment</a>
            </div>

            <!-- Reviews -->
            <div class="card">
                <h3>Trainer Reviews</h3>
                <p>Review your trainers and rate your workout sessions.</p>
                <a href="reviews.php" class="btn">Review Trainers</a>
            </div>

            <!-- Profile -->
            <div class="card">
                <h3>My Profile</h3>
                <p>Update personal information and account details.</p>
                <a href="edit_profile.php" class="btn">Edit Profile</a>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        &copy; <?php echo date("Y"); ?> Fitness Management System | Member Panel
    </footer>

</body>
</html>
