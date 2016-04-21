<?php

if (rex::isBackend() && rex::getUser()) {
  rex_view::addCssFile($this->getAssetsUrl('style.css'));

  // Datepicker
  rex_view::addJSFile($this->getAssetsUrl('js/bootstrap-datepicker.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/bootstrap-datepicker.de.js'));
  // SumoSelect
  rex_view::addJSFile($this->getAssetsUrl('js/jquery.sumoselect.js'));

  rex_extension::register('PACKAGES_INCLUDED', function () {
    if (rex::getUser() && $this->getProperty('compile')) {
      $compiler = new rex_scss_compiler();

      $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('scss/master.scss')]));
      $compiler->setScssFile($scss_files);
      // $compiler->setScssFile($this->getPath('scss/master.scss'));

      // Compile in backend assets dir
      $compiler->setCssFile($this->getPath('assets/css/styles.css'));

      $compiler->compile();

      // Compiled file to copy in frontend assets dir
      rex_file::copy($this->getPath('assets/css/styles.css'), $this->getAssetsPath('css/styles.css'));
        }
    });

    rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));

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
