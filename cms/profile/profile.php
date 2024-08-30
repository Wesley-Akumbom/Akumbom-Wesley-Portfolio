    <?php
    require "../../config/config.php";
    require "../includes/admin_header.php";
    require "../functions/functions.php";

    $errors = [];
    $message = '';

    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = sanitizeInput($_POST['name'], 'string');
        $role = sanitizeInput($_POST['role'], 'string');
        $email = sanitizeInput($_POST['email'], 'email');
        $linkedin_url = sanitizeInput($_POST['linkedin_url'], 'url');
        $github_url = sanitizeInput($_POST['github_url'], 'url');

        // Check for required fields
        if (empty($name) || empty($role) || empty($email)) {
            $errors[] = "Name, Role, and Email are required fields.";
        }

        // Fetch existing profile data
        $stmt = $conn->prepare("SELECT * FROM Profile LIMIT 1");
        $stmt->execute();
        $existing_profile = $stmt->fetch(PDO::FETCH_OBJ);

        if ($existing_profile === false) {
            $existing_profile = null;
        }

        // Handle file uploads
        if ($_FILES['resume']['name']) {
            $resume_name = basename($_FILES['resume']['name']);
            $resume = "../../uploads/resumes/" . $resume_name;
            if (!move_uploaded_file($_FILES['resume']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/Akumbom-Wesley/uploads/resumes/' . $resume_name)) {
                $errors[] = "Error uploading resume. Please try again.";
            }
        } else {
            $resume = $existing_profile ? $existing_profile->resume : null;
        }

        if ($_FILES['image']['name']) {
            $image_name = basename($_FILES['image']['name']);
            $image = "../../uploads/images/" . $image_name;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/Akumbom-Wesley/uploads/images/' . $image_name)) {
                $errors[] = "Error uploading image. Please try again.";
            }
        } else {
            $image = $existing_profile ? $existing_profile->image : null;
        }

        // Proceed only if there are no errors
        if (empty($errors)) {
            try {
                if ($existing_profile) {
                    // Update existing profile
                    $stmt = $conn->prepare("UPDATE Profile SET name = :name, role = :role, resume = :resume, image = :image, linkedin_url = :linkedin_url, github_url = :github_url, email = :email WHERE id = :id");
                    $stmt->execute([
                        ':name' => $name,
                        ':role' => $role,
                        ':resume' => $resume,
                        ':image' => $image,
                        ':linkedin_url' => $linkedin_url,
                        ':github_url' => $github_url,
                        ':email' => $email,
                        ':id' => $existing_profile->id
                    ]);
                } else {
                    // Insert new profile
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
                }

                if ($stmt->rowCount() > 0) {
                    $message = $existing_profile ? "Profile updated successfully!" : "Profile created successfully!";
                } else {
                    $errors[] = "No changes were made to the profile.";
                }
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }

    // Fetch profile data for display
    $stmt = $conn->prepare("SELECT * FROM Profile LIMIT 1");
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_OBJ);

    // Initialize $profile as an empty object if no profile exists
    if (!$profile) {
        $profile = new stdClass();
    }
    ?>

    <h1>Manage Profile</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($profile->name ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="role">Role:</label>
            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($profile->role ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="resume">Resume (PDF):</label>
            <input type="file" id="resume" name="resume" accept=".pdf">
            <?php if (isset($profile->resume)): ?>
                <p>Current resume: <?php echo htmlspecialchars(basename($profile->resume)); ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="image">Profile Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <?php if (isset($profile->image)): ?>
                <p>Current image: <?php echo htmlspecialchars(basename($profile->image)); ?></p>
                <img src="<?php echo htmlspecialchars($profile->image); ?>" alt="Profile Image" style="max-width: 200px;">
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="linkedin_url">LinkedIn URL:</label>
            <input type="url" id="linkedin_url" name="linkedin_url" value="<?php echo htmlspecialchars($profile->linkedin_url ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="github_url">GitHub URL:</label>
            <input type="url" id="github_url" name="github_url" value="<?php echo htmlspecialchars($profile->github_url ?? ''); ?>">
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile->email ?? ''); ?>" required>
        </div>

        <button type="submit">Save Profile</button>
    </form>

    <?php require '../includes/admin_footer.php'; ?>