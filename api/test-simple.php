<?php
header('Content-Type: text/plain');
echo "PHP IS WORKING!\n";
echo "Version: " . phpversion() . "\n";
echo "Files in api folder:\n";
print_r(scandir(__DIR__));
