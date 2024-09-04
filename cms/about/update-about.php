<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];
$message = '';

// Fetch the profile_id
$stmt = $conn->prepare("SELECT id FROM Profile LIMIT 1");
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_OBJ);

if (!$profile) {
    $errors[] = "No profile found. Please create a profile first.";
}

$profile_id = $profile ? $profile->id : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_id) {
    // Fetch existing experience and education data
    $stmt = $conn->prepare("SELECT * FROM about_exp WHERE profile_id = :profile_id");
    $stmt->execute([':profile_id' => $profile_id]);
    $existing_exp = $stmt->fetch(PDO::FETCH_OBJ);

    $stm = $conn->prepare("SELECT * FROM about_edu WHERE profile_id = :profile_id");
    $stm->execute([':profile_id' => $profile_id]);
    $existing_edu = $stm->fetch(PDO::FETCH_OBJ);

    // Create objects for new data
    $about_exp = new stdClass();
    $about_edu = new stdClass();

    // Sanitize and assign new values or keep existing ones
    $about_exp->exp_years = sanitizeInput($_POST['exp_years'], 'int') ?: ($existing_exp->exp_years ?? null);
    $about_exp->exp_field = sanitizeInput($_POST['exp_field'], 'string') ?: ($existing_exp->exp_field ?? null);
    $about_exp->about_me = sanitizeInput($_POST['about_me'], 'string') ?: ($existing_exp->about_me ?? null);

    $about_edu->level = sanitizeInput($_POST['level'], 'string') ?: ($existing_edu->level ?? null);
    $about_edu->certificate = sanitizeInput($_POST['certificate'], 'string') ?: ($existing_edu->certificate ?? null);
    $about_edu->years = sanitizeInput($_POST['years'], 'string') ?: ($existing_edu->years ?? null);

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Update experience information
            $stmt = $conn->prepare("UPDATE about_exp
                                    SET exp_years = :exp_years, exp_field = :exp_field, about_me = :about_me
                                    WHERE profile_id = :profile_id");
            $stmt->execute([
                ':exp_years' => $about_exp->exp_years,
                ':exp_field' => $about_exp->exp_field,
                ':about_me' => $about_exp->about_me,
                ':profile_id' => $profile_id
            ]);

            // Update education information
            $stm = $conn->prepare("UPDATE about_edu
                                   SET level = :level, certificate = :certificate, years = :years
                                   WHERE profile_id = :profile_id");
            $stm->execute([
                ':level' => $about_edu->level,
                ':certificate' => $about_edu->certificate,
                ':years' => $about_edu->years,
                ':profile_id' => $profile_id
            ]);

            $conn->commit();
            $message = "About information updated successfully!";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch existing about data
$stmt = $conn->prepare("SELECT * FROM about_exp WHERE profile_id = :profile_id");
$stmt->execute([':profile_id' => $profile_id]);
$experience = $stmt->fetch(PDO::FETCH_OBJ);

$stm = $conn->prepare("SELECT * FROM about_edu WHERE profile_id = :profile_id");
$stm->execute([':profile_id' => $profile_id]);
$education = $stm->fetch(PDO::FETCH_OBJ);

?>

<h1>Update About Information</h1>

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
        <h2>Experience</h2>
        <div class="form-group">
            <label for="exp_years">Years of Experience:</label>
            <input type="number" id="exp_years" name="exp_years" value="<?php echo htmlspecialchars($experience->exp_years ?? '');?>" required>
        </div>

        <div class="form-group">
            <label for="exp_field">Field of Experience:</label>
            <input type="text" id="exp_field" name="exp_field" value="<?php echo htmlspecialchars($experience->exp_field ?? ''); ?>" required>
        </div>

        <h2>Education</h2>
        <div class="form-group">
            <label for="level">Education Level:</label>
            <input type="text" id="level" name="level" value="<?php echo htmlspecialchars($education->level ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="certificate">Certificate:</label>
            <input type="text" id="certificate" name="certificate" value="<?php echo htmlspecialchars($education->certificate ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="years">Year:</label>
            <input type="text" id="years" name="years" value="<?php echo htmlspecialchars($education->years ?? ''); ?>" required>
        </div>

        <h2>About Me</h2>
        <div class="form-group">
            <label for="about_me">About Me:</label>
            <textarea id="about_me" name="about_me" required><?php echo htmlspecialchars($experience->about_me ?? ''); ?></textarea>
        </div>

        <button type="submit">Update About Information</button>
    </form>
<?php else: ?>
    <p>Please create a profile before managing About information.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>