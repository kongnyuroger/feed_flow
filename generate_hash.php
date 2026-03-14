<?php


$password = 'kenneorla.2026';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<hr>";
echo "<p><strong>Your email:</strong> kenneorla1@gmail.com</p>";
echo "<p><strong>Your password:</strong> " . $password . "</p>";
echo "<p><strong>Generated hash:</strong> <code style='background:#f0f0f0; padding:5px; display:block; margin:10px 0;'>" . $hash . "</code></p>";
echo "<hr>";
echo "<h3>Copy this SQL to insert admin:</h3>";
echo "<textarea rows='5' cols='80' style='padding:10px; font-family:monospace;'>";
echo "INSERT INTO users (full_name, email, password) VALUES \n";
echo "('Kenne Orla', 'kenneorla1@gmail.com', '" . $hash . "');";
echo "</textarea>";
echo "<hr>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Copy the hash above</li>";
echo "<li>Open database.sql</li>";
echo "<li>Replace 'your_generated_hash_here' with this hash</li>";
echo "<li>Run database.sql in phpMyAdmin</li>";
echo "</ol>";
?>