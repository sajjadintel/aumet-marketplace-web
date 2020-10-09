<?php

// Reports all errors
error_reporting(E_ALL);

// Do not display errors for the end-users (security issue)
ini_set('display_errors', 'On');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

date_default_timezone_set("Asia/Dubai");

require_once("vendor/autoload.php");
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$f3 = \Base::instance();

$f3->set('AUTOLOAD', "app/controllers/ | app/classes/ | app/models/");

/* Config */
$f3->set('DEBUG', '9');
$f3->set('UI', 'ui/');
$f3->set('LOGS', 'logs/');
$f3->set('pagesDIR', 'ui/pages');
$f3->set('LOCALES', 'app/languages/');
$f3->set('FALLBACK', 'ar');
$f3->set('ENCODING', 'UTF-8');
$f3->set('uploadDIR', '/files/uploads');

$f3->set('platformVersion', $f3->get('platformVersionDevelopment'));
$f3->set('platformVersionRelease', '?v=1.3');
$f3->set('platformVersionDevelopment', '?v=' . date('His'));
$f3->set('authServerKey', '-SC4,=$?.3:&KRR]:DCQx{~wY!)`+--CkhE`2ur<VCZ(Tk8Pt2YXvdp3mz>3wsW`');

$dbPort = getenv('DB_PORT');

if (getenv('ENV') == Constants::ENV_LOC) {
    $dbHost = getenv('DB_HOST_LOC');
    $dbUsername = getenv('DB_USER_LOC');
    $dbPassword = getenv('DB_PASS_LOC');
} else if (getenv('ENV') == Constants::ENV_PROD) {
    $dbHost = getenv('DB_HOST_PROD');
    $dbUsername = getenv('DB_USER_PROD');
    $dbPassword = getenv('DB_PASS_PROD');
}

$dbDatabaseMain = getenv('DB_NAME_MAIN');

$f3->set('dbUsername', $dbUsername);
$f3->set('dbPassword', $dbPassword);
$f3->set('dbConnectionString', "mysql:host=$dbHost;port=$dbPort;dbname=$dbDatabaseMain");

$f3->set('mailHost', 'smtp.sendgrid.net');
$f3->set('mailUsername', 'apikey');
$f3->set('mailPassword', getenv('SENDGRID_API_KEY'));
$f3->set('mailSMTPSecure', 'tls');
$f3->set('mailPort', 587);
$f3->set('mailFromName', 'Aumet Marketplace');
$f3->set('mailFromEmail', 'no-reply@aumet.tech');
$f3->set('mailBCC', 'a.atrash@aumet.me');

define('CHUNK_SIZE', 1024 * 1024);

global $dbConnection;

$dbConnection = new DB\SQL(
    $f3->get('dbConnectionString'),
    $f3->get('dbUsername'),
    $f3->get('dbPassword'),
    array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
);

if (getenv('ENV') == 'loc'){
    ini_set('mysql.connect_timeout', 300);
    ini_set('default_socket_timeout', 300);     
} else {
    // session_save_path("/tmp");
}

include_once("routes.php");

session_start();
/*
switch ($_SESSION['userLang']) {
    case "en":
    case "ar":
    case "fr":
        $f3->set('LANGUAGE', $_SESSION['userLang']);
        break;
    default:
        $f3->set('LANGUAGE', "ar");
        break;
}
*/
$f3->run();
