<?php

declare(strict_types=1);

session_start();

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    header('response-code: 400');
    die('Access denied.');
}

$url = urlencode('http://localhost:9999/jwt-example.php?apiKey=f3527f1c-210f-4075-99d3-ece98cf2b391');
?>

<ul>
    <li><a href="http://localhost:9999/connect?destination=<?php echo $url?>">Connect without apiKey</a></li>
    <li><a href="http://localhost:9999/connect?apiKey=f3527f1c-210f-4075-99d3-ece98cf2b391&destination=<?php echo $url?>">Connect with apiKey</a></li>
    <li><a href="http://localhost:9999/logout?destination=<?php echo $url?>">Logout</a></li>
    <li><a href="http://localhost:9999/logout-confirm">Logout confirm</a></li>
    <li><a href="http://localhost:9999/refresh?apiKey=f3527f1c-210f-4075-99d3-ece98cf2b391&refresh=<?php echo $_GET['refresh'] ?? '' ?>">Refresh</a></li>
</ul>

    <style>
        td {
            word-wrap: anywhere;
        }
    </style>

<?php
if (!empty($_GET)) {
    echo '<table border="1">';
    echo '<tr><th>Parameter</th><th>Value</th></tr>';
    foreach ($_GET as $key => $value) {

        echo '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($value) . '</td></tr>';
    }
    echo '</table>';
}
