<?php

class AdminManager {
    public function getConnection() :PDO{
       $db = new PDO("mysql:host=localhost;dbname=bd-crud", "root", "root");
        //$db = new PDO("mysql:host=localhost;dbname=lecomte", "lecomte", "Y7d8KuAo");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    public function getDisconnection() :void{
        unset($_SESSION['token']);
        unset($_SESSION['superuser']);
        unset($_SESSION['tabError']);
        unset($_SESSION['tabsErrors']);
        unset($_COOKIE['token']);
        setcookie('token', '', time()-3600);
        session_destroy();
        header('Location: index.php');
    }

    public function getOne($uName) {
        $db = $this->getConnection();
        $request = $db->prepare("SELECT * FROM users where user_name=:id");
        $request->execute(["id"=>$uName]); 

        if($request->rowCount() === 0){
            $result = null;
        } else {
            $result = $request->fetchAll();
        }

        return $result;
    }

    public function create(User $user){
        $db = $this->getConnection();
        $request = $db->prepare("INSERT INTO users (user_name, password) VALUES (:pseudo, :mdp)");
        
        $pass= password_hash($user->pwr, PASSWORD_DEFAULT);
        $request->execute(["pseudo"=>$user->name, "mdp"=>$pass]);
    }

    public function isRememberMe(){
        return isset($_POST['rememberMe']) && $_POST['rememberMe'] === "1";
    }
    
    public function tokenExists() :bool {
        return isset($_COOKIE['token']) && !empty($_COOKIE['token']);
    }
    
    public function defineToken($name)  {
        if ($this->isRememberMe()){
            $options = array ('expires' => time () + 6800, 'path' => '/', 'secure' => FALSE, 'httponly' => TRUE, 'samesite' => 'Strict');

            SetCookie ('cookie', $name, $options);
            
        }
        $_SESSION['token'] = $name;

    }
    
}
