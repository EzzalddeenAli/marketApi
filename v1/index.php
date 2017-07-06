<?php

require '../vendor/autoload.php';
require '../include/Dbhandler.php';

use Slim\App;

$app = new App();
$app->post('/login', function ($request, $res, $args) {
    $contact = $request->getParam('contact');
    $password = $request->getParam('password');
    $db = new Dbhandler();
    $login_succes = $db->checkUserLogin($contact, $password);
    $response = array();
    if ($login_succes) {
        $user_details = $db->userDetails($contact);
        $response['full_name'] = $user_details['firstName'] . " " . $user_details['lastName'];
        $response['user_id'] = $user_details['user_id'];
        $response['error'] = FALSE;
        $response['message'] = "Login successfull";
    } else {
        $response['message'] = "wrong contact and password  try again";
        $response['error'] = TRUE;
    }
    echoResponse($response);
});
$app->post('/create_account', function ($request, $res, $args) {
    $db = new Dbhandler();
    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $contact = $request->getParam('contact');
    $password = $request->getParam('password');
    $userExists = $db->IsUserExists($contact);
    $response = array();
    if ($userExists) {
        $response['message'] = "Account already exists";
        $response['error'] = TRUE;
    } else {
        $create_account = $db->create_account($firstName, $lastName, $contact, $password);
        if ($create_account) {
            $user_details = $db->userDetails($contact);
            $response['full_name'] = $user_details['firstName'] . " " . $user_details['lastName'];
            $response['user_id'] = $user_details['user_id'];
            $response['error'] = FALSE;
            $response['message'] = "Account created successfully";
        }
    }
    echoResponse($response);
});
$app->get('/markets', function ($request, $res, $args) {
    $db = new Dbhandler();
    $result = $db->fetch_markets();
    $response = array();
    while ($row = $result->fetch_assoc()) {
        $array['market_id'] = $row['market_id'];
        $array['market_name'] = $row['market_name'];
        $array['location'] = $row['location'];
        array_push($response, $array);
    }
    echoResponse($response);
});
$app->post('/today_prices', function ($request, $res, $args) {
    $db = new Dbhandler();
    $market_id = $request->getParam('market_id');
    $result = $db->fetch_today_price($market_id);
    $response = array();
    $response['status'] = FALSE;
    if ($result->num_rows) {
        $response["data"] = array();
        while ($row = $result->fetch_assoc()) {
            $array['product_name'] = $row['product_name'];
            $array['quantity'] = $row['quantity'] . " " . $row['units'];
            $array['price'] = $row['price'];
            $array['category_name'] = $row['category_name'];
            array_push($response["data"], $array);
        }
    } else {
        $response['status'] = TRUE;
        $response['message'] = "No price updates available";
    }

    echoResponse($response);
});
$app->post('/filter_by_date', function ($request, $res, $args) {
    $db = new Dbhandler();
    $date = $request->getParam('date');
    $market_id = $request->getParam('market_id');
    $result = $db->filter_by_date($market_id, $date);
    $response = array();
    $response['status'] = FALSE;

    if ($result->num_rows) {
        $response["data"] = array();
        while ($row = $result->fetch_assoc()) {
            $array['product_name'] = $row['product_name'];
            $array['quantity'] = $row['quantity'] . " " . $row['units'];
            $array['price'] = $row['price'];
            $array['category_name'] = $row['category_name'];
            array_push($response["data"], $array);
        }
    } else {
        $response['status'] = TRUE;
        $response['message'] = "No price updates available";
    }

    echoResponse($response);
});

function echoResponse($response) {
    echo json_encode($response);
}

$app->run();
?>