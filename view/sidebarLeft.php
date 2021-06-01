<?php
function getSidebarLeft(){
  $sidebar ='
 <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php" class="brand-link">
      <img src="./assets/dist/img/logoR.png"
           alt="Portfolio RL Logo"
           class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">Admin - RL Portfolio</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="col-sm-12 my-auto">';

        if($_SESSION['token'] != null){
          $sidebar.='
          <a href="index.php?action=disconnect" class="d-block text-center">'.$_SESSION['token'].' déconnexion <i class="right fas fa-angle-right"> </i></a>';
        }
        
$sidebar.='
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
            with font-awesome or any other icon font library -->';
          
          if($_SESSION['superuser'] == true){
            $sidebar.='
              <li class="nav-item has-treeview">
                <a href="index.php?action=addUser&page=addUser" class="nav-link">
                  <i class="nav-icon fas fa-edit"></i>
                  <p>
                    Ajouter un utilisateur
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
              </li>';
          }
          
          if($_SESSION['token'] !=false){
            $nbMessage = recupNbMsg();
            $sidebar.='
          <li class="nav-item has-treeview">
            <a href="index.php?action=list&page=msg" class="nav-link">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Messages reçus
                <i class="fas fa-angle-left right"></i>
                <span class="badge badge-info right">'.$nbMessage.'</span>
              </p>
            </a>
          </li>';
          }
          
          $sidebar.='
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>';
  return $sidebar;
}
