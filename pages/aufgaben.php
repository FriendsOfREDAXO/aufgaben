<?php

// *************************************
//  MarkItUp
// *************************************
if(rex_addon::get('rex_markitup')->isAvailable()) {
  if (!rex_markitup::profileExists('simple')) {

  rex_markitup::insertProfile ('simple', $this->i18n('aufgaben_markitupinfo'), 'textile', 300, 800, 'relative', 'bold,italic,underline,deleted,quote,sub,sup,code,unorderedlist,grouplink[internal|external|mailto]');

  }
}

// *************************************
//  Vars
// *************************************

$func               = rex_request('func', 'string');
$aufgabe            = rex_request('aufgabe', 'string');
$filter_category    = rex_request('filter_category', 'string');
$change_category    = rex_request('change_category', 'string');
$filter_responsible = rex_request('filter_responsible', 'string');
$change_responsible = rex_request('change_responsible', 'string');
$filter_prio        = rex_request('filter_prio', 'string');
$filter_status      = rex_request('filter_status', 'string');
$filter_done        = rex_request('filter_done', 'string');
$current_article_id = rex_request('id', 'int');
$current_user       = rex::getUser()->getId();
$sql_aufgabe        = rex_sql::factory();
$sql_aufgabe->setQuery('SELECT * FROM rex_aufgaben');

  // Mails verschicken
  $mailbetreff = '';
  if ($aufgabe == 'new') {
  	if($this->getConfig('mails') == null) {
  		echo '<div class="alert alert-success">Es wurde keine E-Mail Adresse hinterlegt</div>';
  	}
  	else if($this->getConfig('mails') != null) {
       $mail = new rex_aufgaben();
        $responsibleRequest = rex_sql::factory();
        $responsibleRequest->setQuery('SELECT * FROM rex_aufgaben WHERE responsible != 0');
        foreach($responsibleRequest as $row) {
            $bodyText = "";
            $sql = rex_sql::factory();
            $responseId = $responsibleRequest->getValue('responsible');
    		 $sql->setQuery('SELECT * FROM rex_aufgaben WHERE responsible = ' .$responseId);
            foreach($sql as $row) {
            	$aktuelle_id = $sql->getValue('id');
                $checkTimer = $mail->checkTime($sql->getValue('id'));
                if($checkTimer == true) {
               	 $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '1' WHERE id = ".$aktuelle_id);     
                 $bodyText .= $mail->createMailText($aktuelle_id);
                }
    		 }
        	 
            $mail->send_mails($this->getConfig('mails'),$aktuelle_id, $aufgabe, $mailbetreff, $bodyText, $responseId);             
        }
    }
}


    
  
  if ($aufgabe == 'edit') {
    	if($this->getConfig('mails') == null) {
  		echo '<div class="alert alert-success">Es wurde keine E-Mail Adresse hinterlegt</div>';
  	}
  	 else if($this->getConfig('mails') != null) {
    	$mailbetreff =  $this->i18n('aufgaben_mail_change'); 
        $mail = new rex_aufgaben();
        $responsibleRequest = rex_sql::factory();
        $responsibleRequest->setQuery('SELECT * FROM rex_aufgaben WHERE responsible != 0');
        foreach($responsibleRequest as $row) {
            $bodyText = "";
            $sql = rex_sql::factory();
            $responseId = $responsibleRequest->getValue('responsible');
    		 $sql->setQuery('SELECT * FROM rex_aufgaben WHERE responsible = ' .$responseId);
            foreach($sql as $row) {
            	$aktuelle_id = $sql->getValue('id');
                $checkTimer = $mail->checkTime($sql->getValue('id'));
                if($checkTimer == true) {
                	 $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '1' WHERE id = ".$aktuelle_id);     
                 $bodyText .= $mail->createMailText($aktuelle_id);
                 
                }
    		 }
    
            $mail->send_mails($this->getConfig('mails'),$aktuelle_id, $aufgabe, $mailbetreff, $bodyText, $responseId);             
        }
  	 }       
}
//    $mail->send_mails($this->getConfig('mails'), $aktuelle_id, $aufgabe, $mailbetreff);
  if ($aufgabe == 'edit' || $aufgabe == 'new') {
       $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '2' WHERE id = ".$current_article_id);      
  }


// *************************************
//  Erledigtschalter
// *************************************

if ($func == 'donefilter') {
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setTable('rex_aufgaben_filter');
  $sql->setWhere('user = ' . $current_user);
  $sql->select();
  if ($sql->getRows() > 0) {
    if ($filter_done == '') {
      $filter_done = '0';
    }

    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_filter');
    $sql->setWhere('user = ' . $current_user);
    $sql->setValue('done', $filter_done);
    $sql->update();
  }
  else {
    if ($filter_done == '') {
      $filter_done = '0';
    }

    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_filter');
    $sql->setValue('done', '1');
    $sql->insert();
  }

  $func = '';
}

// *************************************
//  Status
// *************************************

if ($func == 'setstatus') {
   $checkTimeNow = date('Y-m-d H:i:s');
  $new_status = (rex_request('neuerstatus', 'int'));
  $id = rex_request('id', 'int');
  $sql = rex_sql::factory();
  // $sql->setDebug();
  $sql->setTable('rex_aufgaben');
  $sql->setWhere('id = ' . $id);
  $sql->setValue('updatedate', $checkTimeNow);
  $sql->setValue('status', $new_status);
  $sql->setValue('versendet', '2');
  if ($sql->update()) {
  	if($this->getConfig('mails') == null) {
  		echo '<div class="alert alert-success">Es wurde keine E-Mail Adresse hinterlegt</div>';
  	}
  	 else if($this->getConfig('mails') != null) {
        $mail = new rex_aufgaben();
        $responsibleRequest = rex_sql::factory();
        $responsibleRequest->setQuery('SELECT * FROM rex_aufgaben WHERE responsible != 0');
        foreach($responsibleRequest as $row) {
            $bodyText = "";
            $sql = rex_sql::factory();
            $responseId = $responsibleRequest->getValue('responsible');
    		 $sql->setQuery('SELECT * FROM rex_aufgaben WHERE responsible = ' .$responseId);
                foreach($sql as $row) {
            		$aktuelle_id = $sql->getValue('id');
                $checkTimer = $mail->checkTime($sql->getValue('id'));
                if($checkTimer == true) {
                	 $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '1' WHERE id = ".$aktuelle_id);     
                 $bodyText .= $mail->createMailText($aktuelle_id);
                 
                }
    		 }
    		 $mail->send_mails($this->getConfig('mails'),$aktuelle_id, 'change', $this->i18n('aufgaben_mail_change_status'), $bodyText, $responseId);             
        }
  	 }
  }
  $func = '';

}
// *************************************
//  Set Prio
// *************************************

if ($func == 'setprio') {
  $checkTimeNow = date('Y-m-d H:i:s');
  $new_prio = (rex_request('neueprio', 'int'));
  $id = (rex_request('id', 'int'));
    $form = rex_form::factory(rex::getTablePrefix() . 'aufgaben', '', 'id=' . $id);
    $form->getSql()->setValue('updatedate', date('d.m.Y H:i:s', $form->getSql()->getDateTimeValue('updatedate')));
    $field = $form->addReadonlyField('updatedate');
  $sql = rex_sql::factory();
 
  // $sql->setDebug();
  $sql->setTable('rex_aufgaben');
  $sql->setValue('updatedate', $checkTimeNow);
  $sql->setWhere('id = ' . $id);
  $sql->setValue('prio', $new_prio);
  $sql->setValue('versendet', '2');
  if ($sql->update()) {
	if($this->getConfig('mails') == null) {
  		echo '<div class="alert alert-success">Es wurde keine E-Mail Adresse hinterlegt</div>';
  	}
  	else if($this->getConfig('mails') != null) {

        $mail = new rex_aufgaben();
        $responsibleRequest = rex_sql::factory();
        $responsibleRequest->setQuery('SELECT * FROM rex_aufgaben WHERE responsible != 0');
        foreach($responsibleRequest as $row) {
            $bodyText = "";
            $sql = rex_sql::factory();
            $responseId = $responsibleRequest->getValue('responsible');
    		 $sql->setQuery('SELECT * FROM rex_aufgaben WHERE responsible = ' .$responseId);
                foreach($sql as $row) {
            		$aktuelle_id = $sql->getValue('id');
                $checkTimer = $mail->checkTime($sql->getValue('id'));
                if($checkTimer == true) {
                	 $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '1' WHERE id = ".$aktuelle_id);     
                 $bodyText .= $mail->createMailText($aktuelle_id);
                 
                }
    		 }
    

        
            $mail->send_mails($this->getConfig('mails'),$aktuelle_id, 'change', $this->i18n('aufgaben_mail_change_prio'), $bodyText, $responseId);             
        } 
  	}
  }

  $func = '';
}

// *************************************
//  Change Eigentümer
// *************************************

if ($func == 'change_responsible') {
 $checkTimeNow = date('Y-m-d H:i:s');
  $responsible_ids = explode(",",$change_responsible);
  $mail = new rex_aufgaben();
  $sql = rex_sql::factory();
  // $sql->setDebug();

  $sql->setTable('rex_aufgaben');
  $sql->setValue('updatedate', $checkTimeNow);
  $sql->setWhere('id = ' . $responsible_ids[0]);
  $sql->setValue('responsible', $responsible_ids[1]);
  $sql->setValue('versendet', '2');
  if ($sql->update()) {
  	if($this->getConfig('mails') == null) {
  		echo '<div class="alert alert-success">Es wurde keine E-Mail Adresse hinterlegt</div>';
  	}
  	 else if($this->getConfig('mails') != null) {
        $mail = new rex_aufgaben();
        $responsibleRequest = rex_sql::factory();
        $responsibleRequest->setQuery('SELECT * FROM rex_aufgaben WHERE responsible != 0');
        foreach($responsibleRequest as $row) {
            $bodyText = "";
            $sql = rex_sql::factory();
            $responseId = $responsibleRequest->getValue('responsible');
    		 $sql->setQuery('SELECT * FROM rex_aufgaben WHERE responsible = ' .$responseId);
                foreach($sql as $row) {
            		$aktuelle_id = $sql->getValue('id');
                $checkTimer = $mail->checkTime($sql->getValue('id'));
                if($checkTimer == true) {
                	 $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '1' WHERE id = ".$aktuelle_id);     
                 $bodyText .= $mail->createMailText($aktuelle_id);
                 
                }
    		 }
            $mail->send_mails($this->getConfig('mails'),$aktuelle_id, 'change', $this->i18n('aufgaben_mail_change_eigentuemer'), $bodyText, $responseId);             
        }
	}
  }
  $func = '';
}

// *************************************
//  Change category
// *************************************

if ($func == 'change_category') {

  $category_ids = explode(",",$change_category);

  $sql = rex_sql::factory();
  // $sql->setDebug();

  $sql->setTable('rex_aufgaben');
  $sql->setWhere('id = ' . $category_ids[0]);
  $sql->setValue('category', $category_ids[1]);
 
  if ($sql->update()) {
  	if($this->getConfig('mails') == null) {
  		echo '<div class="alert alert-success">Es wurde keine E-Mail Adresse hinterlegt</div>';
  	}
  	else if($this->getConfig('mails') != null) {
  	 $mail = new rex_aufgaben();
        $responsibleRequest = rex_sql::factory();
        $responsibleRequest->setQuery('SELECT * FROM rex_aufgaben WHERE responsible != 0');
        foreach($responsibleRequest as $row) {
            $bodyText = "";
            $sql = rex_sql::factory();
            $responseId = $responsibleRequest->getValue('responsible');
    		 $sql->setQuery('SELECT * FROM rex_aufgaben WHERE responsible = ' .$responseId);
            foreach($sql as $row) {
            	$aktuelle_id = $sql->getValue('id');
                $checkTimer = $mail->checkTime($sql->getValue('id'));
                if($checkTimer == true) {
                	 $sql_aufgabe->setQuery("UPDATE rex_aufgaben SET versendet = '1' WHERE id = ".$aktuelle_id);     
                 $bodyText .= $mail->createMailText($aktuelle_id);
                 
                }
    		 }
            $mail->send_mails($this->getConfig('mails'),$aktuelle_id, $aufgabe, $mailbetreff, $bodyText, $responseId);             
        }
  	}
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
    if ($filter_category == '') {
      $filter_category = $sql->getValue('category');
    }

    if ($filter_responsible == '') {
      $filter_responsible = $sql->getValue('responsible');
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
    $sql->setValue('category', $filter_category);
    $sql->setValue('responsible', $filter_responsible);
    $sql->setValue('prio', $filter_prio);
    $sql->setValue('status', $filter_status);
    $sql->setWhere('user = ' . $current_user);
    $sql->update();
  }
  else {
    if ($filter_category == '') {
      $filter_category = '0';
    }

    if ($filter_responsible == '') {
      $filter_responsible = '0';
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
    $sql->setValue('category', $filter_category);
    $sql->setValue('responsible', $filter_responsible);
    $sql->setValue('prio', $filter_prio);
    $sql->setValue('status', $filter_status);
    $sql->insert();
  }

  if ($filter_category != '0') {
    $addsql.= ' AND a.category IN (' . $filter_category . ')';
  }

  if ($filter_responsible != '0') {
    $addsql.= ' AND a.responsible IN (' . $filter_responsible . ')';
  }

  if ($filter_prio != '0') {
    $addsql.= ' AND a.prio IN (' . $filter_prio . ')';
  }

  if ($filter_status != '0') {
    $addsql.= ' AND a.status IN (' . $filter_status . ')';
  }

  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setQuery('SELECT done FROM rex_aufgaben_filter WHERE user = ' . $current_user);
  $aktueller_done_status = $sql->getValue('done');
  if ($aktueller_done_status == 0) {
    $where = ' a.status > 0';
  }
  else {
    $where = ' a.status != 6';
  }

  $where .= ' and a.category = k.id and a.createuser = u.login ' . $addsql;  
  $query = '	SELECT a.*,
  			 	a.id AS id,
  			 	k.id,
  			 	k.category AS category_name,
  			 	k.color, u.name AS realname 
  			 	FROM ' . rex::getTable('aufgaben') . ' AS a, ' . rex::getTable('aufgaben_categories') . ' AS k, rex_user AS u WHERE ' . $where . ' GROUP BY a.id ORDER BY a.id DESC';

  
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


  $list->setNoRowsMessage('<div class="alert alert-info" role="alert">'.$this->i18n('aufgaben_no_task').'</div>');

  // --------------------
  //  Edit
  // --------------------

  $tdIcon = '<i class="rex-icon rex-icon-edit"></i>';
  $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '"><i class="rex-icon rex-icon-add"></i></a>';

  $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon" style="border-left: 5px solid ###color###">###VALUE###</td>']);
  $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

  // --------------------
  //  remove Colums
  // --------------------

  $list->removeColumn('id');
  $list->removeColumn('description');
  $list->removeColumn('color');
  $list->removeColumn('category_color');
  $list->removeColumn('category_id');
  $list->removeColumn('category_name');
  $list->removeColumn('createdate');
  $list->removeColumn('createuser');
  $list->removeColumn('updateuser');
  $list->removeColumn('observer');
  $list->removeColumn('realname');
  
  // --------------------
  //  set Sortable
  // --------------------

  $list->setColumnSortable('category');
  $list->setColumnSortable('responsible');
  $list->setColumnSortable('prio');
  $list->setColumnSortable('status');
  $list->setColumnSortable('updatedate');
  $list->setColumnSortable('finaldate');
  $list->setColumnSortable('versendet');
  

  // --------------------
  //
  //  Aufgaben (title)
  //  Beschreibung
  //
  // --------------------

  if ($aktueller_done_status == 0) {
    $titleLink = '<a id="doneverbergen" class="erledigtschalter" title="'.$this->i18n('aufgaben_task_hide').'" href="javascript:void(0);">'.$this->i18n('aufgaben_task').'<i class="rex-icon fa-check-square-o"></i></a>';
  }
  else {
    $titleLink = '<a id="doneanzeigen" class="erledigtschalter" title="Aufgaben anzeigen" href="javascript:void(0);">'.$this->i18n('aufgaben_task').'<i class="rex-icon fa-square-o"></i></a>';
  }

  $list->setColumnLabel('title', $titleLink);
  $list->setColumnLayout('title', ['<th>###VALUE###</th>', '<td data-title="'.$this->i18n('aufgaben_task').'" class="td_aufgaben">###VALUE###</td>']);
  $list->setColumnFormat('title', 'custom',
  function ($params)
  {
    $list = $params['list'];
    if ($list->getValue('description') != '') {
      $aufgabe = '<div class="collapsetitle" data-toggle="collapse" data-target="#collapse###id###">' . $list->getValue('title') . '</div>';
    }
    else {
      $aufgabe = $list->getValue('title');
    }

    if ($list->getValue('description')) {
      $text = $list->getValue('description');
      if (rex_addon::get('textile')->isAvailable()) {
        $text = str_replace('<br />', '', $text);
        $text = rex_textile::parse($text);
        $text = str_replace('###', '&#x20;', $text);
      }
      if (rex_addon::get('rex_markitup')->isAvailable()) {
          $text = rex_markitup::parseOutput('textile', $text);
      }

      if (!rex_addon::get('rex_markitup')->isAvailable() AND !rex_addon::get('textile')->isAvailable()) {
        $text = str_replace(PHP_EOL, '<br/>', $text);
      }

      $user_name = rex::getUser()->getValue('login');

      
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

  $list->setColumnLabel('updatedate', $this->i18n('aufgaben_last_update'));
  $list->setColumnLayout('updatedate', ['<th>###VALUE###</th>', '<td data-title="'.$this->i18n('aufgaben_last_update').'" class="td_updatedate">###VALUE###</td>']);
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
      $updateuservalue = $list->getValue('realname');   
    }

    $updatedate = $updatedatevalue . '<br/><span>' . $updateuservalue . '</span>';
    return $updatedate;
  });
  // --------------------
  //
  //  Versenden
  //
  // --------------------
  $list->setColumnLabel('versendet', $this->i18n('aufgaben_versendet'));
  $list->setColumnLayout('versendet', ['<th>###VALUE###</th>', '<td data-title="'.$this->i18n('aufgaben_versendet').'" class="td_versendet">###VALUE###</td>']);
  $list->setColumnFormat('versendet', 'custom', 
  function ($params) 
  {
  	$list = $params['list'];
  	if($list->getValue('versendet') == '1')
  		$versendet = "<i class='fa fa-envelope-o' aria-hidden='true'></i>";
  	else {
  		$versendet = '';
  		
  	}
  	return $versendet;
  });
  // --------------------
  //
  //  Finaldate
  //
  // --------------------

  $list->setColumnLabel('finaldate', $this->i18n('aufgaben_due'));
  $list->setColumnLayout('finaldate', ['<th>###VALUE###</th>', '<td data-title="'.$this->i18n('aufgaben_due_date').'" class="td_finaldate">###VALUE###</td>']);
  $list->setColumnFormat('finaldate', 'custom',
  function ($params)
  {
    $list = $params['list'];
    if ($list->getValue('finaldate') != '') {
      if (date_create($list->getValue('finaldate'))->format('Y-m-d') <= date('Y-m-d')) {
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
  //  categoryfilter
  //
  // --------------------

  $categoryfilter = '';
  $sql = rex_sql::factory();

  // $sql->setDebug();

  $sql->setQuery('SELECT k.* FROM rex_aufgaben_categories k INNER JOIN rex_aufgaben a ON (a.category = k.id) GROUP BY k.id ORDER BY k.category');

  if ($sql->getRows() > 1) {
    $categoryfilter = "<select id='categoryfilter' multiple>";
    for ($i = 0; $i < $sql->getRows(); $i++) {
      $kat_ids = explode(',', $filter_category);
      if (in_array($sql->getValue('id') , $kat_ids)) {
        $selected = 'selected';
      }
      else {
        $selected = '';
      }

      $categoryfilter.= '<option value="' . $sql->getValue('id') . '" ' . $selected . '>' . $sql->getValue('category') . '</option>';
      $sql->next();
    }
    $categoryfilter.= "</select>";
  } else {
    $categoryfilter = '';
  }

  // --------------------
  //
  //  category
  //
  // --------------------

  $list->setColumnLabel('category', $this->i18n('aufgaben_category'));
  $list->setColumnLayout('category', ['<th>###VALUE###<br/>' . $categoryfilter . '</th>', '<td data-title="'.$this->i18n('aufgaben_category').'" class="td_category">###VALUE###</td>']);
  $list->setColumnFormat('category', 'custom',
 function ($params)
  {
    $category = '';
    $list = $params['list'];
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setQuery('SELECT * FROM rex_aufgaben_categories ORDER BY category');
    if ($sql->getRows() > 1) {
    $category .= "<div class='rex-select-style intable'><select class='change_category' >";
      for ($i =0 ; $i < $sql->getRows(); $i++) {
       if ($sql->getValue('id') == $list->getValue('category')) {
              $selected = 'selected';
            }
            else {
              $selected = '';
            }
        $category.= '<option value="'.$list->getValue('id').','.$sql->getValue('id').'" '.$selected.' >'.$sql->getValue('category') . '</option>';
        $sql->next();
      }
    $category.= "</select></div>";
    } else {
      $sql = rex_sql::factory();
      // $sql->setDebug();
      $sql->setTable('rex_aufgaben_categories');
      $sql->setWhere(['id' => $list->getValue('category') ]);
      $sql->select();
    }

    return $category;

    $list = $params['list'];
    $sql = rex_sql::factory();

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
    $responsiblefilter = "<select id='responsiblefilter' multiple>";
    for ($i = 0; $i < $sql->getRows(); $i++) {
      $filter_responsible_ids = explode(',', $filter_responsible);
      if (in_array($sql->getValue('id') , $filter_responsible_ids)) {
        $selected = 'selected';
      }
      else {
        $selected = '';
      }
      $responsiblefilter.= '<option value="' . $sql->getValue('id') . '" ' . $selected . '>' . $sql->getValue('name') . '</option>'; 
  
      $sql->next();
    }

    $responsiblefilter.= "</select>";
  } else {
    $responsiblefilter = '';
  }

  // --------------------
  //
  //  Eigentümer (Zuständig)
  //
  // --------------------

  $list->setColumnLabel('responsible', $this->i18n('aufgaben_responsible'));
  $list->setColumnLayout('responsible', ['<th>###VALUE###<br/>' . $responsiblefilter . '</th>', '<td data-title="'.$this->i18n('aufgaben_responsible').'" class="td_responsible">###VALUE###</td>']);
  $list->setColumnFormat('responsible', 'custom',
  function ($params)
  {
    $responsible = '';
    $list = $params['list'];
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setQuery('SELECT * FROM rex_user ORDER BY login');
    if ($sql->getRows() > 1) {
      $responsible .= "<div class='rex-select-style intable'><select class='change_responsible' >";
      for ($i =0 ; $i < $sql->getRows(); $i++) {
       if ($sql->getValue('id') == $list->getValue('responsible')) {
              $selected = 'selected';
            }
            else {
              $selected = '';
            }
        
        $responsible.= '<option value="'.$list->getValue('id').','.$sql->getValue('id').'" '.$selected.' >'.$sql->getValue('name') . '</option>';
    
        $sql->next();
      }
    $responsible.= "</select></div>";
    } else {
      $sql = rex_sql::factory();
      // $sql->setDebug();
      $sql->setTable('rex_user');
      $sql->setWhere(['id' => $list->getValue('responsible') ]);
      $sql->select();
       if ($sql->getRows() >= 1) {
       
          $responsible = '<span class="single">'.$sql->getValue('name').'</span>';

          
       } else {
          $responsible = '<span class="single">--</span>';
       }
    }

    return $responsible;

    $list = $params['list'];
    $sql = rex_sql::factory();

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

    $list->setColumnLabel('prio', $this->i18n('aufgaben_prio'));
  $list->setColumnLayout('prio', ['<th>###VALUE###<br/>' . $priofilter . '</th>', '<td data-title="'.$this->i18n('aufgaben_prio').'" class="td_prio">###VALUE###</td>']);
  $list->setColumnFormat('prio', 'custom',
  function ($params)
  {
    $list = $params['list'];
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setTable(rex::getTablePrefix() . 'aufgaben');
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
      $list->addLinkAttribute('prio', 'title', $this->i18n('aufgaben_prio').': ' . $i);
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

  $list->setColumnLabel('status',  $this->i18n('aufgaben_status'));
  $list->setColumnLayout('status', ['<th>###VALUE###<br/>' . $statusfilter . '</th>', '<td data-title="'.$this->i18n('aufgaben_status').'" class="td_status">###VALUE###</td>']);
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
      $list->setColumnParams('status', ['func' => 'setstatus', 'id' => '###id###', 'neuerstatus' => $sql->getValue('id')]);

      $status.= $list->getColumnLink('status', '<i class="rex-icon ' . $current . ' ' . $sql->getValue('icon') . ' "></i>');
      $sql->next();
    }

    $status.= "</div>";
    return $status;
  });

  $content = '<div id="aufgaben">' . $list->get() . '</div>';
  $fragment = new rex_fragment();
  $fragment->setVar('title', $this->i18n('aufgaben_title'));
  $fragment->setVar('content', $content, false);
  echo $fragment->parse('core/page/section.php');
}
elseif ($func == 'edit' || $func == 'add') {
  $fieldset = $func == 'edit' ? $this->i18n('aufgaben_edit') : $this->i18n('aufgaben_add');
  $id = rex_request('id', 'int');
  $form = rex_form::factory(rex::getTablePrefix() . 'aufgaben', '', 'id=' . $id);

  $field = $form->addTextField('title');
  $field->setLabel($this->i18n('aufgaben_task'));
  $field->getValidator()->add('notEmpty', $this->i18n('aufgaben_title_empty'));
  $field = $form->addTextareaField('description', null, ['class' => 'form-control  markitupEditor-simple']);

  $field->setLabel($this->i18n('aufgaben_description'));
  $field = $form->addSelectField('category');
  $field->setLabel($this->i18n('aufgaben_category'));
  $field->setPrefix('<div class="rex-select-style">');
  $field->setSuffix('</div>');
  $field->getValidator()->add('notEmpty', $this->i18n('aufgaben_category_empty'));
  $select = $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT category as label, id FROM rex_aufgaben_categories ORDER BY category';
  $select->addOption($this->i18n('aufgaben_please_choose'), '');
  $select->addSqlOptions($query);
  $field = $form->addSelectField('responsible');
  $field->setLabel($this->i18n('aufgaben_responsible'));
  $field->setPrefix('<div class="rex-select-style">');
  $field->setSuffix('</div>');
  $field->getValidator()->add('notEmpty', $this->i18n('aufgaben_responsible_empty'));
  $select = $field->getSelect();
  if ($func == 'add') {
    $select->setSelected($current_user);
  }

  $select->setSize(1);
  $field = $form->addTextField('finaldate');
  $field->setAttribute('id', 'datepicker');
  $field->setLabel($this->i18n('aufgaben_due_date'));
  $query = 'SELECT name as label, id FROM rex_user';  
  $select->addOption($this->i18n('aufgaben_please_choose'), '');
  $select->addSqlOptions($query);
  $field = $form->addSelectField('status');
  $field->setLabel('Status');
  $field->setPrefix('<div class="rex-select-style">');
  $field->setSuffix('</div>');
  $select = $field->getSelect();
  $select->setSize(1);
  $query = 'SELECT status as label, id FROM rex_aufgaben_status';

  // $select->addOption('Offen',1);
  // $select->addOption('done',2);

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
      $field->setLabel($this->i18n('aufgaben_last_change'));
      $updateuser_realname = $form->getSql()->getValue('updateuser');      
      $sql = rex_sql::factory();
      $sql->setQuery("SELECT name FROM rex_user WHERE login = '$updateuser_realname'");
      $field = $form->addReadonlyField('updateuser', $sql->getValue('name'));
      
      $field->setHeader('<div class="col-md-6">');
      $field->setFooter('</div></div>');
      $field->setLabel($this->i18n('aufgaben_by'));
    }

    if ($form->getSql()->getValue('createuser') != '') {
      $form->getSql()->setValue('createdate', date('d.m.Y H:i:s', $form->getSql()->getDateTimeValue('createdate')));
      $field = $form->addReadonlyField('createdate');
      $field->setHeader('<div class="row"><div class="col-md-6">');
      $field->setFooter('</div>');
      $field->setLabel($this->i18n('aufgaben_create_on'));
      $createuser_realname = $form->getSql()->getValue('createuser');      
      $sql = rex_sql::factory();
      $sql->setQuery("SELECT name FROM rex_user WHERE login = '$createuser_realname'");
      $field = $form->addReadonlyField('createuser', $sql->getValue('name'));
      $field->setHeader('<div class="col-md-6">');
      $field->setFooter('</div></div><br/>');
      $field->setLabel($this->i18n('aufgaben_by'));
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
$('#categoryfilter').SumoSelect({okCancelInMulti: true });

$('#priofilter').SumoSelect({ okCancelInMulti: true });
$('#responsiblefilter').SumoSelect({ okCancelInMulti: true });
$('#statusfilter').SumoSelect({ okCancelInMulti: true });

$("#categoryfilter").change(function(){
     $value_k = $("#categoryfilter").val();
     if ($value_k == null) {$value_k = '0'};
     location.replace("index.php?page=aufgaben/aufgaben&func=filter&filter_category="+$value_k );
});
$(".change_category").change(function(){
     $value_ck = $(this).val();
     if ($value_ck == null) $value_ck = '0';
     location.replace("index.php?page=aufgaben/aufgaben&func=change_category&change_category="+$value_ck );
});
$("#responsiblefilter").change(function(){
     $value_e = $("#responsiblefilter").val();
     if ($value_e == null) $value_e = '0';
     location.replace("index.php?page=aufgaben/aufgaben&func=filter&filter_responsible="+$value_e );
});
$(".change_responsible").change(function(){
     $value_ce = $(this).val();
     if ($value_ce == null) $value_ce = '0';
     location.replace("index.php?page=aufgaben/aufgaben&func=change_responsible&change_responsible="+$value_ce );
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


$("#doneverbergen").click(function(){
  location.replace("index.php?page=aufgaben/aufgaben&func=donefilter&filter_done=1" );
});
$("#doneanzeigen").click(function(){
  location.replace("index.php?page=aufgaben/aufgaben&func=donefilter&filter_done=0" );
});

$(".watch").click(function(){

  // location.replace("index.php?page=aufgaben/aufgaben&func=donefilter&filter_done=0" );

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
        previousMonth : '<?= $this->i18n('aufgaben_prev_month'); ?>',
        nextMonth     : '<?= $this->i18n('aufgaben_next_month'); ?>',
        months        : [
          '<?= $this->i18n('aufgaben_januar'); ?>',
          '<?= $this->i18n('aufgaben_februar'); ?>',
          '<?= $this->i18n('aufgaben_maerz'); ?>',
          '<?= $this->i18n('aufgaben_april'); ?>',
          '<?= $this->i18n('aufgaben_mai'); ?>',
          '<?= $this->i18n('aufgaben_juni'); ?>',
          '<?= $this->i18n('aufgaben_juli'); ?>',
          '<?= $this->i18n('aufgaben_august'); ?>',
          '<?= $this->i18n('aufgaben_september'); ?>',
          '<?= $this->i18n('aufgaben_oktober'); ?>',
          '<?= $this->i18n('aufgaben_november'); ?>',
          '<?= $this->i18n('aufgaben_dezember'); ?>'],
        weekdays      : [
        '<?= $this->i18n('aufgaben_sonntag'); ?>',
        '<?= $this->i18n('aufgaben_montag'); ?>',
        '<?= $this->i18n('aufgaben_dienstag'); ?>',
        '<?= $this->i18n('aufgaben_mittwoch'); ?>',
        '<?= $this->i18n('aufgaben_donnerstag'); ?>',
        '<?= $this->i18n('aufgaben_freitag'); ?>',
        '<?= $this->i18n('aufgaben_samstag'); ?>'],
        weekdaysShort : [
        '<?= $this->i18n('aufgaben_so'); ?>',
        '<?= $this->i18n('aufgaben_mo'); ?>',
        '<?= $this->i18n('aufgaben_di'); ?>',
        '<?= $this->i18n('aufgaben_mi'); ?>',
        '<?= $this->i18n('aufgaben_do'); ?>',
        '<?= $this->i18n('aufgaben_fr'); ?>',
        '<?= $this->i18n('aufgaben_sa'); ?>']
      }
    }
);
</script>
