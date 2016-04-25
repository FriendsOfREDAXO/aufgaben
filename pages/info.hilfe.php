<?php


$func = rex_request('func', 'string');

if ($func == 'import_beispieldaten') {

  $current_user       = rex::getUser()->getId();
  $sql = rex_sql::factory();
  //$sql->setDebug();
  $sql->setQuery('SELECT * FROM rex_user WHERE id = '.$current_user);
  $user = $sql->getValue('name');

  $qry = "

  -- Aufgaben

  INSERT IGNORE `rex_aufgaben_aufgaben` VALUES
      (1, 'Fav Icon erstellen', 'Wird immer benötigt',1,1,0,1,now(),now(),'$user','$user','',''),
      (2, 'Touch Icon erstellen', '',1,1,0,1,now(),now(),'$user','$user','',''),
      (3, 'Meta Infos erstellen', 'Sind Ortsbezogene meta Infos wichtig?',1,1,0,1,now(),now(),'$user','$user','',''),
      (4, 'Print.css entwickeln', 'Wird immer vergessen',1,1,0,1,now(),now(),'$user','$user','',''),
      (5, 'robots.txt prüfen', ':-)',7,1,0,1,now(),now(),'$user','$user','','');

  -- Kategorien

  INSERT IGNORE `rex_aufgaben_kategorien` VALUES
      (1,'Grundlagen','#9EAEC2'),
      (2,'Backend','#588D76'),
      (3,'Design','#8D588A'),
      (4,'Funktion','#9EAEC2'),
      (5,'Fehler','#72A3A7'),
      (6,'Wunsch','#FFD83D'),
      (7,'SEO','#437047');

-- User Settings

  INSERT IGNORE `rex_aufgaben_user_settings` VALUES (1,$current_user,5,0);

  ";

  $sql = rex_sql::factory();
  // $sql->setDebug();
  $sql->setQuery($qry);

  echo '<div class="alert alert-success">Der Beispieldaten wurden eingefügt.</div>';
  $func = '';
}

  $file = rex_file::get(rex_path::addon('aufgaben','README.md'));
  $Parsedown = new Parsedown();
  $content =  '<div id="aufgaben">'.$Parsedown->text($file);


$content .= '
<hr/>
<p><a href="index.php?page=aufgaben/info&amp;func=import_beispieldaten"><i class="rex-icon rex-icon-module"></i> Beispieldaten importieren</a>
</p>
</div>
';
$fragment = new rex_fragment();
$fragment->setVar('title', 'Hilfe');
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


