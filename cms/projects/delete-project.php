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

<div class="container">
    <h1 class="text-center">Delete Project</h1>

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

    <?php if ($profile_id && $project_id): ?>
        <div class="card">
            <div class="card-body">
                <p>Are you sure you want to delete the project "<?php echo htmlspecialchars($project->title); ?>"?</p>
                <form method="POST" action="" class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger mr-2">Delete</button>
                    <a href="projects.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center">Please create a profile and a project before deleting.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>