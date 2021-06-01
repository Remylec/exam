<?php

class RealisationManager
{

    public function getConnection(): PDO{
        //$db = new PDO("mysql:host=localhost;dbname=bd-crud", "root", "root");
        $db = new PDO("mysql:host=localhost;dbname=lecomte", "lecomte", "Y7d8KuAo");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }

    public function create(Realisation $realisation): Realisation{
        $db = $this->getConnection();
        $request = $db->prepare("INSERT INTO realisation (title,link,image) VALUES (:title, :link, :image)");
        $request->execute([
            'title'=>$realisation->title,
            'link'=>$realisation->link,
            'image'=>$realisation->image
        ]);

        $realisation->id = $db->lastInsertId();

        return $realisation;
    }

    public function update(Realisation $realisation): Realisation{
        $db = $this->getConnection();
        if($realisation->image == null){
            $sql ="UPDATE realisation SET title = :title, link = :link WHERE realisation.id = :id";
            $tabValues = ["title"=>$realisation->title, "link"=>$realisation->link, "id"=>$realisation->id];
        } else {
            $sql ="UPDATE realisation SET title = :title, link = :link, image =:image WHERE realisation.id = :id";
            $tabValues = ["title"=>$realisation->title, "link"=>$realisation->link,"image"=>$realisation->image, "id"=>$realisation->id];
        }
        $request = $db->prepare($sql); 
        $request->execute( $tabValues);

        return $realisation;
    }

    public function delete(Realisation $realisation):Realisation{
        $db = $this->getConnection();
        
        unlink($_SESSION['pathUpload']."/rea/".$realisation->image);

        $request = $db->prepare("DELETE FROM realisation WHERE realisation.id = :id");
        $request->execute(["id"=>$realisation->id]);
        return $realisation;

    }

    public function getOne(int $id): Realisation{
        $db = $this->getConnection();
        $request = $db->prepare("SELECT * FROM realisation WHERE realisation.id = :id");
        $request->execute(["id"=>$id]);
        

        $realisation = new Realisation();
        if($request->rowCount()>0)
        {   
            $result = $request->fetchAll();
            $realisation->id = $result[0]['id'];
            $realisation->link = $result[0]['link'];
            $realisation->title = $result[0]['title'];
            $realisation->image = $result[0]['image']; 
        }else{
            $realisation->id = null;
        }

        return $realisation;
    }

    public function getAll(): Array {
        $db = $this->getConnection();
        $request = $db ->prepare("SELECT * FROM realisation");
        $request->execute();
        $result = $request->fetchAll();

        $realisations = [];
        foreach ($result as $line){
            $rea = new Realisation();
            $rea->id = $line['id'];
            $rea->link = $line['link'];
            $rea->title = $line['title'];
            $rea->image = $line['image'];

            $realisations[] = $rea;
        }

        return $realisations;
    }




}
