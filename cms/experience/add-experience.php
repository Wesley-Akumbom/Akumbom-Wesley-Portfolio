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
    $title = sanitizeInput($_POST['title'], 'string');
    $skills = array_map('sanitizeInput', explode(',', $_POST['skills']), array_fill(0, count(explode(',', $_POST['skills'])), 'string'));
    $levels = array_map('sanitizeInput', explode(',', $_POST['levels']), array_fill(0, count(explode(',', $_POST['levels'])), 'string'));

    // Combine skills and levels into a single string
    $skillsString = implode(',', $skills);
    $levelsString = implode(',', $levels);

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Insert new experience
            $stmt = $conn->prepare("INSERT INTO experience (skill, title, level, profile_id, created_at, updated_at)
                                   VALUES (:skill, :title, :level, :profile_id, NOW(), NOW())");
            $stmt->execute([
                ':skill' => $skillsString,
                ':title' => $title,
                ':level' => $levelsString,
                ':profile_id' => $profile_id
            ]);

            $conn->commit();
            $message = "Experiences added successfully!";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

<h1>Add Experiences</h1>

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
            <label for="skills">Skills (comma-separated):</label>
            <input type="text" id="skills" name="skills" required>
        </div>
        <div class="form-group">
            <label for="levels">Levels (comma-separated, in the same order as skills):</label>
            <input type="text" id="levels" name="levels" required>
        </div>
        <button type="submit" class="btn">Add Experiences</button>
    </form>
<?php else: ?>
    <p>Please create a profile before adding experiences.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>