<?php


/*=========== FONCTIONS SQUELETTES HTML =========== */

function formSkeleton($title, $content){
  $form ='
  <div class="row justify-content-md-center mt-5">
    <div class="col-8">
      <div class="card primary mt-3">
        <div class="card-header">
          <h1 class="h1">'.$title.'</h1>
        </div>
        <div class="card-body table-responsive p-3">
        '.$content.'
        </div>
      </div>
    </div>
  </div>
  ';

  return $form;
}

function tabSkeleton($title, $tHead, $tBody, $moreContent){
  $tab = '
  <div class="row">
      <div class="col-12">
          <div class="card">
              <!-- /.card-header -->
              <div class="card-header">
                <h3 class="card-title">'.$title.'</h3>
              ';
              if($moreContent !== null){
                $tab.=$moreContent;
              }
              $tab.='
              </div>
              <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap">
                    <thead>'.$tHead.'</thead>

                    <tbody>'.$tBody.'</tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>
  ';
  return $tab;
}

/*=========== FONCTIONS FORMULAIRES =========== */
function logForm($id, bool $error){
  
  $form = '
     <form action="./index.php?submit=1" method="POST">
            <div class="form-group">
                <label for="id">Identifiant</label>
                <input type="text" class="form-control" id="id" name="id" value="'.$id.'">
            </div>

            <div class="form-group">
                <label for="id">Mot de passe</label>
                <input type="password" class="form-control" id="pass" name="pass">
            </div>';

            if($error === true){
              $form.='<p class="alert alert-danger" role="alert">Association identifiant/mot de passe incorrecte</p>';
            }
  $form.='  <div class="form-group">
              <label for="remember_me">Remember me</label>
              <input type="checkbox" id="remember_me" name="rememberMe" value="1">
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
          </form>';
  
  $form = formSkeleton("Identification", $form);
  return $form;
}

function reaForm(Realisation $rea, $action) {

  if($rea->id == null){
    $id=0;
  } else {
    $id = $rea->id;
  }

  $form='
  <form enctype="multipart/form-data" action="./index.php?action='.$action.'&submit='.$id.'&page=rea" method="POST">
      <div class="form-group">
        <label for="title">Titre</label>
        <input type="text" class="form-control" id="title" name="title" value="'.$rea->title.'" >';

     if($rea->title == null && $_SESSION['tabsErrors']['rea']['title']){
         $form.='<p class="alert alert-danger" role="alert">Titre manquant ou trop grand (30 caractères max)</p>';
     }
  $form.='
      </div>
      <div class="form-group">
          <label for="link">Lien</label>
          <input type="text" class="form-control" id="link" name="link" value="'.$rea->link.'">';

          if($rea->link == null && $_SESSION['tabsErrors']['rea']['link']){
              $form.='<p class="alert alert-danger" role="alert">Lien vers la réalisation manquant ou incorrect</p>';
          }
  $form.=
    '</div>
    <div class="form-group">
      <label for="file">image</label>';
      if($action == "update" && $rea->image !== null){
        $form.='<div>
                  <img class="w-50" src="'.$_SESSION['pathUpload']."rea/".$rea->image.'" alt="projet image">
                  <div><em>Ne rien importer si vous gardez la même image</em></div>
                </div>';
      }
      $form.='
      <input type="hidden" name="nomimg" value="'.$rea->image.'">
      <input type="hidden" name="MAX_FILE_SIZE" value="400000">
      <input type="file" class="form-control" id="file" name="picture">
    </div>';
    if($rea->image == null && $_SESSION['tabsErrors']['rea']['image'] && $action != "update"){
      $form.='<p class="alert alert-danger" role="alert">Insérez une image (.png, .jpg, .jepg ou .gif) - Taille max : 400ko</p>';
    }

    $form.='
      <button type="submit" class="btn btn-primary">Submit</button>';
      if($action ==="update"){
          $form.= '
          <a href="./index.php?action=list" class="btn btn-info" role="button">Retour</a>';
      }
  $form.='
  </form>';
  
  if($action === "add"){
    $title = "Ajouter réalisations";
  } else {
    $title = "Modifier réalisation";
  }
 
  $form = formSkeleton($title, $form);
  
  return $form;
}

function servForm(Service $serv, $action) {
  
  if($serv->id == null){
    $id=0;
  } else {
    $id = $serv->id;
  }

  $form='
  <form enctype="multipart/form-data" action="./index.php?action='.$action.'&submit='.$id.'&page=service" method="POST">
      <div class="form-group">
        <label for="title">Intitulé du service</label>
        <input type="text" class="form-control" id="title" name="title" value="'.$serv->title.'" >';

     if($serv->title == null && $_SESSION['tabsErrors']['service']['title']){
         $form.='<p class="alert alert-danger" role="alert">Pas de nom ou nom trop long</p>';
     }
  $form.='
      </div>
    <div class="form-group">
      <label for="file">image</label>';
      
      if($action == "update" && $serv->image !== null){
        $form.='<div>
                  <img class="w-50" src="'.$_SESSION['pathUpload']."serv/".$serv->image.'" alt="Illustration service">
                  <div><em>Ne rien importer si vous gardez la même image</em></div>
                </div>';
      }
      $form.='
      <input type="hidden" name="nomimg" value="'.$serv->image.'">
      <input type="hidden" name="MAX_FILE_SIZE" value="400000">
      <input type="file" class="form-control" id="file" name="picture">
    </div>';
    if($serv->image == null && $_SESSION['tabsErrors']['service']['image'] && $action != "update"){
      $form.='<p class="alert alert-danger" role="alert">Insérez une image (.png, .jpg, .jpeg ou .gif) - Taille max : 400ko</p>';
    }

    $form.='
      <button type="submit" class="btn btn-primary">Submit</button>';
      if($action ==="update"){
          $form.= '
          <a href="./index.php?action=list&page=service" class="btn btn-info" role="button">Retour</a>';
      }
  $form.='
  </form>';
  
  if($action === "add"){
    $title = "Ajouter service";
  } else {
    $title = "Modifier service";
  }
  $form = formSkeleton($title, $form);
  return $form;
}

function userForm(User $user ){
  $error = $_SESSION['tabError'];

  $form = '
  <form action="./index.php?action=addUser&submit=1" method="POST">
    <div class="form-group">
      <label for="id">Identifiant (Entre 4 et 20 caractères (lettres et/ou chiffres uniquement)</label>
      <input type="text" class="form-control" id="id" name="id" value="'.$user->name.'">';

      if($user->name==null && $error["id"] !== null){
        switch ($error["id"]) {
          case 'exist':
            $mesage='Nom déjà existant !';
            break;
          case 'regex' :
            $mesage='L\'identifiant n\'est pas conforme';
            break;
          default:
            $mesage='Identifiant manquant !';
            break;
        }
        $form.='<p class="alert alert-danger" role="alert">'.$mesage.'</p>';
      }
    $form.='</div>

    <div class="form-group">
      <label for="pass">Mot de passe (uniquement lettres et chiffres).</label>
      <input type="password" class="form-control" id="pass" name="pass" value="'.$user->pwr.'">';
      if($user->pwr== null && $error['mdp'] !== null){
        $form.='<p class="alert alert-danger" role="alert">Min 4 - Max 20 caractères (pas de caractères spéciaux)</p>';
      }

    $form.='</div>

    <div class="form-group">
      <label for="confirm">Confirmation mot de passe</label>
      <input type="password" class="form-control" id="confirm" name="confirm">';

      if($error['confirm'] !== null){
        $form.='<p class="alert alert-danger" role="alert">La confirmation n\'est pas identique au mot de passe</p>';
      }

    $form.='</div>
    <button type="submit" class="btn btn-primary">Envoyer</button>
  </form>
  ';

  $form = formSkeleton("Ajouter utilisateur", $form);
  
  return $form;
}

function confDelete($id){
  $conf = '<div class="row justify-content-md-center mt-5">
              <div class="col-8">
                <div class="card primary">
                  <divclass="card-header">
                    <h1>Confirmer la supression</h1>
                  </div>

                  <div class="card-body table-responsive p-0">
                    <a href="./index.php?action=delete&conf='.$id.'" class="btn btn-info" role="button">OUI</a>
                    <a href="./index.php?action=list" class="btn btn-info" role="button">NON</a>
                  </div>
                </div>
              </div>
            </div>';
  return $conf;
}


/*=========== FONCTIONS LISTES - Tableaux =========== */
function listMsg(Array $messages){
  $tHead='
  <tr>
    <th>Personne</th>
    <th>Email</th>
    <th>Message</th>
    
  </tr>';

  $tBody="";
  foreach($messages as $msg){  
    $dateTime = new DateTime($msg->dateHeure);  
    $tBody.='
    <tr>
      <td>'.$msg->nom.' '.$msg->prenom.'</td>
      <td>'.$msg->email.'</td>
      <td>'.$msg->message.'</td>';

      if($_SESSION['superuser']==1){
        $page="msg";
        $tBody.='<td id="btn'.$msg->id.'"><button class="btn btn-info" onClick="afficher(\''.$page.'\','.$msg->id.')">Supprimer</button></td>';
      }
  $tBody.='
    </tr>';
  }
 
  return tabSkeleton("Message",$tHead, $tBody,null);
}

function listRea(Array $realisations) {
    $more = '
    <div class="card-tools">
      <div class="input-group input-group-sm" style="width: 150px;">
        <a class="btn btn-primary" href="?action=add&page=rea">Create</a>
      </div>
    </div>';

    $tHead = '
      <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Lien</th>
        <th>Image</th>
      </tr>';

    $tBody = "";
        foreach ($realisations as $realisation){
            $tBody .= 
            '<tr>
               <td>'.$realisation->id.'</td>
               <td>'.$realisation->title.'</td>
               <td>'.$realisation->link.'</td>
               <td>'.$realisation->image.'</td>';

               if($_SESSION['superuser']==1){
                $page="rea";
                $tBody .='
                <td> <a href="index.php?action=update&page=rea&id='.$realisation->id.'" class="btn btn-info" role="button">Modifier</a></td>
                <td id="btn'.$realisation->id.'"><button class="btn btn-info" onClick="afficher(\''.$page.'\','.$realisation->id.')">Supprimer</button></td>';
               }
    $tBody .=
            '</tr>';
  }


    return tabSkeleton("Réalisation", $tHead, $tBody, $more);
}

function listServ(Array $services){
  $more = '
    <div class="card-tools">
      <div class="input-group input-group-sm" style="width: 150px;">
        <a class="btn btn-primary" href="?action=add&page=service">Create</a>
      </div>
    </div>';
    
  $tHead = '
  <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Image</th>
  </tr>';

  $tBody = '';
  foreach ($services as $key => $service) {
    $tBody.='
    <tr>
      <td>'.$service->id.'</td>
      <td>'.$service->title.'</td>
      <td>'.$service->image.'</td>';
    if($_SESSION['superuser']==1){
      $tBody .='
      <td> <a href="index.php?action=update&page=service&id='.$service->id.'" class="btn btn-info" role="button">Modifier</a></td>
      <td id="btn'.$service->id.'"><button class="btn btn-info" onClick="afficher(\'service\','.$service->id.')">Supprimer</button></td>';
     }
     $tBody.='
    </tr>';
  }

  return tabSkeleton("Service", $tHead, $tBody, $more);
}


/*=========== FONCTION PAGE PRINCIPALE =========== */
function page($content){
  require_once('view/head.php');
  require_once('view/header.php');
  require_once('view/footer.php');
  require_once('view/sidebarLeft.php');

  $sidebarLeft = getSidebarLeft();
    echo '<!DOCTYPE html>
  <html>'.$head.'
    <body class="hold-transition sidebar-mini">
      <main class="wrapper">
        '.$header.$sidebarLeft.'

        <!-- Content Wrapper. Contains page content -->
        <section class="content-wrapper">
      
        '.$content.'
        </section>
        <!-- /.content-wrapper -->
        '.$footer.'

        <!-- Control Sidebar -->
          <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
          </aside>
        <!-- /.control-sidebar -->
      </main>
      <!-- ./wrapper -->
      
      <script src="assets/dist/js/myScript.js"> </script>
    </body>
  </html>';
}

function recupNbMsg(){
  $manager= new ContactMsgManager;
  $ng = count($manager->getAll());
  return $ng;
}
