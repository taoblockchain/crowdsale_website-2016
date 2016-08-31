<?php
/*

*/

/*Define constant to connect to database */
DEFINE('DATABASE_USER', '(removed)');
DEFINE('DATABASE_PASSWORD', '(removed)');
DEFINE('DATABASE_HOST', '(removed)');
DEFINE('DATABASE_NAME', '(removed)');

/*Default time zone, to be able to send mail */
date_default_timezone_set('Europe/Amsterdam');

/*You might not need this */
//ini_set('SMTP', "mail.myt.mu"); // Override The Default Php.ini settings for sending mail

// Make the connection:
$dbc = @mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (!$dbc) {
	trigger_error('Could not connect to MySQL: ' . mysqli_connect_error());
}
?>