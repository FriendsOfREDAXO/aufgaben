<?php

if (rex::isBackend() && rex::getUser()) {

  //////////
  // JS
  //////////
  $current_page = rex_be_controller::getCurrentPage();

    rex_view::addJSFile($this->getAssetsUrl('js/jquery.sumoselect.js'));
    rex_view::addJSFile($this->getAssetsUrl('js/kanban.js'));
    rex_view::addJSFile($this->getAssetsUrl('js/jquery.simplecolorpicker.js'));
    rex_view::addJSFile($this->getAssetsUrl('js/moments.js'));
    rex_view::addJSFile($this->getAssetsUrl('js/pikaday.js'));
    rex_view::addJSFile($this->getAssetsUrl('js/custom.js'));

  ///////////
  // SCSS
  ///////////
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


  ///////////
  // Subpages
  ///////////

  //  aufgaben Listenansicht
  if ($this->getConfig('ansicht') == 'liste' OR $this->getConfig('ansicht') == 'beide') {
    $page = $this->getProperty('page');
    $page['subpages']['aufgaben'] = ['title' =>  $this->i18n('aufgaben_title')];
    $this->setProperty('page', $page);
  }
    if ($this->getConfig('time') == '5Minuten') {
            $this->setConfig('time', 5);
     //   dump($this->getConfig('time'));
    }if ($this->getConfig('time') == '15Minuten') {
            $this->setConfig('time', 15);
      //  dump($this->getConfig('time'));
    }if ($this->getConfig('time') == '30Minuten') {
            $this->setConfig('time', 30);
     //   dump($this->getConfig('time'));
        
    }
        if ($this->getConfig('time') == '60Minuten') {
            $this->setConfig('time', 60);
    //    dump($this->getConfig('time'));
    }if ($this->getConfig('time') == '120Minuten') {
            $this->setConfig('time', 120);
    //    dump($this->getConfig('time'));
    }if ($this->getConfig('time') == '0Minuten') {
            $this->setConfig('time', 0);
     //   dump($this->getConfig('time'));
        
    }
	  if ($this->getConfig('absender') != null) {
		$absender = $this->getConfig('absender');
   		$this->setConfig('absender',$absender);
    }
    if ($this->getConfig('betreff') != null) {
    	$betreff = $this->getConfig('betreff');
    	$this->setConfig('betreff',$betreff);
    }
    if($this->getConfig('send-to-all') == 1) {
    	$checkbox = $this->getConfig('send-to-all');
    	$this->setConfig('send-to-all', $checkbox);
    //	dump($checkbox);
    }
    if($this->getConfig('send-to-all') == 0) {
    	$checkbox = $this->getConfig('send-to-all');
    	$this->setConfig('send-to-all', $checkbox);
    //	dump($checkbox);
    }
    

  //  Kanban Ansicht
  if ($this->getConfig('ansicht') == 'kanban' OR $this->getConfig('ansicht') == 'beide') {
      $page = $this->getProperty('page');
      $page['subpages']['kanban'] = ['title' => 'Kanban Ansicht'];
      $this->setProperty('page', $page);
  }
  //  Kategorien
  $page = $this->getProperty('page');
  $page['subpages']['categories'] = ['title' => $this->i18n('aufgaben_categories'), 'perm' => 'aufgaben[categories]'];
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
    $page['subpages']['info'] = ['title' => $this->i18n('aufgaben_info')];
    $page['subpages']['info']['subpages']['help'] = ['title' => $this->i18n('aufgaben_help')];
    $page['subpages']['info']['subpages']['changelog'] = ['title' => $this->i18n('aufgaben_changelog')];
    $page['subpages']['info']['subpages']['licence'] = ['title' => $this->i18n('aufgaben_licence')];
    $this->setProperty('page', $page);

    //Standard-Delay

if ($this->getConfig('install') == 'true' && rex::getUser()) {
   $current_page = rex_be_controller::getCurrentPage();
   if ($current_page != 'aufgaben/aufgaben') {
      $counter = new rex_aufgaben();
      $counter->show_counter();
   }
}



}

