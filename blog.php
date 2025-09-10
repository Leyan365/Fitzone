<?php
// session_start(); // This is in header.php, so you don't need it here
include('includes/header.php');

// --- Centralized Data for All Blog Posts ---
$blogCategories = [
    'Nutrition Plans' => [
        [
            'title' => 'Balanced Diet Plan for Beginners',
            'image' => 'images/balanced_diet.jpg',
            'excerpt' => 'Discover a comprehensive nutrition plan designed for those starting their fitness journey.',
            'link' => 'nutrition_balanced_diet.php?id=1'
        ],
        [
            'title' => 'High-Protein Diet for Muscle Gain',
            'image' => 'images/high_protein.jpg',
            'excerpt' => 'This high-protein diet plan focuses on essential nutrients and foods for muscle growth.',
            'link' => 'nutrition_high_protein.php?id=2'
        ]
    ],
    'Meal Plans' => [
        [
            'title' => '7-Day Meal Plan for Weight Loss',
            'image' => 'images/weight_loss_meal_plan.png',
            'excerpt' => 'Our 7-day meal plan is packed with low-calorie meals designed for effective weight loss.',
            'link' => 'mealplan_weight_loss.php?id=1'
        ],
        [
            'title' => 'Vegetarian Meal Plan for Beginners',
            'image' => 'images/vegetarian_meal_plan.jpg',
            'excerpt' => 'This vegetarian meal plan is perfect for a plant-based lifestyle, full of flavor and nutrients.',
            'link' => 'mealplan_vegetarian.php?id=2'
        ]
    ],
    'Health-Related Content' => [
        [
            'title' => 'Importance of Hydration',
            'image' => 'images/hydration_importance.jpg',
            'excerpt' => 'Learn why staying hydrated is critical for your fitness journey and overall health.',
            'link' => 'health_hydration.php?id=1'
        ],
        [
            'title' => 'Understanding Macronutrients',
            'image' => 'images/macronutrients.jpg',
            'excerpt' => 'Macronutrients play a crucial role in our diet. Learn how to balance them for optimal health.',
            'link' => 'health_macronutrients.php?id=2'
        ]
    ],
    'Workout Tips' => [
        [
            'title' => '5 Exercises for Core Strength',
            'image' => 'images/core_strength_exercises.webp',
            'excerpt' => 'A strong core is essential for overall fitness. Try these five effective exercises.',
            'link' => 'workout_core_strength.php?id=1'
        ],
        [
            'title' => 'Beginner’s Guide to Strength Training',
            'image' => 'images/strength_training_guide.jpg',
            'excerpt' => 'This guide covers the basics of strength training to help you get started safely and effectively.',
            'link' => 'workout_strength_training.php?id=2'
        ]
    ],
    'Wellness Advice' => [
        [
            'title' => 'Mindfulness for a Healthier Life',
            'image' => 'images/mindfulness.jpg',
            'excerpt' => 'Discover mindfulness techniques you can incorporate into your daily routine for better well-being.',
            'link' => 'wellness_mindfulness.php?id=1'
        ],
        [
            'title' => 'Improving Sleep Quality',
            'image' => 'images/sleep_quality.webp',
            'excerpt' => 'Sleep is essential for recovery and health. Learn strategies to improve your sleep quality.',
            'link' => 'wellness_sleep_quality.php?id=2'
        ]
    ]
];
?>

<main>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-yellow">FitZone Blog</h1>
            <p class="lead text-white-50">Explore our latest articles, tips, and plans to keep you motivated and healthy!</p>
        </div>

        <?php foreach ($blogCategories as $category => $posts): ?>
            <section class="mb-5">
                <h2 class="text-yellow fw-bold mb-4"><?= htmlspecialchars($category) ?></h2>
                <div class="row g-4">
                    <?php foreach ($posts as $post): ?>
                        <div class="col-lg-6 d-flex align-items-stretch">
                            <div class="card bg-dark text-white service-card h-100">
                                <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($post['title']) ?>" style="height: 250px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-danger fw-bold"><?= htmlspecialchars($post['title']) ?></h5>
                                    <p class="card-text text-white-50 flex-grow-1"><?= htmlspecialchars($post['excerpt']) ?></p>
                                    <a href="<?= htmlspecialchars($post['link']) ?>" class="btn btn-danger mt-auto align-self-start">Read More</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <hr class="feature-divider">
        <?php endforeach; ?>
    </div>
</main>

    <?php include('includes/footer.php');?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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