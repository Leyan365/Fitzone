<?php
const FITZONE_MEMBERSHIP_PLANS = [
    'Basic Plan' => ['price' => 'LKR 4,000', 'summary' => 'Unlimited gym access and essential equipment.'],
    'Standard Plan' => ['price' => 'LKR 6,000', 'summary' => 'Gym access, group classes, and two PT sessions.'],
    'Premium Plan' => ['price' => 'LKR 10,000', 'summary' => 'Full access, all classes, monthly PT, and nutrition support.'],
    'VIP Plan' => ['price' => 'LKR 15,000', 'summary' => '24/7 gym access, priority booking, and exclusive workshops.'],
    'Family Plan' => ['price' => 'LKR 20,000', 'summary' => 'Access for up to four family members.'],
];

function membership_plans(): array
{
    return FITZONE_MEMBERSHIP_PLANS;
}

function ensure_fitness_feature_tables(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS membership_requests (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            plan_name VARCHAR(100) NOT NULL,
            plan_price VARCHAR(40) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS class_sessions (
            id INT(11) NOT NULL AUTO_INCREMENT,
            title VARCHAR(120) NOT NULL,
            trainer VARCHAR(100) NOT NULL,
            session_day VARCHAR(20) NOT NULL,
            session_time VARCHAR(20) NOT NULL,
            capacity INT(11) NOT NULL DEFAULT 15,
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            PRIMARY KEY (id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS class_bookings (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            session_id INT(11) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'booked',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY session_id (session_id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");

    $result = $conn->query('SELECT COUNT(*) AS count FROM class_sessions');
    $count = $result ? (int) $result->fetch_assoc()['count'] : 0;

    if ($count > 0) {
        return;
    }

    $sessions = [
        ['Strength Training', 'David Laid', 'Monday', '6:00 AM', 20],
        ['Yoga & Flexibility', 'Anna Kaiser', 'Tuesday', '6:00 AM', 18],
        ['High-Intensity Cardio', 'Sam Sulek', 'Wednesday', '8:00 AM', 16],
        ['Pilates & Mobility', 'Emily Chen', 'Thursday', '2:00 PM', 14],
        ['Nutrition Coaching', 'Sarah Lee', 'Friday', '12:00 PM', 12],
        ['Sports Conditioning', 'Alex Wilson', 'Saturday', '10:00 AM', 18],
    ];

    $stmt = $conn->prepare('INSERT INTO class_sessions (title, trainer, session_day, session_time, capacity) VALUES (?, ?, ?, ?, ?)');

    foreach ($sessions as $session) {
        [$title, $trainer, $day, $time, $capacity] = $session;
        $stmt->bind_param('ssssi', $title, $trainer, $day, $time, $capacity);
        $stmt->execute();
    }

    $stmt->close();
}

function customer_membership_requests(mysqli $conn, int $user_id): array
{
    $stmt = $conn->prepare('
        SELECT id, plan_name, plan_price, status, created_at, updated_at
        FROM membership_requests
        WHERE user_id = ?
        ORDER BY created_at DESC
    ');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $rows;
}

function class_sessions_for_customer(mysqli $conn, int $user_id): array
{
    $stmt = $conn->prepare("
        SELECT
            s.id,
            s.title,
            s.trainer,
            s.session_day,
            s.session_time,
            s.capacity,
            COALESCE(SUM(CASE WHEN b.status = 'booked' THEN 1 ELSE 0 END), 0) AS booked_count,
            MAX(CASE WHEN b.user_id = ? AND b.status = 'booked' THEN 'booked' ELSE NULL END) AS user_status
        FROM class_sessions s
        LEFT JOIN class_bookings b ON b.session_id = s.id
        WHERE s.is_active = 1
        GROUP BY s.id, s.title, s.trainer, s.session_day, s.session_time, s.capacity
        ORDER BY FIELD(s.session_day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.session_time
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $rows;
}

function pending_membership_count(mysqli $conn): int
{
    $result = $conn->query("SELECT COUNT(*) AS count FROM membership_requests WHERE status = 'pending'");
    return $result ? (int) $result->fetch_assoc()['count'] : 0;
}

function active_booking_count(mysqli $conn): int
{
    $result = $conn->query("SELECT COUNT(*) AS count FROM class_bookings WHERE status = 'booked'");
    return $result ? (int) $result->fetch_assoc()['count'] : 0;
}
