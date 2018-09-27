<?php
require 'Slim/Slim.php';

//CONSTANTES DE OBRIGATORIEDADE - Alterar após revisão
define("askForBirthdayDate", false);
define("askForGender", false);

date_default_timezone_set("America/Sao_Paulo");

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

$app->notFound(
    function () use($app)
    {
        notFound();
    });

$app->error(
    function (Exception $e) use($app)
    {

        // notFound();

    });

$app->config('debug', false);

// GET ROUTES

$app->get('/', 'notFound');
$app->get('/checkin/list/:userId/:localId', 'checkinList');
$app->get('/local/info/:localId', 'localInfo');
$app->get('/user/profile/:userId', 'getUserProfile');
$app->get('/notifications/list/:userId/:localId', 'getNotificationsForUser');
$app->get('/local/visual-settings/:localId', 'visualSettings');

// POST ROUTES

$app->post('/checkin/add', 'checkinAdd');
$app->post('/user/profile/update', 'updateUserProfile');
$app->post('/user/profile/updatefacebook', 'updateUserProfileWithFacebookUser');
$app->post('/user/login', 'userLogin');
$app->post('/user/login/availability', 'checkLoginAvailability');
$app->post('/user/forgotpassword', 'userForgotPassword');
$app->post('/user/updatetoken', 'updateUserToken');

// DELETE ROUTES

$app->run();

function notFound()
{
    header('HTTP/1.1 404 Not Found');
    die();
}

function getConn()
{
    if ($_SERVER['SERVER_NAME'] == "localhost") {
        return new PDO('mysql:host=localhost;dbname=loyaltee', 'root', 'mysql', array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
    }
    else
        if ($_SERVER['SERVER_NAME'] == "www.loyaltee.com.br" || $_SERVER['SERVER_NAME'] == "loyaltee.com.br") {
            return new PDO('mysql:host=localhost;dbname=loyaltee_appProd', 'loyaltee_root', 'Redentor1Loyaltee2', array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ));
        }
}

function checkinList($userId, $localId)
{
    $sqlCheckinList = "SELECT C.ID AS checkinId, C.LOCAL_ID AS localId, C.USER_ID AS userId, C.DATE AS date, C.CONSUMED AS consumed
                    FROM CHECKIN AS C
                    WHERE C.USER_ID = :userId
                    AND C.LOCAL_ID = :localId
                    AND C.CONSUMED = 0";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlCheckinList);
        $stmt->bindParam("userId", $userId);
        $stmt->bindParam("localId", $localId);
        $stmt->execute();
        $checkinList = $stmt->fetchAll(PDO::FETCH_OBJ);

        // RESPONSE

        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Dados de cupons recuperados com sucesso.";
        $response->checkins = $checkinList;

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function localInfo($localId)
{
    $sqlLocalInfo = "SELECT L.ID AS localId, L.NAME AS localName, L.STREET AS localStreet, L.NUMBER AS localNumber, L.NEIBORHOOD AS localNeiborhood, L.CITY AS localCity,
                          L.STATE AS localState, L.ZIPCODE AS localZipcode, L.LAT AS localLatitude, L.LON AS localLongitude, L.OPERATING_HOURS AS localOperatingHours,
                          L.COUPONS_OFFERING AS localCouponsOffering, L.COUPONS_PRIZE AS localCouponsPrize, L.CONTACTS AS localContacts
                          FROM LOCAL AS L
                          WHERE L.ID = :localId
                          AND L.DELETED != true";
    $sqlUnits = "SELECT U.ID AS unitId, U.LOCAL_ID AS localId, U.STREET AS localStreet, U.NUMBER AS localNumber, U.NEIBORHOOD AS localNeiborhood, U.CITY AS localCity, U.STATE AS localState,
                            U.ZIPCODE as localZipcode, U.LAT AS localLatitude, U.LON AS localLongitude, U.OPERATING_HOURS AS localOperatingHours, U.CONTACTS AS localContacts
                            FROM UNIT AS U
                            WHERE U.LOCAL_ID = :localId
                            ORDER BY U.ID DESC";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlLocalInfo);
        $stmt->bindParam("localId", $localId);
        $stmt->execute();
        $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

        // SQL AND BIND

        $stmt = $conn->prepare($sqlUnits);
        $stmt->bindParam("localId", $localId);
        $stmt->execute();
        $unitsInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

        // RESPONSE

        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Dados do local recuperados com sucesso.";
        $response->info = $localInfo;
        $response->info->units = $unitsInfo;

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function checkinAdd()
{
    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlCheckinSelect = "SELECT C.ID FROM CHECKIN AS C WHERE C.USER_ID = :userId AND C.TOKEN = :token";
    $sqlCheckinInsert = "INSERT INTO CHECKIN (LOCAL_ID, USER_ID, DATE, CONSUMED, TOKEN, INTERMEDIARY, INTERMEDIARY_ID) VALUES (:localId, :userId, NOW(), 0, :token, :intermediary, :intermediaryId)";
    try {
        $conn = getConn();

        // SELECT
        // SQL AND BIND

        $stmt = $conn->prepare($sqlCheckinSelect);
        $stmt->bindParam("userId", $json->userId);
        $stmt->bindParam("token", $json->token);
        $stmt->execute();
        $checkinInfo = $stmt->fetch(PDO::FETCH_OBJ);
        if ($checkinInfo == null) {

            // SQL AND BIND

            $stmt = $conn->prepare($sqlCheckinInsert);
            $stmt->bindParam("localId", $json->localId);
            $stmt->bindParam("userId", $json->userId);
            $stmt->bindParam("token", $json->token);
            $stmt->bindParam("intermediary", $json->intermediary);
            $stmt->bindParam("intermediaryId", $json->intermediaryId);

            // TOKEN

            $currentDate = date("d-m-Y");
            $localId = $json->localId;
            $sufix = "loyaltee-sufix";
            $tokenFields = $currentDate . "-" . $localId . "-" . $sufix;
            $tokenValidator = md5($currentDate . "-" . $localId . "-" . $sufix);
            if ($json->token == $tokenValidator) {
                $stmt->execute();
                $checkinId = $conn->lastInsertId();

                // RESPONSE

                $response = new stdClass();
                $response->status = 1;
                $response->statusMessage = "Cupom adicionado com sucesso.";

                // $response->checkin = $checkinId;

            }
            else {

                // RESPONSE

                $response = new stdClass();
                $response->status = 2;
                $response->statusMessage = "Parece que o token utilizado não é válido para o dia de hoje. Seu cupom deverá ser gerado manualmente pelo estabelecimento.";
            }

            // OUTPUT

            echo json_encode($response, JSON_NUMERIC_CHECK);
        }
        else {

            // RESPONSE

            $response = new stdClass();
            $response->status = 2;
            $response->statusMessage = "O cupom para este dia já foi adicionado anteriormente.";

            // OUTPUT

            echo json_encode($response, JSON_NUMERIC_CHECK);
        }

        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function getUserProfile($userId)
{
    $sqlUserProfile = "SELECT U.ID AS userId, U.LOGIN AS userLogin, U.FIRSTNAME AS userFirstName, U.LASTNAME AS userLastName, U.BIRTHDAY AS userBirthday, U.EMAIL AS userEmail,
                        U.GENDER AS userGender, IF(U.DELETED = 0, 'false', 'true') AS userDeleted, U.CODE AS userCode, IF(U.UPDATED_DATA = 0, 'false', 'true') AS userUpdatedData,
                        U.PLATFORM AS userPlatform
                        FROM USER AS U
                        WHERE U.ID = :userId";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlUserProfile);
        $stmt->bindParam("userId", $userId);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_OBJ);

        // RESPONSE

        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Dados do usuário recuperados com sucesso.";
        $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
        $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;
        $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

        // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
        $userInfo->askForBirthdayDate = askForBirthdayDate;
        $userInfo->askForGender = askForGender;

        $response->user = $userInfo;

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function updateUserProfile()
{

    // BIRTHDAY FORMAT = YYYY-MM-dd

    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlUserProfileSelect = "SELECT U.ID AS userId, U.LOGIN AS userLogin, U.FIRSTNAME AS userFirstName, U.LASTNAME AS userLastName, U.EMAIL AS userEmail, U.BIRTHDAY AS userBirthday,
                                U.GENDER AS userGender, IF(U.DELETED = 0, 'false', 'true') AS userDeleted, IF(U.UPDATED_DATA = 0, 'false', 'true') AS userUpdatedData, U.PLATFORM AS userPlatform
                                FROM USER U
                                WHERE U.LOGIN = :userLogin
                                AND U.LOCAL_ID = :userLocalId";
    $sqlUserProfileInsert = "INSERT INTO USER (LOGIN, PASSWORD, FIRSTNAME, LASTNAME, BIRTHDAY, EMAIL, GENDER, CODE, DELETED, LOCAL_ID, PLATFORM)
                                VALUES (:userLogin, MD5(:userPassword), :userFirstName, :userLastName, :userBirthday, :userEmail, :userGender, :userCode, 0, :userLocalId, :userPlatform)";
    $sqlUserProfileUpdate = "UPDATE USER U SET ";
    if ($json->userLogin) {
        $sqlUserProfileUpdate.= "LOGIN = :userLogin, ";
    }

    if ($json->userPassword) {
        $sqlUserProfileUpdate.= "PASSWORD = MD5(:userPassword), ";
    }

    if ($json->userFirstName) {
        $sqlUserProfileUpdate.= "FIRSTNAME = :userFirstName, ";
    }

    if ($json->userLastName) {
        $sqlUserProfileUpdate.= "LASTNAME = :userLastName, ";
    }

    if ($json->userBirthday) {
        $sqlUserProfileUpdate.= "BIRTHDAY = :userBirthday, ";
    }

    if ($json->userEmail) {
        $sqlUserProfileUpdate.= "EMAIL = :userEmail, ";
    }

    if ($json->userGender) {
        $sqlUserProfileUpdate.= "GENDER = :userGender, ";
    }

    if ($json->userDeleted) {
        $sqlUserProfileUpdate.= "DELETED = :userDeleted, ";
    }

    if ($json->userUpdatedData) {
        $sqlUserProfileUpdate.= "UPDATED_DATA = :userUpdatedData, ";
    }

    if ($json->userPlatform) {
        $sqlUserProfileUpdate.= "PLATFORM = :userPlatform, ";
    }

    $sqlUserProfileUpdate.= "DELETED = 0 WHERE U.LOGIN = :userLogin AND U.LOCAL_ID = :userLocalId";
    try {
        $conn = getConn();

        // SELECT
        // SQL AND BIND

        $stmt = $conn->prepare($sqlUserProfileSelect);
        $stmt->bindParam("userLogin", $json->userLogin);
        $stmt->bindParam("userLocalId", $json->userLocalId);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userInfo == null) {

            // hash único de 6 caracteres baseado no login do usuário (para evitar de criar cupons manualmente pelo login, que será uma grande string se for usuário do facebook)

            $userCodeBase = $json->userLogin . "-" . $json->userLocalId;
            $userCode = strtoupper(substr(strtolower(preg_replace('/[0-9_\/]+/', '', base64_encode(sha1($userCodeBase)))) , 0, 6));

            // INSERT
            // SQL AND BIND

            $stmt = $conn->prepare($sqlUserProfileInsert);
            $stmt->bindParam("userLogin", $json->userLogin);
            $stmt->bindParam("userPassword", $json->userPassword);
            $stmt->bindParam("userFirstName", $json->userFirstName);
            $stmt->bindParam("userLastName", $json->userLastName);


            if ($json->userBirthday != null){
                $birth = explode("/", $json->userBirthday);
                $birth2 = implode("-", array_reverse($birth));
            }else{
                $birth2 = "";
            }


            if ($json->userGender != null){
                $json->userGender = "";
            }

            $stmt->bindParam("userGender", $json->userGender);
            $stmt->bindParam("userBirthday", $birth2);
            $stmt->bindParam("userEmail", $json->userEmail);
            $stmt->bindParam("userCode", $userCode);
            $stmt->bindParam("userLocalId", $json->userLocalId);
            $stmt->bindParam("userPlatform", $json->userPlatform);
            $stmt->execute();

            // RESPONSE

            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Usuário adicionado com sucesso.";
            $user = new stdClass();
            $user->userId = $conn->lastInsertId();
            $user->userLogin = $json->userLogin;
            $user->userFirstName = $json->userFirstName;
            $user->userLastName = $json->userLastName;
            $user->userEmail = $json->userEmail;
            $user->userLocalId = $json->userLocalId;
            $user->userPlatform = $json->userPlatform;

            // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
            $user->askForBirthdayDate = askForBirthdayDate;
            $user->askForGender = askForGender;

            $response->user = $user;

            // $response->codeBase = $userCodeBase;
            // $response->code = $userCode;

            echo json_encode($response, JSON_NUMERIC_CHECK);
        }
        else {

            // UPDATE
            // SQL AND BIND

            $stmt = $conn->prepare($sqlUserProfileUpdate);
            $emptyParam = "";

            // CONDITIONS TO MATCH

            if ($json->userLogin) {
                $stmt->bindParam("userLogin", $json->userLogin);
            }

            if ($json->userPassword) {
                $stmt->bindParam("userPassword", $json->userPassword);
            }

            if ($json->userFirstName) {
                $stmt->bindParam("userFirstName", $json->userFirstName);
            }

            if ($json->userLastName) {
                $stmt->bindParam("userLastName", $json->userLastName);
            }

            $birth = explode("/", $json->userBirthday);
            $birth2 = implode("-", array_reverse($birth));

            if ($json->userBirthday) {
                $stmt->bindParam("userBirthday", $birth2);
            }

            if ($json->userEmail) {
                $stmt->bindParam("userEmail", $json->userEmail);
            }

            if ($json->userGender) {
                $stmt->bindParam("userGender", $json->userGender);
            }

            if ($json->userDeleted) {
                $stmt->bindParam("userDeleted", $json->userDeleted);
            }

            if ($json->userUpdatedData) {
                $stmt->bindParam("userUpdatedData", $json->userUpdatedData);
            }

            if ($json->userLocalId) {
                $stmt->bindParam("userLocalId", $json->userLocalId);
            }

            $platform = $json->userPlatform;

            // echo json_encode($platform, JSON_NUMERIC_CHECK);
            // exit;

            if ($json->userPlatform) {
                $stmt->bindParam("userPlatform", $platform);
            }

            $stmt->execute();
            $affectedData = $stmt->rowCount(); //número de linhas afetadas
            if ($affectedData > 0) {

                // RESPONSE

                $response = new stdClass();
                $response->status = 1;
                $response->statusMessage = "Usuário alterado com sucesso.";
                $stmt = $conn->prepare($sqlUserProfileSelect);
                $stmt->bindParam("userLogin", $json->userLogin);
                $stmt->bindParam("userLocalId", $json->userLocalId);
                $stmt->execute();
                $userInfo = $stmt->fetch(PDO::FETCH_OBJ);

                $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
                $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;
                $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

                $user = new stdClass();

                // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
                $user->askForBirthdayDate = askForBirthdayDate;
                $user->askForGender = askForGender;

                $response->user = $userInfo;
                echo json_encode($response, JSON_NUMERIC_CHECK);
            }
            else {

                // RESPONSE

                $response = new stdClass();
                $response->status = 1; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados
                $response->statusMessage = "Os dados do usuário não foram alterados pois não foram enviadas modificações.";

                $stmt = $conn->prepare($sqlUserProfileSelect);
                $stmt->bindParam("userLogin", $json->userLogin);
                $stmt->bindParam("userLocalId", $json->userLocalId);
                $stmt->execute();
                $userInfo = $stmt->fetch(PDO::FETCH_OBJ);

                $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
                $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;
                $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

                // $userInfo->teste = $sqlUserProfileUpdate;

                $user = new stdClass();

                // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
                $user->askForBirthdayDate = askForBirthdayDate;
                $user->askForGender = askForGender;

                $response->user = $userInfo;
                echo json_encode($response, JSON_NUMERIC_CHECK);
            }
        }

        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function updateUserProfileWithFacebookUser()
{

    // BIRTHDAY FORMAT = YYYY-MM-dd

    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlUserProfileSelect = "SELECT U.ID AS userId, U.LOGIN AS userLogin, U.FIRSTNAME AS userFirstName, U.LASTNAME AS userLastName, U.EMAIL AS userEmail, U.BIRTHDAY AS userBirthday,
                                U.GENDER AS userGender, IF(U.DELETED = 0, 'false', 'true') AS userDeleted, IF(U.UPDATED_DATA = 0, 'false', 'true') AS userUpdatedData, U.PLATFORM AS userPlatform
                                FROM USER U
                                WHERE U.LOGIN = :userLogin
                                AND U.LOCAL_ID = :userLocalId";
    $sqlUserProfileInsert = "INSERT INTO USER (LOGIN, FIRSTNAME, LASTNAME, BIRTHDAY, EMAIL, GENDER, CODE, DELETED, LOCAL_ID, PLATFORM)
                                VALUES (:userLogin, :userFirstName, :userLastName, :userBirthday, :userEmail, :userGender, :userCode, 0, :userLocalId, :userPlatform)";
    $sqlUserProfileUpdate = "UPDATE USER U SET ";
    if ($json->userLogin) {
        $sqlUserProfileUpdate.= "LOGIN = :userLogin, ";
    }

    if ($json->userPassword) {
        $sqlUserProfileUpdate.= "PASSWORD = MD5(:userPassword), ";
    }

    if ($json->userFirstName) {
        $sqlUserProfileUpdate.= "FIRSTNAME = :userFirstName, ";
    }

    if ($json->userLastName) {
        $sqlUserProfileUpdate.= "LASTNAME = :userLastName, ";
    }

    if ($json->userBirthday) {
        $sqlUserProfileUpdate.= "BIRTHDAY = :userBirthday, ";
    }

    if ($json->userEmail) {
        $sqlUserProfileUpdate.= "EMAIL = :userEmail, ";
    }

    if ($json->userGender) {
        $sqlUserProfileUpdate.= "GENDER = :userGender, ";
    }

    if ($json->userDeleted) {
        $sqlUserProfileUpdate.= "DELETED = :userDeleted, ";
    }

    if ($json->userUpdatedData) {
        $sqlUserProfileUpdate.= "UPDATED_DATA = :userUpdatedData, ";
    }

    if ($json->userPlatform) {
        $sqlUserProfileUpdate.= "PLATFORM = :userPlatform, ";
    }

    $sqlUserProfileUpdate.= "DELETED = 0 WHERE U.LOGIN = :userLogin ";
    $sqlUserProfileUpdate.= "AND U.LOCAL_ID = :userLocalId ";
    $sqlUserProfileUpdate.= "AND U.UPDATED_DATA = 0"; //só atualizar com os dados vindos do facebook se o usuário não os editou no aplicativo ainda

    try {
        $conn = getConn();

        // SELECT
        // SQL AND BIND

        $stmt = $conn->prepare($sqlUserProfileSelect);
        $stmt->bindParam("userLogin", $json->userLogin);
        $stmt->bindParam("userLocalId", $json->userLocalId);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_OBJ);

        if ($userInfo == null) {

            // hash único de 6 caracteres baseado no login do usuário (para evitar de criar cupons manualmente pelo login, que será uma grande string se for usuário do facebook)

            $userCodeBase = $json->userLogin . "-" . $json->userLocalId;
            $userCode = strtoupper(substr(strtolower(preg_replace('/[0-9_\/]+/', '', base64_encode(sha1($userCodeBase)))) , 0, 6));

            // INSERT
            // SQL AND BIND

            $stmt = $conn->prepare($sqlUserProfileInsert);
            $emptyParam = "";

            if ($json->userLogin) {
                $stmt->bindParam("userLogin", $json->userLogin);
            }

            if ($json->userFirstName) {
                $stmt->bindParam("userFirstName", $json->userFirstName);
            }
            else {
                $stmt->bindParam("userFirstName", $emptyParam);
            }

            if ($json->userLastName) {
                $stmt->bindParam("userLastName", $json->userLastName);
            }
            else {
                $stmt->bindParam("userLastName", $emptyParam);
            }

            if ($json->userBirthday) {
                $stmt->bindParam("userBirthday", implode("-", array_reverse(explode("/", $json->userBirthday))));
            }
            else {
                $stmt->bindParam("userBirthday", $emptyParam);
            }

            if ($json->userEmail) {
                $stmt->bindParam("userEmail", $json->userEmail);
            }
            else {
                $stmt->bindParam("userEmail", $emptyParam);
            }

            if ($json->userGender) {
                $stmt->bindParam("userGender", $json->userGender);
            }
            else {
                $stmt->bindParam("userGender", $emptyParam);
            }

            if ($json->userLocalId) {
                $stmt->bindParam("userLocalId", $json->userLocalId);
            }

            if ($json->userPlatform) {
                $stmt->bindParam("userPlatform", $json->userPlatform);
            }

            $stmt->bindParam("userCode", $userCode);
            $stmt->execute();

            // RESPONSE

            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Usuário adicionado com sucesso.";
            $user = new stdClass();
            $user->userId = $conn->lastInsertId();
            $user->userLogin = $json->userLogin;
            $user->userFirstName = $json->userFirstName;
            $user->userLastName = $json->userLastName;
            $user->userEmail = $json->userEmail;
            $user->userLocalId = $json->userLocalId;
            $user->userPlatform = $json->userPlatform;

            // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
            $user->askForBirthdayDate = askForBirthdayDate;
            $user->askForGender = askForGender;

            $response->user = $user;

            // $response->codeBase = $userCodeBase;
            // $response->code = $userCode;

            echo json_encode($response, JSON_NUMERIC_CHECK);
        }
        else {

            // UPDATE
            // SQL AND BIND

            $stmt = $conn->prepare($sqlUserProfileUpdate);

            // CONDITIONS TO MATCH

            if ($json->userLogin) {
                $stmt->bindParam("userLogin", $json->userLogin);
            }

            if ($json->userPassword) {
                $stmt->bindParam("userPassword", $json->userPassword);
            }

            if ($json->userFirstName) {
                $stmt->bindParam("userFirstName", $json->userFirstName);
            }

            if ($json->userLastName) {
                $stmt->bindParam("userLastName", $json->userLastName);
            }

            if ($json->userBirthday) {
                $stmt->bindParam("userBirthday", implode("-", array_reverse(explode("/", $json->userBirthday))));
            }

            if ($json->userEmail) {
                $stmt->bindParam("userEmail", $json->userEmail);
            }

            if ($json->userGender) {
                $stmt->bindParam("userGender", $json->userGender);
            }

            if ($json->userDeleted) {
                $stmt->bindParam("userDeleted", $json->userDeleted);
            }

            if ($json->userUpdatedData) {
                $stmt->bindParam("userUpdatedData", $json->userUpdatedData);
            }

            if ($json->userLocalId) {
                $stmt->bindParam("userLocalId", $json->userLocalId);
            }

            if ($json->userPlatform) {
                $stmt->bindParam("userPlatform", $json->userPlatform);
            }

            $stmt->execute();
            $affectedData = $stmt->rowCount(); //número de linhas afetadas
            if ($affectedData > 0) {

                // RESPONSE

                $response = new stdClass();
                $response->status = 1;
                $response->statusMessage = "Usuário alterado com sucesso.";
                $stmt = $conn->prepare($sqlUserProfileSelect);
                $stmt->bindParam("userLogin", $json->userLogin);
                $stmt->bindParam("userLocalId", $json->userLocalId);
                $stmt->execute();
                $userInfo = $stmt->fetch(PDO::FETCH_OBJ);
                $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
                $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;
                $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

                $user = new stdClass();

                // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
                $user->askForBirthdayDate = askForBirthdayDate;
                $user->askForGender = askForGender;

                $response->user = $userInfo;
                echo json_encode($response, JSON_NUMERIC_CHECK);
            }
            else {

                // RESPONSE

                $response = new stdClass();
                $response->status = 1;
                $response->statusMessage = "Os dados do usuário não foram alterados pois não foram enviadas modificações, o usuário já os atualizou pelo aplicativo.";
                $stmt = $conn->prepare($sqlUserProfileSelect);
                $stmt->bindParam("userLogin", $json->userLogin);
                $stmt->bindParam("userLocalId", $json->userLocalId);
                $stmt->execute();
                $userInfo = $stmt->fetch(PDO::FETCH_OBJ);
                $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
                $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;
                $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

                $user = new stdClass();

                // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
                $user->askForBirthdayDate = askForBirthdayDate;
                $user->askForGender = askForGender;

                $response->user = $userInfo;
                echo json_encode($response, JSON_NUMERIC_CHECK);
            }
        }

        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function userLogin()
{
    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlCheckinInsert = "SELECT U.ID AS userId, U.LOGIN AS userLogin, U.FIRSTNAME AS userFirstName, U.LASTNAME AS userLastName, U.BIRTHDAY AS userBirthday, U.EMAIL AS userEmail,
                        U.GENDER AS userGender, U.CODE AS userCode, IF(U.DELETED = 0, 'false', 'true') AS userDeleted, IF(U.UPDATED_DATA = 0, 'false', 'true') AS userUpdatedData, U.PLATFORM AS userPlatform
                        FROM USER AS U
                        WHERE U.LOGIN = :userLogin
                        AND U.PASSWORD = MD5(:userPassword)
                        AND U.LOCAL_ID = :userLocalId";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlCheckinInsert);
        $stmt->bindParam("userLogin", $json->userLogin);
        $stmt->bindParam("userPassword", $json->userPassword);
        $stmt->bindParam("userLocalId", $json->userLocalId);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userInfo != null) {

            // RESPONSE

            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Dados do usuário recuperados com sucesso.";
            $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
            $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;
            $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

            // OBRIGATORIEDADE DE PREENCHER DADOS - RETIRAR QUANDO FOR PASSAR POR AVALIAÇÕES
            $userInfo->askForBirthdayDate = askForBirthdayDate;
            $userInfo->askForGender = askForGender;

            $response->user = $userInfo;
        }
        else {

            // RESPONSE

            $response = new stdClass();
            $response->status = 2;
            $response->statusMessage = "Falha! Usuário ou senha incorretos.";
        }

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function checkLoginAvailability()
{
    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlCheckinInsert = "SELECT U.ID AS userId
                            FROM USER U
                            WHERE U.LOGIN = :userLogin
                            AND U.LOCAL_ID = :userLocalId";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlCheckinInsert);
        $stmt->bindParam("userLogin", $json->userLogin);
        $stmt->bindParam("userLocalId", $json->userLocalId);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userInfo == null) {

            // SUCCESS

            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Login disponível.";
        }
        else {

            // FAIL

            $response = new stdClass();
            $response->status = 2;
            $response->statusMessage = "Parece que este login já existe.";
        }

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function userForgotPassword()
{
    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlUserSelect = "SELECT U.ID AS userId, U.CODE as userCode, U.PASSWORD as userPassword, L.NAME as localName
                        FROM USER AS U
                        INNER JOIN LOCAL L ON L.ID = U.LOCAL_ID
                        WHERE U.EMAIL = :userEmail
                        AND U.LOCAL_ID = :userLocalId";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlUserSelect);
        $stmt->bindParam("userEmail", $json->userEmail);
        $stmt->bindParam("userLocalId", $json->userLocalId);
        $stmt->execute();
        $userInfo = $stmt->fetch(PDO::FETCH_OBJ);
        if ($userInfo != null) {
            $userRequest = MD5(MD5(MD5(MD5(MD5(MD5(MD5($userInfo->userCode)))))));
            if ($userInfo->userPassword != null) {
                $to = $json->userEmail;
                $subject = "[ $userInfo->localName ] - Recuperação de senha";
                $message = "Você requisitou a recuperação da sua senha no aplicativo $userInfo->localName, um aplicativo Loyaltee Mobile. Por favor, acesse o link para definir sua nova senha.<p><a href='https://www.loyaltee.com.br/manager/forgot-password.php?r=$userRequest'>https://www.loyaltee.com.br/manager/forgot-password.php?r=$userRequest</a></p>";
                $headers = "From: naoresponda@loyaltee.com.br\r\n";
                $headers.= "Reply-To: naoresponda@loyaltee.com.br\r\n";
                $headers.= "MIME-Version: 1.0\r\n";
                $headers.= "Content-Type: text/html; charset=UTF-8\r\n";
                mail($to, $subject, $message, $headers);

                // RESPONSE

                $response = new stdClass();
                $response->status = 1;
                $response->statusMessage = "E-mail de redefinição de senha enviado com sucesso.";
            }
            else {
                $response = new stdClass();
                $response->status = 2;
                $response->statusMessage = "Este e-mail está atribuído à uma conta com login por Facebook.";
            }
        }
        else {

            // RESPONSE

            $response = new stdClass();
            $response->status = 2;
            $response->statusMessage = "Parece que não existe nenhum usuário cadastro com este e-mail.";
        }

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function getNotificationsForUser($userId, $localId)
{
    $sqlNotifications = "SELECT N.ID AS notificationId, N.USER_ID AS userId, CA.LOCAL_ID AS localId, CA.TITLE AS notificationTitle, CA.MESSAGE AS notificationMessage,
                            N.DELIVERY_DATE AS notificationDeliveryDate, IF(N.READ = 0, 'false', 'true') AS notificationRead
                            FROM NOTIFICATION AS N
                            INNER JOIN CAMPAIGN AS CA ON CA.ID = N.CAMPAIGN_ID
                            WHERE N.USER_ID = :userId
                            AND CA.LOCAL_ID = :localId
                            AND CA.DELETED = 0
                            AND N.DELIVERY_DATE < NOW()
                            ORDER BY N.DELIVERY_DATE DESC, N.ID DESC";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlNotifications);
        $stmt->bindParam("userId", $userId);
        $stmt->bindParam("localId", $localId);
        $stmt->execute();
        $notificationsInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

        // RESPONSE

        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Notificações recuperadas com sucesso.";
        $response->notifications = $notificationsInfo;

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function updateUserToken()
{

    // BIRTHDAY FORMAT = YYYY-MM-dd

    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());
    $sqlUserProfileUpdate = "UPDATE USER U SET TOKEN = :userToken WHERE ID = :userId";
    try {
        $conn = getConn();
        $stmt = $conn->prepare($sqlUserProfileUpdate);
        $stmt->bindParam("userId", $json->userId);
        $stmt->bindParam("userToken", $json->userToken);
        $stmt->execute();
        $affectedData = $stmt->rowCount(); //número de linhas afetadas
        if ($affectedData > 0) {

            // RESPONSE

            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Token do usuário alterado com sucesso.";
            echo json_encode($response, JSON_NUMERIC_CHECK);
        }
        else {

            // RESPONSE

            $response = new stdClass();
            $response->status = 1; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados
            $response->statusMessage = "Token do usuário não alterado, pois não foram enviadas alterações";
            echo json_encode($response, JSON_NUMERIC_CHECK);
        }

        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}

function visualSettings($localId)
{
    $sqlAssetsInfo = "SELECT AT.ID AS assetsId, AT.IMAGE_LOGO AS assetsImageLogo, AT.IMAGE_INFO AS assetsImageInfo, AT.IMAGE_FACEBOOK_SHARE AS assetsImageFacebookShare,
                          AT.HASHTAG_FACEBOOK_SHARE AS assetsHashtagFacebookShare, AT.COUPON_NO AS assetsCouponNo, AT.COUPON_YES AS assetsCouponYes, AT.COLOR_PRIMARY AS assetsColorPrimary,
                          AT.COLOR_SECONDARY AS assetsColorSecondary, AT.MAP_ZOOM AS assetsMapZoom, AT.LOCAL_ID AS localId
                          FROM ASSETS AS AT
                          WHERE AT.LOCAL_ID = :localId";
    try {
        $conn = getConn();

        // SQL AND BIND

        $stmt = $conn->prepare($sqlAssetsInfo);
        $stmt->bindParam("localId", $localId);
        $stmt->execute();
        $assets = $stmt->fetch(PDO::FETCH_OBJ);

        // RESPONSE

        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Ajustes visuais recuperados com sucesso.";
        $response->info = $assets;

        // OUTPUT

        echo json_encode($response, JSON_NUMERIC_CHECK);
        $conn = null;
    }

    catch(PDOException $e) {
        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());
        die();
    }
}
