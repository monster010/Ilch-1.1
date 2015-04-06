<?php

//   Copyright by Manuel Staechele
//   Support www.ilch.de
//   Modded by Mairu f�r News Extended

defined ('main') or die ( 'no direct access' );

$news_groups = 0;
foreach ($_SESSION['authgrp'] as $id => $bool){
	$news_groups = $news_groups | pow(2, $id);
}


$tn_id = intval(@db_result($news_opts = db_query("SELECT v1, v2 FROM prefix_allg WHERE k = 'news' LIMIT 1"),0,0));
$abf = 'SELECT *
        FROM prefix_news
		WHERE (((' . pow(2, abs($_SESSION['authright'])) . " | news_recht) = news_recht) OR
			(news_groups != 0 AND ((news_groups ^ $news_groups) != (news_groups | $news_groups)))) AND `show` > 0 AND `show` <= UNIX_TIMESTAMP() AND news_id != '.$tn_id.' AND archiv != 1 AND (endtime IS NULL OR endtime > UNIX_TIMESTAMP())
              ORDER BY news_time DESC
		LIMIT 0,5";
$erg = db_query($abf);
if (loggedin()) {
    $admin = '';
    if (user_has_admin_right($menu, false)) {
        $admin = '<br><a class="box" href="admin.php?news">jetzt eine News erstellen</a>';
    }
}
if ( @db_num_rows($erg) == 0 ) {
	echo '<div class="text-center">kein Newseintrag vorhanden'.$admin.'</div>';
} 
echo '<div class="tdweight100">';
while ($row = db_fetch_object($erg)) {
	echo '<div class="tdweight10 text-left ilch_float_l"><strong>&raquo;</strong></div><div class="text-left"><a  class="box" href="index.php?news-'.$row->news_id.'" title="'.$row->news_title.'">'.((strlen($row->news_title)<23) ? $row->news_title : substr($row->news_title,0,23).'...').'</a></div>';
}
echo '</div>';

?>