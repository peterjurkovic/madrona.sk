<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
<div class="breadcrumb">Nachádzate sa:<a href="./index.php">Domov</a> &raquo;<a href="./index.php?p=article">Správa obsahu</a></div>
<strong class="h1">Správa obsahu</strong>

<div class="left">
		<strong class="h">Štruktúra web stránky</strong>
        <strong><a href="./index.php?p=article&amp;aid=0" class="root"><?php echo $_SERVER["SERVER_NAME"]; ?> </a></strong>
        <ul id="tree">
        <?php  echo articleTree($conn, 0, $aid); ?>	
        </ul>
</div>

<div class="right">
	<form class="search">
    	<input type="text" name="q" id="article-title_sk" />
        <input type="submit" class="ibtn"  value="Hladať" />
    </form>
	
    <div class="cbox">
        <strong class="h img article">Zoznam stránok v kategórii</strong>
        <div class="breadcrumb">
        Zobrazené: <a href="./index.php"><?php echo $_SERVER['SERVER_NAME']; ?></a>
        <?php echo articleAdminBC($conn, $aid ,"&raquo;"); ?>
        </div>
        <?php
            $count = count($conn->simpleQuery("SELECT `id_article` FROM `article` WHERE sub_id=".$aid));
            $config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);    
        ?>
        <table class="tc" id="dnd">
          <thead>
              <tr class="nodrop nodrag">
                <th scope="col">&nbsp;ID</th>
                <th scope="col">&nbsp;Názov</th>
                <th scope="col">&nbsp;Poradie</th>
                <th scope="col">&nbsp;Aktívna</th>
                <th scope="col">&nbsp;Zobrazená</th>
                <th scope="col">&nbsp;Vytvorená</th>
                <th scope="col">&nbsp;Zmazať</th>
              </tr>
          </thead>
          <tbody class="article">
            <?php echo ( printArticles($conn, $aid, $config)) ; ?>
         </tbody>
    </table>
    
        <a href="./index.php?p=article&amp;sp=new&amp;aid=<?php echo $aid; ?>" class="btn2 newPage" title="Pridať stránku do tejto sekcie">Pridať stránku do tejto sekcie</a>
        <?php 
        $nav = new Navigator($count, $s , '/index.php?'.preg_replace("/&s=[0-9]/", "", $_SERVER['QUERY_STRING']) , $config["adminPagi"]);
        $nav->setSeparator("&amp;s=");
        echo $nav->simpleNumNavigator();
        ?>
            <div class="box hidden">
                <input type="text" name="article" value="Názov stránky"  class="w250 clickClear"/>
                <a class="btn"  id="new" href="#aid<?php echo $aid; ?>" title="Pridat stranku ?">Uložiť</a>
            </div>
        <div class="clear"></div>
     </div>
        
        <!-- Article  -->
        <?php
			if($aid != 0){
				$data = $conn->select("SELECT a.`id_article`, a.`type`,a.`active`, a.`avatar1`, a.`avatar2`, a.`avatar3`, a.`editor`, a.`edit`, a.`create`, a.`hits`, a.`title_sk`, a.`subtitle_sk`, a.`header_sk`, a.`content_sk`, u.`login`
					FROM  `article` a
					INNER JOIN `user` u
					ON a.`id_user`=u.`id_user`
					WHERE a.`id_article`=? 
					LIMIT 1", array($aid ));
				if($data == null){
					echo '<p class="error">Stránka s ID: '.$aid. ' neexistuje.</p>';
				}else{
				$data[0] = cleanArticle($data[0]);
		?>
        
        <div class="cbox">
        	<strong class="h img aedit">Editácia obsahu</strong>
            	
        		<span class="tinfo odd"> 
                	<?php $editor = getUserById($conn, (int)$data[0]["editor"], "login");   ?>
                    <strong>Vytvorená: </strong> <?php echo strftime("%d.%m.%Y / %H:%M", $data[0]['create']).' / '.$data[0]["login"]; ?> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Upravená: </strong> <?php if(isset($editor["login"]))echo strftime("%d.%m.%Y / %H:%M", $data[0]['edit']).' / '.$editor["login"]; else echo " - "?> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Zobrazená:</strong> <?php echo $data[0]["hits"]; ?>x
                </span>

            <form name="article">
            	
                
                <div class="i">
                	<label>Typ: </label><select class="w200" name="type"><?php echo getOptions( $conn, "article_type", "name",  0, 0,$data[0]['type']); ?></select>
                </div>
                
                <div class="i odd">
                	<label>Jazyky: </label><div class="langs"><?php echo printLangs( $config, $aid ); ?></div>
                </div>
                
                 <div class="i">
                	<label>Názov stránky:</label><input  maxlength="200" type="text" class="w400" name="title_" value="<?php echo $data[0]['title_sk']; ?>" />
                </div>

                <div class="i odd">
                	<label>Podnadpis:</label><input  maxlength="255" type="text"  class="w400" name="subtitle_" value="<?php echo $data[0]['subtitle_sk']; ?>" />
                </div>
                
                <div class="i">
                	<label>Aktivita: </label><select class="w200" name="active"><?php 
					echo ($data[0]["active"] == 0 ? '<option value="0">Nezobrazovať</option><option value="1">Zobrazovať</option>' : '<option value="1">Zobrazovať</option><option value="0">Nezobrazovať</option>');?>
                    </select>
                </div>
                
                 <div class="i odd">
                	<label>Uvodný text:</label><textarea name="header_" rows="5" cols="63" class="w520"><?php echo $data[0]['header_sk']; ?></textarea>
                </div>
                
                <div class="i">
                	<strong class="label">&darr; Obsah stránky &darr;</strong>
                    <textarea name="content_" id="editor1" rows="10" cols="83"></textarea>
                    <div id="content"></div>
                     <div id="data" class="hidden"><?php echo $data[0]["content_sk"]; ?></div>
                        <div id="ContentEditor"></div>
                          <script type="text/javascript">
                          	var editor = CKEDITOR.replace( 'editor1' );				
							editor.setData(document.getElementById("data").innerHTML);
							CKFinder.setupCKEditor( editor, './ckfinder/' ) ;
                          </script>
                          
                          
                </div>
                
                <div class="i odd">
                	<input type="hidden" value="<?php echo $aid; ?>" name="id" />
                    <input type="hidden" value="sk" name="lang" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
                
            </form>
        </div>
        
         <!-- 
          <div class="cbox">
        	<strong class="h img aedit">Klúčové slová</strong>
            <input type="text" class="w520" name="keywords" />
            <a class="btn" id="kwd" href="#aid<?php echo $aid; ?>">Uložiť</a>
          </div>
          -->
         
          
          
          <div class="cbox">
        	<strong class="h img av">Avatáry</strong>
            	<form name="avatars" id="avatars" action="./inc/ajax.post.php" method="post" enctype="multipart/form-data">
                   <div class="wrp">
                        
                        <div class="ibox">
                            <strong>Avatar 1</strong>
                            	
                            <div id="avatar1">
                                    <?php 
                                       if($data[0]['avatar1'] != "") { 
                                       		 echo '<a href="../../data/avatars/'.$data[0]['avatar1'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               					  '<a href="#id'.$aid.'" title="article#avatar1#'.$data[0]['avatar1'].'" class="del hidden"></a>'. 
											 	  '<img src="./inc/img.php?url=../../data/avatars/'.$data[0]['avatar1'].'&amp;w=100&amp;h=100&amp;type=crop"  class="img" alt="" />';
                                        }else{
											echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrazok." />';
										}
                                    ?> 
                                  
                            </div>
                                <input name="avatar1" class="f" type="file" maxlength="45" />
                          </div>
                        
                         <div class="ibox"> 
                            <strong>Avatar 2</strong>
                             <div id="avatar2">
                                    <?php 
                                        if($data[0]['avatar2'] != "") { 
                                        	echo '<a href="../../data/avatars/'.$data[0]['avatar2'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               					  '<a href="#id'.$aid.'" title="article#avatar2#'.$data[0]['avatar2'].'" class="del hidden"></a>'. 
											 	  '<img src="./inc/img.php?url=../../data/avatars/'.$data[0]['avatar2'].'&amp;w=100&amp;h=100&amp;type=crop" class="img" alt="" />';
                                        }else{
											echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrazok." />';
										}
                                        
                                    ?> 
                              </div>
                              <input name="avatar2" class="f" type="file" maxlength="45" />
                        </div>
                        
                         <div class="ibox"> 
                            <strong>Avatar 3</strong>
                             <div id="avatar3">
                                    <?php 
                                        if($data[0]['avatar3'] != "") { 
                                        	echo '<a href="../../data/avatars/'.$data[0]['avatar3'].'" title="Zobraziť obrázok" class="show hidden"></a>'.
                               					  '<a href="#id'.$aid.'" title="article#avatar3#'.$data[0]['avatar3'].'" class="del hidden"></a>'. 
											 	  '<img src="./inc/img.php?url=../../data/avatars/'.$data[0]['avatar3'].'&amp;w=100&amp;h=100&amp;type=crop" class="img" alt="" />';
                                        }else{
											echo '<img src="./img/noavatar.png" alt="Nie je nahratý obrazok." />';
										}
                                        
                                    ?> 
                              </div>
                              <input name="avatar3" class="f" type="file" maxlength="45" />
                        </div>
                        <div class="clear"></div>
                    </div>
                    
            	
                
                <input type="hidden" value="<?php echo $aid; ?>" name="id" />
                <input type="hidden" value="article" name="table" />
                <input type="hidden" value="10" name="act" />
                
                <input type="submit" class="ibtn2" value="Nahrať" /><img src="./img/ajax-loader.gif"  class="loader" alt="Nahrávam..." />
                </form>
                 <div class="clear"></div>
          </div>
   
           <div class="cbox">
           		<strong class="h img ga">Galéria</strong>
                <form  id="uploader" name="gallery" action="./inc/ajax.post.php" method="post" enctype="multipart/form-data">
                    <input name="img1" class="f" type="file" maxlength="45" />
                    <input name="img2" class="f" type="file" maxlength="45" />
                    <input name="img3" class="f" type="file" maxlength="45" />
                    <input name="img4" class="f" type="file" maxlength="45" />
                    <input name="img5" class="f" type="file" maxlength="45" />
                    <input type="hidden" value="<?php echo $aid; ?>" name="id" />
                    <input type="hidden" value="gallery" name="dirName" />
                    <input type="hidden" value="13" name="act" />
                    <input type="submit" class="ibtn2" value="Nahrať" /><img src="./img/ajax-loader.gif"  class="loader" alt="Nahrávam..." />
                </form>
                
                <div id="gallery"  class="clear">
                	<?php echo gallery($config, $aid);  ?>             
                </div>
                <div class="clear"></div>
           </div>
                
        <?php
				}
			}
            ?>

    
</div>
<div class="clear"></div>



