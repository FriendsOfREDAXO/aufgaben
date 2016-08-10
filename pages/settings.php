<?php

$info = '';
$warning = '';
$content = '<div id="aufgaben">';

$func = rex_request('func', 'string');
if ($func == 'update') {
  $this->setConfig(rex_post('config', [
        ['ansicht', 'string'],
        ['mails', 'array[string]'],
    ]));

  header('Location: '.rex_getUrl(rex_url::currentBackendPage()));
  exit;
}

$content .=  '
<div class="rex-form">
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <fieldset>
          <input type="hidden" name="func" value="update" />';


$sel_ansicht = new rex_select();
$sel_ansicht->setId('aufgaben-ansicht');
$sel_ansicht->setName('config[ansicht]');

$sel_ansicht->setSize(1);
$sel_ansicht->setAttribute('class', 'form-control');
$sel_ansicht->setSelected($this->getConfig('ansicht'));
foreach (['beide' => $this->i18n('aufgaben_settings_beide'), 'liste' => $this->i18n('aufgaben_settings_liste'), 'kanban' => $this->i18n('aufgaben_settings_kanban')] as $type => $name) {
    $sel_ansicht->addOption($name, $type);
}

$n = [];
$formElements = [];

$n['label'] = '<label for="aufgaben-ansicht">'.$this->i18n('aufgaben_settings_ansicht').'</label>';
$n['field'] = '<div class="rex-select-style">'.$sel_ansicht->get().'</div>';
$formElements[] = $n;

$tableSelect = new rex_select();
$tableSelect->setMultiple();
$tableSelect->setId('aufgabe-mails');
$tableSelect->setName('config[mails][]');
$tableSelect->setAttribute('class', 'form-control');

// Alle  E-Mail Adressen holen
$sql_mail = rex_sql::factory();
//$sql_admin->setDebug();
$sql_mail->setTable('rex_user');
$sql_mail->setWhere('email !="" AND status = 1');
$sql_mail->select();
if ($sql_mail->getRows()) {
  for($i=0; $i<$sql_mail->getRows(); $i++) {
    $tableSelect->addOption($sql_mail->getValue('email'), $sql_mail->getValue('email'));
    if (in_array($sql_mail->getValue('email'), $this->getConfig('mails'))) {
        $tableSelect->setSelected($sql_mail->getValue('email'));
    }
    $sql_mail->next();
  }
}

$n = [];
$n['label'] = '<label for="aufgaben-mails">'.$this->i18n('aufgaben_settings_e_mails_an').'</label>';
$n['field'] = $tableSelect->get();

$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

$content .= '
        </fieldset>
        <fieldset class="rex-form-action">';

$formElements = [];

$n = [];
$n['field'] = '<div class="btn-toolbar"><button id="rex-out5-border-save" type="submit" name="config-submit" class="btn btn-save rex-form-aligned" value="1">'. $this->i18n('aufgaben_settings_save').'</button></div>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/submit.php');

$content .= '
        </fieldset>
    </form>
  </div>
</div>';


$fragment = new rex_fragment();
$fragment->setVar('id', 'aufgaben');
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('aufgaben_settings_title'));
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');


