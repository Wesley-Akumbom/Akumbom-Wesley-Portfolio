<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Fetch the experience to be updated
if (isset($_GET['id'])) {
    $experienceId = sanitizeInput($_GET['id'], 'int');
    $stmt = $conn->prepare("SELECT * FROM experience WHERE id = :id");
    $stmt->execute([':id' => $experienceId]);
    $experience = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$experience) {
        echo "Experience not found.";
        exit;
    }
} else {
    echo "Experience not found.";
    exit;
}

// Handle experience update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $skills = sanitizeInput($_POST['skills']);
    $levels = sanitizeInput($_POST['levels']);

    try {
        $conn->beginTransaction();
        $stmt = $conn->prepare("UPDATE experience SET title = :title, skill = :skills, level = :levels WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':skills' => $skills,
            ':levels' => $levels,
            ':id' => $experienceId
        ]);
        $conn->commit();
        $message = "Experience updated successfully!";
    } catch (PDOException $e) {
        $conn->rollBack();
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

<div class="manage-experiences-header">
    <h1>Update Experience</h1>
</div>

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

<form method="POST" action="">
    <input type="hidden" name="id" value="<?php echo $experience['id']; ?>">
    <div class="form-group">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $experience['title']; ?>" required>
    </div>
    <div class="form-group">
        <label for="skills">Skills (comma-separated):</label>
        <input type="text" id="skills" name="skills" value="<?php echo $experience['skill']; ?>" required>
    </div>
    <div class="form-group">
        <label for="levels">Levels (comma-separated, in the same order as skills):</label>
        <input type="text" id="levels" name="levels" value="<?php echo $experience['level']; ?>" required>
    </div>
    <button type="submit" name="update" class="btn">Update Experience</button>
    <a href="experience.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once "../includes/admin_footer.php"; ?>