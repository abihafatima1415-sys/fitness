<?php
session_start();
include('../config/db_connect.php');

// Only allow logged-in trainers
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Trainer') {
    header("Location: ../login.php");
    exit;
}

$trainer_id = $_SESSION['user_id'];
$message = "";

// Handle approval/rejection
if (isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = intval($_GET['id']);
    if ($_GET['action'] === 'approve') {
        mysqli_query($conn, "UPDATE appointments SET status='Approved' WHERE id='$appointment_id' AND trainer_id='$trainer_id'");
        $message = "<p style='color:green; text-align:center;'>Appointment approved successfully!</p>";
    } elseif ($_GET['action'] === 'reject') {
        mysqli_query($conn, "UPDATE appointments SET status='Rejected' WHERE id='$appointment_id' AND trainer_id='$trainer_id'");
        $message = "<p style='color:red; text-align:center;'>Appointment rejected.</p>";
    }
}

// Fetch bookings for this trainer
$bookings = mysqli_query($conn, "
    SELECT a.*, m.full_name AS member_name, m.email AS member_email
    FROM appointments a
    JOIN members m ON a.member_id = m.id
    WHERE a.trainer_id = '$trainer_id'
    ORDER BY a.appointment_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings | Trainer Panel</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bgcolor);
            margin: 0;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 900px;
            background: #fff;
            margin: 60px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: var(--primary-color);
            color: white;
        }
        .status {
            font-weight: 500;
            text-transform: capitalize;
        }
        .pending { color: orange; }
        .approved { color: green; }
        .rejected { color: red; }
        .action-btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            margin: 0 3px;
        }
        .approve-btn { background-color: var(--primary-color); }
        .reject-btn { background-color: #e74c3c; }
        .approve-btn:hover { background-color: #019e8f; }
        .reject-btn:hover { background-color: #c0392b; }
        a.back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
        }
        a.back:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h2>Booked Appointments</h2>
    <div class="message"><?= $message; ?></div>

    <table>
        <tr>
            <th>Member Name</th>
            <th>Email</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php if(mysqli_num_rows($bookings) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($bookings)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['member_name']); ?></td>
                    <td><?= htmlspecialchars($row['member_email']); ?></td>
                    <td><?= htmlspecialchars($row['appointment_date']); ?></td>
                    <td><?= htmlspecialchars($row['appointment_time']); ?></td>
                    <td class="status <?= strtolower($row['status']); ?>"><?= htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if($row['status'] === 'Pending'): ?>
                            <a href="?action=approve&id=<?= $row['id']; ?>" class="action-btn approve-btn">Approve</a>
                            <a href="?action=reject&id=<?= $row['id']; ?>" class="action-btn reject-btn">Reject</a>
                        <?php else: ?>
                            --
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No bookings found.</td></tr>
        <?php endif; ?>
    </table>

    <a href="dashboard.php" class="back">Back to Dashboard</a>
</div>
</body>
</html>
