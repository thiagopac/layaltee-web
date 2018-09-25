<?php
require 'Slim/Slim.php';

date_default_timezone_set("America/Sao_Paulo");


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

$app->notFound(function () use ($app) {
    notFound();
});

$app->error(function (\Exception $e) use ($app) {
		// notFound();
});

$app->config('debug', false);

//GET ROUTES
$app->get('/', 'notFound');
$app->get('/local/info/:localId','localInfo');
$app->get('/campaign/list/:localId','campaignsList');
$app->get('/campaign/info/:campaignId','campaignInfo');
$app->get('/user/profile/:userCode/:userLocalId','getUserProfile');
$app->get('/checkin/list/:userId/:localId','checkinList');
$app->get('/prize/list/:localId','prizeList');
$app->get('/dashboard/rowone/:localId','dashBoardRowOne');
$app->get('/dashboard/rowtwo/:localId','dashBoardRowTwo');
$app->get('/dashboard/rowthree/:localId','dashBoardRowThree');
$app->get('/dashboard/rowfour/:localId','dashBoardRowFour');
$app->get('/local/visual-settings/:localId','visualSettings');
$app->get('/unit/list/:localId','unitsList');
$app->get('/unit/info/:unitId','unitList');
//POST ROUTES
$app->post('/admin/login','adminLogin');
$app->post('/admin/profile/update','updateAdminProfile');
$app->post('/local/info/update','updateLocalInfo');
$app->post('/campaign/info/update','updateCampaignInfo');
$app->post('/checkin/manual/add','checkinManualAdd');
$app->post('/checkin/manual/addForPromotion','checkinManualAddForPromotion');
$app->post('/checkin/consume','checkinConsume');
$app->post('/campaign/delete','campaignDelete');
$app->post('/campaign/notification/calculate','campaignNotificationCalculateReach');
$app->post('/campaign/notification/send','campaignNotificationSend');
$app->post('/prize/add','prizeAdd');
$app->post('/prize/delete','prizeDelete');
$app->post('/push/sendpushnotification','sendPushNotification');
$app->post('/user/forgotpassword','userForgotPassword');
$app->post('/site/contact','siteContactForm');
$app->post('/unit/info/update','updateUnitInfo');

//DELETE ROUTES

$app->run();

function notFound(){
	header('HTTP/1.1 404 Not Found');
	die();
}

function getConn(){
  if($_SERVER['SERVER_NAME'] == "localhost"){
    return new PDO('mysql:host=localhost;dbname=loyaltee','root','mysql',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  }else if ($_SERVER['SERVER_NAME'] == "www.loyaltee.com.br" || $_SERVER['SERVER_NAME'] == "loyaltee.com.br"){
    return new PDO('mysql:host=localhost;dbname=loyaltee_appProd','loyaltee_root','Redentor1Loyaltee2',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
  }
}

function adminLogin(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

	$sqlAdminSelect = "SELECT LA.ID AS adminId, LA.LOGIN AS adminLogin, LA.FIRSTNAME AS adminFirstName, LA.LASTNAME AS adminLastName, LA.EMAIL AS adminEmail,
                        IF(LA.DELETED = 0, 'false', 'true') AS adminDeleted, LA.LOCAL_ID AS adminLocalId
                    FROM LOCAL_ADMIN AS LA
                    WHERE LA.LOGIN = :adminLogin
                    AND LA.PASSWORD = MD5(:adminPassword)";

	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlAdminSelect);
      $stmt->bindParam("adminLogin",$json->adminLogin);
      $stmt->bindParam("adminPassword",$json->adminPassword);
      $stmt->execute();
      $adminInfo = $stmt->fetch(PDO::FETCH_OBJ);

      if ($adminInfo != null) {

        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Dados do administrador recuperados com sucesso.";

        $adminInfo->adminDeleted = $adminInfo->adminDeleted == "true" ? true : false;

        $response->admin = $adminInfo;

      }else{
        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Falha! Usuário ou senha incorretos.";
      }

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function updateAdminProfile(){

  //BIRTHDAY FORMAT = YYYY-MM-dd

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlAdminProfileSelect = "SELECT LA.ID AS adminId, LA.LOGIN AS adminLogin, LA.FIRSTNAME AS adminFirstName, LA.LASTNAME AS adminLastName, LA.EMAIL AS adminEmail,
                            IF(LA.DELETED = 0, 'false', 'true') AS adminDeleted, LA.LOCAL_ID AS adminLocalId
                            FROM LOCAL_ADMIN AS LA
                            WHERE LA.LOGIN = :adminLogin";

  $sqlAdminProfileInsert = "INSERT INTO LOCAL_ADMIN (LOGIN, PASSWORD, FIRSTNAME, LASTNAME, EMAIL, DELETED)
                            VALUES (:adminLogin, MD5(:adminPassword), :adminFirstName, :adminLastName, :adminEmail, 0)";

  $sqlAdminProfileUpdate = "UPDATE LOCAL_ADMIN LA SET ";

  if ($json->adminLogin) { $sqlAdminProfileUpdate .= "LOGIN = :adminLogin, "; }
  if ($json->adminPassword) { $sqlAdminProfileUpdate .= "PASSWORD = MD5(:adminPassword), "; }
  if ($json->adminFirstName) { $sqlAdminProfileUpdate .= "FIRSTNAME = :adminFirstName, "; }
  if ($json->adminLastName) { $sqlAdminProfileUpdate .= "LASTNAME = :adminLastName, ";}
  if ($json->adminEmail) { $sqlAdminProfileUpdate .= "EMAIL = :adminEmail, "; }
  $sqlAdminProfileUpdate .= "DELETED = 0 WHERE LA.LOGIN = :adminLogin";

	try{
			$conn = getConn();

      //SELECT
      //SQL AND BIND
      $stmt = $conn->prepare($sqlAdminProfileSelect);
      $stmt->bindParam("adminLogin",$json->adminLogin);
      $stmt->execute();
      $adminInfo = $stmt->fetch(PDO::FETCH_OBJ);

      if ($adminInfo == null) {

            //hash único de 6 caracteres baseado no login do usuário (para evitar de criar cupons manualmente pelo login, que será uma grande string se for usuário do facebook)
            $adminCodeBase = $json->adminLogin;

            //INSERT
            //SQL AND BIND
            $stmt = $conn->prepare($sqlAdminProfileInsert);
            $stmt->bindParam("adminLogin",$json->adminLogin);
            $stmt->bindParam("adminPassword",$json->adminPassword);
            $stmt->bindParam("adminFirstName",$json->adminFirstName);
            $stmt->bindParam("adminLastName",$json->adminLastName);
            $stmt->bindParam("adminEmail",$json->adminEmail);

            $stmt->execute();

            //RESPONSE
            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Administrador adicionado com sucesso.";

            $admin = new stdClass();
            $admin->adminId = $conn->lastInsertId();
            $admin->adminLogin = $json->adminLogin;
            $admin->adminFirstName = $json->adminFirstName;
            $admin->adminLastName = $json->adminLastName;
            $admin->adminEmail = $json->adminEmail;
            $response->admin = $admin;

            echo json_encode($response, JSON_NUMERIC_CHECK);

      }else{

            //UPDATE
            //SQL AND BIND
            $stmt = $conn->prepare($sqlAdminProfileUpdate);

            //CONDITIONS TO MATCH
            if ($json->adminLogin) { $stmt->bindParam("adminLogin",$json->adminLogin); }
            if ($json->adminPassword) { $stmt->bindParam("adminPassword",$json->adminPassword); }
            if ($json->adminFirstName) { $stmt->bindParam("adminFirstName",$json->adminFirstName); }
            if ($json->adminLastName) { $stmt->bindParam("adminLastName",$json->adminLastName); }
            if ($json->adminEmail) { $stmt->bindParam("adminEmail",$json->adminEmail); }
            if ($json->adminDeleted) { $stmt->bindParam("adminDeleted",$json->adminDeleted); }

            $stmt->execute();
            $affectedData = $stmt->rowCount(); //número de linhas afetadas

            if ($affectedData > 0) {
              //RESPONSE
              $response = new stdClass();
              $response->status = 1;
              $response->statusMessage = "Administrador alterado com sucesso.";

              $stmt = $conn->prepare($sqlAdminProfileSelect);
              $stmt->bindParam("adminLogin",$json->adminLogin);
              $stmt->execute();
              $adminInfo = $stmt->fetch(PDO::FETCH_OBJ);

              $adminInfo->adminDeleted = $adminInfo->adminDeleted == "true" ? true : false;

              $admin = new stdClass();
              $response->admin = $adminInfo;

              echo json_encode($response, JSON_NUMERIC_CHECK);
            }else{
              //RESPONSE
              $response = new stdClass();
              $response->status = 1; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados
              $response->statusMessage = "Os dados do administrador não foram alterados pois não foram enviadas modificações.";

              $stmt = $conn->prepare($sqlAdminProfileSelect);
              $stmt->bindParam("adminLogin",$json->adminLogin);
              $stmt->execute();
              $adminInfo = $stmt->fetch(PDO::FETCH_OBJ);

              $adminInfo->adminDeleted = $adminInfo->adminDeleted == "true" ? true : false;

              // $adminInfo->teste = $sqlAdminProfileUpdate;

              $admin = new stdClass();
              $response->admin = $adminInfo;

              echo json_encode($response, JSON_NUMERIC_CHECK);
            }
      }

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function localInfo($localId){

	$sqlLocalInfo = "SELECT L.ID AS localId, L.NAME AS localName, L.STREET AS localStreet, L.NUMBER AS localNumber, L.NEIBORHOOD AS localNeiborhood, L.CITY AS localCity,
                      L.STATE AS localState, L.ZIPCODE AS localZipcode, L.LAT AS localLatitude, L.LON AS localLongitude, L.OPERATING_HOURS AS localOperatingHours,
                      L.COUPONS_OFFERING AS localCouponsOffering, L.COUPONS_PRIZE AS localCouponsPrize, L.CONTACTS AS localContacts
                      FROM LOCAL AS L
                      WHERE L.ID = :localId";

	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlLocalInfo);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Dados do local recuperados com sucesso.";

      $response->info = $localInfo;

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function updateLocalInfo(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlLocalInfoSelect = "SELECT L.ID AS localId, L.NAME AS localName, L.STREET AS localStreet, L.NUMBER AS localNumber, L.NEIBORHOOD AS localNeiborhood, L.CITY AS localCity,
                          L.STATE AS localState, L.ZIPCODE AS localZipcode, L.LAT AS localLatitude, L.LON AS localLongitude, L.OPERATING_HOURS AS localOperatingHours,
                          L.COUPONS_OFFERING AS localCouponsOffering, L.COUPONS_PRIZE AS localCouponsPrize, L.CONTACTS AS localContacts
                          FROM LOCAL AS L
                          WHERE L.ID = :localId";

	$sqlLocalInfoInsert = "INSERT INTO LOCAL (NAME, STREET, NUMBER, NEIBORHOOD, CITY, STATE, ZIPCODE, LAT, LON, OPERATING_HOURS, CONTACTS, COUPONS_OFFERING, COUPONS_PRIZE, DELETED)
                            VALUES (:localName, :localStreet, :localNumber, :localNeiborhood, :localCity, :localState, :localZipcode, :localLatitude, :localLongitude, :localOperatingHours, :localContacts, :localCouponsOffering, :localCouponsPrize, 0)";

  $sqlLocalInfoUpdate = "UPDATE LOCAL L SET ";

  if ($json->localName) { $sqlLocalInfoUpdate .= "NAME = :localName, "; }
  if ($json->localStreet) { $sqlLocalInfoUpdate .= "STREET = :localStreet, "; }
  if ($json->localNumber) { $sqlLocalInfoUpdate .= "NUMBER = :localNumber, "; }
  if ($json->localNeiborhood) { $sqlLocalInfoUpdate .= "NEIBORHOOD = :localNeiborhood, ";}
  if ($json->localCity) { $sqlLocalInfoUpdate .= "CITY = :localCity, "; }
  if ($json->localState) { $sqlLocalInfoUpdate .= "STATE = :localState, "; }
  if ($json->localZipcode) { $sqlLocalInfoUpdate .= "ZIPCODE = :localZipcode, "; }
  if ($json->localLatitude) { $sqlLocalInfoUpdate .= "LAT = :localLatitude, "; }
  if ($json->localLongitude) { $sqlLocalInfoUpdate .= "LON = :localLongitude, "; }
  if ($json->localOperatingHours) { $sqlLocalInfoUpdate .= "OPERATING_HOURS = :localOperatingHours, "; }
  if ($json->localContacts) { $sqlLocalInfoUpdate .= "CONTACTS = :localContacts, "; }
  if ($json->localCouponsOffering) { $sqlLocalInfoUpdate .= "COUPONS_OFFERING = :localCouponsOffering, "; }
  if ($json->localDeleted != null) { $sqlLocalInfoUpdate .= "DELETED = :localDeleted, "; } //deleted não está no último lugar pois o último tem que ser um parâmetro obrigatório pois a string update não pode ter vírgula antes do where
  if ($json->localCouponsPrize) { $sqlLocalInfoUpdate .= "COUPONS_PRIZE = :localCouponsPrize "; }

  $sqlLocalInfoUpdate .= "WHERE L.ID = :localId";

	try{
			$conn = getConn();

      //SELECT
      //SQL AND BIND
      $stmt = $conn->prepare($sqlLocalInfoSelect);
      $stmt->bindParam("localId",$json->localId);
      $stmt->execute();
      $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

      if ($localInfo == null) {

            //INSERT
            //SQL AND BIND
            $stmt = $conn->prepare($sqlLocalInfoInsert);
            $stmt->bindParam("localName",$json->localName);
            $stmt->bindParam("localStreet",$json->localStreet);
            $stmt->bindParam("localNumber",$json->localNumber);
            $stmt->bindParam("localNeiborhood",$json->localNeiborhood);
            $stmt->bindParam("localCity",$json->localCity);
            $stmt->bindParam("localState",$json->localState);
            $stmt->bindParam("localZipcode",$json->localZipcode);
            $stmt->bindParam("localLatitude",$json->localLatitude);
            $stmt->bindParam("localLongitude",$json->localLongitude);
            $stmt->bindParam("localOperatingHours",$json->localOperatingHours);
            $stmt->bindParam("localContacts",$json->localContacts);
            $stmt->bindParam("localCouponsOffering",$json->localCouponsOffering);
            $stmt->bindParam("localCouponsPrize",$json->localCouponsPrize);

            $stmt->execute();

            //RESPONSE
            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Local adicionado com sucesso.";

            $local = new stdClass();
            $local->localId = $conn->lastInsertId();
            $local->localName = $json->localName;
            $local->localStreet = $json->localStreet;
            $local->localNumber = $json->localNumber;
            $local->localNeiborhood = $json->localNeiborhood;
            $local->localCity = $json->localCity;
            $local->localState = $json->localState;
            $local->localZipcode = $json->localZipcode;
            $local->localLatitude = $json->localLatitude;
            $local->localLongitude = $json->localLongitude;
            $local->localOperatingHours = $json->localOperatingHours;
            $local->localContacts = $json->localContacts;
            $local->localCouponsOffering = $json->localCouponsOffering;
            $local->localCouponsPrize = $json->localCouponsPrize;

            $response->info = $local;

            echo json_encode($response, JSON_NUMERIC_CHECK);

      }else{

            //UPDATE
            //SQL AND BIND
            $stmt = $conn->prepare($sqlLocalInfoUpdate);


            //CONDITIONS TO MATCH
            if ($json->localId) { $stmt->bindParam("localId",$json->localId); }
            if ($json->localName) { $stmt->bindParam("localName",$json->localName); }
            if ($json->localStreet) { $stmt->bindParam("localStreet",$json->localStreet); }
            if ($json->localNumber) { $stmt->bindParam("localNumber",$json->localNumber); }
            if ($json->localNeiborhood) { $stmt->bindParam("localNeiborhood",$json->localNeiborhood); }
            if ($json->localCity) { $stmt->bindParam("localCity",$json->localCity); }
            if ($json->localState) { $stmt->bindParam("localState",$json->localState); }
            if ($json->localZipcode) { $stmt->bindParam("localZipcode",$json->localZipcode); }
            if ($json->localLatitude) { $stmt->bindParam("localLatitude",$json->localLatitude); }
            if ($json->localLongitude) { $stmt->bindParam("localLongitude",$json->localLongitude); }
            if ($json->localOperatingHours) { $stmt->bindParam("localOperatingHours",$json->localOperatingHours); }
            if ($json->localContacts) { $stmt->bindParam("localContacts",$json->localContacts); }
            if ($json->localCouponsOffering) { $stmt->bindParam("localCouponsOffering",$json->localCouponsOffering); }
            if ($json->localCouponsPrize) { $stmt->bindParam("localCouponsPrize",$json->localCouponsPrize); }
            if ($json->localDeleted != null) { $stmt->bindParam("localDeleted",$json->localDeleted); }


            $stmt->execute();
            $affectedData = $stmt->rowCount(); //número de linhas afetadas

            if ($affectedData > 0) {
              //RESPONSE
              $response = new stdClass();
              $response->status = 1;
              $response->statusMessage = "Local alterado com sucesso.";

              $stmt = $conn->prepare($sqlLocalInfoSelect);
              $stmt->bindParam("localId",$json->localId);
              $stmt->execute();
              $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

              $localInfo->localDeleted = $localInfo->localDeleted == "true" ? true : false;

              $response->info = $localInfo;

              echo json_encode($response, JSON_NUMERIC_CHECK);
            }else{
              //RESPONSE
              $response = new stdClass();
              $response->status = 1; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados
              $response->statusMessage = "Os dados do local não foram alterados pois não foram enviadas modificações.";

              $stmt = $conn->prepare($sqlLocalInfoSelect);
              $stmt->bindParam("localId",$json->localId);
              $stmt->execute();
              $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

              $localInfo->localDeleted = $localInfo->localDeleted == "true" ? true : false;

              $response->info = $localInfo;

              echo json_encode($response, JSON_NUMERIC_CHECK);
            }
      }

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function campaignsList($localId){

  $sqlCampaigns = "SELECT CA.ID AS campaignId, CA.LOCAL_ID AS campaignLocalId, CA.TITLE AS campaignTitle, CA.MESSAGE AS campaignMessage,
                        IF(CA.DELETED = 0, 'false', 'true') AS campaignDeleted
                        FROM CAMPAIGN AS CA
                        WHERE CA.LOCAL_ID = :localId
                        AND CA.DELETED = 0
                        ORDER BY CA.ID DESC";

  try{
      $conn = getConn();

      //SQL AND BIND
      $stmt = $conn->prepare($sqlCampaigns);
      $stmt->bindParam("localId",$localId);
      $stmt->execute();
      $campaignsInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Campanhas recuperadas com sucesso.";

      $response->campaigns = $campaignsInfo;

      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);

      $conn = null;

  } catch(PDOException $e){

      header('HTTP/1.1 400 Bad request');
      echo json_encode($e->getMessage());

      die();
  }

}

function campaignInfo($campaignId){

  $sqlCampaign = "SELECT CA.ID AS campaignId, CA.LOCAL_ID AS campaignLocalId, CA.TITLE AS campaignTitle, CA.MESSAGE AS campaignMessage,
                        IF(CA.DELETED = 0, 'false', 'true') AS campaignDeleted
                        FROM CAMPAIGN AS CA
                        WHERE CA.ID = :campaignId";

  try{
      $conn = getConn();

      //SQL AND BIND
      $stmt = $conn->prepare($sqlCampaign);
      $stmt->bindParam("campaignId",$campaignId);
      $stmt->execute();
      $campaignInfo = $stmt->fetch(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Campanha recuperada com sucesso.";

      $response->info = $campaignInfo;

      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);

      $conn = null;

  } catch(PDOException $e){

      header('HTTP/1.1 400 Bad request');
      echo json_encode($e->getMessage());

      die();
  }

}

function updateCampaignInfo(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlCampaignInfoSelect = "SELECT CA.ID AS campaignId, CA.LOCAL_ID AS campaignLocalId, CA.TITLE AS campaignTitle, CA.MESSAGE AS campaignMessage,
                          IF(CA.DELETED = 0, 'false', 'true') AS campaignDeleted
                          FROM CAMPAIGN AS CA
                          WHERE CA.ID = :campaignId";

	$sqlCampaignInfoInsert = "INSERT INTO CAMPAIGN (LOCAL_ID, TITLE, MESSAGE, DELETED)
                            VALUES (:campaignLocalId, :campaignTitle, :campaignMessage, 0)";

  $sqlCampaignInfoUpdate = "UPDATE CAMPAIGN CA SET ";

  if ($json->campaignLocalId) { $sqlCampaignInfoUpdate .= "LOCAL_ID = :campaignLocalId, "; }
  if ($json->campaignTitle) { $sqlCampaignInfoUpdate .= "TITLE = :campaignTitle, "; }
  if ($json->campaignDeleted != null) { $sqlCampaignInfoUpdate .= "DELETED = :campaignDeleted, "; } //no json, deleted precisa ser enviado como string e a diferenciação por null é para aceitar string com 0 dentro "0"
  if ($json->campaignMessage) { $sqlCampaignInfoUpdate .= "MESSAGE = :campaignMessage "; }

  $sqlCampaignInfoUpdate .= "WHERE CA.ID = :campaignId";

  // echo json_encode($sqlCampaignInfoUpdate, JSON_NUMERIC_CHECK);
  // exit;

	try{
			$conn = getConn();

      //SELECT
      //SQL AND BIND
      $stmt = $conn->prepare($sqlCampaignInfoSelect);
      $stmt->bindParam("campaignId",$json->campaignId);
      $stmt->execute();
      $campaignInfo = $stmt->fetch(PDO::FETCH_OBJ);

      if ($campaignInfo == null) {

            //INSERT
            //SQL AND BIND
            $stmt = $conn->prepare($sqlCampaignInfoInsert);
            $stmt->bindParam("campaignLocalId",$json->campaignLocalId);
            $stmt->bindParam("campaignTitle",$json->campaignTitle);
            $stmt->bindParam("campaignMessage",$json->campaignMessage);

            $stmt->execute();

            //RESPONSE
            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Campanha adicionada com sucesso.";

            $campaign = new stdClass();
            $campaign->campaignId = $conn->lastInsertId();
            $campaign->campaignLocalId = $json->campaignLocalId;
            $campaign->campaignTitle = $json->campaignTitle;
            $campaign->campaignMessage = $json->campaignMessage;

            $response->info = $campaign;

            echo json_encode($response, JSON_NUMERIC_CHECK);

      }else{

            //UPDATE
            //SQL AND BIND
            $stmt = $conn->prepare($sqlCampaignInfoUpdate);

            //CONDITIONS TO MATCH
            if ($json->campaignId) { $stmt->bindParam("campaignId",$json->campaignId); }
            if ($json->campaignLocalId) { $stmt->bindParam("campaignLocalId",$json->campaignLocalId); }
            if ($json->campaignTitle) { $stmt->bindParam("campaignTitle",$json->campaignTitle); }
            if ($json->campaignMessage) { $stmt->bindParam("campaignMessage",$json->campaignMessage); }
            if ($json->campaignDeleted != null) { $stmt->bindParam("campaignDeleted",$json->campaignDeleted); }

            $stmt->execute();
            $affectedData = $stmt->rowCount(); //número de linhas afetadas

            if ($affectedData > 0) {
              //RESPONSE
              $response = new stdClass();
              $response->status = 1;
              $response->statusMessage = "Campanha alterada com sucesso.";

              $stmt = $conn->prepare($sqlCampaignInfoSelect);
              $stmt->bindParam("campaignId",$json->campaignId);
              $stmt->execute();
              $campaignInfo = $stmt->fetch(PDO::FETCH_OBJ);

              $campaignInfo->campaignDeleted = $campaignInfo->campaignDeleted == "true" ? true : false;

              $response->info = $campaignInfo;

              echo json_encode($response, JSON_NUMERIC_CHECK);
            }else{
              //RESPONSE
              $response = new stdClass();
              $response->status = 1; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados
              $response->statusMessage = "Os dados da campanha não foram alterados pois não foram enviadas modificações.";

              $stmt = $conn->prepare($sqlCampaignInfoSelect);
              $stmt->bindParam("campaignId",$json->campaignId);
              $stmt->execute();
              $campaignInfo = $stmt->fetch(PDO::FETCH_OBJ);

              $campaignInfo->campaignDeleted = $campaignInfo->campaignDeleted == "true" ? true : false;

              $response->info = $campaignInfo;

              echo json_encode($response, JSON_NUMERIC_CHECK);
            }
      }

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function getUserProfile($userCode, $userLocalId){

	$sqlUserProfile = "SELECT U.ID AS userId, U.LOGIN AS userLogin, U.FIRSTNAME AS userFirstName, U.LASTNAME AS userLastName, U.BIRTHDAY AS userBirthday, U.EMAIL AS userEmail,
                    U.GENDER AS userGender, IF(U.DELETED = 0, 'false', 'true') AS userDeleted, U.CODE AS userCode, IF(U.UPDATED_DATA = 0, 'false', 'true') AS userUpdatedData, COUNT(C.ID) AS validCheckins
                    FROM USER AS U
                    LEFT OUTER JOIN CHECKIN C ON C.USER_ID = U.ID AND C.CONSUMED = 0
                    WHERE U.CODE = :userCode
                    AND U.LOCAL_ID = :userLocalId";

	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlUserProfile);
      $stmt->bindParam("userCode",$userCode);
      $stmt->bindParam("userLocalId",$userLocalId);
			$stmt->execute();
      $userInfo = $stmt->fetch(PDO::FETCH_OBJ);

      if ($userInfo->userCode != null) {
        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Dados do usuário recuperados com sucesso.";

        $userInfo->userDeleted = $userInfo->userDeleted == "true" ? true : false;
        $userInfo->userUpdatedData = $userInfo->userUpdatedData == "true" ? true : false;

        $userInfo->userBirthday = implode("/", array_reverse(explode("-", $userInfo->userBirthday)));

        $response->user = $userInfo;

        //OUTPUT
  			echo json_encode($response, JSON_NUMERIC_CHECK);

      }else{

        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Usuário não encontrado";

        //OUTPUT
  			echo json_encode($response, JSON_NUMERIC_CHECK);
      }



			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function checkinManualAdd(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlCheckinSelect = "SELECT C.ID FROM CHECKIN AS C WHERE C.USER_ID = :userId AND C.TOKEN = :token";

	$sqlCheckinInsert = "INSERT INTO CHECKIN (LOCAL_ID, USER_ID, DATE, CONSUMED, TOKEN, INTERMEDIARY, INTERMEDIARY_ID) VALUES (:localId, :userId, NOW(), 0, :token, :intermediary, :intermediaryId)";

  try{
      $conn = getConn();

      //SELECT
      //SQL AND BIND
      $stmt = $conn->prepare($sqlCheckinSelect);
      $stmt->bindParam("userId",$json->userId);
      $stmt->bindParam("token",$json->token);
      $stmt->execute();
      $checkinInfo = $stmt->fetch(PDO::FETCH_OBJ);

      if ($checkinInfo == null) {

        //SQL AND BIND
        $stmt = $conn->prepare($sqlCheckinInsert);
        $stmt->bindParam("localId",$json->localId);
        $stmt->bindParam("userId",$json->userId);
        $stmt->bindParam("token",$json->token);
        $stmt->bindParam("intermediary",$json->intermediary);
        $stmt->bindParam("intermediaryId",$json->intermediaryId);

        //TOKEN
        $currentDate = date("d-m-Y");
        $localId = $json->localId;
        $sufix = "loyaltee-sufix";

        $tokenFields = $currentDate."-".$localId."-".$sufix;
        $tokenValidator = md5($currentDate."-".$localId."-".$sufix);

        if ($json->token == $tokenValidator) {
          $stmt->execute();
          $checkinId = $conn->lastInsertId();

          //RESPONSE
          $response = new stdClass();
          $response->status = 1;
          $response->statusMessage = "Cupom manual adicionado com sucesso.";
          // $response->checkin = $checkinId;

        }else{
          //RESPONSE
          $response = new stdClass();
          $response->status = 2;
          $response->statusMessage = "Parece que o token gerado para o checkin manual é inválido. Por favor, contacte o suporte.";
        }

        //OUTPUT
        echo json_encode($response, JSON_NUMERIC_CHECK);

      }else{

        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Este usuário já recebeu o cupom para o dia de hoje através do aplicativo ou manualmente. Tem certeza de que deseja gerar outro cupom?";

        //OUTPUT
        echo json_encode($response, JSON_NUMERIC_CHECK);
      }

      $conn = null;

      } catch(PDOException $e){

          header('HTTP/1.1 400 Bad request');
          echo json_encode($e->getMessage());

          die();

      }
}

function checkinManualAddForPromotion(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

	$sqlCheckinInsert = "INSERT INTO CHECKIN (LOCAL_ID, USER_ID, DATE, CONSUMED, TOKEN, INTERMEDIARY, INTERMEDIARY_ID) VALUES (:localId, :userId, NOW(), 0, :token, :intermediary, :intermediaryId)";

  try{
      $conn = getConn();

      //SQL AND BIND
      $stmt = $conn->prepare($sqlCheckinInsert);
      $stmt->bindParam("localId",$json->localId);
      $stmt->bindParam("userId",$json->userId);
      $stmt->bindParam("token",$json->token);
      $stmt->bindParam("intermediary",$json->intermediary);
      $stmt->bindParam("intermediaryId",$json->intermediaryId);

      //TOKEN
      $currentDate = date("d-m-Y");
      $localId = $json->localId;
      $sufix = "loyaltee-sufix";

      $tokenFields = $currentDate."-".$localId."-".$sufix;
      $tokenValidator = md5($currentDate."-".$localId."-".$sufix);

      if ($json->token == $tokenValidator) {

        for ($i=0; $i < $json->qtyCoupons; $i++) {
              $stmt->execute();
          }


        $checkinId = $conn->lastInsertId();

        //RESPONSE
        $response = new stdClass();
        $response->status = 1;

        if ($json->qtyCoupons > 1) {
          $response->statusMessage = $json->qtyCoupons." cupons manuais adicionados com sucesso.";
        }else{
          $response->statusMessage = "Cupom manual adicionado com sucesso.";
        }

        // $response->checkin = $checkinId;

      }else{
        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Parece que o token gerado para o checkin manual é inválido. Por favor, contacte o suporte.";
      }

      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);

      $conn = null;

      } catch(PDOException $e){

          header('HTTP/1.1 400 Bad request');
          echo json_encode($e->getMessage());

          die();

      }
}

function checkinList($userId, $localId){

  $concatAdmin = "CONCAT(LA.FIRSTNAME, ' ',LA.LASTNAME)";
  $concatUser = "CONCAT(U.FIRSTNAME, ' ',U.LASTNAME)";

	$sqlCheckinList = "SELECT DISTINCT C.ID AS checkinId, C.LOCAL_ID AS localId, C.USER_ID AS userId, C.DATE AS date, C.CONSUMED AS consumed, IF(C.INTERMEDIARY = \"ADMIN\", 'Manual', 'Aplicativo') AS checkinType, IF(C.INTERMEDIARY = \"ADMIN\", $concatAdmin, $concatUser) AS checkinIntermediary
                FROM CHECKIN AS C
                INNER JOIN USER AS U ON U.ID = C.USER_ID
                INNER JOIN LOCAL_ADMIN AS LA ON C.INTERMEDIARY_ID = IF(C.INTERMEDIARY = \"ADMIN\", LA.ID, U.ID)
                WHERE C.USER_ID = :userId
                AND C.LOCAL_ID = :localId";

	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlCheckinList);
			$stmt->bindParam("userId",$userId);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();

      $checkinList = $stmt->fetchAll(PDO::FETCH_OBJ);

      $countValidCheckin = 0;

      foreach ($checkinList as $row) {
        if ($row->consumed == 0) {
            $countValidCheckin++;
        }
      }


      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Dados de cupons recuperados com sucesso.";
      $response->validCheckins = $countValidCheckin;

      $response->checkins = $checkinList;

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function checkinConsume(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlCheckinUpdate = "UPDATE CHECKIN C SET CONSUMED = !CONSUMED WHERE C.ID = :checkinId";

  // echo json_encode($sqlCheckinUpdate, JSON_NUMERIC_CHECK);
  // exit;

	try{
			$conn = getConn();

      //UPDATE
      //SQL AND BIND
      $stmt = $conn->prepare($sqlCheckinUpdate);
      $stmt->bindParam("checkinId",$json->checkinId);
      $stmt->execute();

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Cupom alterado com sucesso.";

      echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function campaignDelete(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlCampaignUpdate = "UPDATE CAMPAIGN CA SET DELETED = 1 WHERE CA.ID = :campaignId";

  // echo json_encode($sqlCampaignUpdate, JSON_NUMERIC_CHECK);
  // exit;

	try{
			$conn = getConn();

      //UPDATE
      //SQL AND BIND
      $stmt = $conn->prepare($sqlCampaignUpdate);
      $stmt->bindParam("campaignId",$json->campaignId);
      $stmt->execute();
      $affectedData = $stmt->rowCount(); //número de linhas afetadas

      if ($affectedData > 0) {
        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Campanha removida com sucesso.";

        echo json_encode($response, JSON_NUMERIC_CHECK);
      }else{
        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Ocorreu um erro ao remover a campanha.";

        echo json_encode($response, JSON_NUMERIC_CHECK);
      }

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function campaignNotificationCalculateReach(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlUsersReceive = "SELECT U.ID AS userID, U.FIRSTNAME AS userFirstName, U.GENDER AS userGender, YEAR(CURDATE()) - YEAR(BIRTHDAY) AS userAge FROM USER AS U WHERE U.LOCAL_ID = :localId AND U.DELETED = 0";

  try{
      $conn = getConn();

      //SELECT
      //SQL AND BIND
      $stmt = $conn->prepare($sqlUsersReceive);
      $stmt->bindParam("localId",$json->localId);

      $stmt->execute();
      $users = $stmt->fetchAll(PDO::FETCH_OBJ);

      $usersResponse = array();

      foreach ($users as $user) {

        //comparando gênero
        if ($user->userGender == $json->campaignNotificationGenderMale) {
          //comparando idade
          if ($user->userAge >= $json->campaignAgeStart && $user->userAge <= $json->campaignAgeStop) {
            array_push($usersResponse, $user);
          }
        }else if($user->userGender == $json->campaignNotificationGenderFemale){
          //comparando idade
          if ($user->userAge >= $json->campaignAgeStart && $user->userAge <= $json->campaignAgeStop) {
            array_push($usersResponse, $user);
          }
        }else if($user->userGender == $json->campaignNotificationGenderOthers){
          //comparando idade
          if ($user->userAge >= $json->campaignAgeStart && $user->userAge <= $json->campaignAgeStop) {
            array_push($usersResponse, $user);
          }
        }

      }

      $uniqueArray = array_unique($usersResponse, SORT_REGULAR);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Usuários selecionados com sucesso.";
      $response->usersCount = count($uniqueArray);
      $response->users = $uniqueArray;

      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);

      $conn = null;

      } catch(PDOException $e){

          header('HTTP/1.1 400 Bad request');
          echo json_encode($e->getMessage());

          die();

      }
}

function campaignNotificationSend(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlUsersReceive = "SELECT U.ID AS userId, U.FIRSTNAME AS userFirstName, U.GENDER AS userGender, YEAR(CURDATE()) - YEAR(BIRTHDAY) AS userAge, U.TOKEN as userToken FROM USER AS U WHERE U.LOCAL_ID = :localId AND U.DELETED = 0";

  $sqlCampaign = "SELECT CA.TITLE AS campaignTitle, CA.MESSAGE AS campaignMessage
                        FROM CAMPAIGN AS CA
                        WHERE CA.ID = :campaignId";

  $sqlNotificationInsert = "INSERT INTO NOTIFICATION (CAMPAIGN_ID, USER_ID, DELIVERY_DATE) VALUES (:campaignId, :userId, :campaignDeliveryDate)";

  try{
      $conn = getConn();

      //SELECT
      //SQL AND BIND
      $stmt = $conn->prepare($sqlUsersReceive);
      $stmt->bindParam("localId",$json->localId);

      $stmt->execute();
      $users = $stmt->fetchAll(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlCampaign);
      $stmt->bindParam("campaignId",$json->campaignId);

      $stmt->execute();
      $campaign = $stmt->fetch(PDO::FETCH_OBJ);

      $usersIds = array();
      $usersTokens = array();

      foreach ($users as $user) {

        //comparando gênero
        if ($user->userGender == $json->campaignNotificationGenderMale) {
          //comparando idade
          if ($user->userAge >= $json->campaignAgeStart && $user->userAge <= $json->campaignAgeStop) {
            array_push($usersIds, $user->userId);
            array_push($usersTokens, $user->userToken);
          }
        }else if($user->userGender == $json->campaignNotificationGenderFemale){
          //comparando idade
          if ($user->userAge >= $json->campaignAgeStart && $user->userAge <= $json->campaignAgeStop) {
            array_push($usersIds, $user->userId);
            array_push($usersTokens, $user->userToken);
          }
        }else if($user->userGender == $json->campaignNotificationGenderOthers){
          //comparando idade
          if ($user->userAge >= $json->campaignAgeStart && $user->userAge <= $json->campaignAgeStop) {
            array_push($usersIds, $user->userId);
            array_push($usersTokens, $user->userToken);
          }
        }

      }

      $uniqueArray = array_unique($usersIds, SORT_REGULAR);
      $uniqueArrayTokens = array_unique($usersTokens, SORT_REGULAR);

      foreach ($uniqueArray as $userId) {

        $stmt = $conn->prepare($sqlNotificationInsert);
        $stmt->bindParam("userId",$userId);
        $stmt->bindParam("campaignId",$json->campaignId);
        $stmt->bindParam("campaignDeliveryDate",$json->campaignDeliveryDate);

        $stmt->execute();
        $affectedData += $stmt->rowCount();
      }


      if ($affectedData > 0) {
        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Notificações enviadas com sucesso.";
        $response->quantidade = count($uniqueArray);

        $fcmResult = sendPushNotificationInternal($uniqueArrayTokens, $campaign->campaignTitle, $campaign->campaignMessage);

        $response->fcmResult = $fcmResult;

      }else{
        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Nenhuma notificação foi enviada, revise sua seleção.";
      }


      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);

      $conn = null;

      } catch(PDOException $e){

          header('HTTP/1.1 400 Bad request');
          echo json_encode($e->getMessage());

          die();

      }
}

function prizeList($localId){

  $sqlCampaigns = "SELECT P.ID AS prizeId, CONCAT(LA.FIRSTNAME, ' ', LA.LASTNAME) AS adminFullName, P.DIN AS prizeDate,
                        CONCAT(U.FIRSTNAME, ' ', U.LASTNAME) AS userFullName
                        FROM PRIZE AS P
                        INNER JOIN USER U ON U.ID = P.USER_ID
                        INNER JOIN LOCAL_ADMIN LA ON LA.ID = P.ADMIN_ID
                        WHERE P.LOCAL_ID = :localId
                        AND P.DELETED = 0
                        ORDER BY P.ID DESC";

  try{
      $conn = getConn();

      //SQL AND BIND
      $stmt = $conn->prepare($sqlCampaigns);
      $stmt->bindParam("localId",$localId);
      $stmt->execute();
      $prizesInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Prêmios recuperados com sucesso.";

      $response->prizes = $prizesInfo;

      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);

      $conn = null;

  } catch(PDOException $e){

      header('HTTP/1.1 400 Bad request');
      echo json_encode($e->getMessage());

      die();
  }

}

function prizeAdd(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

	$sqlCheckinInsert = "INSERT INTO PRIZE (LOCAL_ID, USER_ID, ADMIN_ID) VALUES (:localId, :userId, :adminId)";

  try{
      $conn = getConn();

      //SQL AND BIND
      $stmt = $conn->prepare($sqlCheckinInsert);
      $stmt->bindParam("localId",$json->localId);
      $stmt->bindParam("userId",$json->userId);
      $stmt->bindParam("adminId",$json->adminId);
      $stmt->execute();
      $checkinId = $conn->lastInsertId();
      $affectedData = $stmt->rowCount(); //número de linhas afetadas

      if ($affectedData > 0) {

        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Prêmio concedido com sucesso.";

        //OUTPUT
        echo json_encode($response, JSON_NUMERIC_CHECK);
    }else{

      $response = new stdClass();
      $response->status = 2;
      $response->statusMessage = "Não foi possível conceder o prêmio.";

      //OUTPUT
      echo json_encode($response, JSON_NUMERIC_CHECK);
    }

      $conn = null;

      } catch(PDOException $e){

          header('HTTP/1.1 400 Bad request');
          echo json_encode($e->getMessage());

          die();

      }
}

function prizeDelete(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlPrizeUpdate = "UPDATE PRIZE P SET DELETED = 1 WHERE P.ID = :prizeId";

  // echo json_encode($sqlCampaignUpdate, JSON_NUMERIC_CHECK);
  // exit;

	try{
			$conn = getConn();

      //UPDATE
      //SQL AND BIND
      $stmt = $conn->prepare($sqlPrizeUpdate);
      $stmt->bindParam("prizeId",$json->prizeId);
      $stmt->execute();
      $affectedData = $stmt->rowCount(); //número de linhas afetadas

      if ($affectedData > 0) {
        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Prêmio removido com sucesso.";

        echo json_encode($response, JSON_NUMERIC_CHECK);
      }else{
        //RESPONSE
        $response = new stdClass();
        $response->status = 2;
        $response->statusMessage = "Ocorreu um erro ao remover o prêmio.";

        echo json_encode($response, JSON_NUMERIC_CHECK);
      }

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function dashBoardRowOne($localId){

	$sqlFrameOne = "SELECT COUNT(U.ID), U.ID AS userID FROM USER AS U INNER JOIN CHECKIN C ON C.USER_ID = U.ID WHERE C.LOCAL_ID = :localId GROUP BY U.ID HAVING COUNT(U.ID) > 1";

  $sqlFrameTwo  = "SELECT COUNT(U.ID), U.ID AS userID FROM USER AS U INNER JOIN CHECKIN C ON C.USER_ID = U.ID WHERE C.LOCAL_ID = :localId GROUP BY U.ID HAVING COUNT(U.ID) = 1 ";

  $sqlFrameThree = "SELECT U.ID FROM USER AS U WHERE U.ID NOT IN (SELECT C.USER_ID FROM CHECKIN AS C) AND U.LOCAL_ID = :localId";

  $sqlFrameFour = "SELECT COUNT(U.ID), U.ID AS userID FROM USER AS U INNER JOIN CHECKIN C ON C.USER_ID = U.ID WHERE C.LOCAL_ID = :localId GROUP BY U.ID HAVING COUNT(U.ID) >= 20";

  $sqlRowOneUserTotal = "SELECT U.ID FROM USER AS U WHERE U.LOCAL_ID = :localId";

  try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlFrameOne);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameOneResultQtd = $stmt->fetchAll(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwo);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameTwoResultQtd = $stmt->fetchAll(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameThree);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameThreeResultQtd = $stmt->fetchAll(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameFour);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameFourResultQtd = $stmt->fetchAll(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlRowOneUserTotal);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlRowOneResult = $stmt->fetchAll(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Dados da linha 1 do dashboard recuperados com sucesso.";

      $rowOne = new stdClass();

      $rowOne->rowOneFrameOneTitle = "Taxa de retorno";
      $rowOne->rowOneFrameOneSubtitle = "Usuários que ganharam mais de um cupom";
      $rowOne->rowOneFrameOneQtd = $sqlFrameOneResultQtd != null ? COUNT($sqlFrameOneResultQtd) : 0;
      $rowOne->rowOneFrameOnePercent = $sqlFrameOneResultQtd != null ? (COUNT($sqlFrameOneResultQtd) / COUNT($sqlRowOneResult)) * 100 : 0;

      $rowOne->rowOneFrameTwoTitle = "Taxa de não-retorno";
      $rowOne->rowOneFrameTwoSubtitle = "Usuários que só ganharam um cupom";
      $rowOne->rowOneFrameTwoQtd = $sqlFrameTwoResultQtd != null ? COUNT($sqlFrameTwoResultQtd) : 0;
      $rowOne->rowOneFrameTwoPercent = $sqlFrameTwoResultQtd != null ? (COUNT($sqlFrameTwoResultQtd) / COUNT($sqlRowOneResult)) * 100 : 0;

      $rowOne->rowOneFrameThreeTitle = "Taxa de usuários desconhecidos";
      $rowOne->rowOneFrameThreeSubtitle = "Usuários que tem o app e não tem cupons";
      $rowOne->rowOneFrameThreeQtd = $sqlFrameThreeResultQtd != null ? COUNT($sqlFrameThreeResultQtd) : 0;
      $rowOne->rowOneFrameThreePercent = $sqlFrameThreeResultQtd != null ? (COUNT($sqlFrameThreeResultQtd) / COUNT($sqlRowOneResult)) * 100 : 0;

      $rowOne->rowOneFrameFourTitle = "Taxa de usuários fiéis";
      $rowOne->rowOneFrameFourSubtitle = "Usuários com mais de 20 cupons";
      $rowOne->rowOneFrameFourQtd = $sqlFrameFourResultQtd != null ? COUNT($sqlFrameFourResultQtd) : 0;
      $rowOne->rowOneFrameFourPercent = $sqlFrameFourResultQtd != null ? (COUNT($sqlFrameFourResultQtd) / COUNT($sqlRowOneResult)) * 100 : 0;


      $response->rowOne = $rowOne;

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function dashBoardRowTwo($localId){

	$sqlFrameOne = "SELECT (SELECT COUNT(ID) FROM USER WHERE LOCAL_ID = :localId) AS userTotal, (SELECT COUNT(ID) FROM USER WHERE DIN >= (NOW()-INTERVAL 1 MONTH) AND LOCAL_ID = :localId) AS userLastMonth,
                  (SELECT COUNT(ID) FROM USER WHERE DIN >= (NOW()-INTERVAL 1 WEEK) AND LOCAL_ID = :localId) AS userLastWeek";

  $sqlFrameTwo  = "SELECT (SELECT COUNT(ID) FROM CHECKIN WHERE LOCAL_ID = :localId) AS checkinTotal, (SELECT COUNT(ID) FROM CHECKIN WHERE DATE >= (NOW()-INTERVAL 1 MONTH) AND LOCAL_ID = :localId) AS checkinLastMonth,
                  (SELECT COUNT(ID) FROM CHECKIN WHERE DATE >= (NOW()-INTERVAL 1 WEEK) AND LOCAL_ID = :localId) AS checkinLastWeek";

  $sqlFrameThree = "SELECT (SELECT COUNT(ID) FROM PRIZE WHERE LOCAL_ID = :localId) AS prizeTotal, (SELECT COUNT(ID) FROM PRIZE WHERE DIN >= (NOW()-INTERVAL 1 MONTH) AND LOCAL_ID = :localId) AS prizeLastMonth,
                  (SELECT COUNT(ID) FROM PRIZE WHERE DIN >= (NOW()-INTERVAL 1 WEEK) AND LOCAL_ID = :localId) AS prizeLastWeek";


	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlFrameOne);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameOneResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwo);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameTwoResultQtd = $stmt->fetch(PDO::FETCH_OBJ);
      //
      $stmt = $conn->prepare($sqlFrameThree);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameThreeResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Dados da linha 2 do dashboard recuperados com sucesso.";


      $rowTwo = new stdClass();

      $rowTwo->rowTwoFrameOneLineOneTitle = "Usuários no período";
      $rowTwo->rowTwoFrameOneLineOneSubtitle = "Usuários cadastrados em todo período";
      $rowTwo->rowTwoFrameOneLineOneValue = $sqlFrameOneResultQtd->userTotal;

      $rowTwo->rowTwoFrameOneLineTwoTitle = "Usuários no último mês";
      $rowTwo->rowTwoFrameOneLineTwoSubtitle = "Usuários cadastrados no último mês";
      $rowTwo->rowTwoFrameOneLineTwoValue = $sqlFrameOneResultQtd->userLastMonth;

      $rowTwo->rowTwoFrameOneLineThreeTitle = "Usuários na última semana";
      $rowTwo->rowTwoFrameOneLineThreeSubtitle = "Usuários cadastrados nos últimos 7 dias";
      $rowTwo->rowTwoFrameOneLineThreeValue = $sqlFrameOneResultQtd->userLastWeek;

      //

      $rowTwo->rowTwoFrameTwoLineOneTitle = "Cupons no período";
      $rowTwo->rowTwoFrameTwoLineOneSubtitle = "Cupons gerados em todo período";
      $rowTwo->rowTwoFrameTwoLineOneValue = $sqlFrameTwoResultQtd->checkinTotal;

      $rowTwo->rowTwoFrameTwoLineTwoTitle = "Cupons no último mês";
      $rowTwo->rowTwoFrameTwoLineTwoSubtitle = "Cupons gerados nos últimos 30 dias";
      $rowTwo->rowTwoFrameTwoLineTwoValue = $sqlFrameTwoResultQtd->checkinLastMonth;

      $rowTwo->rowTwoFrameTwoLineThreeTitle = "Cupons na última semana";
      $rowTwo->rowTwoFrameTwoLineThreeSubtitle = "Cupons gerados nos últimos 7 dias";
      $rowTwo->rowTwoFrameTwoLineThreeValue = $sqlFrameTwoResultQtd->checkinLastWeek;

      //

      $rowTwo->rowTwoFrameThreeLineOneTitle = "Prêmios no período";
      $rowTwo->rowTwoFrameThreeLineOneSubtitle = "Prêmios dados em todo período";
      $rowTwo->rowTwoFrameThreeLineOneValue = $sqlFrameThreeResultQtd->prizeTotal;

      $rowTwo->rowTwoFrameThreeLineTwoTitle = "Prêmios no último mês";
      $rowTwo->rowTwoFrameThreeLineTwoSubtitle = "Prêmios dados nos últimos 30 dias";
      $rowTwo->rowTwoFrameThreeLineTwoValue = $sqlFrameThreeResultQtd->prizeLastMonth;

      $rowTwo->rowTwoFrameThreeLineThreeTitle = "Prêmios na última semana";
      $rowTwo->rowTwoFrameThreeLineThreeSubtitle = "Prêmios dados nos últimos 7 dias";
      $rowTwo->rowTwoFrameThreeLineThreeValue = $sqlFrameThreeResultQtd->prizeLastWeek;

      $response->rowTwo = $rowTwo;

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function dashBoardRowThree($localId){

	$sqlFrameOne = "SELECT (SELECT COUNT(GENDER) FROM USER WHERE GENDER = 'M' AND LOCAL_ID = :localId) AS genderMale,
                         (SELECT COUNT(GENDER) FROM USER WHERE GENDER = 'F' AND LOCAL_ID = :localId) AS genderFemale,
                         (SELECT COUNT(GENDER) FROM USER WHERE GENDER = 'O' AND LOCAL_ID = :localId) AS genderOthers";

  $sqlFrameTwoDates  = "SELECT(subdate(CURRENT_DATE, 7)) AS '7', (subdate(CURRENT_DATE, 6)) AS '6',
                              (subdate(CURRENT_DATE, 5)) AS '5', (subdate(CURRENT_DATE, 4)) AS '4', (subdate(CURRENT_DATE, 3)) AS '3', (subdate(CURRENT_DATE, 2)) AS '2', (subdate(CURRENT_DATE, 1)) AS '1'";

  $sqlFrameTwoMale = "SELECT
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 7) AND C.LOCAL_ID = :localId) AS '7',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 6) AND C.LOCAL_ID = :localId) AS '6',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 5) AND C.LOCAL_ID = :localId) AS '5',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 4) AND C.LOCAL_ID = :localId) AS '4',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 3) AND C.LOCAL_ID = :localId) AS '3',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 2) AND C.LOCAL_ID = :localId) AS '2',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'M' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 1) AND C.LOCAL_ID = :localId) AS '1'";

$sqlFrameTwoFemale = "SELECT
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 7) AND C.LOCAL_ID = :localId) AS '7',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 6) AND C.LOCAL_ID = :localId) AS '6',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 5) AND C.LOCAL_ID = :localId) AS '5',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 4) AND C.LOCAL_ID = :localId) AS '4',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 3) AND C.LOCAL_ID = :localId) AS '3',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 2) AND C.LOCAL_ID = :localId) AS '2',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'F' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 1) AND C.LOCAL_ID = :localId) AS '1'";

$sqlFrameTwoOthers = "SELECT
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 7) AND C.LOCAL_ID = :localId) AS '7',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 6) AND C.LOCAL_ID = :localId) AS '6',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 5) AND C.LOCAL_ID = :localId) AS '5',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 4) AND C.LOCAL_ID = :localId) AS '4',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 3) AND C.LOCAL_ID = :localId) AS '3',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 2) AND C.LOCAL_ID = :localId) AS '2',
(SELECT COUNT(C.ID) FROM CHECKIN C INNER JOIN USER U ON U.ID = C.USER_ID AND U.GENDER = 'O' WHERE DATE(C.DATE) = subdate(CURRENT_DATE, 1) AND C.LOCAL_ID = :localId) AS '1'";

  $sqlFrameThree = "SELECT SUM(CASE WHEN U.PLATFORM = 'iOS' THEN 1 ELSE 0 END) AS iOS,
	                         SUM(CASE WHEN U.PLATFORM = 'Android' THEN 1 ELSE 0 END) AS Android,
                           COUNT(*) as total
                           FROM USER U
                           WHERE U.LOCAL_ID = :localId
                           AND U.DELETED = 0";


	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlFrameOne);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameOneResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwoDates);
			$stmt->execute();
      $sqlFrameTwoResultDates = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwoMale);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameTwoResultMale = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwoFemale);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameTwoResultFemale = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwoOthers);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameTwoResultOthers = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameThree);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameThreeResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Dados da linha 3 do dashboard recuperados com sucesso.";


      $rowThree = new stdClass();

      $rowThree->rowThreeFrameOneLineOneTitle = "Homens";
      $rowThree->rowThreeFrameOneLineOneSubtitle = "Qtd. de usuários do gênero masculino";
      $rowThree->rowThreeFrameOneLineOneValue = $sqlFrameOneResultQtd->genderMale;

      $rowThree->rowThreeFrameOneLineTwoTitle = "Mulheres";
      $rowThree->rowThreeFrameOneLineTwoSubtitle = "Qtd. de usuários do gênero feminino";
      $rowThree->rowThreeFrameOneLineTwoValue = $sqlFrameOneResultQtd->genderFemale;

      $rowThree->rowThreeFrameOneLineThreeTitle = "Outros";
      $rowThree->rowThreeFrameOneLineThreeSubtitle = "Qtd. de usuários de outros gêneros";
      $rowThree->rowThreeFrameOneLineThreeValue = $sqlFrameOneResultQtd->genderOthers;

      //

      $rowThree->rowThreeFrameTwoGraphTitle = "Freqûencia nos últimos 7 dias";
      $rowThree->rowThreeFrameTwoGraphLegend1 = "Homens";
      $rowThree->rowThreeFrameTwoGraphLegend2 = "Mulheres";
      $rowThree->rowThreeFrameTwoGraphLegend3 = "Outros";

      $arrDates = array();
      foreach ($sqlFrameTwoResultDates as $strDate) {
        $arrDates[] = $strDate;
      }

      $rowThree->rowThreeFrameTwoGraphDates = $arrDates;

      $arrMale = array();
      foreach ($sqlFrameTwoResultMale as $strMale) {
        $arrMale[] = $strMale;
      }

      $arrFemale = array();
      foreach ($sqlFrameTwoResultFemale as $strFemale) {
        $arrFemale[] = $strFemale;
      }

      $arrOthers = array();
      foreach ($sqlFrameTwoResultOthers as $strOthers) {
        $arrOthers[] = $strOthers;
      }

      $rowThree->rowThreeFrameTwoGraphMale = $arrMale;
      $rowThree->rowThreeFrameTwoGraphFemale = $arrFemale;
      $rowThree->rowThreeFrameTwoGraphOthers = $arrOthers;

      //

      $rowThree->rowThreeFrameThreeGraphTitle = "Usuários por plataforma";
      $rowThree->rowThreeFrameThreeGraphSubtitle = "Quantidade de usuários por Sistema Operacional mobile";

      $rowThree->rowThreeFrameThreeGraphValue = $sqlFrameThreeResultQtd->total;
      //
      $rowThree->rowThreeFrameThreeGraphLegend1 = "iOS";
      $rowThree->rowThreeFrameThreeGraphSlice1 = $sqlFrameThreeResultQtd->iOS != null ? $sqlFrameThreeResultQtd->iOS/$sqlFrameThreeResultQtd->total * 100 : 0;
      //
      $rowThree->rowThreeFrameThreeGraphLegend2 = "Android";
      $rowThree->rowThreeFrameThreeGraphSlice2 = $sqlFrameThreeResultQtd->Android != null ? $sqlFrameThreeResultQtd->Android/$sqlFrameThreeResultQtd->total * 100 : 0;


      $response->rowThree = $rowThree;

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function dashBoardRowFour($localId){

  $sqlFrameOne = "SELECT COUNT(CA.ID) AS totalCampaign FROM CAMPAIGN CA WHERE CA.LOCAL_ID = :localId AND CA.DELETED = 0";

  $sqlFrameTwo = "SELECT COUNT(N.ID) AS totalNotificaion FROM NOTIFICATION N INNER JOIN CAMPAIGN CA ON CA.ID = N.CAMPAIGN_ID WHERE CA.LOCAL_ID = :localId";

  $sqlFrameThree = "SELECT COUNT(C.ID) AS totalManual FROM CHECKIN C WHERE C.LOCAL_ID = :localId AND C.INTERMEDIARY = 'ADMIN'";

  $sqlFrameFour = "SELECT COUNT(C.ID) AS totalUser FROM CHECKIN C WHERE C.LOCAL_ID = :localId AND C.INTERMEDIARY = 'USER'";

	try{
			$conn = getConn();

			//SQL AND BIND
			$stmt = $conn->prepare($sqlFrameOne);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameOneResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameTwo);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameTwoResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameThree);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameThreeResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      $stmt = $conn->prepare($sqlFrameFour);
      $stmt->bindParam("localId",$localId);
			$stmt->execute();
      $sqlFrameFoureResultQtd = $stmt->fetch(PDO::FETCH_OBJ);

      //RESPONSE
      $response = new stdClass();
      $response->status = 1;
      $response->statusMessage = "Dados da linha 4 do dashboard recuperados com sucesso.";

      $rowFour = new stdClass();

      //
      $rowFour->rowFourFrameOneTitle = "Total de campanhas criadas";
      $rowFour->rowFourFrameOneValue = $sqlFrameOneResultQtd->totalCampaign;
      //
      $rowFour->rowFourFrameTwoTitle = "Total de notificações enviadas";
      $rowFour->rowFourFrameTwoValue = $sqlFrameTwoResultQtd->totalNotificaion;
      //
      $rowFour->rowFourFrameThreeTitle = "Total de cupons gerados manualmente";
      $rowFour->rowFourFrameThreeValue = $sqlFrameThreeResultQtd->totalManual;
      //
      $rowFour->rowFourFrameFourTitle = "Total de cupons gerados por usuários";
      $rowFour->rowFourFrameFourValue = $sqlFrameFoureResultQtd->totalUser;

      $response->rowFour = $rowFour;

      //OUTPUT
			echo json_encode($response, JSON_NUMERIC_CHECK);

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function sendPushNotificationInternal($registration_ids, $campaignTitle, $campaignMessage) {

    // API access key from Google API's Console
    define('API_ACCESS_KEY', 'AIzaSyDyFiVkrn5f49bgYieUnsU6OarAGQLOvsM');

    $registration_ids = array_values($registration_ids);

    #prep the bundle
         $msg = array
              (
        'title'	=> $campaignTitle,
    		'body' 	=> $campaignMessage,
        'sound' => "default"

              );
    	$fields = array
    			(
    				// 'to'		=> json_encode($registration_ids),
            'registration_ids' => $registration_ids,
    				'notification'	=> $msg
    			);


    	$headers = array
    			(
    				'Authorization: key=' . API_ACCESS_KEY,
    				'Content-Type: application/json'
    			);
    #Send Reponse To FireBase Server
    		$ch = curl_init();
    		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    		curl_setopt( $ch,CURLOPT_POST, true );
    		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    		$result = curl_exec($ch );
    		// echo $result;
    		curl_close($ch);

        return $result;
}

function sendPushNotification() {

    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());

    // API access key from Google API's Console
    define('API_ACCESS_KEY', 'AIzaSyDyFiVkrn5f49bgYieUnsU6OarAGQLOvsM');

    $registration_ids = array_values($json->registration_ids);

    #prep the bundle
         $msg = array
              (
        'body' 	=> $json->body,
        'title'	=> $json->title,
        'sound' => "default",
        'badge' => 1

              );
    	$fields = array
    			(
    				// 'to'		=> json_encode($registration_ids),
            'registration_ids' => $registration_ids,
    				'notification'	=> $msg
    			);


    	$headers = array
    			(
    				'Authorization: key=' . API_ACCESS_KEY,
    				'Content-Type: application/json'
    			);
    #Send Reponse To FireBase Server
    		$ch = curl_init();
    		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    		curl_setopt( $ch,CURLOPT_POST, true );
    		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    		$result = curl_exec($ch );
    		echo $result;
    		curl_close( $ch );
}

function userForgotPassword(){

  $request = \Slim\Slim::getInstance()->request();
	$json = json_decode($request->getBody());

  $sqlUserUpdate = "UPDATE USER U SET PASSWORD = MD5(:userPassword) WHERE MD5(MD5(MD5(MD5(MD5(MD5(MD5(U.CODE))))))) = :userRequest";

	try{
			$conn = getConn();

      //UPDATE
      //SQL AND BIND
      $stmt = $conn->prepare($sqlUserUpdate);
      $stmt->bindParam("userRequest",$json->userRequest);
      $stmt->bindParam("userPassword",$json->userPassword);
      $stmt->execute();
      $affectedData = $stmt->rowCount(); //número de linhas afetadas

      if ($affectedData > 0) {
        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Senha alterada com sucesso.";

        echo json_encode($response, JSON_NUMERIC_CHECK);
      }else{
        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Senha alterada com sucesso."; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados

        echo json_encode($response, JSON_NUMERIC_CHECK);
      }

			$conn = null;

	} catch(PDOException $e){

			header('HTTP/1.1 400 Bad request');
			echo json_encode($e->getMessage());

			die();
	}
}

function siteContactForm() {

  $request = \Slim\Slim::getInstance()->request();
  $json = json_decode($request->getBody());

  // $to = $json->userEmail;
  $to = "contato@loyaltee.com.br";
  $subject = "[Contato site] - ".$json->local;
  $message = "<p><b>Nome:</b> $json->name</p><p><b>Estabelecimento:</b> $json->local</p><p><b>Telefone:</b> $json->telephone</p><p><b>E-mail:</b> $json->email</p><p><b>Mensagem:</b> $json->message</p>";
  $headers = "From: $json->email\r\n";
  $headers .= "Reply-To: $json->email\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

  mail($to, $subject, $message, $headers);

  $response = new stdClass();
  $response->status = 1;
  $response->statusMessage = "Mensagem enviada com sucesso! Em breve entraremos em contato.";
  echo json_encode($response, JSON_NUMERIC_CHECK);

}

function visualSettings($localId){

    $sqlAssetsInfo = "SELECT AT.ID AS assetsId, AT.IMAGE_LOGO AS assetsImageLogo, AT.IMAGE_INFO AS assetsImageInfo, AT.IMAGE_FACEBOOK_SHARE AS assetsImageFacebookShare,
                      AT.HASHTAG_FACEBOOK_SHARE AS assetsHashtagFacebookShare, AT.COUPON_NO AS assetsCouponNo, AT.COUPON_YES AS assetsCouponYes, AT.COLOR_PRIMARY AS assetsColorPrimary,
                      AT.COLOR_SECONDARY AS assetsColorSecondary, AT.MAP_ZOOM AS assetsMapZoom, AT.LOCAL_ID AS localId
                      FROM ASSETS AS AT
                      WHERE AT.LOCAL_ID = :localId";

    try{
        $conn = getConn();

        //SQL AND BIND
        $stmt = $conn->prepare($sqlAssetsInfo);
        $stmt->bindParam("localId",$localId);
        $stmt->execute();
        $assets = $stmt->fetch(PDO::FETCH_OBJ);

        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Ajustes visuais recuperados com sucesso.";

        $response->info = $assets;

        //OUTPUT
        echo json_encode($response, JSON_NUMERIC_CHECK);

        $conn = null;

    } catch(PDOException $e){

        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());

        die();
    }
}

function updateUnitInfo(){

    $request = \Slim\Slim::getInstance()->request();
    $json = json_decode($request->getBody());

    $sqlUnitInfoSelect = "SELECT U.ID AS unitId, U.LOCAL_ID AS localId, U.STREET AS localStreet, U.NUMBER AS localNumber, U.NEIBORHOOD AS localNeiborhood, U.CITY AS localCity,
                          U.STATE AS localState, U.ZIPCODE AS localZipcode, U.LAT AS localLatitude, U.LON AS localLongitude, U.OPERATING_HOURS AS localOperatingHours,
                          U.CONTACTS AS localContacts
                          FROM UNIT AS U
                          WHERE U.ID = :unitId";

    $sqlUnitInfoInsert = "INSERT INTO UNIT (LOCAL_ID, STREET, NUMBER, NEIBORHOOD, CITY, STATE, ZIPCODE, LAT, LON, OPERATING_HOURS, CONTACTS)
                            VALUES (:localId, :localStreet, :localNumber, :localNeiborhood, :localCity, :localState, :localZipcode, :localLatitude, :localLongitude, :localOperatingHours, :localContacts)";

    $sqlUnitInfoUpdate = "UPDATE UNIT U SET ";

    if ($json->localId) { $sqlUnitInfoUpdate .= "LOCAL_ID = :localId, "; }
    if ($json->localStreet) { $sqlUnitInfoUpdate .= "STREET = :localStreet, "; }
    if ($json->localNumber) { $sqlUnitInfoUpdate .= "NUMBER = :localNumber, "; }
    if ($json->localNeiborhood) { $sqlUnitInfoUpdate .= "NEIBORHOOD = :localNeiborhood, ";}
    if ($json->localCity) { $sqlUnitInfoUpdate .= "CITY = :localCity, "; }
    if ($json->localState) { $sqlUnitInfoUpdate .= "STATE = :localState, "; }
    if ($json->localZipcode) { $sqlUnitInfoUpdate .= "ZIPCODE = :localZipcode, "; }
    if ($json->localLatitude) { $sqlUnitInfoUpdate .= "LAT = :localLatitude, "; }
    if ($json->localLongitude) { $sqlUnitInfoUpdate .= "LON = :localLongitude, "; }
    if ($json->localOperatingHours) { $sqlUnitInfoUpdate .= "OPERATING_HOURS = :localOperatingHours, "; }
    if ($json->localContacts) { $sqlUnitInfoUpdate .= "CONTACTS = :localContacts "; }

    $sqlUnitInfoUpdate .= "WHERE U.ID = :unitId";

    try{
        $conn = getConn();

        //SELECT
        //SQL AND BIND
        $stmt = $conn->prepare($sqlUnitInfoSelect);
        $stmt->bindParam("unitId",$json->unitId);
        $stmt->execute();
        $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

        if ($localInfo == null) {

            //INSERT
            //SQL AND BIND
            $stmt = $conn->prepare($sqlUnitInfoInsert);
            $stmt->bindParam("localId",$json->localId);
            $stmt->bindParam("localStreet",$json->localStreet);
            $stmt->bindParam("localNumber",$json->localNumber);
            $stmt->bindParam("localNeiborhood",$json->localNeiborhood);
            $stmt->bindParam("localCity",$json->localCity);
            $stmt->bindParam("localState",$json->localState);
            $stmt->bindParam("localZipcode",$json->localZipcode);
            $stmt->bindParam("localLatitude",$json->localLatitude);
            $stmt->bindParam("localLongitude",$json->localLongitude);
            $stmt->bindParam("localOperatingHours",$json->localOperatingHours);
            $stmt->bindParam("localContacts",$json->localContacts);

            $stmt->execute();

            //RESPONSE
            $response = new stdClass();
            $response->status = 1;
            $response->statusMessage = "Local adicionado com sucesso.";

            $local = new stdClass();
            $local->unitId = $conn->lastInsertId();
            $local->localId = $json->localId;
            $local->localStreet = $json->localStreet;
            $local->localNumber = $json->localNumber;
            $local->localNeiborhood = $json->localNeiborhood;
            $local->localCity = $json->localCity;
            $local->localState = $json->localState;
            $local->localZipcode = $json->localZipcode;
            $local->localLatitude = $json->localLatitude;
            $local->localLongitude = $json->localLongitude;
            $local->localOperatingHours = $json->localOperatingHours;
            $local->localContacts = $json->localContacts;

            $response->info = $local;

            echo json_encode($response, JSON_NUMERIC_CHECK);

        }else{

            //UPDATE
            //SQL AND BIND
            $stmt = $conn->prepare($sqlUnitInfoUpdate);

//            echo json_encode($sqlUnitInfoUpdate, JSON_NUMERIC_CHECK);
//            exit;


            //CONDITIONS TO MATCH
            if ($json->unitId) { $stmt->bindParam("unitId",$json->unitId); }
            if ($json->localId) { $stmt->bindParam("localId",$json->localId); }
            if ($json->localStreet) { $stmt->bindParam("localStreet",$json->localStreet); }
            if ($json->localNumber) { $stmt->bindParam("localNumber",$json->localNumber); }
            if ($json->localNeiborhood) { $stmt->bindParam("localNeiborhood",$json->localNeiborhood); }
            if ($json->localCity) { $stmt->bindParam("localCity",$json->localCity); }
            if ($json->localState) { $stmt->bindParam("localState",$json->localState); }
            if ($json->localZipcode) { $stmt->bindParam("localZipcode",$json->localZipcode); }
            if ($json->localLatitude) { $stmt->bindParam("localLatitude",$json->localLatitude); }
            if ($json->localLongitude) { $stmt->bindParam("localLongitude",$json->localLongitude); }
            if ($json->localOperatingHours) { $stmt->bindParam("localOperatingHours",$json->localOperatingHours); }
            if ($json->localContacts) { $stmt->bindParam("localContacts",$json->localContacts); }


            $stmt->execute();
            $affectedData = $stmt->rowCount(); //número de linhas afetadas

            if ($affectedData > 0) {
                //RESPONSE
                $response = new stdClass();
                $response->status = 1;
                $response->statusMessage = "Local alterado com sucesso.";

                $stmt = $conn->prepare($sqlUnitInfoSelect);
                $stmt->bindParam("unitId",$json->unitId);
                $stmt->execute();
                $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

                $response->info = $localInfo;

                echo json_encode($response, JSON_NUMERIC_CHECK);
            }else{
                //RESPONSE
                $response = new stdClass();
                $response->status = 1; //não é código de erro pois o banco de dados pode não ter retornado linhas alteradas porque os dados enviados eram os mesmos já no banco de dados
                $response->statusMessage = "Os dados do local não foram alterados pois não foram enviadas modificações.";

                $stmt = $conn->prepare($sqlUnitInfoSelect);
                $stmt->bindParam("localId",$json->localId);
                $stmt->execute();
                $localInfo = $stmt->fetch(PDO::FETCH_OBJ);

                $response->info = $localInfo;

                echo json_encode($response, JSON_NUMERIC_CHECK);
            }
        }

        $conn = null;

    } catch(PDOException $e){

        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());

        die();
    }
}

function unitsList($localId){

    $sqlUnits = "SELECT U.ID AS unitId, U.LOCAL_ID AS localId, U.STREET AS localStreet, U.NUMBER AS localNumber, U.NEIBORHOOD AS localNeiborhood, U.CITY AS localCity, U.STATE AS localState,
                        U.ZIPCODE as localZipcode
                        FROM UNIT AS U
                        WHERE U.LOCAL_ID = :localId
                        ORDER BY U.ID DESC";

    try{
        $conn = getConn();

        //SQL AND BIND
        $stmt = $conn->prepare($sqlUnits);
        $stmt->bindParam("localId",$localId);
        $stmt->execute();
        $unitsInfo = $stmt->fetchAll(PDO::FETCH_OBJ);

        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Unidades recuperadas com sucesso.";

        $response->units = $unitsInfo;

        //OUTPUT
        echo json_encode($response, JSON_NUMERIC_CHECK);

        $conn = null;

    } catch(PDOException $e){

        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());

        die();
    }

}

function unitList($unitId){

    $sqlUnits = "SELECT U.ID AS unitId, U.LOCAL_ID AS localId, U.STREET AS localStreet, U.NUMBER AS localNumber, U.NEIBORHOOD AS localNeiborhood, U.CITY AS localCity, U.STATE AS localState,
                        U.ZIPCODE as localZipcode, U.LAT AS localLatitude, U.LON AS localLongitude, U.OPERATING_HOURS AS localOperatingHours, U.CONTACTS AS localContacts
                        FROM UNIT AS U
                        WHERE U.ID = :unitId";

    try{
        $conn = getConn();

        //SQL AND BIND
        $stmt = $conn->prepare($sqlUnits);
        $stmt->bindParam("unitId",$unitId);
        $stmt->execute();
        $info = $stmt->fetch(PDO::FETCH_OBJ);

        //RESPONSE
        $response = new stdClass();
        $response->status = 1;
        $response->statusMessage = "Unidade recuperada com sucesso.";

        $response->info = $info;

        //OUTPUT
        echo json_encode($response, JSON_NUMERIC_CHECK);

        $conn = null;

    } catch(PDOException $e){

        header('HTTP/1.1 400 Bad request');
        echo json_encode($e->getMessage());

        die();
    }

}