<nav>
    	<ul>
        	<li class="t"><a class="home"  href="./">Ovládací panel</a></li>
            <li class="t"><a class="article" href="./index.php?p=article">Správa obsahu</a></li>
            <li class="t"><a class="users" href="./index.php?p=user">Správa užívateľov</a>
            	<ul>
                	<li><a href="./index.php?p=user&amp;sp=edit&amp;uid=<?php echo $_SESSION['id']; ?>">Môj profil</a></li>
                    <li><a href="./index.php?p=user">Zoznam užívateľov</a></li>
                    <li><a href="./index.php?p=user&amp;sp=add">Pridať užívateľa</a></li>
                    <li><a href="./index.php?p=user&amp;sp=logs">Logy užívateľov</a></li>
                </ul>
            </li>
            <li class="t"><a class="set" href="./index.php?p=settings">Nastavenia</a></li>
            <li class="t"><a class="stats" href="./index.php?p=stats">Štatistiky</a></li>
            <li class="t"><a class="help" href="./index.php?p=help">Pomocník</a></li>
        </ul>
    </nav>