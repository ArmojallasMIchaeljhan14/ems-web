<?php
// Check current PHP configuration
echo "<h2>PHP Upload Configuration</h2>";
echo "<table border='1'>";
echo "<tr><th>Setting</th><th>Current Value</th><th>Required</th></tr>";

$settings = [
    'upload_max_filesize' => '50M',
    'post_max_size' => '50M', 
    'memory_limit' => '256M',
    'max_execution_time' => '300',
    'max_input_time' => '300'
];

foreach ($settings as $setting => $required) {
    $current = ini_get($setting);
    $color = (parse_size($current) >= parse_size($required)) ? 'green' : 'red';
    echo "<tr style='color: $color'>";
    echo "<td>$setting</td>";
    echo "<td>$current</td>";
    echo "<td>$required</td>";
    echo "</tr>";
}

echo "</table>";

function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
    $size = preg_replace('/[^0-9\.]/', '', $size);
    if ($unit) {
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    }
    return round($size);
}

echo "<h2>PHP Info</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Loaded ini file: " . php_ini_loaded_file() . "</p>";
echo "<p>Scanned ini files: " . php_ini_scanned_files() . "</p>";
?>
