<?php
  if(!isset($_GET['s']))  { $_GET['s'] = 1; }else{  $_GET['s'] = intval( $_GET['s'] ); }
  
  require_once "admin/config.php";
  require_once "admin/inc/fnc.main.php";
  require_once "admin/page/fnc.page.php";
    
  try{
    $lang = "sk";
    $conn = Database::getInstance($config['db_server'], $config['db_user'], $config['db_pass'], $config['db_name']);
    $meta = MAIN();
  }catch(MysqlException $e){
    echo $e->getMessage();
  } 
  if(intval($meta['c_status']) == 0){
    die($meta['c_offline_msg']);
  }
  
// print_r($meta);
 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $meta["title_${lang}"] . " - " . $meta["c_name"]; ?></title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/lightbox.min.css" rel="stylesheet">
    <link href="/css/main.css" rel="stylesheet">
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php include './inc/print.ga.php';  ?>
  </head>
  <body>
  
  <!-- Header -->
  <header>
    <div class="container shaddow-gray">
      <a href="/" id="logo" title="Madrona, s.r.o, Predaj farieb pre priemysel a stavebnictvo"></a>
    </div>
  </header> 

  <?php 
    include './inc/print.nav.php'; 
    include './inc/print.header.php'; 
    include './inc/print.content.php'; 
    include './inc/print.footer.php'; 
  ?>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="/js/lightbox.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/script.js"></script>
  </body>
</html>