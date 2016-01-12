<?php

$func = rex_request('func', 'string');

if ($func == 'setstatus') {
  $new_status = (rex_request('neuerstatus', 'int'));
  $id = (rex_request('id', 'int'));

  $sql = rex_sql::factory();
  // $sql->setDebug();
  $sql->setTable('rex_aufgaben_aufgaben');
  $sql->setWhere('id = '.$id);
  $sql->setValue('status', $new_status);

  if ($sql->update()) {
     echo '<div class="alert alert-success">Der Status wurde aktualisiert.</div>';
  }
  $func = '';
}

if ($func == 'setprio') {
  $new_prio = (rex_request('neueprio', 'int'));
  $id = (rex_request('id', 'int'));

  $sql = rex_sql::factory();
  // $sql->setDebug();
  $sql->setTable('rex_aufgaben_aufgaben');
  $sql->setWhere('id = '.$id);
  $sql->setValue('prio', $new_prio);

  if ($sql->update()) {
     echo '<div class="alert alert-success">Die Priorität wurde aktualisiert.</div>';
  }
  $func = '';
}

if ($func == '') {
  $query = 'SELECT * FROM rex_aufgaben_aufgaben WHERE status = 6 ORDER BY id DESC';

  $list = rex_list::factory($query, 30, 'aufgaben');
  $list->setNoRowsMessage('Keine Aufgaben vorhanden');

  $tdIcon = '<i class="rex-icon fa-calendar-check-o"></i>';
  $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '"><i class="rex-icon rex-icon-add"></i></a>';
  $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
  $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

  $list->removeColumn('id');
  $list->removeColumn('beschreibung');

  $list->setColumnSortable('kategorie');
  $list->setColumnSortable('eigentuemer');
  $list->setColumnSortable('prio');

  $list->setColumnSortable('status');

  $list->setColumnLabel('titel', 'Aufgaben');
  $list->setColumnLayout('titel', ['<th>###VALUE###</th>', '<td class="td_aufgaben">###VALUE###</td>']);
  $list->setColumnFormat('titel', 'custom', function ($params) {
    $list = $params['list'];
    if ($list->getValue('beschreibung') != '') {
      $aufgabe = '<div class="collapsetitel" data-toggle="collapse" data-target="#collapse###id###">' . $list->getValue('titel') . '</div>';
    } else {
      $aufgabe = $list->getValue('titel');
    }

    if($list->getValue('beschreibung')) {
      $textile = htmlspecialchars_decode($list->getValue('beschreibung'));
      $textile = str_replace('<br />', '', $textile);
      $textile = rex_textile::parse($textile);
      $textile = str_replace('###', '&#x20;', $textile);

      $user_name = rex::getUser()->getValue('name') != '' ? rex::getUser()->getValue('name') : rex::getUser()->getValue('login');
      $textile = str_replace('*****', '<div class="aufgabentrenner">'.date("d.m.y").' - '. htmlspecialchars($user_name).'</div>', $textile);
      $beschreibung = '<div id="collapse###id###" class="collapse"><br/>'.$textile.'</div>';
    } else {
      $beschreibung = '';
    }
    $aufgabe .= $beschreibung;
    return $aufgabe;
  });


  // Kategorie
  $list->setColumnLabel('kategorie', 'Kategorie');
  $list->setColumnLayout('kategorie', ['<th>###VALUE###</th>', '<td class="td_kategorie">###VALUE###</td>']);
  $list->setColumnFormat('kategorie', 'custom', function ($params) {
    $list = $params['list'];
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setTable(rex::getTablePrefix().'aufgaben_kategorien');
    $sql->setWhere(['id'=>$list->getValue('kategorie')]);
    $sql->select();
    $kategorie = $sql->getValue('kategorie');
    return $kategorie;
  });

  // Eigentümer
 $list->setColumnLabel('eigentuemer', 'Zuständig');
 $list->setColumnLayout('eigentuemer', ['<th>###VALUE###</th>', '<td class="td_eigentuemer">###VALUE###</td>']);
 $list->setColumnFormat('eigentuemer', 'custom', function ($params) {
   $list = $params['list'];
   $sql = rex_sql::factory();
   // $sql->setDebug();
   $sql->setTable('rex_user');
   $sql->setWhere(['id'=>$list->getValue('eigentuemer')]);
   $sql->select();
   $eigentuemer = $sql->getValue('name');
   return $eigentuemer;
  });

  // prio
  $list->setColumnLabel('prio', 'Prio');
  $list->setColumnLayout('prio', ['<th>###VALUE###</th>', '<td class="td_prio">###VALUE###</td>']);
  $list->setColumnFormat('prio', 'custom', function ($params) {
    $list = $params['list'];
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setTable(rex::getTablePrefix().'aufgaben_aufgaben');
    $sql->setWhere(['id'=>$list->getValue('prio')]);
    $sql->select();
    $prio = "<div class='priowrapper'>";
    for($i=1; $i<4; $i++) {
      if($list->getValue('prio') < $i) {
        $star  = 'fa-star-o';
      } else {
        $star  = 'fa-star';
      }
        $list->setColumnParams('prio', ['func' => 'setprio', 'id' => '###id###', 'neueprio' => $i]);
        $prio .= $list->getColumnLink('prio', '<i class="rex-icon '.$current.' '.$star.' "></i>');
        $sql->next();
      }
      $prio .= "</div>";
      return $prio;
    });

  // Status
  $list->setColumnLabel('status', 'Status');
  $list->setColumnLayout('status', ['<th>###VALUE###</th>', '<td class="td_status">###VALUE###</td>']);
  $list->setColumnFormat('status', 'custom', function ($params) {
    $list = $params['list'];
    $sql = rex_sql::factory();
    //$sql->setDebug();
    $sql->setTable(rex::getTablePrefix().'aufgaben_status');
    $sql->select();
    $status = "<div class='stati'>";
    for($i=0; $i<$sql->getRows(); $i++) {
      if($list->getValue('status') == $sql->getValue('id')) {
        $current  = 'current';
      } else {
        $current  = '';
      }
        $list->setColumnParams('status', ['func' => 'setstatus', 'id' => '###id###', 'neuerstatus' => $sql->getValue('id')]);
        $status .= $list->getColumnLink('status', '<i class="rex-icon '.$current.' '.$sql->getValue('icon').' "></i>');
        $sql->next();
      }
    $status .= "</div>";
    return $status;
  });

  $list->addColumn('edit', '<i class="rex-icon rex-icon-edit"></i>');
  $list->setColumnLayout('edit', ['<th>###VALUE###</th>', '<td class="td_edit">###VALUE###</td>']);
  $list->setColumnLabel('edit', '');
  $list->setColumnParams('edit', ['func' => 'edit', 'id' => '###id###']);
  $list->addLinkAttribute('', 'class', 'rex-edit');

  $content = $list->get();
  $fragment = new rex_fragment();
  $fragment->setVar('content', $content, false);
  echo $fragment->parse('core/page/section.php');

} elseif ($func == 'edit' || $func == 'add') {

  $fieldset = $func == 'edit' ? 'Aufgabe editieren' : 'Aufgabe hinzufügen';
  $id = rex_request('id', 'int');

  $form = rex_form::factory(rex::getTablePrefix().'aufgaben_aufgaben', '', 'id='.$id);

  $field = $form->addTextField('titel');
  $field->setLabel('Titel');

  $field = $form->addTextareaField('beschreibung');
  $field->setLabel('Beschreibung');

  $field = $form->addSelectField('kategorie');
  $field->setLabel("Kategorie");
  $field->getValidator()->add('notEmpty', 'Bitte eine Kategorie auswählen.');
  $select =& $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT kategorie as label, id FROM rex_aufgaben_kategorien';
  $select->addOption('Bitte wählen','');
  $select->addSqlOptions($query);


  $field = $form->addSelectField('eigentuemer');
  $field->setLabel('Zuständig');
  $field->getValidator()->add('notEmpty', 'Bitte geben Sie an wer für dies Aufgabe zustädig ist.');
  $select =& $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT name as label, id FROM rex_user';
  $select->addOption('Bitte wählen','');
  $select->addSqlOptions($query);

  $field = $form->addSelectField('status');
  $field->setLabel('Status');
  $select =& $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT status as label, id FROM rex_aufgaben_status';
  # $select->addOption('Offen',1);
  # $select->addOption('Erledigt',2);
  $select->addSqlOptions($query);

  if ($func == 'edit') {
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
