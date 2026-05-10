<?php
require_once __DIR__ . '/includes/helpers.php';

// --- Data for Membership Plans ---
$plans = [
    [
        "name" => "Basic Plan",
        "image" => "images/basic.jpg",
        "price" => "LKR 4,000",
        "description" => "Ideal for those who want access to essential gym facilities. This plan provides unlimited access to our gym equipment during open hours.",
        "features" => ["Unlimited gym access", "Access to basic equipment", "Monthly or Quarterly options"]
    ],
    [
        "name" => "Standard Plan",
        "image" => "images/standard.webp",
        "price" => "LKR 6,000",
        "description" => "Perfect for fitness enthusiasts looking for more variety. Access our gym plus popular group classes like Yoga, Pilates, and Zumba.",
        "features" => ["Unlimited gym & group class access", "Two complimentary PT sessions", "Monthly, Quarterly, or Yearly options"]
    ],
    [
        "name" => "Premium Plan",
        "image" => "images/premium.webp",
        "price" => "LKR 10,000",
        "description" => "For those serious about fitness, this plan includes full gym access, all group classes, and personalized training sessions each month.",
        "features" => ["Full gym & all class access", "Four PT sessions per month", "Nutritional support & meal planning"]
    ],
    [
        "name" => "VIP Plan",
        "image" => "images/vip.jpg",
        "price" => "LKR 15,000",
        "description" => "Take your fitness journey to the next level with our all-inclusive VIP Plan, featuring 24/7 gym access and exclusive workshops.",
        "features" => ["24/7 gym access", "Eight PT sessions per month", "Priority booking & exclusive workshops"]
    ],
    [
        "name" => "Family Plan",
        "image" => "images/family.jpg",
        "price" => "LKR 20,000",
        "description" => "Fitness is more fun together! This plan allows up to four family members to access the gym and enjoy family-friendly classes.",
        "features" => ["Access for up to 4 family members", "Family-oriented classes & events", "Discounted personal training rates"]
    ]
];

// --- Data for Trainers ---
$trainers = [
    ["name" => "David Laid", "image" => "images/david.webp", "specialty" => "Specializes in <strong>strength training</strong> and <strong>interval workouts</strong>. He has 10+ years of experience helping clients reach their fitness goals."],
    ["name" => "Sam Sulek", "image" => "images/sam.webp", "specialty" => "A certified trainer with a passion for <strong>high-intensity interval training (HIIT)</strong>. His classes are dynamic, intense, and designed to boost endurance."],
    ["name" => "Anna Kaiser", "image" => "images/anna.jpg", "specialty" => "A <strong>certified yoga instructor</strong> with a decade of experience in Vinyasa and Hatha yoga, focusing on flexibility, mindfulness, and stress relief."],
    ["name" => "Alex Wilson", "image" => "images/alex.jpg", "specialty" => "A <strong>strength and conditioning coach</strong> who helps clients build muscle, enhance endurance, and improve athletic performance with safe, effective techniques."],
    ["name" => "Sarah Lee", "image" => "images/sarah.jpg", "specialty" => "A <strong>personal trainer and nutrition specialist</strong> with a holistic approach, guiding clients through balanced eating habits and workout routines."],
    ["name" => "Emily Chen", "image" => "images/emily.webp", "specialty" => "A <strong>Pilates instructor</strong> known for her focus on core strength, posture, and flexibility. Her classes are ideal for all ages and fitness levels."]
];
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership - FitZone</title>
    <?php include('includes/head_assets.php'); ?>
</head>
<body>
<?php include('includes/header.php'); ?>

<main class="page-main">
    <section class="page-hero text-center">
        <div class="container">
            <p class="section-kicker">Flexible fitness access</p>
            <h1 class="display-4 fw-bold text-yellow">Membership Plans</h1>
            <p class="lead text-white-50">Choose the plan that matches your goals, schedule, and level of support.</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <?php foreach ($plans as $plan): ?>
                <div class="col-lg-6 d-flex align-items-stretch">
                    <div class="card bg-dark text-white service-card membership-plan-card w-100">
                        <div class="row g-0">
                            <div class="col-md-5">
                                <img src="<?= e($plan['image']) ?>" class="img-fluid rounded-start h-100" alt="<?= e($plan['name']) ?>" style="object-fit: cover;">
                            </div>
                            <div class="col-md-7">
                                <div class="card-body d-flex flex-column h-100">
                                    <h3 class="card-title text-yellow"><?= e($plan['name']) ?></h3>
                                    <p class="card-text text-white-50 flex-grow-1"><?= e($plan['description']) ?></p>
                                    <ul class="list-unstyled text-white-50 mb-3">
                                        <?php foreach ($plan['features'] as $feature): ?>
                                            <li>&check; <?= e($feature) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="mt-auto">
                                        <h4 class="fw-bold"><?= e($plan['price']) ?> <small class="text-white-50 fw-normal">/ month</small></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <hr class="feature-divider">

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section-kicker">Expert guidance</p>
                <h1 class="display-4 fw-bold text-yellow">Meet Our Trainers</h1>
                <p class="lead text-white-50">Expert guidance for your fitness journey.</p>
            </div>

            <div class="row g-4">
                <?php foreach ($trainers as $trainer): ?>
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                    <div class="card text-white bg-dark service-card trainer-card text-center h-100">
                        <img src="<?= e($trainer['image']) ?>" class="card-img-top" alt="<?= e($trainer['name']) ?>" style="height: 300px; object-fit: cover; object-position: top;">
                        <div class="card-body">
                            <h4 class="card-title"><?= e($trainer['name']) ?></h4>
                            <p class="card-text text-white-50"><?= $trainer['specialty'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

    <?php include('includes/footer.php');?>

<script>
    // Script for scrolled navbar (optional, but recommended if using sticky-top navbar)
    const nav = document.querySelector('.navbar');
    if (nav) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    }
</script>

</body>
</html>
