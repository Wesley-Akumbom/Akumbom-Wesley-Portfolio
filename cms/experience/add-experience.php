<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];
$message = '';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: ".ADMINURL."");
    exit; // Ensure the script stops after redirection
}

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

<div class="container">
    <h1 class="text-center">Add Experiences</h1>

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

    <?php if ($profile_id): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            <table id="skill-table" class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Skill</th>
                        <th>Level</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="text" name="skill[]" class="form-control"></td>
                        <td><input type="text" name="level[]" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="skill[]" class="form-control"></td>
                        <td><input type="text" name="level[]" class="form-control"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="skill[]" class="form-control"></td>
                        <td><input type="text" name="level[]" class="form-control"></td>
                    </tr>
                </tbody>
            </table>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Add Experiences</button>
                <button type="button" class="btn btn-secondary btn-add">Add Row</button>
            </div>
        </form>

        <script>
            document.querySelector('.btn-add').addEventListener('click', function() {
                let newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="text" name="skill[]" class="form-control"></td>
                    <td><input type="text" name="level[]" class="form-control"></td>
                `;
                document.querySelector('#skill-table tbody').appendChild(newRow);
            });
        </script>
    <?php else: ?>
        <p>Please create a profile before adding experiences.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>