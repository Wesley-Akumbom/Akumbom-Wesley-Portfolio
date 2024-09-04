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

// Fetch the project to delete
$project_id = $_GET['id'] ?? null;
if (!$project_id) {
    $errors[] = "No project ID provided.";
}

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = :id AND profile_id = :profile_id");
$stmt->execute([':id' => $project_id, ':profile_id' => $profile_id]);
$project = $stmt->fetch(PDO::FETCH_OBJ);

if (!$project) {
    $errors[] = "Project not found.";
}

// Handle project deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_id && $project_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = :id AND profile_id = :profile_id");
        $stmt->execute([':id' => $project_id, ':profile_id' => $profile_id]);
        $message = "Project deleted successfully!";
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}
?>

<h1>Delete Project</h1>

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

<?php if ($profile_id && $project_id): ?>
    <div class="delete-project-container">
        <p>Are you sure you want to delete the project "<?php echo htmlspecialchars($project->title); ?>"?</p>
        <form method="POST" action="">
            <button type="submit" class="btn btn-color-2">Delete</button>
            <a href="projects.php" class="btn btn-color-2">Cancel</a>
        </form>
    </div>
<?php else: ?>
    <p>Please create a profile and a project before deleting.</p>
<?php endif; ?>

<?php require_once "../includes/admin_footer.php"; ?>