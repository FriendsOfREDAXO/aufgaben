<?php

if (rex::isBackend() && rex::getUser()) {
  rex_view::addCssFile($this->getAssetsUrl('style.css'));

function show_counter()
{

    $counter        = 0;
    $current_user   = rex::getUser()->getId();

    $sql_counter = rex_sql::factory();
    //$sql_counter->setDebug();
    $sql_counter->setQuery('SELECT counter FROM rex_aufgaben_user_settings WHERE user = '.$current_user);

    $ersetzen = '</i> Aufgaben <span class="label label-default">'.$counter.'</span></a>';


    if ($sql_counter->getRows() > 0) {

      $counter = $sql_counter->getValue('counter');
      $ersetzen = '</i> Aufgaben <span class="label label-danger">'.$counter.'</span></a>';

    }
    rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) use ($ersetzen){
          $suchmuster = '</i> Aufgaben';
          $ersetzen = $ersetzen;
          $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
      });

  }
}

if ($this->getConfig('install') == 'true' && rex::getUser()) {
  $current_page = rex_be_controller::getCurrentPage();
  if ($current_page != 'aufgaben/aufgaben') {
    show_counter();
  }
}

?>
