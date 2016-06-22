<?php

if (rex::isBackend() && rex::getUser()) {

  /* Einstellungen */

  rex_view::addJSFile($this->getAssetsUrl('js/moments.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/pikaday.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/jquery.sumoselect.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/jquery.simplecolorpicker.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/custom.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/kanban.js'));

  rex_extension::register('PACKAGES_INCLUDED', function () {
    if (rex::getUser() && $this->getProperty('compile')) {

      $compiler = new rex_scss_compiler();

      $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('scss/master.scss')]));
      $compiler->setScssFile($scss_files);
      $compiler->setCssFile($this->getPath('assets/css/styles.css'));
      $compiler->compile();
      rex_file::copy($this->getPath('assets/css/styles.css'), $this->getAssetsPath('css/styles.css'));
        }
    });
  rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));
}

// Subpages
//  Listen Ansicht
if ($this->getConfig('ansicht') == 'liste' OR $this->getConfig('ansicht') == 'beide') {
    $page = $this->getProperty('page');
    $page['subpages']['aufgaben'] = ['title' => 'Aufgaben'];
    $this->setProperty('page', $page);
}

//  Kanban Ansicht
if ($this->getConfig('ansicht') == 'kanban' OR $this->getConfig('ansicht') == 'beide') {
    $page = $this->getProperty('page');
    $page['subpages']['kanban'] = ['title' => 'Kanban Ansicht'];
    $this->setProperty('page', $page);
}

//  Kategorien
  $page = $this->getProperty('page');
  $page['subpages']['kategorien'] = ['title' => 'Kategorien', 'perm' => 'aufgaben[kategorien]'];
  $this->setProperty('page', $page);

//  Einstellungen
  $page = $this->getProperty('page');
  $page['subpages']['settings'] = ['title' => 'Einstellungen', 'perm' =>'admin[]'];
  $this->setProperty('page', $page);

//  Import /( Export)
  $page = $this->getProperty('page');
  $page['subpages']['exim'] = ['title' => 'Export', 'perm' =>'admin[]' ];
  $page['subpages']['exim']['subpages']['export'] = ['title' => 'Export'];
//  $page['subpages']['exim']['subpages']['import'] = ['title' => 'Import'];
  $this->setProperty('page', $page);

//  Info
  $page = $this->getProperty('page');
  $page['subpages']['info'] = ['title' => 'Info'];
  $page['subpages']['info']['subpages']['hilfe'] = ['title' => 'Hilfe'];
  $page['subpages']['info']['subpages']['changelog'] = ['title' => 'Changelog'];
  $page['subpages']['info']['subpages']['lizenz'] = ['title' => 'Lizenz'];
  $this->setProperty('page', $page);

// /Subpages


if ($this->getConfig('install') == 'true' && rex::getUser()) {
   $current_page = rex_be_controller::getCurrentPage();
   if ($current_page != 'aufgaben/aufgaben') {
      $counter = new rex_aufgaben();
      $counter->show_counter();
   }
}

