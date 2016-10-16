<section id="help">
	<?php if(!$auth->isLogined()){ die("Neautorizovaný prístup."); } ?>
    <div class="breadcrumb">
        Nachádzate sa:
        <a href="./index.php">Domov</a> &raquo;
        <a href="./index.php?p=help">Pomocník</a>
    </div>
	<strong class="h1">Pomocník</strong>
    
  	<ul class="help">
    	<li><a href="#h1">1. Ako je možné pridať novú stránku do hlavnej sekcie ?</a></li>
        <li><a href="#h2">2. Ako je možné pridať podstránku ku stránke ?</a></li>
        <li><a href="#h3">3. Ako sa dá pridať obrázok do obsahu stránky ?</a></li>
        <li><a href="#h4">4. Ako je možné pridať súbor na stiahnutie do obsahu stránky ?</a></li>
        <li><a href="#h5">5. Ako je možné zmazať stránku ?</a></li>
        <li><a href="#h6">6. Ako je možné zmeniť poradie stránok ?</a></li>
    </ul>
    
     <div id="h1" class="hbox">
     		<h2>1. Ako je možné pridať novú stránku do hlavnej sekcie ?</h2>
            <p>
            	1. Novú stránku je možné pridať v sekcii <strong>Správa obsahu</strong><br />
            	2. Potom sa klikne v ľavom panely na odkaz <a title="./img/help/1.jpg">www.nazov-vasej-domeny.sk</a><br />
                3. Následne sa zobrazia všetky stránky, ktoré sa nachádzajú v hlavnej sekcii, pod ktorými sa nachádza tlačidlo <a title="./img/help/2.jpg">Pridať stránku do tejto sekcie</a><br />
                4. Po kliknutí na tlačitko <a title="./img/help/2.jpg">Pridať stránku do tejto sekcie</a> sa zobrazí <a title="./img/help/3.jpg">formulár</a> do ktorého sa zadá názov novej stránky a klikne na uložiť.<br />
                5. Novo pridaná stránka je označená <a title="./img/help/4.jpg">zelenou farbou</a>. Po rozkliknutí sa môže pridať obsah.
            </p>
     </div>
     
     
     <div id="h2" class="hbox">
     		<h2>2. Ako je možné pridať podstránku ku stránke ?</h2>
            <p>
            	1. Novú stránku resp. podstránku je možné pridať v sekcii <strong>Správa obsahu</strong><br />
            	2. V prípade ak chceme pridať podstránku do konkrétnej stránky, je nutné aby sa na nu kliklo. (Pr. Ak chceme pridať podstránku k stránke <a title="./img/help/5.jpg">"poradenstvo"</a>, klikne sa na ňu.)<br />
                3. Následne sa v <a title="./img/help/6.jpg">pravom panely</a> zobrazia všetky podstránky ktoré stránka <a title="./img/help/5.jpg">"poradenstvo"</a> obsahuje.<br />
                4. Po kliknutí na tlačitko <a title="./img/help/2.jpg">Pridať stránku do tejto sekcie</a> sa zobrazí <a title="./img/help/3.jpg">formulár</a> do ktorého sa zadá názov novej stránky a klikne na uložiť.<br />
                5. Novo pridaná stránka je označená <a title="./img/help/4.jpg">zelenou farbou</a>. Po rozkliknutí sa môže pridať obsah.
            </p>
     </div>
     
     <div id="h3" class="hbox">
     		<h2>3. Ako sa dá pridať obrázok do obsahu stránky ?</h2>
            <p>
            	1. Obrázok do obsahu stránky je možné pridať v sekcii "Správa obsahu". Je nutné rozkliknúť konkrétmu stránku do ktorej chcem pridať obázok. <br /><br />
                <em>Príklad:</em><br /> 
                 2.1 Ak by som chcel pridať obrázok do obsahu <a title="./img/help/5.jpg">"poradenstvo"</a>, klikne sa na ňu.<br />
            
                 2.2 Následne sa v editore klikne na <a title="./img/help/7.jpg">ikonu orbázka</a>.<br />
                 2.3 Vyskočí okno s názvom <a title="./img/help/8.jpg">"Vlastnosti obrázka</a>.<br /><br />
                 <strong>2.4 V pripade ak sa obrázok nachádza na vašom počítači:</strong><br />
                 2.4.1 V okne <a title="./img/help/8.jpg">"Vlastnosti obrázku"</a> sa klikne na kartu <a title="./img/help/9.jpg">"Odoslať"</a>  (krok 1)<br />
            
                 2.4.2 V pčítači sa <a title="./img/help/9.jpg">vyhľadá konkrétný obrázok</a>, ktorý chceme nahrať (krok 2)<br />
                 2.4.3 Následne sa klikne na tlačidlo <a title="./img/help/9.jpg">Odoslať na server</a>(krok 3)<br /><br />
                 <strong>2.5 Obrázok už raz bol nahratý, nachádza sa na servery:</strong><br />
                 2.5.1 Ak sa obrázok už na servery nachádza klikne sa na tlačítko <a title="./img/help/10.jpg">"Prochádzať server"</a><br />
            
                 2.5.2 Následne sa zobrzí nové okno v ktorom je zoznam obrázkov</a><br />
                 2.5.3 Vyberie sa konkrétný obrázok a klikne sa naň pravým tlačidlom myši<br />
                 2.5.4 Zobrazí sa <a title="./img/help/11.jpg">roletové menu</a>, v ktorom klikneme na "Vybrať".<br /><br />
                 2.6 Už len klikne na tlačidlo "OK" a obrázok je vložený v editore, po uložení na danej stránke.<br />
            </p>
     </div>
     
     
     <div id="h4" class="hbox">
     		<h2>4. Ako je možné pridať súbor na stiahnutie do obsahu stránky ?</h2>
            <p>
            	 1. Súbor na stiahnutie sa pridáva tak, že sa vloží klasický odkaz na stránky, ktorý okdazuje na daný súbor.<br /><br />
                 <em>Príklad:</em><br /> 	
                  2.1 Ak by som chcel pridať obrázok do obsahu <a title="./img/help/5.jpg">"poradenstvo"</a>, klikne sa na ňu.<br />
                 2.2 Následne sa v editore klikne na <a title="./img/help/12.jpg">ikonu odkazu</a>.<br />
            
                 2.3 Zobrazí okno s názvom <a title="./img/help/13.jpg">"Odkaz"</a>.<br /><br />
                 <strong>2.4 V pripade ak sa súbor nachádza na vašom počítači:</strong><br />
                 2.4.1 V okne <a title="./img/help/13.jpg">"Odkaz"</a> sa prekline na kartu <a title="./img/help/14.jpg">"Odoslať"</a>
                 2.4.2 V pamäti pčítača sa <a title="./img/help/14.jpg">vyhľadá konkrétný súbor</a>, ktorý chceme nahrať (krok 2)<br />
            
                 2.4.3 Následne sa klikne na tlačidlo <a title="./img/help/14.jpg">Odoslať na server</a> (krok 3)<br /><br />
                 <strong>2.5 Súbor už raz bol nahratý, resp. nachádza sa na servery:</strong><br />
                 2.5.1 Ak už bol súbor raz nahratý, klikne sa na tlačítko <a title="./img/help/15.jpg">"Prochádzať server"</a><br />
                 2.5.2 Zobrazí sa okno v ktorom sa nachádzajú všetky nahraté súbory</a><br />
                 2.5.3 Pravým tlačidlom myši sa klikne na daný súbor</a><br />
            
                 2.5.4 Zobrazí sa <a title="./img/help/16.jpg">roletové menu</a> a klikne sa na vybrať</a><br /><br />
                 2.6 Už len klikne na tlačidlo "OK" a odkaz na súbor na stiahnutie je vložený v editore, po uložení sa začne zobrazovať.<br />
            </p>
     </div>
     
      <div id="h5" class="hbox">
     		<h2>5. Ako je možné zmazať stránku ?</h2>
            <p>
            	 1. Stránky je možné mazať v sekcii "Správa obsahu"<br />
                 2. V boxe "Zoznam stránok v kategórii" sa klikne na <a title="./img/help/17.jpg">červený krížik</a> konkretnej stránky.<br /><br />
                 <em>Pozn. ak odstraňovaná stránka obsahuje podstránky, je nutné prva odstrániť všetky podstránky a až potom danú stránku.</em>
            </p>
     </div>
     
      <div id="h6" class="hbox">
     		<h2>6. Ako je možné zmeniť poradie stránok ?</h2>
            <p>
            	 Poradie položiek v menu je možné meniť v sekcii "Správa obsahu". V tabulke "Zoznam stránok v kategorii". Spôsob zmeny je realizovaný tzv. "Drag and Drop" v preklade potiahni a pusť.<br /><br />
                 <em>Príklad:</em><br /> 	
                 1. Chceme aby bola stránka <a title="./img/help/18.jpg">"Dosdávatelia a spolupráca"</a> umiestnená pred stránkou <a title="./img/help/18.jpg">"stavebná činnosť"</a><br />
                 2. Nastavíme sa myšou na stĺpec poradie a riadok stránky "Dosdávatelia a spolupráca", zobrazí <a title="./img/help/19.jpg">pohybový kurzor.</a><br />
                 3. Následne sa klikne a ľavým tlačidlom myši <strong>(tlačidlo je stále stlačené)</strong> a <a title="./img/help/20.jpg">presunie</a> sa stránka "Dosdávatelia a spolupráca" pred stránku "stavebná činnosť"
            </p>
     </div>
     <div class="clear"></div>
</section>