<?php
session_start();

date_default_timezone_set('America/Los_Angeles');

class config {

	//Database connection. Remember to upload the .sql file too!
	static $DB_SERVER    = 'localhost';
	static $DB_NAME      = 'check-in';
	static $DB_USERNAME  = 'bob';
	static $DB_PASSWORD  = 'password';

	//Site URL and location
	static $DOMAIN       = "localhost";
	static $SITE_URL     = 'https://localhost';
	static $SITE_DIR     = '/var/www/html';

	//Site Info
	static $SITE_NAME    = 'Check In';
}
try {
    $dbh = new PDO('mysql:host='. config::$DB_SERVER .';dbname='. config::$DB_NAME .';charset=utf8', config::$DB_USERNAME, config::$DB_PASSWORD);

} catch(PDOException $ex) {
	phpinfo();
    die("$ex<br /><br />Please configure the MySQL database in config.php and also upload <a href='database.sql'>the .sql file included</a> to the database.");
}
?>
