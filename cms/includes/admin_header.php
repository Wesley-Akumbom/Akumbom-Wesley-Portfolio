<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio CMS - Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Portfolio Admin</h1>
            <a href="../profile/profile.php" class="text-white">Home</a>
        </div>
    </header>

    <div class="d-flex flex-grow-1 wrapper"> <!-- Updated wrapper class -->
        <?php if (isset($_SESSION['user_id'])): ?> <!-- Check if user is logged in -->
            <nav class="sidebar p-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../profile/profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../about/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../experience/experience.php">Experience</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../projects/projects.php">Projects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/admin-logout.php">Logout</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
        
        <main class="flex-grow-1 p-3">