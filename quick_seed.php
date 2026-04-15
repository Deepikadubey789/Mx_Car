<?php
// Quick direct database update for testing
$pdo = new PDO('mysql:host=localhost;dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));

$carId = 101;

// Add 30 days of view data
for ($i = 0; $i < 30; $i++) {
    $date = date('Y-m-d', strtotime("-" . (30 - $i) . " days"));
    $views = rand(100, 300);
    $pdo->exec("INSERT INTO cr_car_views (car_id, date, views, created_at, updated_at) 
               VALUES ($carId, '$date', $views, NOW(), NOW())
               ON DUPLICATE KEY UPDATE views = $views, updated_at = NOW()");
}

// Add 8 bookings for car 101
for ($i = 0; $i < 8; $i++) {
    $startDate = date('Y-m-d', strtotime("+" . rand(1, 20) . " days"));
    $endDate = date('Y-m-d', strtotime($startDate . " +" . rand(1, 5) . " days"));
    $total = rand(300, 800);
    
    $pdo->exec("INSERT INTO cr_bookings (user_id, status, total, created_at, updated_at) 
               VALUES (1, 'confirmed', $total, NOW(), NOW())");
    $bookingId = $pdo->lastInsertId();
    
    $pdo->exec("INSERT INTO cr_booking_cars (booking_id, car_id, rental_start_date, rental_end_date, created_at) 
               VALUES ($bookingId, $carId, '$startDate', '$endDate', NOW())");
}

$result = $pdo->query("SELECT SUM(views) as total_views FROM cr_car_views WHERE car_id = $carId");
$views = $result->fetch(PDO::FETCH_ASSOC)['total_views'];

$result = $pdo->query("SELECT COUNT(*) as total FROM cr_booking_cars WHERE car_id = $carId");
$bookings = $result->fetch(PDO::FETCH_ASSOC)['total'];

echo "✓ Created 30 days of view data for car 101\n";
echo "✓ Created 8 test bookings for car 101\n";
echo "✓ Total views: $views\n";
echo "✓ Total bookings: $bookings\n";
echo "\nNow run: php artisan car-rentals:generate-demand-pricing-recommendations --days=30 --sync\n";
