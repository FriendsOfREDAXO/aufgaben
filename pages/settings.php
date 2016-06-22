<?php

$info = '';
$warning = '';
$content = '';


$func = rex_request('func', 'string');
if ($func == 'update') {
  $this->setConfig(rex_post('config', [
        ['ansicht', 'string'],
    ]));

  $content .= rex_view::info('Änderung gespeichert');
  header('Location: '.rex_getUrl(rex_url::currentBackendPage()));
  exit;
}


$sel_ansicht = new rex_select();
$sel_ansicht->setId('aufgaben-ansicht');
$sel_ansicht->setName('config[ansicht]');
$sel_ansicht->setSize(1);
$sel_ansicht->setAttribute('class', 'form-control');
$sel_ansicht->setSelected($this->getConfig('ansicht'));
foreach (['beide' => 'Beide Ansichten', 'liste' => 'Liste', 'kanban' => 'Kanban'] as $type => $name) {
    $sel_ansicht->addOption($name, $type);
}

$content .=  '
<div class="rex-form">
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <fieldset>
          <input type="hidden" name="func" value="update" />';

$n = [];
$formElements = [];
$n = [];
$n['label'] = '<label for="aufgaben-ansicht">Ansicht</label>';
$n['field'] = $sel_ansicht->get();
$formElements[] = $n;


$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '
        </fieldset>

        <fieldset class="rex-form-action">';

$formElements = [];

$n = [];
$n['field'] = '<div class="btn-toolbar"><button id="rex-out5-border-save" type="submit" name="config-submit" class="btn btn-save rex-form-aligned" value="1">Einstellungen speichern</button></div>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/submit.php');

$content .= '
        </fieldset>

    </form>
</div>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', 'Einstellungen');
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


