<?php require "../includes/admin_footer.php" ; ?>
<?php require "../../config/config.php; " ?>

<?php 
    
    //fetch profile id
    $stmt = $conn->prepare("SELECT id FROM profile LIMIT 1");
    $stmt->execute();
    $fetch = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$profile) {
        $errors[] = "No profile found. Please create a profile first.";
    }
    
    
    $profile_id = $profile ? $profile->id : null;
    
?>