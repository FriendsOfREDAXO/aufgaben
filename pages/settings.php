<?php

$info = '';
$warning = '';
$content = '';

if (rex_post('config-submit', 'boolean')) {
    $this->setConfig(rex_post('config', [
        ['kanban', 'string'],
    ]));

    $content .= rex_view::info('Änderung gespeichert');
}

$content .= '
<div id="aufgaben_settings">
<form action="index.php" method="post" id="aufgaben_settings">

  <input type="hidden" name="page" value="aufgaben/settings" />
  <input type="hidden" name="func" value="update" />

  <fieldset>
    <legend>Ansichten</legend>

    <div class="row">
      <div class="col-xs-12 col-sm-6 col-sm-push-6 abstand">
        <input class="rex-form-text" type="checkbox" id="rex-form-auth" name="config[kanban]" value="aktiviert" ';
        if($this->getConfig('kanban')== 'aktiviert') $content .= 'checked="checked"';

$content .= ' />
        <label for="rex-form-auth">Kanban Ansicht anzeigen</label>
      </div>
    </div>


  </fieldset>

  <div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-push-6">
      <button class="btn btn-save right" type="submit" name="config-submit" value="1" title="Einstellungen speichern">Einstellungen speichern</button>
    </div>
  </div>

  </form>

  ';

$content .= '</div>';
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');

