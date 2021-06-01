<?php
/*=== HEADER ===*/
if($_SESSION['token'] != false){
  $header = '
    <header>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left navbar links -->
      <ul class="navbar-nav">
      <!--<li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i></a>
        </li>-->
        <li class="nav-item d-none d-sm-inline-block">
          <a href="./index.php?page=rea" class="nav-link">Réalisations</a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
          <a href="./index.php?page=service" class="nav-link">Services</a>
        </li>

      </ul>
      <div class="navbar-nav ml-auto">
  
      </div>
    </nav>
    <!-- /.navbar -->
    </header>';
} else {
  $header='<nav class="main-header navbar navbar-expand navbar-white navbar-light"></nav>';
}
