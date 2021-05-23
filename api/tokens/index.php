<?php
include_once '../config/config.php';
include_once '../config/database.php';
include_once '../objects/token.php';
include_once '../util/util.php';

// prevent notices

if (!$GLOBALS['debug'] ) {
    error_reporting(0);
}

$method = $_SERVER['REQUEST_METHOD'];
if ($GLOBALS['debug']) echo "method=" . $method . "\n";

$request = explode("/", substr(@$_SERVER['PATH_INFO'], 1));
if ($GLOBALS['debug']) echo "request=" . implode(",", $request) . "\n";

// get database connection
$database = new Database();
$db = $database->getConnection();

switch ($method) {
    case 'PUT':
        doPut($db, $request, file_get_contents('php://input'));
        break;
    case 'POST':
        doPost($db, $request, file_get_contents('php://input'));
        break;
    case 'GET':
        doGet($db, $request);
        break;
    default:
        handle_error($db, $request);  
        break;
}

function doGet($db, $request) {
    if ($GLOBALS['debug']) echo "doGet" . "\n";

    // param code check
    if (isset($_GET["code"])) {
        $code = $_GET["code"];
    } else {
        $code = null;
    }
    
    $token = new Token($db);
    $data = $token->select($code);
    Token::writeJsonResponse($data);
    echo $request;
}

function doPost($db, $request, $json) {
    if ($GLOBALS['debug']) echo "doPost" . "\n";
    $data = new stdClass();
    $data->status = "doPost";
    Token::writeJsonResponse($data);
}

function doPut($db, $request, $json) {
    if ($GLOBALS['debug']) echo "doPut" . "\n";
    $body = json_decode($json);
    $token = new Token($db);
    $token->import($body);
    $data = $token->update();
    Token::writeJsonResponse($data);
}

function handle_error($db, $request) {
    if ($GLOBALS['debug']) echo "handle_error";
    $data = new stdClass();
    $data->status = "handle_error";
    Token::writeJsonResponse($data);
}

?>