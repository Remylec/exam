<?php

class ContactMsgManager{
    public function getConnection() :PDO{
        //$db = new PDO("mysql:host=localhost;dbname=bd-crud", "root", "root");
        $db = new PDO("mysql:host=localhost;dbname=lecomte", "lecomte", "Y7d8KuAo");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }



    public function getAll(): Array {
        $db = $this->getConnection();
        $request = $db ->prepare("SELECT * FROM contactMsg");
        $request->execute();
        $result = $request->fetchAll();

        $messages = [];
        foreach ($result as $line){
            $msg = new ContactMsg();
            $msg->id = $line['id_cm'];
            $msg->nom = $line['nom'];
            $msg->prenom = $line['prenom'];
            $msg->email = $line['email'];
            $msg->message = $line['message'];

            $messages[] = $msg;
        }

        return $messages;
    }

    public function getOne(int $id): ContactMsg{
        $db = $this->getConnection();
        $request = $db->prepare("SELECT `id_cm` FROM contactmsg WHERE contactmsg.`id_cm` = :id");
        $request->execute(["id"=>$id]);
        
        $message = new ContactMsg;
        if($request->rowCount()>0)
        {   
            $result = $request->fetch();
            $message->id = $result['id_cm'];

        }else{
            $message->id = null;
        }

        return $message;
    }

    public function create(ContactMsg $msg): ContactMsg{
        $db = $this->getConnection();
        $request = $db->prepare("INSERT INTO contactMsg (id_cm,nom,prenom,email,message) VALUES (null,:nom, :prenom, :email,:message)");
        $request->execute([
            'nom'=>$msg->name,
            'prenom'=>$msg->firstName,
            'email'=>$msg->email,
            'message'=> $msg->message
        ]);

        $msg->id = $db->lastInsertId();

        return $msg;
    }

    public function delete(ContactMsg $message): void{
        $db = $this->getConnection();

        $request = $db->prepare("DELETE FROM `contactmsg` WHERE `contactmsg`.`id_cm` = :id");
        $request->execute(["id"=>$message->id]);

    }
}
