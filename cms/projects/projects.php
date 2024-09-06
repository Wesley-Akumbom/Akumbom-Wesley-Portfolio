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

// Fetch the projects
$stmt = $conn->prepare("SELECT * FROM projects WHERE profile_id = :profile_id");
$stmt->execute([':profile_id' => $profile_id]);
$projects = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<div class="container">
    <h1 class="text-center">Projects</h1>

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

    <?php if ($profile_id && !empty($projects)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>GitHub URL</th>
                        <th>Live URL</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($project->image); ?>" alt="<?php echo htmlspecialchars($project->title); ?>" style="max-width: 100px;"></td>
                            <td><?php echo htmlspecialchars($project->title); ?></td>
                            <td><a href="<?php echo htmlspecialchars($project->github_url); ?>" target="_blank"><?php echo htmlspecialchars($project->github_url); ?></a></td>
                            <td><a href="<?php echo htmlspecialchars($project->website_url); ?>" target="_blank"><?php echo htmlspecialchars($project->website_url); ?></a></td>
                            <td>
                                <a href="update-project.php?id=<?php echo $project->id; ?>" class="btn btn-primary">Update</a>
                                <a href="delete-project.php?id=<?php echo $project->id; ?>" class="btn btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <a href="delete-all-projects.php" class="btn btn-danger">Delete All Projects</a>
            <a href="create-project.php" class="btn btn-success" style="margin-left: 10px;">Add a New Project</a>
        </div>
    <?php else: ?>
        <p class="text-center">No projects found.</p>
    <?php endif; ?>

    <?php if (!$profile_id): ?>
        <p class="text-center">Please create a profile before adding projects.</p>
    <?php endif; ?>
</div>

<?php require_once "../includes/admin_footer.php"; ?>