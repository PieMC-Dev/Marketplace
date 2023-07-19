<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'piemc';

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
?>
