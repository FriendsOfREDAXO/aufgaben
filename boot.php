<?php

if (rex::isBackend() && rex::getUser()) {
  rex_view::addCssFile($this->getAssetsUrl('style.css'));

  // Datepicker
  rex_view::addCssFile($this->getAssetsUrl('default.css'));
  rex_view::addCssFile($this->getAssetsUrl('default.date.css'));
  rex_view::addJSFile($this->getAssetsUrl('picker.js'));
  rex_view::addJSFile($this->getAssetsUrl('picker.date.js'));

function show_counter()
{

    $counter        = 0;
    $current_user   = rex::getUser()->getId();

    $sql_counter = rex_sql::factory();
    //$sql_counter->setDebug();
    $sql_counter->setQuery('SELECT counter FROM rex_aufgaben_user_settings WHERE user = '.$current_user);

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

if ($this->getConfig('install') == 'true' && rex::getUser()) {
  $current_page = rex_be_controller::getCurrentPage();
  if ($current_page != 'aufgaben/aufgaben') {
    show_counter();
  }
}

?>
