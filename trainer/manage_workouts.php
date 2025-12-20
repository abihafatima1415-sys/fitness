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
$edit_workout = null;

// Handle Add/Edit Workout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = intval($_POST['member_id']);
    $plan_name = mysqli_real_escape_string($conn, $_POST['plan_name']);
    $exercises = mysqli_real_escape_string($conn, $_POST['exercises']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);

    if (!empty($_POST['workout_id'])) {
        // Update existing workout
        $workout_id = intval($_POST['workout_id']);
        mysqli_query($conn, "UPDATE workouts SET member_id='$member_id', plan_name='$plan_name', exercises='$exercises', duration='$duration' WHERE id='$workout_id' AND trainer_id='$trainer_id'");
        $message = "<p style='color:green; text-align:center;'>Workout plan updated successfully!</p>";
    } else {
        // Add new workout
        mysqli_query($conn, "INSERT INTO workouts (trainer_id, member_id, plan_name, exercises, duration) VALUES ('$trainer_id','$member_id','$plan_name','$exercises','$duration')");
        $message = "<p style='color:green; text-align:center;'>Workout plan added successfully!</p>";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $workout_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM workouts WHERE id='$workout_id' AND trainer_id='$trainer_id'");
    $message = "<p style='color:red; text-align:center;'>Workout plan deleted.</p>";
}

// Handle Edit Load
if (isset($_GET['edit'])) {
    $workout_id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM workouts WHERE id='$workout_id' AND trainer_id='$trainer_id'");
    $edit_workout = mysqli_fetch_assoc($result);
}

// Fetch members for assigning workouts
$members = mysqli_query($conn, "SELECT id, full_name FROM members ORDER BY full_name ASC");

// Fetch workouts assigned by this trainer
$workouts = mysqli_query($conn, "
    SELECT w.*, m.full_name AS member_name
    FROM workouts w
    JOIN members m ON w.member_id = m.id
    WHERE w.trainer_id='$trainer_id'
    ORDER BY w.id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><link rel="stylesheet" href="<?php echo $base_url;?>/css/site.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Workouts | Trainer Panel</title>
<style>
/* ------------------------------
   Global Styles
------------------------------- */
body {
    font-family: 'Poppins', sans-serif;
    background: var(--bgcolor);
    color: #333;
    margin: 0;
}

/* ------------------------------
   Container
------------------------------- */
.container {
    width: 90%;
    max-width: 900px;
    margin: 50px auto;
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
   Messages
------------------------------- */
.message {
    text-align: center;
    margin-bottom: 20px;
}

/* ------------------------------
   Form
------------------------------- */
form {
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

select,
input,
textarea,
button {
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
}

input,
select,
textarea {
    flex: 1 1 100%;
}

textarea {
    resize: vertical;
    height: 80px;
}

button {
    background: var(--primary-color);
    color: #fff;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #019e8f;
}

/* ------------------------------
   Table Styling
------------------------------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th,
td {
    padding: 12px;
    border-bottom: 1px solid #ccc;
    text-align: center;
}

th {
    background: var(--primary-color);
    color: #fff;
}

/* ------------------------------
   Action Buttons
------------------------------- */
.action-btn {
    padding: 6px 12px;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
    font-weight: 500;
    margin: 0 3px;
    transition: 0.3s;
}

.edit-btn {
    background: #f39c12;
}

.delete-btn {
    background: #e74c3c;
}

.edit-btn:hover {
    background: #d68910;
}

.delete-btn:hover {
    background: #c0392b;
}

/* ------------------------------
   Back Link
------------------------------- */
a.back {
    display: block;
    text-align: center;
    margin-top: 20px;
    color: var(--primary-color);
    text-decoration: none;
}

a.back:hover {
    text-decoration: underline;
}

</style>
</head>
<body>
<div class="container">
<h2>Manage Workout Plans</h2>
<div class="message"><?= $message; ?></div>

<!-- Add/Edit Workout Form -->
<form method="POST" action="">
    <input type="hidden" name="workout_id" value="<?= $edit_workout['id'] ?? ''; ?>">
    <select name="member_id" required>
        <option value="">-- Select Member --</option>
        <?php while($m = mysqli_fetch_assoc($members)): ?>
            <option value="<?= $m['id']; ?>" <?= isset($edit_workout) && $edit_workout['member_id']==$m['id'] ? 'selected' : ''; ?>>
                <?= htmlspecialchars($m['full_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    <input type="text" name="plan_name" placeholder="Plan Name" value="<?= $edit_workout['plan_name'] ?? ''; ?>" required>
    <textarea name="exercises" placeholder="Exercises / Description" required><?= $edit_workout['exercises'] ?? ''; ?></textarea>
    <input type="text" name="duration" placeholder="Duration (e.g., 4 weeks)" value="<?= $edit_workout['duration'] ?? ''; ?>" required>
    <button type="submit"><?= isset($edit_workout) ? 'Update Workout' : 'Save Workout'; ?></button>
    <?php if(isset($edit_workout)): ?>
        <a href="manage_workouts.php" style="margin-left:10px; color:#e74c3c;">Cancel</a>
    <?php endif; ?>
</form>

<!-- Workout Plans Table -->
<table>
    <tr>
        <th>Member</th>
        <th>Plan Name</th>
        <th>Exercises</th>
        <th>Duration</th>
        <th>Actions</th>
    </tr>
    <?php if(mysqli_num_rows($workouts) > 0): ?>
        <?php while($w = mysqli_fetch_assoc($workouts)): ?>
            <tr>
                <td><?= htmlspecialchars($w['member_name']); ?></td>
                <td><?= htmlspecialchars($w['plan_name']); ?></td>
                <td><?= htmlspecialchars($w['exercises']); ?></td>
                <td><?= htmlspecialchars($w['duration']); ?></td>
                <td>
                    <a href="?edit=<?= $w['id']; ?>" class="action-btn edit-btn">Edit</a>
                    <a href="?delete=<?= $w['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No workout plans added yet.</td></tr>
    <?php endif; ?>
</table>

<a href="dashboard.php" class="back"> Back to Dashboard</a> 
</div>
</body>
</html>
