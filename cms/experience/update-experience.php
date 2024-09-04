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
    $title = isset($_POST['title']) ? sanitizeInput($_POST['title'], 'string') : null;
    $skills = [];
    $levels = [];

    for ($i = 0; $i < count($_POST['skill']); $i++) {
        $skill = sanitizeInput($_POST['skill'][$i], 'string');
        $level = sanitizeInput($_POST['level'][$i], 'string');

        if (!empty($skill) && empty($level)) {
            $errors[] = "Please fill in the level for the skill: " . $skill;
        } elseif (!empty($skill) || !empty($level)) {
            $skills[] = $skill;
            $levels[] = $level;
        }
    }

    if ($title === null && empty($skills) && empty($levels)) {
        $errors[] = "Please update at least one field.";
    } else {
        try {
            $conn->beginTransaction();
            $stmt = $conn->prepare("UPDATE experience SET title = COALESCE(:title, title), skill = :skills, level = :levels WHERE id = :id");
            $stmt->execute([
                ':title' => $title,
                ':skills' => implode(',', $skills),
                ':levels' => implode(',', $levels),
                ':id' => $experienceId
            ]);
            $conn->commit();
            $message = "Experience updated successfully!";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch the existing skills and levels
$existingSkills = explode(',', $experience['skill']);
$existingLevels = explode(',', $experience['level']);
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
        <input type="text" id="title" name="title" value="<?php echo $experience['title']; ?>">
    </div>
    <table id="skill-table" class="experience-table">
        <thead>
            <tr>
                <th>Skill</th>
                <th>Level</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($existingSkills); $i++): ?>
                <tr>
                    <td><input type="text" name="skill[]" value="<?php echo $existingSkills[$i]; ?>"></td>
                    <td><input type="text" name="level[]" value="<?php echo $existingLevels[$i]; ?>"></td>
                </tr>
            <?php endfor; ?>
            <tr>
                <td><input type="text" name="skill[]"></td>
                <td><input type="text" name="level[]"></td>
            </tr>
        </tbody>
    </table>
    <button type="button" class="btn btn-add">Add Row</button>
    <button type="submit" class="btn">Update Experience</button>
    <a href="experience.php" class="btn btn-secondary">Cancel</a>
</form>

<script>
    document.querySelector('.btn-add').addEventListener('click', function() {
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="skill[]"></td>
            <td><input type="text" name="level[]"></td>
        `;
        document.querySelector('#skill-table tbody').appendChild(newRow);
    });
</script>

<?php require_once "../includes/admin_footer.php"; ?>