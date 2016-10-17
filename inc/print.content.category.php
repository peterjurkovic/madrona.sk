
<?php

  function image($name, $link, $title){
    if(strlen($name) != 0){
        return '<a class="ni" href="'.$link.'" title="'.$title.'"><img src="/i/150-150-crop/avatars/'.$name.'" alt="'.$title.'" /></a>';
    }else{
        return '<a class="ni" href="'.$link.'" title="'.$title.'"><img src="/img/no.jpg" alt="'.$title.'" /></a>';
    }
  }

	 $arr = getArticle("categ", $meta["id_article"]);

    for($i=0; $i < count($arr); $i++){
      $link = linker($arr[$i]["id_article"], 1);
    
    ?>
     <div class="col-lg-12 category">
          <h3><a href="<?php echo $link; ?>"><?php echo $arr[$i]["title_${lang}"]; ?> &raquo;</a></h3>
          <?php 
            if(strlen($arr[$i]["header_${lang}"]) > 0){
              echo '<p>'.$arr[$i]["header_${lang}"].'</p>';
            }
          ?>
      </div>
     <?php
  }

?>
