<?php

function isFormSubmitted():bool{
    return(isset($_GET['submit']));
}


function dataValidation($page){
    $regexTitle = "#^[\w -éèêëàâîïôùûüÿæœç]{1,30}$#";
    switch ($page) {
        case 'rea':
            $instance = new Realisation(); 

            if(isset($_POST['title']) ){
                if(preg_match($regexTitle, $_POST['title'])){
                    $instance->title = transformData($_POST['title']);
                }
            }

            if(isset($_POST['link'])){
                if(preg_match('#(https?://)([\w\d.&:\#@%/;$~_?\+\-=]*)#', $_POST['link'])){
                    $instance->link = transformData($_POST['link']);
                } 
            }
            //&& $instance->link !== null && $instance->title!== null
            if(isset($_FILES['picture'])  ){
                $intance = isValidImage($instance,"rea/");
            }
               
            break;
        case 'log':
            
            if(isset($_POST['id']) && !empty($_POST['pass']) ){
                $manager = new AdminManager();
                $instance = $manager->getOne($_POST['id']);

                if($instance !== null){
                    if(password_verify($_POST['pass'],$instance[0]['password'])){
                        $manager->defineToken($instance[0]['user_name']);

                        if($instance[0]["superuser"] == true){
                           $_SESSION['superuser'] = true;
                        }

                        redirection($page);
                    }
                    else {
                        $instance = null;
                    }
                }
            } else {
                $instance = null;
            }

            break;
        case 'addUser':
            $instance = new User;
            $_SESSION['tabError'] = ["id" => null, "mdp" => null,"confirm" => null];

            //Identifiant
            if(isset($_POST['id'])){
                $manager = new AdminManager();

                $id = transformData($_POST['id']);
                $id = strtolower($id);

                $regex= "#^[a-z][a-z0-9]{3,20}$#";
                if(preg_match($regex, $id)){
                    $exist = $manager->getOne($id); 
                    if($exist !== null){
                        $_SESSION['tabError']["id"] ="exist";
                    } else{
                        $instance->name = $id;
                    }
                } else{
                    $_SESSION['tabError']['id'] = "regex";
                }      
            } else {
                $_SESSION['tabError']['id'] = "empty";
            }

            //Mot de passe
            if(isset($_POST['pass']) && !empty($_POST['pass'])){
                $pass = transformData($_POST['pass']);

                $regex = "#^\w{3,20}$#";
                if(!preg_match($regex, $pass)){
                    $_SESSION['tabError']['mdp'] = 1;
                    
                } else {
                    $instance->pwr = $pass;
                    if(isset($_POST['confirm']) && !empty($_POST['confirm'])){
                        $confirm = transformData($_POST['confirm']);

                        if(strcmp($pass, $confirm) !== 0){
                            $_SESSION['tabError']['confirm'] = 1;
                        }
                    } else {
                        $_SESSION['tabError']['confirm'] = 1;
                    }
                }
            } else {
                $_SESSION['tabError']['mdp'] = "empty";
            }
            
            break;
        case 'service' :
            $instance = new Service;

            if(isset($_POST['title']) ){
                if(preg_match($regexTitle, $_POST['title']))
                $instance->title = transformData($_POST['title']);
            }
            
            if(isset($_FILES['picture'])){
                $intance = isValidImage($instance, "serv/");
            }
            break;
        default:
            $instance=[];
            break;
    }
    return $instance;
}


function transformData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}



function treatmentAction($page, $action, $message, $manager){
    $instance = dataValidation($page);
    
    /*Gestion des erreurs*/
    foreach($instance as $key => $val){
        if($key !=='id' && $val == null && !($key=="image" && $action =="update" )){
            $_SESSION['tabsErrors'][$page][$key]=true;
        } else {
            $_SESSION['tabsErrors'][$page][$key]=false;
        }
    }

    if($instance->image == "error"){
        $_SESSION['tabsErrors'][$page]['image']=true;
        //$instance->image = transformData($_POST['nomimg']);
    }

    if(in_array(true, $_SESSION['tabsErrors'][$page])){
        $instance->id = intval($_GET['submit']);

        if($page == "rea")
            $content = reaForm($instance, $action);
        else{
            $content = servForm($instance, $action);
        }
        $_SESSION['tabsErrors'][$page] = resetTabError($page);

    } else {
        if($action === "update"){
            
             $instance->id = $_GET['submit'];
             if($instance->image !== null && $action == "update"){
                $recup= $manager->getOne($instance->id);
                @unlink($_SESSION['pathUpload']."rea/".$recup->image);
             }
             $manager->update($instance);
             
             $content = '<div class="row justify-content-md-center mt-5">
                                <div class="col-8">
                                    <h1 class="alert alert-success">Modification enregistrée !</h1>
                                    <a href="./index.php?action=list&page='.$page.'" class="btn btn-info" role="button">Retour</a>
                                </div>
                            </div>';
            
        } else{
            $manager->create($instance);
            $content = '
            <h1 class="alert alert-success">'.$message.'</h1> 
            <a href="./index.php?action=list&page='.$page.'" class="btn btn-info" role="button">Retour</a>';
        }
        
    }
    return $content;
}

function isExist($tab){
    $keys = ["name", "firstName", "email", "message"];
    $exist = true;
    for ($i=0; $i < count($keys) ; $i++) { 
        if(!isset($tab[$keys[$i]])){
            $exist=false;
            break;
        }
    }

    return $exist;
}

function isValidImage($instance,$dos){
    $error = false;

   $newName = bin2hex(random_bytes(8));
   $legalExtentions = [".jpg", ".JPG", ".png", ".jpeg", "JPEG", ".gif"];
   $maxSize = "400000";

   $file = $_FILES['picture'];
   $actualName = $file['tmp_name'];
   $size = $file['size'];
   $extension = strrchr($file['name'],'.');

   if(empty($actualName) || $size == 0){
       $error = true;   
   }
   
   if(!$error)
   {
        if(in_array($extension, $legalExtentions) && $size <= $maxSize){
            /*Vérification de l'existant du nom du fichier sur le serveur*/
            while(file_exists($_SESSION['pathUpload'].$dos.$newName.$extension)){
                $newName = bin2hex(random_bytes(8));
            }
            move_uploaded_file($actualName, $_SESSION['pathUpload'].$dos.$newName.$extension);
            $instance->image = $newName.$extension;
        } else {
            @unlink($_SESSION['pathUpload'].$dos.$newName.$extension);

            if(isset($_POST['nomimg']) && !empty($_POST['nomimg'])){
                $instance->image = "error";
            }
        }
   } else {
        if(isset($_POST['nomimg']) && !empty($_POST['nomimg'])){
            $instance->image = transformData($_POST['nomimg']);
        }
   }

   return $instance;
}

function redirection($page){
    header('Location: ./index.php?action=list&page='.$page);
}


function validContact($tab):array{

    $tabRegex = [
        "email"=>"#^.+@.+\.[a-zA-Z]{2,}$#",
        "name" =>"#^[a-zA-Z]{1,30}$#", 
        "firstName" =>"#^[a-zA-Z]{1,30}$#", 
        "message"=>"#^.{1,100}$#"];

    foreach ($tab as $key => $elem) {
        if(!preg_match($tabRegex[$key], $elem)){
            $tab[$key]=null;
        }
    }
    return $tab;
}

function resetTabError($page){
    switch ($page) {
        case 'rea':
            $tab =["title"=>null, "link"=>null, "image"=>null];
            break;
        
        case 'service':
        $tab = ["title"=>null, "image"=>null];
            break;
        
        case "log" : 
            $tab = ["login"=>null, "pass"=>"null"];
            break;
    }

    return $tab;
}
