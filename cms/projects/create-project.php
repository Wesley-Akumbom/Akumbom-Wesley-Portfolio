<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";


session_start(); // Start the session

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $profile_id) {
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $title = sanitizeInput($_POST['title'], 'string');
    $github_url = sanitizeInput($_POST['github_url'], 'string');
    $website_url = sanitizeInput($_POST['website_url'], 'string');

    if (empty($image) || empty($title) || empty($github_url)) {
        $errors[] = "Please fill in all the required fields.";
    }

    if (empty($errors)) {
        try {
            $image_dir = "../../uploads/images/";
            $image_path = $image_dir . basename($image);
            move_uploaded_file($image_tmp, $image_path);

            $stmt = $conn->prepare("INSERT INTO projects (image, title, github_url, website_url, profile_id) 
                                                VALUES (:image, :title, :github_url, :website_url, :profile_id)");
            $stmt->execute([
                ':image' => $image_path,
                ':title' => $title,
                ':github_url' => $github_url,
                ':website_url' => $website_url,
                ':profile_id' => $profile_id
            ]);
            $message = "Project created successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <h1 class="text-center">Create a New Project</h1>

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
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Project Image:</label>
                <input type="file" id="image" name="image" class="form-control d-none" required>
                <button type="button" class="btn btn-secondary mt-2" onclick="document.getElementById('image').click();">Select Image</button>
            </div>

            <div class="form-group">
                <label for="title">Project Name:</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="github_url">GitHub URL:</label>
                <input type="text" id="github_url" name="github_url" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="website_url">Website URL:</label>
                <input type="text" id="website_url" name="website_url" class="form-control">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Create Project</button>
            </div>
        </form>
    <?php else: ?>
        <p class="text-center">Please create a profile before adding a project.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>