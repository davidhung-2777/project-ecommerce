<?php
// Test database connection
echo "<h1>Test Database Connection</h1>";

// Test 1: Check .env file
echo "<h2>1. Check .env file</h2>";
if (file_exists(__DIR__ . '/../.env')) {
    echo "✅ .env file exists<br>";
    echo "<pre>";
    echo htmlspecialchars(file_get_contents(__DIR__ . '/../.env'));
    echo "</pre>";
} else {
    echo "❌ .env file NOT found<br>";
}

// Test 2: Check vendor/autoload
echo "<h2>2. Check vendor/autoload</h2>";
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    echo "✅ vendor/autoload.php exists<br>";
    require __DIR__ . '/../vendor/autoload.php';
} else {
    echo "❌ vendor/autoload.php NOT found<br>";
    die("Please run: composer install");
}

// Test 3: Load .env
echo "<h2>3. Load .env</h2>";
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    echo "✅ .env loaded successfully<br>";
    echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NOT SET') . "<br>";
    echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NOT SET') . "<br>";
    echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'NOT SET') . "<br>";
} catch (Exception $e) {
    echo "❌ Error loading .env: " . $e->getMessage() . "<br>";
}

// Test 4: Connect to database
echo "<h2>4. Test Database Connection</h2>";
try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $port = $_ENV['DB_PORT'] ?? '3306';
    $dbname = $_ENV['DB_DATABASE'] ?? 'decornest';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    echo "Connecting to: $username@$host:$port/$dbname<br>";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ <strong style='color: green;'>Database connected successfully!</strong><br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ Found {$result['count']} users in database<br>";
    
    // Show tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "✅ Tables in database: " . implode(', ', $tables) . "<br>";
    
} catch (PDOException $e) {
    echo "❌ <strong style='color: red;'>Database connection failed!</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<a href='/project-ecommerce12/'>Go to Homepage</a>";
