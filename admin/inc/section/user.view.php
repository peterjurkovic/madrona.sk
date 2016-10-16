<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
<div class="breadcrumb">
	Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?p=user">Správa užívateľov</a>
</div>
<strong class="h1">Správa užívateľov</strong>

<div class="left">
		<?php include dirname(__FILE__)."/user.nav.php" ?>
</div>

<div class="right">
	<form class="search">
    	<input type="text" name="q" id="user-login" />
        <input type="submit" class="ibtn"  value="Hladať" />
    </form>
		<div class="cbox">
            <strong class="h img article">Zoznam registrovaných užívateľov</strong>
             <?php
                $count = $conn->simpleQuery("SELECT count(*) FROM `user`");
                $count = $count[0]["count(*)"];
                $config['offset'] = ($s == 1 ? 0 :  ($s * $config["adminPagi"]) - $config["adminPagi"]);    
            ?>
            <table class="tc" id="dnd" >
              <thead>
                  <tr>
                    <th scope="col">&nbsp;ID</th>
                    <th scope="col">&nbsp;Login</th>
                    <th scope="col">&nbsp;Aktivný</th>
                    <th scope="col">&nbsp;Registrovaný</th>
                    <th scope="col">&nbsp;Typ užívateľa</th>
                    <th scope="col">&nbsp;Meno</th>
                    <th scope="col">&nbsp;Zmazať</th>
                  </tr>
              </thead>
              <tbody class="user">
                <?php  echo ( printUsers($conn, $config) ) ; ?>
             </tbody>
        </table>
        	<div class="clear"></div>
        </div>
        
        <?php
			if($uid != 0){
        	$data = $conn->select("SELECT `id_user`, `id_user_type`, `login`, `active`, `reg_time`, `email`, `givenname`, `surname`, `edit` FROM `user` WHERE `id_user`=?", array($uid));
			if($data == null){
				echo '<p class="error">Užívateľ s ID: '.$uid. ' neexistuje.</p>';
			}else{
			$data[0] = array_map("clean", $data[0]);
		?>
        
        <div class="cbox">
        	<strong class="h img profile">Úprava užívateľa: <?php echo $data[0]['login']; ?></strong>
            	
        		<span class="tinfo odd"> 
                    <strong>Registrovaný od: </strong> <?php echo strftime("%d.%m.%Y / %H:%M", $data[0]['reg_time']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Posledná zmena v profile: </strong> <?php if(isInt($data[0]['edit']))echo strftime("%d.%m.%Y / %H:%M", (int)$data[0]['edit']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                </span>

            <form name="user" class="ajax">
            	
                
                <div class="i">
                	<label>Typ: </label><select class="w200" name="id_user_type"><?php echo getOptions( $conn, "user_type", "name",   $data[0]['id_user_type'], ($_SESSION['type'] != 5 ? 5 : null)); ?></select>
                </div>
                
                <div class="i odd">
                	<label>Meno:</label><input  maxlength="35" type="text" class="w200" name="givenname" value="<?php echo $data[0]['givenname']; ?>" />
                </div>
                
                <div class="i">
                	<label>Priezvisko:</label><input  maxlength="35" type="text" class="w200" name="surname" value="<?php echo $data[0]['surname']; ?>" />
                </div>
                
                <div class="i odd">
                	<label>E-mail:</label><input  maxlength="35" type="text" class="w200 required email" name="email" value="<?php echo $data[0]['email']; ?>" />
                </div> 	
                
                <div class="i">
                	<label>Aktivita: </label><select class="w200" name="active"><?php 
					echo ($data[0]["active"] == 0 ? '<option value="0">Neumožniť prihlásenie</option><option value="1">Umožniť prihlásenie</option>' : '<option value="1">Umožniť prihlásenie</option><option value="0">Neumožniť prihlásenie</option>');?>
                    </select>
                </div>
                
                
                <div class="i odd">
                	<input type="hidden" value="<?php echo $uid; ?>" name="id" />
                    <input type="hidden" value="15" name="act" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
                
            </form>
        </div>
        <?php
				}
			}
		?>

</div>
<div class="clear"></div>


