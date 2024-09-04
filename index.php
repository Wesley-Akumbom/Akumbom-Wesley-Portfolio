<?php require "includes/header.php"; ?>
<?php require "config/config.php"; ?>
<?php require "cms/functions/functions.php" ?>


<?php 

    // Fetch experience data
    $stmt = $conn->prepare("SELECT * FROM experience ORDER BY updated_at DESC");
    $stmt->execute();
    $experiences = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Group the experiences by title
    $experienceGroups = [];
    foreach ($experiences as $experience) {
        $group = $experience->title;
        if (!isset($experienceGroups[$group])) {
            $experienceGroups[$group] = [];
        }
        $experienceGroups[$group][] = $experience;
    }

    //fetch about-experience information
    $find = $conn->prepare("SELECT * FROM about_exp ORDER BY updated_at DESC");
    $find->execute();
    $about_exp = $find->fetchAll(PDO::FETCH_OBJ);

    //fetch education information
    $select = $conn->prepare("SELECT * FROM about_edu ORDER BY updated_at DESC");
    $select->execute();
    $about_edu = $select->fetchAll(PDO::FETCH_OBJ);

    // Fetch profile data
    $stmt = $conn->prepare("SELECT * FROM Profile LIMIT 1");
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_OBJ);

    // Check if profile exists
    if (!$profile) {
        die("Profile not found. Please create a profile in the admin area.");
    }
?>

    <section id="profile">
      <div class="section__pic-container">
      <img src="./uploads/images/<?php echo htmlspecialchars(basename($profile->image)); ?>" 
                alt="<?php echo htmlspecialchars($profile->name); ?> profile picture" />
      </div>
      <div class="section__text">
        <p class="section__text__p1">Hello, I'm</p>
        <h1 class="title"><?php echo $profile->name ?></h1>
        <p class="section__text__p2"><?php echo $profile->role ?></p>
        <div class="btn-container">
          <button
            class="btn btn-color-2"
            onclick="window.open('<?php echo $profile->resume ?>')"
          >
            Download Resume
          </button>
          <button class="btn btn-color-1" onclick="location.href='./#contact'">
            Contact Info
          </button>
        </div>
        <div id="socials-container">
          <img
            src="./uploads/images/linkedin.png"
            alt="My LinkedIn profile"
            class="icon"
            onclick="location.href='<?php echo $profile->linkedin_url ?>'"
          />
          <img
            src="./uploads/images/github.png"
            alt="My Github profile"
            class="icon"
            onclick="location.href='<?php echo $profile->github_url ?>'"
          />
        </div>
      </div>
    </section>

    <section id="about">
    <p class="section__text__p1">Get To Know More</p>
    <h1 class="title">About Me</h1>
    <div class="section-container">
        <div class="about-details-container">
            <div class="about-containers">
                <div class="details-container">
                    <img src="./uploads/images/experience.png" alt="Experience icon" class="icon" />
                    <h3>Experience</h3>
                    <br>
                    <?php foreach ($about_exp as $exp): ?>
                        <p><?php echo $exp->exp_years; ?>+ years<br /><?php echo $exp->exp_field; ?></p><br>
                    <?php endforeach; ?>
                </div>
                <div class="details-container">
                    <img src="./uploads/images/education.png" alt="Education icon" class="icon" />
                    <h3>Education</h3>
                    <br>
                    <?php foreach ($about_edu as $edu): ?>
                        <p><?php echo $edu->level; ?><br /><?php echo $edu->certificate; ?> (<?php echo $edu->years; ?>)</p>
                        <br>
                        <?php endforeach; ?>
                </div>
            </div>
            
    
            <div class="text-container">
                <p>
                    <?php echo htmlspecialchars( $about_exp[0]->about_me ?? 'I am a firm believer in writing clean, efficient, and well-documented code. I enjoy solving complex problems and continuously strive to improve my skills and stay updated with the latest industry trends.'); ?>
                </p>
            </div>
        </div>
    </div>
    <img src="./uploads/images/arrow.png" alt="Arrow icon" class="icon arrow" onclick="location.href='./#experience'" />
  </section>

  <section id="experience">
    <p class="section__text__p1">Explore My</p>
    <h1 class="title">Experience</h1>
    <div class="experience-details-container">
        <div class="about-containers">
            <?php foreach ($experienceGroups as $group => $groupExperiences): ?>
            <div class="details-container">
                <h2 class="experience-sub-title"><?php echo htmlspecialchars($group); ?></h2>
                <div class="article-container">
                    <?php foreach ($groupExperiences as $exp): ?>
                    <article>
                        <div class="skill-container">
                            <?php
                            $skills = explode(',', $exp->skill);
                            $levels = explode(',', $exp->level);
                            for ($i = 0; $i < count($skills); $i++):
                            ?>
                            <div class="skill-item">
                                <!-- <img src="./uploads/images/checkmark.png" alt="Checkmark icon" class="icon" /> -->
                                <div class="skill-info">
                                    <h3><?php echo htmlspecialchars(trim($skills[$i])); ?></h3>
                                    <p><?php echo htmlspecialchars(trim($levels[$i])); ?></p>
                                    <br>
                                </div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <img src="./uploads/images/arrow.png" alt="Arrow icon" class="icon arrow" onclick="location.href='./#projects'" />
</section>

    <section id="projects">
      <p class="section__text__p1">Browse My Recent</p>
      <h1 class="title">Projects</h1>
      <div class="experience-details-container">
        <div class="about-containers">
          <div class="details-container color-container">
            <div class="article-container">
              <img
                src="./uploads/images/project-1.png"
                alt="Project 1"
                class="project-img" 
              />
            </div>
            <h2 class="experience-sub-title project-title">Dummy Project One</h2>
            <div class="btn-container">
              <button
                class="btn btn-color-2 project-btn"
                onclick="location.href='https://github.com/'"
              >
                Github
              </button>
              <button
                class="btn btn-color-2 project-btn"
                onclick="location.href='https://github.com/'"
              >
                Live Demo
              </button>
            </div>
          </div>
          <div class="details-container color-container">
            <div class="article-container">
              <img
                src="./uploads/images/project-2.png"
                alt="Project 2"
                class="project-img"
              />
            </div>
            <h2 class="experience-sub-title project-title"> Dummy Project Two</h2>
            <div class="btn-container">
              <button
                class="btn btn-color-2 project-btn"
                onclick="location.href='https://github.com/'"
              >
                Github
              </button>
              <button
                class="btn btn-color-2 project-btn"
                onclick="location.href='https://github.com/'"
              >
                Live Demo
              </button>
            </div>
          </div>
          <div class="details-container color-container">
            <div class="article-container">
              <img
                src="./uploads/images/project-3.png"
                alt="Project 3"
                class="project-img"
              />
            </div>
            <h2 class="experience-sub-title project-title">Dummy Project Three</h2>
            <div class="btn-container">
              <button
                class="btn btn-color-2 project-btn"
                onclick="location.href='https://github.com/'"
              >
                Github
              </button>
              <button
                class="btn btn-color-2 project-btn"
                onclick="location.href='https://github.com/'"
              >
                Live Demo 
              </button>
            </div>
          </div>
        </div>
      </div>
      <img
        src="./uploads/images/arrow.png"
        alt="Arrow icon"
        class="icon arrow"
        onclick="location.href='./#contact'"
      />
    </section>
    <section id="contact">
      <p class="section__text__p1">Get in Touch</p>
      <h1 class="title">Contact Me</h1>
      <div class="contact-info-upper-container">
        <div class="contact-info-container">
          <img
            src="./uploads/images/email.png"
            alt="Email icon"
            class="icon contact-icon email-icon"
          />
          <p><a href="mailto:akumbomwesley7@gmail.com">Akumbom Wesley</a></p>
        </div>
        <div class="contact-info-container">
          <img
            src="./uploads/images/linkedin.png"
            alt="LinkedIn icon"
            class="icon contact-icon"
          />
          <p><a href="https://www.linkedin.com/in/akumbom-wesley-b978ab235?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app">LinkedIn</a></p>
        </div>
      </div>
    </section>

    <?php require "includes/footer.php"; ?>