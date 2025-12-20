<?php
session_start();
include('../config/db_connect.php');

// Only allow logged-in members
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'Member') {
    header("Location: ../login.php");
    exit;
}

$member_id = $_SESSION['user_id'];
$message = "";

// Handle form submission (book appointment)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainer_id = mysqli_real_escape_string($conn, $_POST['trainer_id']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);

    // Insert booking record
    $query = "INSERT INTO appointments (member_id, trainer_id, appointment_date, appointment_time, status)
              VALUES ('$member_id', '$trainer_id', '$appointment_date', '$appointment_time', 'Pending')";
    
    if (mysqli_query($conn, $query)) {
        $message = "<p style='color:green;text-align:center;'>Appointment booked successfully!</p>";
    } else {
        $message = "<p style='color:red;text-align:center;'>Error booking appointment: " . mysqli_error($conn) . "</p>";
    }
}

// Fetch all trainers
$trainers = mysqli_query($conn, "SELECT id, full_name, shift FROM trainers");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment | Fitness Management System</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fa;
            color: #333;
            margin: 0;
        }
        .container {
            width: 90%;
            max-width: 600px;
            background-color: #fff;
            margin: 60px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #00c8b3;
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        select, input {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        select:focus, input:focus {
            border-color: #00c8b3;
            outline: none;
        }
        button {
            background-color: #00c8b3;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #019e8f;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #00c8b3;
            color: white;
        }
        .status {
            font-weight: 500;
            text-transform: capitalize;
        }
        .pending { color: orange; }
        .approved { color: green; }
        .rejected { color: red; }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #00c8b3;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Book Appointment</h2>
    <div class="message"><?= $message; ?></div>

    <!-- Appointment Booking Form -->
    <form method="POST">
        <label for="trainer_id">Select Trainer:</label>
        <select name="trainer_id" id="trainer_id" required>
            <option value="">-- Choose Trainer --</option>
            <?php while ($trainer = mysqli_fetch_assoc($trainers)) { ?>
                <option value="<?= $trainer['id']; ?>">
                    <?= htmlspecialchars($trainer['full_name']) . " (" . htmlspecialchars($trainer['shift']) . " Shift)"; ?>
                </option>
            <?php } ?>
        </select>

        <label for="appointment_date">Appointment Date:</label>
        <input type="date" name="appointment_date" id="appointment_date" required>

        <label for="appointment_time">Appointment Time:</label>
        <input type="time" name="appointment_time" id="appointment_time" required>

        <button type="submit">Book Appointment</button>
    </form>

    <h3 style="color:#00c8b3; text-align:center; margin-top:30px;">Your Appointments</h3>
    <table>
        <tr>
            <th>Trainer</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        <?php
        // Fetch user’s previous appointments
        $appointments = mysqli_query($conn, "
            SELECT a.*, t.full_name AS trainer_name 
            FROM appointments a
            JOIN trainers t ON a.trainer_id = t.id
            WHERE a.member_id = '$member_id'
            ORDER BY a.appointment_date DESC
        ");
        if (mysqli_num_rows($appointments) > 0) {
            while ($row = mysqli_fetch_assoc($appointments)) {
                $statusClass = strtolower($row['status']);
                echo "<tr>
                    <td>" . htmlspecialchars($row['trainer_name']) . "</td>
                    <td>" . htmlspecialchars($row['appointment_date']) . "</td>
                    <td>" . htmlspecialchars($row['appointment_time']) . "</td>
                    <td class='status $statusClass'>" . htmlspecialchars($row['status']) . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center;'>No appointments found.</td></tr>";
        }
        ?>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
