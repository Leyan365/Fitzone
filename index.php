<?php
require_once __DIR__ . '/includes/helpers.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone Fitness Center</title>
    <?php include('includes/head_assets.php'); ?>
</head>
<body>
<?php include('includes/header.php'); ?>

<header class="header-with-bg">
    <div class="container d-flex flex-column align-items-center justify-content-center h-100 text-white">
        <h1 class="display-3 fw-bold">FitZone Fitness Center</h1>
        <p class="lead">Your journey to health and fitness starts here.</p>
        <div class="hero-actions">
            <a href="register.php" class="btn btn-warning btn-lg fw-bold">Join Now</a>
            <a href="membership.php" class="btn btn-outline-light btn-lg">View Plans</a>
        </div>
    </div>
</header>

<main>
    <section class="py-5">
        <div class="container text-center">
            <p class="section-kicker">Train with purpose</p>
            <h2 class="text-yellow fw-bold">Build Your Ideal Body</h2>
            <img
                src="images/body15.webp"
                alt="Build Your Body"
                class="img-fluid rounded my-4 mx-auto d-block hover-zoom"
                style="max-width:500px;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <p class="lead text-white-50">At Fitzone Fitness Center, we help you build your ideal body while maintaining long-term health. Through consistent workouts, you can tone muscles, shed excess weight, and build a physique that reflects your hard work and dedication.</p>
                    <p class="lead text-white-50">Beyond appearance, our team’s fitness programs strengthen your heart, boost metabolism, and improve overall well-being. By committing to regular exercise, you're investing in both your physical health and your confidence.</p>
                </div>
            </div>
            <div class="row g-4 mt-4">
                <div class="col-md-3 col-6"><img src="images/body8.jpg" alt="Fitness" class="img-fluid rounded hover-zoom"></div>
                <div class="col-md-3 col-6"><img src="images/body6.jpg" alt="Fitness" class="img-fluid rounded hover-zoom"></div>
                <div class="col-md-3 col-6"><img src="images/body5.jpg" alt="Fitness" class="img-fluid rounded hover-zoom"></div>
                <div class="col-md-3 col-6"><img src="images/body9.jpg" alt="Fitness" class="img-fluid rounded hover-zoom"></div>
            </div>
        </div>
    </section>

    <hr class="feature-divider">

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-5 feature-panel p-4">
                <div class="col-lg-6">
                    <p class="section-kicker">Mind and body</p>
                    <h3 class="text-white mb-3">A Transformative Journey for Body & Mind</h3>
                    <p class="text-white-50">Working out is a transformative journey that enhances both your body and mind. Each session in the gym challenges you to push your limits. Fitness not only improves your physical appearance but also cultivates a positive mindset.</p>
                    <p class="text-white-50">Every drop of sweat brings you closer to your goals. Each workout is an opportunity to challenge yourself and discover what you are truly capable of. Embrace the transformative power of fitness at Fitzone fitness Center!</p>
                </div>
                <div class="col-lg-6">
                    <img src="images/body11.jpg" alt="Transformative Journey" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <hr class="feature-divider">

    <section class="py-5">
        <div class="container">
            <p class="section-kicker text-center">What we offer</p>
            <h2 class="text-center text-yellow fw-bold mb-5">Our Services</h2>
            <div class="row g-4">
                <?php
                $services = [
                    ["title" => "Cardio", "text" => "Boost your endurance and burn calories with our dynamic cardio workouts.", "img" => "images/cardio.webp"],
                    ["title" => "Strength Training", "text" => "Build muscle, boost endurance, and enhance overall fitness.", "img" => "images/strength.jpg"],
                    ["title" => "Yoga", "text" => "Discover the power of yoga for flexibility, balance, and inner peace.", "img" => "images/yoga.webp"],
                    ["title" => "Personal Training", "text" => "One-on-one training sessions with our expert trainers to help you achieve your fitness goals.", "img" => "images/personaltraining.jpg"],
                    ["title" => "Group Classes", "text" => "Join our dynamic group classes that make fitness fun and motivating.", "img" => "images/grp.jpg"],
                    ["title" => "Nutrition Counseling", "text" => "Get personalized nutrition advice to complement your fitness routine.", "img" => "images/nutritioncounseling.webp"],
                    ["title" => "State of the art Equipment", "text" => "Access the latest technology for a safe, effective, and enjoyable workout experience.", "img" => "images/equipment.png"],
                    ["title" => "Bodybuilding", "text" => "Transform your physique with our dedicated bodybuilding programs.", "img" => "images/bodybuilding.jpg"],
                    ["title" => "Online Training", "text" => "Personalized fitness guidance from your home, tailored to your goals and schedule.", "img" => "images/onlinetraining.jpg"]
                ];

                foreach ($services as $service):
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card text-white bg-dark service-card h-100">
                            <img src="<?= e($service['img']) ?>" class="card-img-top" alt="<?= e($service['title']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= e($service['title']) ?></h5>
                                <p class="card-text flex-grow-1"><?= e($service['text']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 text-center bg-dark">
        <div class="container">
            <h3 class="text-success">Join Us Today!</h3>
            <p class="text-white-50">Register now and experience the FitZone difference!</p>
            <a href="register.php" class="btn btn-success btn-lg">Join Us</a>
        </div>
    </section>

    <hr class="feature-divider">

    <section class="py-5">
        <div class="container">
            <div class="row align-items-center g-5 feature-panel p-4">
                <div class="col-lg-6">
                    <img src="images/aboutuswall.webp" class="img-fluid rounded shadow-lg" alt="About FitZone">
                </div>
                <div class="col-lg-6">
                   <h2 class="display-4 fst-italic" style="color: #ffc107;">About Us</h2>
                    <p class="text-white-50">Welcome to Fitzone Fitness Center, a modern fitness club in Kurunegala. Our mission is to offer a fitness experience that is personalized to your unique goals, delivering results while inspiring and energizing you. We provide top-quality equipment, expert-led training programs, and exclusive amenities rarely found in other gyms.</p>
                    <p class="text-white-50">Our philosophy is to be the best in the fitness industry. We are driven by a genuine purpose: to understand and prioritize our members' needs. Founded by Anuj Pathirage, who believes in the power of a healthy lifestyle, Fitzone aims to offer fitness in a refined and exclusive space.</p>
                    <p class="text-white-50">Our mission is to inspire peak performance in body, mind, and spirit. We envision a fitness center that not only strengthens our members' physical health but also nourishes their well-being.</p>
                </div>
            </div>
        </div>
    </section>
</main>

    <?php include('includes/footer.php');?>

</body>

</html>
