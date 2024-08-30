<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];
$message = '';
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : null;

// Fetch the profile_id
$stmt = $conn->prepare("SELECT id FROM Profile LIMIT 1");
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    $errors[] = "No profile found. Please create a profile first.";
}

$profile_id = $profile ? $profile['id'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_id) {
    $exp_years = sanitizeInput($_POST['exp_years'], 'int');
    $exp_field = sanitizeInput($_POST['exp_field'], 'string');
    $level = sanitizeInput($_POST['level'], 'string');
    $certificate = sanitizeInput($_POST['certificate'], 'string');
    $year = sanitizeInput($_POST['year'], 'string');

    // Validation
    if (empty($exp_years) || empty($exp_field) || empty($level) || empty($certificate) || empty($year)) {
        $errors[] = "All fields are required.";
    }

    if (empty($errors)) {
        try {
            if ($editId) {
                // Update existing record
                $stmt = $conn->prepare("UPDATE About SET exp_years = :exp_years, exp_field = :exp_field, level = :level, certificate = :certificate, year = :year WHERE id = :id AND profile_id = :profile_id");
                $stmt->bindParam(':id', $editId);
            } else {
                // Insert new record
                $stmt = $conn->prepare("INSERT INTO About (exp_years, exp_field, level, certificate, year, profile_id) VALUES (:exp_years, :exp_field, :level, :certificate, :year, :profile_id)");
            }

            $stmt->bindParam(':exp_years', $exp_years);
            $stmt->bindParam(':exp_field', $exp_field);
            $stmt->bindParam(':level', $level);
            $stmt->bindParam(':certificate', $certificate);
            $stmt->bindParam(':year', $year);
            $stmt->bindParam(':profile_id', $profile_id);

            $stmt->execute();

            $message = $editId ? "About information updated successfully!" : "New about information added successfully!";
            $editId = null; // Reset edit mode
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// If in edit mode, fetch the specific record
if ($editId) {
    $stmt = $conn->prepare("SELECT * FROM About WHERE id = :id AND profile_id = :profile_id");
    $stmt->bindParam(':id', $editId);
    $stmt->bindParam(':profile_id', $profile_id);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$editData) {
        $errors[] = "Invalid edit request.";
        $editId = null;
    }
}
?>

<h1><?php echo $editId ? 'Edit' : 'Add'; ?> About Information</h1>

<?php if (!empty($errors)): ?>
    <div class="error-messages">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($message)): ?>
    <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<?php if ($profile_id): ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="exp_years">Years of Experience:</label>
            <input type="number" id="exp_years" name="exp_years" value="<?php echo $editId ? htmlspecialchars($editData['exp_years']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="exp_field">Field of Experience:</label>
            <input type="text" id="exp_field" name="exp_field" value="<?php echo $editId ? htmlspecialchars($editData['exp_field']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="level">Education Level:</label>
            <input type="text" id="level" name="level" value="<?php echo $editId ? htmlspecialchars($editData['level']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="certificate">Certificate:</label>
            <input type="text" id="certificate" name="certificate" value="<?php echo $editId ? htmlspecialchars($editData['certificate']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="year">Year:</label>
            <input type="text" id="year" name="year" value="<?php echo $editId ? htmlspecialchars($editData['year']) : ''; ?>" required>
        </div>

        <button type="submit"><?php echo $editId ? 'Update' : 'Add'; ?> About Information</button>
    </form>

    <h2>Existing About Information</h2>

    <h3>Experience</h3>
    <ul>
        <?php foreach ($aboutData['experience'] as $exp): ?>
            <li>
                <?php echo htmlspecialchars($exp['years']); ?> years in <?php echo htmlspecialchars($exp['field']); ?>
                <a href="?edit=<?php echo htmlspecialchars($exp['id']); ?>">Edit</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Education</h3>
    <ul>
        <?php foreach ($aboutData['education'] as $edu): ?>
            <li>
                <?php echo htmlspecialchars($edu['level']); ?> - <?php echo htmlspecialchars($edu['certificate']); ?> (<?php echo htmlspecialchars($edu['year']); ?>)
                <a href="?edit=<?php echo htmlspecialchars($edu['id']); ?>">Edit</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Please create a profile before adding About information.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>