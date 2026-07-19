<?php
$redis = new Redis();
try {
    $redis->connect('127.0.0.1', 6379);
    echo "Success! Connected to Redis.";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
