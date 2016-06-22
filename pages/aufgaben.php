
<?php

// *************************************
//  Vars
// *************************************

$func = rex_request('func', 'string');
$aufgabe = rex_request('aufgabe', 'string');
$filter_kategorien = rex_request('filter_kategorien', 'string');
$filter_eigentuemer = rex_request('filter_eigentuemer', 'string');
$filter_prio = rex_request('filter_prio', 'string');
$filter_status = rex_request('filter_status', 'string');
$filter_erledigt = rex_request('filter_erledigt', 'string');
$current_user = rex::getUser()->getId();


  // Mails verschicken
  $mailbetreff = '';
  if ($aufgabe == 'edit' || $aufgabe == 'new' ) {
    if ($aufgabe == 'edit') { $mailbetreff =  'Geänderte Aufgabe'; }
    if ($aufgabe == 'new') { $mailbetreff =  'Neue Aufgabe Aufgabe'; }
    $aktuelle_id = rex_request('id', 'int');
    $mail = new rex_aufgaben();
    $mail->send_mails($this->getConfig('mails'), $aktuelle_id, $aufgabe, $mailbetreff);
  }

// *************************************
//  Erledigtschalter
// *************************************

if ($func == 'erledigtfilter') {
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setTable('rex_aufgaben_filter');
  $sql->setWhere('user = ' . $current_user);
  $sql->select();
  if ($sql->getRows() > 0) {
    if ($filter_erledigt == '') {
      $filter_erledigt = '0';
    }

    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_filter');
    $sql->setWhere('user = ' . $current_user);
    $sql->setValue('erledigt', $filter_erledigt);
    $sql->update();
  }
  else {
    if ($filter_erledigt == '') {
      $filter_erledigt = '0';
    }

    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_filter');
    $sql->setValue('erledigt', '1');
    $sql->insert();
  }

  $func = '';
}

// *************************************
//  Status
// *************************************

if ($func == 'setstatus') {
  $new_status = (rex_request('neuerstatus', 'int'));
  $id = (rex_request('id', 'int'));
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setTable('rex_aufgaben_aufgaben');
  $sql->setWhere('id = ' . $id);
  $sql->setValue('status', $new_status);
  if ($sql->update()) {

    //    echo '<div class="alert alert-success">Der Status wurde aktualisiert.</div>';

  }

  $func = '';
}

// *************************************
//  Set Prio
// *************************************

if ($func == 'setprio') {
  $new_prio = (rex_request('neueprio', 'int'));
  $id = (rex_request('id', 'int'));
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setTable('rex_aufgaben_aufgaben');
  $sql->setWhere('id = ' . $id);
  $sql->setValue('prio', $new_prio);
  if ($sql->update()) {

    //   echo '<div class="alert alert-success">Die Priorität wurde aktualisiert.</div>';

  }

  $func = '';
}

// --------------------
//  Ausgabe der Tabelle
// --------------------

if ($func == '' || $func == 'filter') {
  $addsql = '';
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setTable('rex_aufgaben_filter');
  $sql->setWhere('user = ' . $current_user);
  $sql->select();
  if ($sql->getRows() > 0) {
    if ($filter_kategorien == '') {
      $filter_kategorien = $sql->getValue('kategorie');
    }

    if ($filter_eigentuemer == '') {
      $filter_eigentuemer = $sql->getValue('eigentuemer');
    }

    if ($filter_prio == '') {
      $filter_prio = $sql->getValue('prio');
    }

    if ($filter_status == '') {
      $filter_status = $sql->getValue('status');
    }

    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_filter');
    $sql->setValue('kategorie', $filter_kategorien);
    $sql->setValue('eigentuemer', $filter_eigentuemer);
    $sql->setValue('prio', $filter_prio);
    $sql->setValue('status', $filter_status);
    $sql->setWhere('user = ' . $current_user);
    $sql->update();
  }
  else {
    if ($filter_kategorien == '') {
      $filter_kategorien = '0';
    }

    if ($filter_eigentuemer == '') {
      $filter_eigentuemer = '0';
    }

    if ($filter_prio == '') {
      $filter_prio = '0';
    }

    if ($filter_status == '') {
      $filter_status = '0';
    }

    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_filter');
    $sql->setValue('user', $current_user);
    $sql->setValue('kategorie', $filter_kategorien);
    $sql->setValue('eigentuemer', $filter_eigentuemer);
    $sql->setValue('prio', $filter_prio);
    $sql->setValue('status', $filter_status);
    $sql->insert();
  }

  if ($filter_kategorien != '0') {
    $addsql.= ' AND a.kategorie IN (' . $filter_kategorien . ')';
  }

  if ($filter_eigentuemer != '0') {
    $addsql.= ' AND a.eigentuemer IN (' . $filter_eigentuemer . ')';
  }

  if ($filter_prio != '0') {
    $addsql.= ' AND a.prio IN (' . $filter_prio . ')';
  }

  if ($filter_status != '0') {
    $addsql.= ' AND a.status IN (' . $filter_status . ')';
  }

  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setQuery('SELECT erledigt FROM rex_aufgaben_filter WHERE user = ' . $current_user);
  $aktueller_erledigt_status = $sql->getValue('erledigt');
  if ($aktueller_erledigt_status == 0) {
    $where = ' a.status > 0';
  }
  else {
    $where = ' a.status != 6';
  }

  $query = 'SELECT  a.*,
                    a.id AS id,
                    k.id,
                    k.kategorie AS kategorie_name,
                    k.farbe
            FROM    ' . rex::getTable('aufgaben_aufgaben') . ' AS a
            LEFT JOIN  ' . rex::getTable('aufgaben_kategorien') . ' AS k
            ON a.kategorie = k.id
            WHERE ' . $where . ' ' . $addsql . ' ORDER BY a.id DESC';
  $list = rex_list::factory($query, 30, 'aufgaben');

  // Anzahl der Aufgaben

  $anzahl = $list->getRows();
  $sql_anzahl = rex_sql::factory();

  // $sql_anzahl->setDebug();

  $sql_anzahl->setTable('rex_aufgaben_user_settings');
  $sql_anzahl->setWhere('user = ' . $current_user);
  $sql_anzahl->select();
  if ($sql_anzahl->getRows() > 0) {
    $sql_anzahl_update = rex_sql::factory();
    $sql_anzahl_update->setTable('rex_aufgaben_user_settings');
    $sql_anzahl_update->setWhere('user = ' . $current_user);
    $sql_anzahl_update->setValue('counter', $anzahl);
    $sql_anzahl_update->update();
  }
  else {
    $sql_anzahl_insert = rex_sql::factory();
    $sql_anzahl_insert->setTable('rex_aufgaben_user_settings');
    $sql_anzahl_insert->setValue('user', $current_user);
    $sql_anzahl_insert->setValue('counter', $anzahl);
    $sql_anzahl_insert->insert();
  }

  $counter = new rex_aufgaben();
  $counter->show_counter();


  $list->setNoRowsMessage('<div class="alert alert-info" role="alert"><strong>Keine Aufgaben vorhanden.</strong><br/><br/>Mögliche Ursachen:<br/><br/><ul><li>es ist keine Aufgabe angelegt</li><li>keine der vorhandenen Aufgaben erfüllt auf die eingestellten Filterkriterien</li><li>die Ausgabe der erledigten Aufgaben ist abgeschaltet</li></ul></div>');

  // --------------------
  //  Edit
  // --------------------

  $tdIcon = '<i class="rex-icon rex-icon-edit"></i>';
  $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '"><i class="rex-icon rex-icon-add"></i></a>';

  // $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon" style="border-left: 5px solid ###farbe###">###VALUE###<a class="watch" href="javascript:void(0);"><i class="rex-icon fa-eye-slash"></i></a></td>']);

  $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon" style="border-left: 5px solid ###farbe###">###VALUE###</td>']);
  $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

  // --------------------
  //  remove Colums
  // --------------------

  $list->removeColumn('id');
  $list->removeColumn('beschreibung');
  $list->removeColumn('farbe');
  $list->removeColumn('kategorie_farbe');
  $list->removeColumn('kategorie_id');
  $list->removeColumn('kategorie_name');
  $list->removeColumn('createdate');
  $list->removeColumn('createuser');
  $list->removeColumn('updateuser');
  $list->removeColumn('observer');

  // --------------------
  //  set Sortable
  // --------------------

  $list->setColumnSortable('kategorie');
  $list->setColumnSortable('eigentuemer');
  $list->setColumnSortable('prio');
  $list->setColumnSortable('status');
  $list->setColumnSortable('updatedate');
  $list->setColumnSortable('finaldate');

  // --------------------
  //
  //  Aufgaben (title)
  //  Beschreibung
  //
  // --------------------

  if ($aktueller_erledigt_status == 0) {
    $titleLink = '<a id="erledigtverbergen" class="erledigtschalter" title="Erledigte Aufgaben verbergen" href="javascript:void(0);">Aufgaben<i class="rex-icon fa-check-square-o"></i></a>';
  }
  else {
    $titleLink = '<a id="erledigtanzeigen" class="erledigtschalter" title="Erledigte Aufgaben anzeigen" href="javascript:void(0);">Aufgaben<i class="rex-icon fa-square-o"></i></a>';
  }

  $list->setColumnLabel('titel', $titleLink);
  $list->setColumnLayout('titel', ['<th>###VALUE###</th>', '<td data-title="Aufgaben" class="td_aufgaben">###VALUE###</td>']);
  $list->setColumnFormat('titel', 'custom',
  function ($params)
  {
    $list = $params['list'];
    if ($list->getValue('beschreibung') != '') {
      $aufgabe = '<div class="collapsetitel" data-toggle="collapse" data-target="#collapse###id###">' . $list->getValue('titel') . '</div>';
    }
    else {
      $aufgabe = $list->getValue('titel');
    }

    if ($list->getValue('beschreibung')) {
      $text = htmlspecialchars_decode($list->getValue('beschreibung'));
      if (rex_addon::get('textile')->isAvailable()) {
        $text = str_replace('<br />', '', $text);
        $text = rex_textile::parse($text);
        $text = str_replace('###', '&#x20;', $text);
      }
      else {
        $text = str_replace(PHP_EOL, '<br/>', $text);
      }

      $user_name = rex::getUser()->getValue('login');
      $text = str_replace('*****', '<div class="aufgabentrenner">' . date("d.m.y") . ' - ' . htmlspecialchars($user_name) . '</div>', $text);
      $beschreibung = '<div id="collapse###id###" class="collapse"><br/>' . $text . '</div>';
    }
    else {
      $beschreibung = '';
    }

    $aufgabe.= $beschreibung;
    return $aufgabe;
  });

  // --------------------
  //
  //  UpdateDate
  //
  // --------------------

  $list->setColumnLabel('updatedate', 'Letzte Aktualisierung');
  $list->setColumnLayout('updatedate', ['<th>###VALUE###</th>', '<td data-title="Letze Aktualisierung" class="td_updatedate">###VALUE###</td>']);
  $list->setColumnFormat('updatedate', 'custom',
  function ($params)
  {
    $list = $params['list'];
    if ($list->getValue('updatedate') == '0000-00-00 00:00:00') {
      $updatedatevalue = '-';
    }
    else {
      $updatedatevalue = date('d.m.Y H:i', strtotime($list->getValue('updatedate')));
    }

    if ($list->getValue('updateuser') == '') {
      $updateuservalue = '';
    }
    else {
      $updateuservalue = $list->getValue('updateuser');
    }

    $updatedate = $updatedatevalue . '<br/><span>' . $updateuservalue . '</span>';
    return $updatedate;
  });

  // --------------------
  //
  //  Finaldate
  //
  // --------------------

  $list->setColumnLabel('finaldate', 'Fällig');
  $list->setColumnLayout('finaldate', ['<th>###VALUE###</th>', '<td data-title="Fälligkeitsdatum" class="td_finaldate">###VALUE###</td>']);
  $list->setColumnFormat('finaldate', 'custom',
  function ($params)
  {
    $list = $params['list'];
    if ($list->getValue('finaldate') != '') {
      if ($list->getValue('finaldate') <= date('Y-m-d')) {
        $finaldate = "<span class='text-danger'>" . date('d.m.Y', strtotime($list->getValue('finaldate'))) . "</span>";
      }
      else {
        $finaldate = date('d.m.Y', strtotime($list->getValue('finaldate')));
      }
    }
    else {
      $finaldate = '';
    }

    return $finaldate;
  });

  // --------------------
  //
  //  Kategoriefilter
  //
  // --------------------

  $kategoriefilter = '';
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setQuery('SELECT k.* FROM rex_aufgaben_kategorien k INNER JOIN rex_aufgaben_aufgaben a ON (a.kategorie = k.id) GROUP BY k.id ORDER BY k.kategorie');

  if ($sql->getRows() > 1) {
    $kategoriefilter = "<select id='kategoriefilter' multiple>";
    for ($i = 0; $i < $sql->getRows(); $i++) {
      $kat_ids = explode(',', $filter_kategorien);
      if (in_array($sql->getValue('id') , $kat_ids)) {
        $selected = 'selected';
      }
      else {
        $selected = '';
      }

      $kategoriefilter.= '<option value="' . $sql->getValue('id') . '" ' . $selected . '>' . $sql->getValue('kategorie') . '</option>';
      $sql->next();
    }
    $kategoriefilter.= "</select>";
  } else {
    $kategoriefilter = '';
  }

  // --------------------
  //
  //  Kategorie
  //
  // --------------------

  $list->setColumnLabel('kategorie', 'Kategorie');
  $list->setColumnLayout('kategorie', ['<th>###VALUE###<br/>' . $kategoriefilter . '</th>', '<td data-title="Kategorie" class="td_kategorie">###VALUE###</td>']);
  $list->setColumnFormat('kategorie', 'custom',
  function ($params)
  {
    $list = $params['list'];
    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable(rex::getTablePrefix() . 'aufgaben_kategorien');
    $sql->setWhere(['id' => $list->getValue('kategorie') ]);
    $sql->select();
    $kategorie = $sql->getValue('kategorie');
    return $kategorie;
  });

  // --------------------
  //
  //  Eigentümerfilter (Zuständig)
  //
  // --------------------

  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setQuery('SELECT * FROM rex_user ORDER BY login');
  if ($sql->getRows() > 1) {
    $eigentuemerfilter = "<select id='eigentuemerfilter' multiple>";
    for ($i = 0; $i < $sql->getRows(); $i++) {
      $filter_eigentuemer_ids = explode(',', $filter_eigentuemer);
      if (in_array($sql->getValue('id') , $filter_eigentuemer_ids)) {
        $selected = 'selected';
      }
      else {
        $selected = '';
      }

      $eigentuemerfilter.= '<option value="' . $sql->getValue('id') . '" ' . $selected . '>' . $sql->getValue('login') . '</option>';
      $sql->next();
    }

    $eigentuemerfilter.= "</select>";
  } else {
    $eigentuemerfilter = '';
  }

  // --------------------
  //
  //  Eigentümer (Zuständig)
  //
  // --------------------

  $list->setColumnLabel('eigentuemer', 'Zuständig');
  $list->setColumnLayout('eigentuemer', ['<th>###VALUE###<br/>' . $eigentuemerfilter . '</th>', '<td data-title="Zuständig" class="td_eigentuemer">###VALUE###</td>']);
  $list->setColumnFormat('eigentuemer', 'custom',
  function ($params)
  {
    $list = $params['list'];
    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_user');
    $sql->setWhere(['id' => $list->getValue('eigentuemer') ]);
    $sql->select();
    $eigentuemer = $sql->getValue('login');
    return $eigentuemer;
  });

  // --------------------
  //
  //  Priofilter
  //
  // --------------------

  $filter_prio_arr = str_split($filter_prio);
  $priofilter = "<select id='priofilter' multiple>";
  for ($i = 1; $i <= 3; $i++) {
    if (in_array($i, $filter_prio_arr)) {
      $selected = 'selected';
    }
    else {
      $selected = '';
    }

    $priofilter.= '<option value="' . $i . '" ' . $selected . '>Prio ' . $i . '</option>';
  }

  $priofilter.= "</select>";

  // --------------------
  //
  //  Prio
  //
  // --------------------

  $list->setColumnLabel('prio', 'Prio');
  $list->setColumnLayout('prio', ['<th>###VALUE###<br/>' . $priofilter . '</th>', '<td data-title="Prio" class="td_prio">###VALUE###</td>']);
  $list->setColumnFormat('prio', 'custom',
  function ($params)
  {
    $list = $params['list'];
    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable(rex::getTablePrefix() . 'aufgaben_aufgaben');
    $sql->setWhere(['id' => $list->getValue('prio') ]);
    $sql->select();
    $prio = "<div class='priowrapper'>";
    for ($i = 0; $i < 4; $i++) {
      if ($list->getValue('prio') < $i) {
        $star = 'fa-star-o';
      }
      else {
        $star = 'fa-star';
      }

      $list->addLinkAttribute('prio', 'title', 'Prio: ' . $i);
      $list->setColumnParams('prio', ['func' => 'setprio', 'id' => '###id###', 'neueprio' => $i]);
      if ($i == 0) {
        $prio.= $list->getColumnLink('prio', '<i class="rex-icon fa-remove "></i>');
        continue;
      }

      $prio.= $list->getColumnLink('prio', '<i class="rex-icon ' . $star . ' "></i>');
      $sql->next();
    }

    $prio.= "</div>";
    return $prio;
  });

  // --------------------
  //
  //  Statusfilter
  //
  // --------------------

  $statusfilter = '';
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setQuery('SELECT * FROM rex_aufgaben_status ORDER BY id');
  $statusfilter = "<select id='statusfilter' multiple>";
  for ($i = 0; $i < $sql->getRows(); $i++) {
    $filter_status_ids = explode(',', $filter_status);
    if (in_array($sql->getValue('id') , $filter_status_ids)) {
      $selected = 'selected';
    }
    else {
      $selected = '';
    }

    $statusfilter.= '<option value="' . $sql->getValue('id') . '" ' . $selected . '>' . $sql->getValue('status') . '</option>';
    $sql->next();
  }

  $statusfilter.= "</select>";

  // --------------------
  //
  //  Status
  //
  // --------------------

  $list->setColumnLabel('status', 'Status');
  $list->setColumnLayout('status', ['<th>###VALUE###<br/>' . $statusfilter . '</th>', '<td data-title="Status" class="td_status">###VALUE###</td>']);
  $list->setColumnFormat('status', 'custom',
  function ($params)
  {
    $list = $params['list'];
    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable(rex::getTablePrefix() . 'aufgaben_status');
    $sql->select();
    $status = "<div class='status'>";
    for ($i = 0; $i < $sql->getRows(); $i++) {
      if ($list->getValue('status') == $sql->getValue('id')) {
        $current = 'current';
      }
      else {
        $current = '';
      }

      $list->addLinkAttribute('status', 'title', $sql->getValue('status'));
      $list->setColumnParams('status', ['func' => 'setstatus', 'id' => '###id###', 'neuerstatus' => $sql->getValue('id') ]);
      $status.= $list->getColumnLink('status', '<i class="rex-icon ' . $current . ' ' . $sql->getValue('icon') . ' "></i>');
      $sql->next();
    }

    $status.= "</div>";
    return $status;
  });

  $content = '<div id="aufgaben">' . $list->get() . '</div>';
  $fragment = new rex_fragment();
  $fragment->setVar('content', $content, false);
  echo $fragment->parse('core/page/section.php');
}
elseif ($func == 'edit' || $func == 'add') {
  $fieldset = $func == 'edit' ? 'Aufgabe editieren' : 'Aufgabe hinzufügen';
  $id = rex_request('id', 'int');
  $form = rex_form::factory(rex::getTablePrefix() . 'aufgaben_aufgaben', '', 'id=' . $id);
  $field = $form->addTextField('titel');
  $field->setLabel('Titel');
  $field->getValidator()->add('notEmpty', 'Bitte einen Titel angeben.');
  $field = $form->addTextareaField('beschreibung');
  $field->setLabel('Beschreibung');
  $field = $form->addSelectField('kategorie');
  $field->setLabel("Kategorie");
  $field->setPrefix('<div class="rex-select-style">');
  $field->setSuffix('</div>');
  $field->getValidator()->add('notEmpty', 'Bitte eine Kategorie auswählen.');
  $select = $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT kategorie as label, id FROM rex_aufgaben_kategorien ORDER BY kategorie';
  $select->addOption('Bitte wählen', '');
  $select->addSqlOptions($query);
  $field = $form->addSelectField('eigentuemer');
  $field->setLabel('Zuständig');
  $field->setPrefix('<div class="rex-select-style">');
  $field->setSuffix('</div>');
  $field->getValidator()->add('notEmpty', 'Bitte geben Sie an wer für diese Aufgabe zuständig ist.');
  $select = $field->getSelect();
  if ($func == 'add') {
    $select->setSelected($current_user);
  }

  $select->setSize(1);
  $field = $form->addTextField('finaldate');
  $field->setAttribute('id', 'datepicker');
  $field->setLabel('Fälligkeitsdatum');

  // date('d.m.Y H:i:s'

  $query = 'SELECT login as label, id FROM rex_user';
  $select->addOption('Bitte wählen', '');
  $select->addSqlOptions($query);
  $field = $form->addSelectField('status');
  $field->setLabel('Status');
  $field->setPrefix('<div class="rex-select-style">');
  $field->setSuffix('</div>');
  $select = $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT status as label, id FROM rex_aufgaben_status';

  // $select->addOption('Offen',1);
  // $select->addOption('Erledigt',2);

  $select->addSqlOptions($query);
  if ($func == 'add') {
    $form->addParam('aufgabe', 'new');
  }

  if ($func == 'edit') {
    $form->addParam('aufgabe', 'edit');
    if ($form->getSql()->getValue('updateuser') != '') {
      $form->getSql()->setValue('updatedate', date('d.m.Y H:i:s', $form->getSql()->getDateTimeValue('updatedate')));
      $field = $form->addReadonlyField('updatedate');
      $field->setHeader('<hr/><div class="row"><div class="col-md-6">');
      $field->setFooter('</div>');
      $field->setLabel('Letzte Änderung am ');
      $field = $form->addReadonlyField('updateuser');
      $field->setHeader('<div class="col-md-6">');
      $field->setFooter('</div></div>');
      $field->setLabel('von');
    }

    if ($form->getSql()->getValue('createuser') != '') {
      $form->getSql()->setValue('createdate', date('d.m.Y H:i:s', $form->getSql()->getDateTimeValue('createdate')));
      $field = $form->addReadonlyField('createdate');
      $field->setHeader('<div class="row"><div class="col-md-6">');
      $field->setFooter('</div>');
      $field->setLabel('Erstellt am ');
      $field = $form->addReadonlyField('createuser');
      $field->setHeader('<div class="col-md-6">');
      $field->setFooter('</div></div><br/>');
      $field->setLabel('von');
    }

    $form->addParam('id', $id);
  }

  $content = $form->get();
  $fragment = new rex_fragment();
  $fragment->setVar('class', 'edit', false);
  $fragment->setVar('title', "$fieldset");
  $fragment->setVar('body', $content, false);
  $content = $fragment->parse('core/page/section.php');
  echo $content;

}

?>
<script>
$('#kategoriefilter').SumoSelect({okCancelInMulti: true });

$('#priofilter').SumoSelect({ okCancelInMulti: true });
$('#eigentuemerfilter').SumoSelect({ okCancelInMulti: true });
$('#statusfilter').SumoSelect({ okCancelInMulti: true });

$("#kategoriefilter").change(function(){
     $value_k = $("#kategoriefilter").val();
     if ($value_k == null) {$value_k = '0'};
     location.replace("index.php?page=aufgaben/aufgaben&func=filter&filter_kategorien="+$value_k );
});
$("#eigentuemerfilter").change(function(){
     $value_e = $("#eigentuemerfilter").val();
     if ($value_e == null) $value_e = '0';
     location.replace("index.php?page=aufgaben/aufgaben&func=filter&filter_eigentuemer="+$value_e );
});
$("#priofilter").change(function(){
     $value_p = $("#priofilter").val();
     if ($value_p == null) {$value_p = '0'};
     location.replace("index.php?page=aufgaben/aufgaben&func=filter&filter_prio="+$value_p );
});
$("#statusfilter").change(function(){
     $value_s = $("#statusfilter").val();
     if ($value_s == null) {$value_s = '0'};
     location.replace("index.php?page=aufgaben/aufgaben&func=filter&filter_status="+$value_s );
});

$("#erledigtverbergen").click(function(){
  location.replace("index.php?page=aufgaben/aufgaben&func=erledigtfilter&filter_erledigt=1" );
});
$("#erledigtanzeigen").click(function(){
  location.replace("index.php?page=aufgaben/aufgaben&func=erledigtfilter&filter_erledigt=0" );
});

$(".watch").click(function(){

  // location.replace("index.php?page=aufgaben/aufgaben&func=erledigtfilter&filter_erledigt=0" );

  $(this).toggleClass( "enabled" );
});

$("select.form-control").on('change', function () {
  $(this).blur();
});

 var picker = new Pikaday(
    {
      field: $('#datepicker')[0] ,
      format: 'DD.MM.YYYY',
      i18n: {
        previousMonth : 'Nächster Monat',
        nextMonth     : 'Vorheriger Monat',
        months        : ['Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'],
        weekdays      : ['Sonntag','Montag','Dienstag','Mittwoch','Donjnerstag','Freitag','Samstag'],
        weekdaysShort : ['So','Mo','Di','Mi','Do','Fr','Sa']
      }
    }
);


</script>
