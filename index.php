<?php
session_start();
$_SESSION['superuser'] = (isset($_SESSION['superuser']))?$_SESSION['superuser']:FALSE;
$_SESSION['token'] = (isset($_SESSION['token']))?$_SESSION['token']:FALSE;

if(!isset($_SESSION['tabsErrors'])){
    $_SESSION['tabsErrors'] = [
        "log" =>["login"=>null, "pass"=>"null"],
        "rea"=>["title"=>null, "link"=>null, "image"=>null],
        "service"=>["title"=>null, "image"=>null]
    ];
}

require_once('model/Realisation.php');
require_once('model/manager/RealisationManager.php');
require_once('model/Service.php');
require_once('model/manager/ServiceManager.php');
require_once('model/model.php');
require_once('model/User.php');
require_once('model/ContactMsg.php');
require_once('model/manager/ContactMsgManager.php');
require_once('model/manager/AdminManager.php');
require_once('view/view.php');

$admin = new AdminManager;
if($_SESSION['token'] != false){
    $_SESSION['pathUpload'] = "assets/dist/img/upload/";
    $managerRea = new RealisationManager;
    $managerMsg = new ContactMsgManager;
    $managerServ = new ServiceManager;

    if (isset($_GET['action'])){
        $action = $_GET['action'];
    }else{
        $action = 'list';
    }

    if(isset($_GET['page'])){
        $page = $_GET['page'];
    } else {
        $page = "rea";
    }

    switch($action) {
        case 'list':
            if(isset($_GET['page'])){
                switch ($page) {
                    case 'msg':
                        $instances = $managerMsg->getAll();
                        $content = listMsg($instances);
                        break;
                    case 'service' :
                        $instances = $managerServ->getAll();
                        $content = listServ($instances);
                        break;
                    default:
                        header('Location: ./index.php');
                        break;
                }
            }else{
                $instances = $managerRea->getAll();
                $content = listRea($instances);
            }
            break;
 
        case 'add':
            if($page == "rea"){
                 $message="Réalisation ajoutée !";
                 $instance = new Realisation;
                 $manager = $managerRea;
            } else {
                 $message="Service ajouté";
                 $instance = new Service;
                 $manager = $managerServ;
            }

            if(isFormSubmitted()){
                  $content = treatmentAction($page,$action, $message,$manager);
            } else {
                if($page == "rea"){
                    $content = reaForm($instance, $action);
                }else {
                    $content = servForm($instance, $action); 
                }
            }
            break;

        case 'update' :
            if($page == "rea"){
                $message = "Réalisation modifiée !";
                $manager = $managerRea;
            } else {
                $message = "Service modifié !";
                $manager = $managerServ;
            }

            if(isFormSubmitted()){
                $content=treatmentAction($page,$action, $message, $manager);
            } else{
                $instance = $manager->getOne($_GET['id']);
                if($instance->id != null){
                    if($page == "rea"){
                        $content = reaForm($instance, $action);
                    }
                    else {$content = servForm($instance, $action);
                    }
                    $_SESSION['tabsErrors'][$page] = resetTabError($page);
                } else {
                    redirection($page);
                }
            }
            break;
        
        case 'delete' :
           switch ($page) {
                case 'rea':
                   if(isset($_GET['id']) && isset($_POST['confirm'])){
                       $realisation = $managerRea->getOne(intval($_GET['id']));

                       if($realisation->id !== null){
                            $managerRea->delete($realisation);
                           header('Location: ./index.php');
                       }else{
                           $content = '<p class="alert alert-danger">L\'élément n\'a pas été trouvé. Suppression impossible.</p>
                           <a href="./index.php?action=list" class="btn btn-info" role="button">RETOUR</a>';
                       }
                   } else {
                       redirection("rea");
                   }
                   break;
                case 'service' :
                    if(isset($_GET['id']) && isset($_POST['confirm']) ) {
                        $service = $managerServ->getOne(intval($_GET['id']));

                        if($service->id !== null){
                            $managerServ->delete($service);
                            redirection("service");
                        } else {
                            $content = '<p class="alert alert-danger">L\'élément n\'a pas été trouvé. Suppression impossible.</p>
                            <a href="./index.php?action=list&page=service" class="btn btn-info" role="button">RETOUR</a>';
                        }
                    }else {
                        redirection("service");
                    }
                   break;
                case "msg" :
                    if(isset($_GET['id']) && isset($_POST['confirm'])){
                        $message = $managerMsg->getOne(intval($_GET['id']));

                        if($message->id !== null){
                            $managerMsg->delete($message);
                        } 
                        redirection("msg");
                    }
                    break;
                default:
                   redirection("rea");
                   break;
           }
           
            break;
        case 'disconnect' :
            $managerRea = new AdminManager();
            $managerRea->getDisconnection();
            break;
        case 'addUser' :
            
            if(isFormSubmitted()){
                $user = dataValidation("addUser", null);

                if(!empty($user)){
                    $error=false;
                    foreach($_SESSION['tabError'] as  $val){
                        if($val !== null){
                            $error = true;
                            break;
                        }
                    }

                    if($error == true){
                    $content = userForm($user); 
                    } else {
                        $manager = new AdminManager;
                        $manager->create($user);

                        $content='
                        <h1 class="alert alert-success">L\'utilisateur a été ajouté.</h1> 
                        <a href="./index.php?action=list" class="btn btn-info" role="button">Retour</a>';
                    }
                    
                }else{
                    $content = '<h1 class="alert alert-danger">ERREUR : le tableau a été vidé</h1>';
                }
                
                } else {
                    $_SESSION['tabError'] =["id" => null, "mdp" => null,"confirm" => null];
                    $user = new User;
                    $content = userForm($user);
                }
            break;
        default :
            redirection("rea");
            break;
    } 
    
}else{
    if($admin->tokenExists()){
        $_SESSION['token'] = $_COOKIE['token'];
        redirection("rea");
    } else {

        if(isFormSubmitted()){
    
            $log = dataValidation("log");        
            if($log == null){
                $content = logForm($log, true);
            } else {
                redirection("rea");
            }
        } else {
            $content = logForm(null, false);
        }
    }
}

page($content);

if($page != "msg")
    $_SESSION['tabsErrors'][$page] = resetTabError($page);
