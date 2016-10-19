
<?php

  function image($name, $link, $title){
    if(strlen($name) != 0){
        return '<a class="ni" href="'.$link.'" title="'.$title.'"><img src="/i/100-100-crop/avatars/'.$name.'" alt="'.$title.'" /></a>';
    }else{
        return '<a class="ni" href="'.$link.'" title="'.$title.'"><img src="/img/no.jpg" alt="'.$title.'" /></a>';
    }
  }

	 $arr = getArticle("categ", $meta["id_article"]);

    for($i=0; $i < count($arr); $i++){
      $link = linker($arr[$i]["id_article"], 1);
    
    ?>
     <div class="row reference">
          <div class="col-lg-2 col-md-3">
            <?php echo image($arr[$i]["avatar1"], $link, $arr[$i]["title_${lang}"]); ?>
          </div>
          <div class="col-lg-10 col-md-9">
            <h3><a href="<?php echo $link; ?>"><?php echo $arr[$i]["title_${lang}"]; ?> &raquo;</a></h3>
          </div>
      </div>
     <?php
  }

?>
