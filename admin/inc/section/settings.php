<section>
	<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
    <div class="breadcrumb">
        Nachádzate sa:
        <a href="./index.php">Domov</a> &raquo;
        <a href="./index.php?p=settings">Nastavenia</a>
    </div>
	<strong class="h1">Nastavenia</strong>
    
     <div class="settings right">
       <?php
       		
			
		$config = array_merge($config,  getConfig($conn));
		
	   ?>
        <div class="cbox">
        	 <strong class="h img seo">Meta nastavenia</strong>
            <form name="seo" class="ajax">
                <div class="i">
                            <label class="tt">Názov stránky:</label><input  maxlength="50" type="text" class="w400" name="c_name" value="<?php echo $config['c_name']; ?>" />
                </div>
                 <div class="i odd">
                            <label>Titulok úvednej stránky:</label><input name="c_title" maxlength="100" type="text" class="w400" value="<?php  echo $config['c_title']; ?>" />
                 </div>
                 <div class="i">
                            <label>Popis stránky:</label><textarea name="c_descr" rows="3" class="big h100" cols="53" ><?php  echo $config['c_descr']; ?></textarea>
                 </div>
                 <div class="i odd">
                        <label>Indexacia do vyhľadávačov: </label>
                        <select class="w200" name="c_robots"> 
                            <?php
                            	if($config['c_robots'] == "intex,follow"){
									echo '<option value="index,follow">Povoliť</option><option value="noindex,nofollow">Zakázať</option>';
								}else{
									echo '<option value="noindex,nofollow">Zakázať</option><option value="index,follow">Povoliť</option>';
								}
							
							?>
                        </select>
                 </div>
                  <div class="i">
                  <input type="hidden" value="18" name="act" />
                    <input type="submit" class="ibtn2" value="Uložiť" />
                  </div>
             </form>
         </div>
         
         
         <div class="cbox">
        	 <strong class="h img sys">Ostatné nastavenia</strong>
             <form name="sys" class="ajax">
                	<div class="i">
                    	<label>Stránkovanie databáza:</label><input  maxlength="3" type="text" class="c w45 required" name="c_pagi" value="<?php echo $config['c_pagi'] ?>" />
               		</div>
                    <div class="i odd">
                        <label>Stránkovanie galéria:</label><input  maxlength="3" type="text" class="c w45 required" name="c_pagi_g" value="<?php echo $config['c_pagi_g'] ?>" />
                    </div>
                    <div class="i">
                    	<label>E-mail:</label><input  maxlength="200" type="text" class="w200 required" name="c_email" value="<?php echo $config['c_email'] ?>" />
                    </div>
                    <div class="i">
                    	<input type="hidden" value="19" name="act" />
                    	<input type="submit" class="ibtn2" value="Uložiť" />
                    </div>
              </form>
         </div>
         
         <div class="cbox">
        	 <strong class="h img off">Vypnutie web stránky</strong>
                <p class="info">Toto nastavenie môže meniť len užívateľ typu: Super administrátor</p>
              <form name="status" class="ajax">
                <div class="i">
                            <label>Stav: </label>
                            <select class="w200" name="c_status"> 
                            	<option value="1">Zapnutá</option><option value="0">Vypnutá</option>
                            </select>
                </div> 
                <div class="i odd">
                        <label>Off-line správa:</label><textarea name="c_offline_msg" rows="3" cols="53" class="big h100"><?php // echo $data[0]['header_sk']; ?></textarea>
            	</div>
                 <div class="i">
                <input type="hidden" value="20" name="act" />
             	<input type="submit" class="ibtn2" value="Uložiť" />
              </div>
             </form>
         </div>
    
    	
     </div>
     <div class="clear"></div>
</section>