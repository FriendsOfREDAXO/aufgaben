<?php

$file = rex_file::get(rex_path::addon('aufgaben','CHANGELOG.md'));
$Parsedown = new Parsedown();

$content =  '<div id="aufgaben">'.$Parsedown->text($file).'</div>';

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('aufgaben_changelog'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


