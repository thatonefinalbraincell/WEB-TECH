<?php
session_start();

// Ensure patient ID exists in session; otherwise redirect to index.php
if (empty($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

// Fetch patient record using prepared statement
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch();

// If patient not found in database, clear invalid session and redirect
if (!$patient) {
    unset($_SESSION['patient_id']);
    header("Location: index.php");
    exit;
}

// Check session flags for flash notifications
$isExisting   = !empty($_SESSION['is_existing']);
$isRegistered = !empty($_SESSION['is_registered']);
$isUpdated    = !empty($_SESSION['is_updated']);

// Clear flags so messages do not persist upon refresh
unset($_SESSION['is_existing'], $_SESSION['is_registered'], $_SESSION['is_updated']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Patient Details</h1>

    <?php if ($isExisting): ?>
        <div class="alert alert-success">Welcome back, <?php echo htmlspecialchars($patient['name']); ?>!</div>
    <?php elseif ($isRegistered): ?>
        <div class="alert alert-success">Registration successful! Welcome, <?php echo htmlspecialchars($patient['name']); ?>.</div>
    <?php elseif ($isUpdated): ?>
        <div class="alert alert-success">Patient details updated successfully.</div>
    <?php endif; ?>

    <p class="subtitle">Here are the recorded patient details on file.</p>

    <div class="details-list">
        <div class="detail-row">
            <div class="detail-label">Full Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($patient['name']); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Phone Number</div>
            <div class="detail-value"><?php echo htmlspecialchars($patient['phone']); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Age</div>
            <div class="detail-value"><?php echo htmlspecialchars($patient['age']); ?> years</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Gender</div>
            <div class="detail-value"><?php echo htmlspecialchars($patient['gender']); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Address</div>
            <div class="detail-value"><?php echo nl2br(htmlspecialchars($patient['address'])); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Reason for Visit</div>
            <div class="detail-value"><?php echo htmlspecialchars($patient['reason']); ?></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Registered On</div>
            <div class="detail-value"><?php echo htmlspecialchars($patient['created_at']); ?></div>
        </div>
    </div>

    <!-- One primary button per page -->
    <a href="edit.php" class="btn-primary">Edit details</a>

    <div class="links">
        <a href="logout.php" class="link-secondary">Logout / New search</a>
    </div>
</div>
</body>
</html>
