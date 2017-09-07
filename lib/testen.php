 function save_datas($aktuelle_id, $aufgabe) {
       if ($aufgabe != '') {

      // Aufgabe holen
      if ($aktuelle_id == 0) {
        $expand_query = 'ORDER BY id DESC LIMIT 1';
        $aufagen_art = 'new';
      } else {
        $expand_query = 'WHERE id = '.$aktuelle_id;
        $aufagen_art = 'edit';
      }
      $sql_aufgabe = rex_sql::factory();
      // $sql_aufgabe->setDebug();
      $sql_aufgabe->setQuery('SELECT * FROM rex_aufgaben '.$expand_query);

      if ($sql_aufgabe->getRows()) {
        // Mailinhalt
        $mail_titel         = $sql_aufgabe->getValue('title');
        $mail_beschreibung  = $sql_aufgabe->getValue('description');
        $mail_prio          = $sql_aufgabe->getValue('prio');
        $mail_status        = $sql_aufgabe->getValue('status');
        $mail_eigentuemer  = $sql_aufgabe->getValue('responsible');          
        $creatuser_realname = $sql_aufgabe->getValue('createuser');      
        $creatuser_sql = rex_sql::factory();
        $creatuser_sql->setQuery("SELECT name FROM rex_user WHERE login = '$creatuser_realname'");
        $mail_creatuser  = $creatuser_sql->getValue('name');  
        $updateuser_realname = $sql_aufgabe->getValue('updateuser');      
        $updateuser_sql = rex_sql::factory();
        $updateuser_sql->setQuery("SELECT name FROM rex_user WHERE login = '$updateuser_realname'");
        $mail_updateuser  = $updateuser_sql->getValue('name');  

        $mail_finaldate     = $sql_aufgabe->getValue('finaldate');

        if ($mail_finaldate == '') {
          $mail_finaldate = '--';
        }
          
          $data = array($mail_titel, $mail_beschreibung, $mail_prio, $mail_status, $mail_eigentuemer, $creatuser_realname, $creatuser_sql, $mail_creatuser, $updateuser_realname, $updateuser_sql, $mail_updateuser, $mail_finaldate);
          dump($data);
          
        $sql_status_name = rex_sql::factory();
        // $sql_status_name->setDebug();
        $sql_status_name->setQuery('SELECT status FROM rex_aufgaben_status WHERE id = '.$mail_status);
        $mail_status = $sql_status_name->getValue('status');


        $sql_eigentuemer_name = rex_sql::factory();
        $sql_eigentuemer_name->setQuery('SELECT * FROM rex_user WHERE id = '.$mail_eigentuemer);
        $mail_eigentuemer = $sql_eigentuemer_name->getValue('name');


        if(rex_addon::get('textile')->isAvailable()) {
          $text_beschreibung = str_replace('<br />', '', $mail_beschreibung);
          $text_beschreibung = rex_textile::parse($text_beschreibung);
          $text_beschreibung = str_replace('###', '&#x20;', $text_beschreibung);
        } else {
          $text_beschreibung = str_replace(PHP_EOL,'<br/>', $mail_beschreibung );
        }
  }
       }
  }