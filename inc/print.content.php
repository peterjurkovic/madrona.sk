<?php
	
	function pageGallery($id, $title){
		$file = new File();
		$images = $file->scanFolder("data/gallery/$id/");

		if(count($images) != 0){
			return '<div id="gallery">'.printPageImages($images, $id, 100,  $title).'</div>';
		}
	}
	$article = getArticle("full", $meta["id_article"]);
?>
<section class="container main shaddow-gray">
<?php
	if($meta['sub_id'] != 0){
		echo '<ul class="breadcrumb">'.bc($meta['id_article'], '&raquo;').'</ul>';
	}
?>
  <h1><?php echo $article[0]["title_${lang}"]; ?></h1>
  <article>
		<?php
			if($meta["id_article"] == 18){
		      include "print.content.contact.php";
		    }else if($meta["type"] == 2){
		      include "print.content.category.php";
		    }else{
		      echo $article[0]["content_${lang}"].pageGallery($meta["id_article"], $article[0]["title_${lang}"]);
		    }    
		?> 
  </article>
</section>