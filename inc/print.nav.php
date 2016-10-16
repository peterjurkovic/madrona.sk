<div class="main-nav">
    <div class="container shaddow-nav">
     <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <?php   
                $nav = printMenu(0, "", $meta['id_article'], true);
                echo $nav;  
            ?>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
  </div>
</div>