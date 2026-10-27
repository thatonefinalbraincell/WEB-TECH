<?php
session_start();

// Ensure patient ID exists in session; otherwise redirect to index.php
if (empty($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

// Fetch current patient details
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$patient = $stmt->fetch();

if (!$patient) {
    unset($_SESSION['patient_id']);
    header("Location: index.php");
    exit;
}

$errors = [];
$name    = $patient['name'];
$phone   = $patient['phone'];
$age     = $patient['age'];
$gender  = $patient['gender'];
$address = $patient['address'];
$reason  = $patient['reason'];

// Handle POST request to update details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $age     = trim($_POST['age'] ?? '');
    $gender  = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $reason  = trim($_POST['reason'] ?? '');

    // 1. Full name validation: required, letters and spaces only
    if ($name === '') {
        $errors['name'] = 'Full name is required.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors['name'] = 'Full name must contain letters and spaces only.';
    }

    // 2. Age validation: required, number between 0 and 120
    if ($age === '') {
        $errors['age'] = 'Age is required.';
    } elseif (!ctype_digit($age) || (int)$age < 0 || (int)$age > 120) {
        $errors['age'] = 'Age must be a number between 0 and 120.';
    }

    // 3. Gender validation: required
    $allowed_genders = ['Male', 'Female', 'Other'];
    if ($gender === '') {
        $errors['gender'] = 'Please select a gender.';
    } elseif (!in_array($gender, $allowed_genders, true)) {
        $errors['gender'] = 'Invalid gender selected.';
    }

    // 4. Address validation: required
    if ($address === '') {
        $errors['address'] = 'Address is required.';
    }

    // 5. Reason for visit validation: required
    if ($reason === '') {
        $errors['reason'] = 'Reason for visit is required.';
    }

    // If no validation errors, update patient record in database
    if (empty($errors)) {
        $updateStmt = $pdo->prepare("UPDATE patients SET name = ?, age = ?, gender = ?, address = ?, reason = ? WHERE id = ?");
        $updateStmt->execute([$name, (int)$age, $gender, $address, $reason, $_SESSION['patient_id']]);

        $_SESSION['is_updated'] = true;
        header("Location: view.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Patient Details</h1>
    <p class="subtitle">Update your information below. Phone number cannot be changed.</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">Please fix the highlighted errors before saving.</div>
    <?php endif; ?>

    <form action="edit.php" method="POST" id="editForm">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" class="<?php echo isset($errors['name']) ? 'input-error' : ''; ?>">
            <span class="field-error" id="error-name"><?php echo htmlspecialchars($errors['name'] ?? ''); ?></span>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number (Read-only)</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>
            <span class="field-error" id="error-phone"></span>
        </div>

        <div class="form-group">
            <label for="age">Age</label>
            <input type="number" id="age" name="age" min="0" max="120" value="<?php echo htmlspecialchars($age); ?>" class="<?php echo isset($errors['age']) ? 'input-error' : ''; ?>">
            <span class="field-error" id="error-age"><?php echo htmlspecialchars($errors['age'] ?? ''); ?></span>
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" class="<?php echo isset($errors['gender']) ? 'input-error' : ''; ?>">
                <option value="">Select Gender</option>
                <option value="Male" <?php echo $gender === 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $gender === 'Female' ? 'selected' : ''; ?>>Female</option>
                <option value="Other" <?php echo $gender === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <span class="field-error" id="error-gender"><?php echo htmlspecialchars($errors['gender'] ?? ''); ?></span>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="<?php echo isset($errors['address']) ? 'input-error' : ''; ?>"><?php echo htmlspecialchars($address); ?></textarea>
            <span class="field-error" id="error-address"><?php echo htmlspecialchars($errors['address'] ?? ''); ?></span>
        </div>

        <div class="form-group">
            <label for="reason">Reason for Visit</label>
            <input type="text" id="reason" name="reason" value="<?php echo htmlspecialchars($reason); ?>" class="<?php echo isset($errors['reason']) ? 'input-error' : ''; ?>">
            <span class="field-error" id="error-reason"><?php echo htmlspecialchars($errors['reason'] ?? ''); ?></span>
        </div>

        <!-- Primary button -->
        <button type="submit" class="btn-primary">Save Changes</button>
    </form>

    <div class="links">
        <a href="view.php" class="link-secondary">Cancel and return to details</a>
        <a href="logout.php" class="link-secondary">Logout / New search</a>
    </div>
</div>
<script src="validate.js"></script>
</body>
</html>
