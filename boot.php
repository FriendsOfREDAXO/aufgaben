<?php

if (rex::isBackend()) {
    rex_view::addCssFile($this->getAssetsUrl('style.css'));


      rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep){
      $suchmuster = '<a href="index.php?page=aufgaben/aufgaben"><i class="rex-icon fa-calendar-check-o"></i> Aufgaben</a>';
      $ersetzen = '<a href="index.php?page=aufgaben/aufgaben"><i class="rex-icon fa-calendar-check-o"></i> Aufgaben <span class="label label-danger">'.$this->getConfig('anzahl').'</span></a>';
      $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
    });

}
