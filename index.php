<?php
require 'db.php';

$conn = getDB();

echo ($conn) ? "SI" : "NO";