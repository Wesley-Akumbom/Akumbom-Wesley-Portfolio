<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

session_start(); // Start the session

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

// Fetch the project to update
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_id && $project_id) {
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $title = sanitizeInput($_POST['title'], 'string');
    $github_url = sanitizeInput($_POST['github_url'], 'string');
    $website_url = sanitizeInput($_POST['website_url'], 'string');

    $updateFields = [];
    $updateParams = [];

    if (!empty($image)) {
        $image_dir = "../../uploads/images/";
        $image_path = $image_dir . basename($image);
        move_uploaded_file($image_tmp, $image_path);
        $updateFields[] = "image = :image";
        $updateParams[':image'] = $image_path;
    }

    if (!empty($title)) {
        $updateFields[] = "title = :title";
        $updateParams[':title'] = $title;
    }

    if (!empty($github_url)) {
        $updateFields[] = "github_url = :github_url";
        $updateParams[':github_url'] = $github_url;
    }

    if (!empty($website_url)) {
        $updateFields[] = "website_url = :website_url";
        $updateParams[':website_url'] = $website_url;
    }

    if (!empty($updateFields)) {
        try {
            $updateQuery = "UPDATE projects SET " . implode(", ", $updateFields) . " WHERE id = :id AND profile_id = :profile_id";
            $updateParams[':id'] = $project_id;
            $updateParams[':profile_id'] = $profile_id;

            $stmt = $conn->prepare($updateQuery);
            $stmt->execute($updateParams);
            $message = "Project updated successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    } else {
        $errors[] = "Please fill in at least one field to update.";
    }
}
?>

<div class="container">
    <h1 class="text-center">Update Project</h1>

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
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Project Image:</label>
                <div>
                    <img src="<?php echo htmlspecialchars($project->image); ?>" alt="Current Project Image" style="max-width: 100px; height: auto; display: block; margin-bottom: 10px;">
                </div>
                <input type="file" id="image" name="image" class="form-control d-none"> <!-- Hidden file input -->
                <button type="button" class="btn btn-secondary mt-2" onclick="document.getElementById('image').click();">Select Image</button>
            </div>

            <div class="form-group">
                <label for="title">Project Title:</label>
                <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($project->title); ?>">
            </div>

            <div class="form-group">
                <label for="github_url">GitHub URL:</label>
                <input type="text" id="github_url" name="github_url" class="form-control" value="<?php echo htmlspecialchars($project->github_url); ?>">
            </div>

            <div class="form-group">
                <label for="website_url">Website URL:</label>
                <input type="text" id="website_url" name="website_url" class="form-control" value="<?php echo htmlspecialchars($project->website_url); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Update Project</button>
        </form>
    <?php else: ?>
        <p>Please create a profile and a project before updating.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>