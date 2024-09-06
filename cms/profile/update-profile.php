<?php
require_once "../../config/config.php";
require_once "../functions/functions.php";
require_once "../includes/admin_header.php";

// Initialize variables
$errors = [];
$message = '';

// Check if there's a success parameter in the URL and set the success message
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "Profile updated successfully!";
}

// Fetch existing profile data
$stmt = $conn->prepare("SELECT * FROM Profile LIMIT 1");
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_OBJ);

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
        $resume = $profile->resume;
    }

    if ($_FILES['image']['name']) {
        $image_name = basename($_FILES['image']['name']);
        $image = "../../uploads/images/" . $image_name;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/Akumbom-Wesley/uploads/images/' . $image_name)) {
            $errors[] = "Error uploading image. Please try again.";
        }
    } else {
        $image = $profile->image;
    }

    // Proceed only if there are no errors
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE Profile SET name = :name, role = :role, resume = :resume, image = :image, linkedin_url = :linkedin_url, github_url = :github_url, email = :email WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':role' => $role,
                ':resume' => $resume,
                ':image' => $image,
                ':linkedin_url' => $linkedin_url,
                ':github_url' => $github_url,
                ':email' => $email,
                ':id' => $profile->id
            ]);

            if ($stmt->rowCount() > 0) {
                // Redirect after successful update
                header("Location: update-profile.php?success=1");
                exit; // Ensure the script stops after redirection
            } else {
                $errors[] = "No changes were made to the profile.";
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <h1 class="text-center">Update Profile</h1>

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

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($profile->name); ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <input type="text" id="role" name="role" class="form-control" value="<?php echo htmlspecialchars($profile->role); ?>" required>
        </div>

        <div class="form-group">
            <label for="resume">Resume (PDF):</label>
            <input type="file" id="resume" name="resume" class="form-control" accept=".pdf">
            <p>Current resume: <?php echo htmlspecialchars(basename($profile->resume)); ?></p>
        </div>

        <div class="form-group">
            <label for="image">Profile Image:</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <p>Current image: <?php echo htmlspecialchars(basename($profile->image)); ?></p>
            <img src="<?php echo htmlspecialchars($profile->image); ?>" alt="Profile Image" style="max-width: 200px; height: auto;">
        </div>

        <div class="form-group">
            <label for="linkedin_url">LinkedIn URL:</label>
            <input type="url" id="linkedin_url" name="linkedin_url" class="form-control" value="<?php echo htmlspecialchars($profile->linkedin_url); ?>">
        </div>

        <div class="form-group">
            <label for="github_url">GitHub URL:</label>
            <input type="url" id="github_url" name="github_url" class="form-control" value="<?php echo htmlspecialchars($profile->github_url); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($profile->email); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php require_once "../includes/admin_footer.php"; ?>