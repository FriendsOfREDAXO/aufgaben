<?php

$func = rex_request('func', 'string');

if ($func == 'import_beispieldaten') {

  $qry = "

  -- Aufgaben

  INSERT IGNORE `rex_aufgaben_aufgaben` VALUES
      (1, 'Fav Icon erstellen', '',1,1,0,1),
      (2, 'Touch Icon erstellen', 'Zur Darstellung von Bildern in der Detailansicht im Medienpool',1,1,0,1),
      (3, 'Meta Infos erstellen', 'Sind Ortsbezogene meta Infos wichtig?',1,1,0,1),
      (4, 'Print.css entwickeln', 'gg',1,1,0,1),
      (5, 'robots.txt erstellen', '444444',7,1,0,1);

  -- Kategorien

  INSERT IGNORE `rex_aufgaben_kategorien` VALUES
      (1,'Grundlagen'),
      (2,'Backend'),
      (3,'Design'),
      (4,'Funktion'),
      (5,'Fehler'),
      (6,'Wunsch'),
      (7,'SEO');
  ";

  $sql = rex_sql::factory();
  // $sql->setDebug();
  $sql->setQuery($qry);

  echo '<div class="alert alert-success">Der Beispieldaten wurden eingefügt.</div>';
  $func = '';
}


$content = '

<h3>Text folgt</h3>

<a href="index.php?page=aufgaben/info&amp;func=import_beispieldaten"><i class="rex-icon rex-icon-module"></i> Beispieldaten importieren</a>

';
$fragment = new rex_fragment();
$fragment->setVar('title', 'Info');
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


