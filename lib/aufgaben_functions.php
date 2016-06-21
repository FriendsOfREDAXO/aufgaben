<?php

class rex_aufgaben {


  // Beispiel
  var $zaehler = 0;

  public function test($var) {
    if (!empty($var)) {
      $this->zaehler += $var;
    }
  }

  /*
  $aufgaben = new rex_aufgaben();

  $aufgaben->test(1);
  echo $aufgaben->zaehler; // ausgabe 1
  $aufgaben->test(10);
  echo $aufgaben->zaehler; // ausgabe 11
  */


  public function show_counter() {

    $counter        = 0;
    $current_user   = rex::getUser()->getId();

    $sql_counter = rex_sql::factory();
    //$sql_counter->setDebug();
    $sql_counter->setQuery('SELECT counter FROM rex_aufgaben_user_settings WHERE user = '.$current_user);

    if ($sql_counter->getRows() > 0) {

      $ersetzen = '</i> Aufgaben <span class="label label-default">'.$counter.'</span></a>';
      $counter = $sql_counter->getValue('counter');

      if ($counter > 0) {
        $ersetzen = '</i> Aufgaben <span class="label label-danger">'.$counter.'</span></a>';
      } else {
        $ersetzen = '</i> Aufgaben <span class="label label-default">'.$counter.'</span></a>';
      }

      rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) use ($ersetzen){
        $suchmuster = '</i> Aufgaben';
        $ersetzen = $ersetzen;
        $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
      });
    }
  }


}





