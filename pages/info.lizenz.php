<?php

 $file = rex_file::get(rex_path::addon('aufgaben','LICENSE.md'));
 $Parsedown = new Parsedown();

 $content =  $Parsedown->text($file);

$fragment = new rex_fragment();
$fragment->setVar('title', 'Lizenz');
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
