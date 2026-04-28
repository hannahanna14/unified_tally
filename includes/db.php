<?php
date_default_timezone_set('Asia/Manila');
$db_file = __DIR__ . '/../db/tally.db';
$is_new = !file_exists($db_file);

if (!file_exists(__DIR__ . '/../db')) {
    mkdir(__DIR__ . '/../db', 0777, true);
}

try {
    $db = new PDO('sqlite:' . $db_file);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($is_new) {
        $query = "CREATE TABLE IF NOT EXISTS tally_records (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            point_of_entry TEXT,
            referral_source TEXT,
            classification TEXT,
            gender TEXT,
            civil_status TEXT,
            address TEXT,
            occupation TEXT,
            house TEXT,
            light_source TEXT,
            water_source TEXT,
            educational_attainment TEXT,
            household_members TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $db->exec($query);
        
        $query_users = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE,
            password TEXT
        )";
        $db->exec($query_users);
        
        // Add default admin user (admin / admin123)
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $hash]);
    }
} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>
