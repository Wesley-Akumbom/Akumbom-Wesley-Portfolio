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
    // Sanitize and assign new values
    $skill = sanitizeInput($_POST['skill'], 'string');
    $title = sanitizeInput($_POST['title'], 'string');
    $level = sanitizeInput($_POST['level'], 'string');

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Insert new experience
            $stmt = $conn->prepare("INSERT INTO experience (skill, title, level, profile_id, created_at, updated_at)
                                   VALUES (:skill, :title, :level, :profile_id, NOW(), NOW())");
            $stmt->execute([
                ':skill' => $skill,
                ':title' => $title,
                ':level' => $level,
                ':profile_id' => $profile_id
            ]);

            $conn->commit();
            $message = "Experience added successfully!";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

<h1>Add Experience</h1>

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
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
        <div class="form-group">
            <label for="skill">Skill:</label>
            <input type="text" id="skill" name="skill" required>
        </div>
        <div class="form-group">
            <label for="level">Level:</label>
            <select id="level" name="level" required>
                <option value="">Select Level</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
                <option value="Expert">Expert</option>
            </select>
        </div>
        <button type="submit" class="btn">Add Experience</button>
    </form>
<?php else: ?>
    <p>Please create a profile before adding experience.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>