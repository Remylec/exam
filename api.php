<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, origin");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

require_once('model/Realisation.php');
require_once('model/manager/RealisationManager.php');
require_once('model/ContactMsg.php');
require_once('model/manager/ContactMsgManager.php');
require_once('model/Service.php');
require_once('model/manager/ServiceManager.php');
require_once('view/view.php');
require_once('model/model.php');
date_default_timezone_set('Europe/Amsterdam');

if (isset($_GET['entity'])) {
    switch ($_GET['entity']) {
        case 'realisation':
            $manager = new RealisationManager;
            $data = $manager->getAll();
            $data = json_encode($data);
            echo $data;

            break;
        case 'contact' :
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                exit;
            }
            $manager = new ContactMsgManager();
            $recupJson = json_decode(file_get_contents('php://input'), true);

            if ($recupJson != null) {
                $tab = $recupJson;

                //if(!in_array(null, $tab)){
                $contactMsg = new ContactMsg;
                //$dateHeure = date('Y-m-d H:i:s');

                $contactMsg->name = $tab['name'];
                $contactMsg->firstName = $tab['firstName'];
                $contactMsg->email = $tab['email'];
                $contactMsg->message = $tab['message'];
                //$contactMsg->dateHeure = $dateHeure;

                $manager->create($contactMsg);

                //}
            }

            break;


        case 'service' :
            $manager = new ServiceManager;
            $data = $manager->getAll();
            $data = json_encode($data);
            echo $data;
            break;

    }
}
