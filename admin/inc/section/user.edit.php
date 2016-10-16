<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
<div class="breadcrumb">
	Nachádzate sa:
	<a href="./index.php">Domov</a> &raquo;
    <a href="./index.php?p=user">Správa užívateľov</a> &raquo;
    <a href="./index.php?p=user">Úprava profilu</a>
</div>
<strong class="h1">Správa užívateľov</strong>

<div class="left">
		<?php include dirname(__FILE__)."/user.nav.php" ?>
</div>

<div class="right">       
        <?php
			if($uid != 0){
        	$data = $conn->select("SELECT * FROM `user` WHERE `id_user`=? LIMIT 1", array($uid));
			if($data == null){
				echo '<p class="error">Užívateľ s ID: '.$uid. ' neexistuje.</p>';
			}else{
			$data[0] = array_map("clean", $data[0]);

		?>
        
        <div class="cbox">
        	<strong class="h img profile">Úprava užívateľa: <?php echo $data[0]['login']; ?></strong>
            	
        		<span class="tinfo odd"> 
                    <strong>Registrovaný od: </strong> <?php echo strftime("%d.%m.%Y / %H:%M", $data[0]['reg_time']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>Posledná zmena v profile: </strong> <?php if(strlen($data[0]['edit']) != 0)echo strftime("%d.%m.%Y / %H:%M", $data[0]['edit']); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                </span>

            <form name="user"  class="ajax">
            	
                
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
                	<input type="hidden" value="15" name="act" />
                	<input type="hidden" value="<?php echo $uid; ?>" name="id" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
                
            </form>
        </div>
        
        
        <div class="cbox">
        	<strong class="h img profile">Zmena prihlasovacieho hesla: <?php echo $data[0]['login']; ?></strong>

            <form name="pass"  class="ajax">
            	<p class="info">  Heslo musí mať minimálne 5 znakov.</p>
            	
                <div class="i odd">
                	<label>Súčastné heslo:</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="oldpass" />
                </div>
                
                 <div class="i">
                	<label>Nové heslo:</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="newpass1" />
                </div>
                
                 <div class="i odd">
                	<label>Nové heslo (kontrola):</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="newpass2" />
                </div>

                <div class="i">
                	<input type="hidden" value="16" name="act" />
                	<input type="hidden" value="<?php echo $uid; ?>" name="id" />
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


