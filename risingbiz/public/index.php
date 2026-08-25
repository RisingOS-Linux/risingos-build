<?php
require_once __DIR__ . '/../bootstrap.php';

$stmt = $db->query("SELECT sqlite_version()");
$version = $stmt->fetchColumn();

echo "<h1>RisingBiz</h1>";
echo "<p>SQLite version: $version</p>";
