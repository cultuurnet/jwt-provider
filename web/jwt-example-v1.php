<?php
declare(strict_types=1);

session_start();

$actual_link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

$url = ('http://localhost:9999/jwt-example-v1.php');
?>

<ul>
    <li><a href="http://localhost:9999/connect?destination=<?php echo $url?>">Connect</a></li>
    <li><a href="http://localhost:9999/logout?destination=<?php echo $url?>">Logout</a></li>
    <li><a href="http://localhost:9999/register">Register</a></li>
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
