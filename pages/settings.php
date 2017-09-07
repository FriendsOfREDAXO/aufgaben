<?php

$info = '';
$warning = '';
$content = '<div id="aufgaben">';

$func = rex_request('func', 'string');
if ($func == 'update') {
  $this->setConfig(rex_post('config', [
        ['ansicht', 'string'],
        ['mails', 'array[string]'],
        ['time', 'integer'],
		['absender', 'string'],
		['betreff', 'string'],
		['send-to-all', 'string']
    ]));

  header('Location: '.rex_getUrl(rex_url::currentBackendPage()));
  exit;
}

$content .=  '
<div class="rex-form">
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <fieldset>
          <input type="hidden" name="func" value="update" />';


//DELAY TIME
$del_Time = new rex_select();
$del_Time->setId('delay_Time');
$del_Time->setName('config[time]');
$del_Time->setSize(1);
$del_Time->setAttribute('class', 'form-control');
$del_Time->setSelected($this->getConfig('time'));
foreach ([ 5 => $this->i18n('5_minuten_verzoegerung'), 15 => $this->i18n('15_minuten_verzoegerung'), 30 => $this->i18n('30_minuten_verzoegerung'), 60 => $this->i18n('60_minuten_verzoegerung'), 120 => $this->i18n('120_minuten_verzoegerung'), 0 => $this->i18n('0_minuten_verzoegerung') ] as $type1 => $time) {
    $del_Time->addOption($time, $type1);
}

$n = [];
$formElements = [];

$n['label'] = '<label for="delay_Time">'.$this->i18n('zeit_ansicht').'</label>';
$n['field'] = '<div class="rex-select-style">'.$del_Time->get().'</div>';
$formElements[] = $n;

//Empfänger setzen
$n = [];
$n['label'] = '<label for="absender_Email">' . $this->i18n('absender_Email') . '</label>';
$n['field'] = '<input class="form-control" type="text" id="absender_Email" name="config[absender]" value="'.$this->getConfig("absender").'" placeholder="'.$this->i18n("absender_Email_placeholder").'"/>';
$formElements[] = $n;



//Betreff der Mail setzen
$n = [];
$n['label'] = '<label for="betreff_Email">' . $this->i18n('betreff_Email') . '</label>';
$n['field'] = '<input class="form-control" type="text" id="betreff_Email" name="config[betreff]" value="'.$this->getConfig("betreff").'" placeholder="'.$this->i18n("betreff_Email_placeholder").'"/>';
$formElements[] = $n;



//ANSICHT SETZEN
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

//send-to-all or send-to-responsible
$n['label'] = '<label for="send-to-all-checkbox">' . $this->i18n('send-to-all-checkbox') . '</label>';
$n['field'] = '<input type="checkbox" id="send-to-all-checkbox" name="config[send-to-all]"' . (!empty($this->getConfig('send-to-all')) && $this->getConfig('send-to-all') == '1' ? ' checked="checked"' : '') . ' value="1" />';
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


