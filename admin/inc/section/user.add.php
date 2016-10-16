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
        <div class="cbox">
        	<strong class="h img profile">Pridanie nového užívateľa</strong>
            	

            <form name="add">
            	
                
                <div class="i">
                	<label>Typ: </label><select class="w200" name="id_user_type"><?php echo getOptions( $conn, "user_type", "name", 0, ($_SESSION['type'] != 5 ? 5 : null)); ?></select>
                </div>
                
                <div class="i odd">
                	<label>Login:</label><input  maxlength="35" type="text" class="w200 required" name="login"  />
                </div>
                
                <div class="i">
                	<label>Heslo:</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="pass1" />
                </div>
                
                 <div class="i odd">
                	<label>Kontrola hesla:</label><input  maxlength="35" type="password" class="w200 required fiveplus" name="pass2" />
                </div>
                
                <div class="i ">
                	<label>E-mail:</label><input  maxlength="35" type="text" class="w200 required email" name="email"/>
                </div> 	
                
                <div class="i odd">
                	<label>Aktivita: </label><select class="w200" name="active"> 
						<option value="0">Neumožniť prihlásenie</option>
                        <option value="1">Umožniť prihlásenie</option>
                    </select>
                </div>
                
                 <div class="i">
                	<label>Meno:</label><input  maxlength="35" type="text" class="w200" name="givenname"/>
                </div> 	
                
                 <div class="i odd">
                	<label>Priezvisko:</label><input  maxlength="35" type="text" class="w200" name="surname"/>
                </div> 	
                
                
                <div class="i">
                	<input type="hidden" value="17" name="act" />
                	<input type="submit"  class="ibtn2" name="button" value="Uložiť" />
                    <div class="clear"></div>
                </div>
                
            </form>
        </div>
</div>
<div class="clear"></div>


