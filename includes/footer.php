<?php require "config/config.php"; ?>

<?php 

   // Fetch profile data
   $stmt = $conn->prepare("SELECT * FROM Profile LIMIT 1");
   $stmt->execute();
   $profile = $stmt->fetch(PDO::FETCH_OBJ);

?>

<footer>
  <nav>
    <div class="nav-links-container">
      <ul class="nav-links">
        <li><a href="#about">About</a></li>
        <li><a href="#experience">Experience</a></li>
        <li><a href="#projects">Projects</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </div>
  </nav>
  <p>Copyright &#169; 2024 <?php echo $profile->name ?>. All Rights Reserved.</p>
</footer>
<script src="script.js"></script>
</body>
</html>