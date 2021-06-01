<?php

class ServiceManager{
    public function getConnection(): PDO{
        //$db = new PDO("mysql:host=localhost;dbname=bd-crud", "root", "root");
        $db = new PDO("mysql:host=localhost;dbname=lecomte", "lecomte", "Y7d8KuAo");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    public function create(Service $service): Service{
        $db = $this->getConnection();
        $request = $db->prepare("INSERT INTO `services` (`id-serv`, `intitule`, `image`) VALUES (NULL, :title, :image)");
       
        $request->execute([
            'title'=>$service->title,
            'image'=>$service->image
        ]);
        
        $service->id = $db->lastInsertId();

        return $service;
    }

    public function update(Service $service): Service{
        $db = $this->getConnection();
        if($service->image == null){
            $sql ="UPDATE services SET intitule = :title WHERE services.`id-serv` = :id";
            $tabValues = ["title"=>$service->title, "id"=>$service->id];
        } else {
            $sql = "UPDATE services SET intitule = :title, image =:image WHERE services.`id-serv` = :id";
            $tabValues = ["title"=>$service->title, "image"=>$service->image, "id"=>$service->id];
        }
        $request = $db->prepare($sql); 
        $request->execute( $tabValues);

        return $service;
    }

    public function delete(Service $service):Service{
        $db = $this->getConnection();
        $path = $_SESSION['pathUpload'].'serv/'.$service->image;
        unlink($path);

        $request = $db->prepare("DELETE FROM services WHERE services.`id-serv` = :id");
        $request->execute(["id"=>$service->id]);
        return $service;

    }

    public function getOne(int $id): Service{
        $db = $this->getConnection();
        $request = $db->prepare("SELECT * FROM services WHERE services.`id-serv` = :id");
        $request->execute(["id"=>$id]);
        

        $service = new Service;
        if($request->rowCount()>0)
        {   
            $result = $request->fetch();
            $service->id = $result['id-serv'];
            $service->title = $result['intitule'];
            $service->image = $result['image'];

        }else{
            $service->id = null;
             
        }

        return $service;
    }

    public function getAll(): Array {
        $db = $this->getConnection();
        $request = $db ->prepare("SELECT * FROM services");
        $request->execute();
        $result = $request->fetchAll();

        $services = [];
        foreach ($result as $line){
            $serv = new service();
            $serv->id = $line['id-serv'];
            $serv->title = $line['intitule'];
            $serv->image = $line['image'];

            $services[] = $serv;
        }

        return $services;
    }
}
