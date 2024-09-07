<?php 
require_once "../../config/config.php";
require_once "../functions/functions.php";

session_start(); // Start the session

require_once "../includes/admin_header.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: " . ADMINURL . "");
    exit; // Ensure the script stops after redirection
}

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
$errors = [];
$message = '';

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

<div class="container my-5">
    <h1 class="text-center mb-4">Update Experience</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($experience['title']); ?>" required>
        </div>
        <table id="skill-table" class="table table-bordered">
            <thead>
                <tr>
                    <th>Skill</th>
                    <th>Level</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($existingSkills); $i++): ?>
                    <tr>
                        <td><input type="text" name="skill[]" class="form-control" value="<?php echo htmlspecialchars($existingSkills[$i]); ?>"></td>
                        <td><input type="text" name="level[]" class="form-control" value="<?php echo htmlspecialchars($existingLevels[$i]); ?>"></td>
                    </tr>
                <?php endfor; ?>
                <tr>
                    <td><input type="text" name="skill[]" class="form-control"></td>
                    <td><input type="text" name="level[]" class="form-control"></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-success mb-3" id="add-row">Add Row</button>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Update Experience</button>
            <a href="experience.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('add-row').addEventListener('click', function() {
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="skill[]" class="form-control"></td>
            <td><input type="text" name="level[]" class="form-control"></td>
        `;
        document.querySelector('#skill-table tbody').appendChild(newRow);
    });
</script>

<?php require_once "../includes/admin_footer.php"; ?>