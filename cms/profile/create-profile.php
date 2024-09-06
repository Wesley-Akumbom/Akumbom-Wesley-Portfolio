<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";


session_start(); // Start the session

require_once "../includes/admin_header.php";

$errors = [];
$message = '';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page if not logged in
    header("location: ".ADMINURL."");
    exit; // Ensure the script stops after redirection
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'], 'string');
    $role = sanitizeInput($_POST['role'], 'string');
    $email = sanitizeInput($_POST['email'], 'email');
    $linkedin_url = sanitizeInput($_POST['linkedin_url'], 'url');
    $github_url = sanitizeInput($_POST['github_url'], 'url');

    // Check for required fields
    if (empty($name) || empty($role) || empty($email)) {
        $errors[] = "Name, Role, and Email are required fields.";
    }

    // Handle file uploads
    if ($_FILES['resume']['name']) {
        $resume_name = basename($_FILES['resume']['name']);
        $resume = "../../uploads/resumes/" . $resume_name;
        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/Akumbom-Wesley/uploads/resumes/' . $resume_name)) {
            $errors[] = "Error uploading resume. Please try again.";
        }
    } else {
        $resume = null;
    }

    if ($_FILES['image']['name']) {
        $image_name = basename($_FILES['image']['name']);
        $image = "../../uploads/images/" . $image_name;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/Akumbom-Wesley/uploads/images/' . $image_name)) {
            $errors[] = "Error uploading image. Please try again.";
        }
    } else {
        $image = null;
    }

    // Proceed only if there are no errors
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO Profile (name, role, resume, image, linkedin_url, github_url, email) VALUES (:name, :role, :resume, :image, :linkedin_url, :github_url, :email)");
            $stmt->execute([
                ':name' => $name,
                ':role' => $role,
                ':resume' => $resume,
                ':image' => $image,
                ':linkedin_url' => $linkedin_url,
                ':github_url' => $github_url,
                ':email' => $email
            ]);

            if ($stmt->rowCount() > 0) {
                $message = "Profile created successfully!";

                // Redirect to the profile.php page
                header("Location: profile.php");
                exit;
            } else {
                $errors[] = "Failed to create the profile.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php require_once "../includes/admin_header.php"; ?>

<h1>Create Profile</h1>

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

<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="role">Role:</label>
        <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>" required>
    </div>

    <div class="form-group">
        <label for="resume">Resume (PDF):</label>
        <input type="file" id="resume" name="resume" accept=".pdf">
    </div>

    <div class="form-group">
        <label for="image">Profile Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
    </div>

    <div class="form-group">
        <label for="linkedin_url">LinkedIn URL:</label>
        <input type="url" id="linkedin_url" name="linkedin_url" value="<?php echo htmlspecialchars($_POST['linkedin_url'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="github_url">GitHub URL:</label>
        <input type="url" id="github_url" name="github_url" value="<?php echo htmlspecialchars($_POST['github_url'] ?? ''); ?>">
    </div>

    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
    </div>

    <button type="submit">Create Profile</button>
</form>

<?php require_once "../includes/admin_footer.php"; ?>