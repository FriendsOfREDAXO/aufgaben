<?php

if (rex::isBackend() && rex::getUser()) {

  rex_view::addJSFile($this->getAssetsUrl('js/moments.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/pikaday.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/jquery.sumoselect.js'));
  rex_view::addJSFile($this->getAssetsUrl('js/custom.js'));

  rex_extension::register('PACKAGES_INCLUDED', function () {
    if (rex::getUser() && $this->getProperty('compile')) {

      $compiler = new rex_scss_compiler();

      $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('scss/master.scss')]));
      $compiler->setScssFile($scss_files);
      $compiler->setCssFile($this->getPath('assets/css/styles.css'));
      $compiler->compile();
      rex_file::copy($this->getPath('assets/css/styles.css'), $this->getAssetsPath('css/styles.css'));
        }
    });
  rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));

  function show_counter() {
    $counter        = 0;
    $current_user   = rex::getUser()->getId();

    $sql_counter = rex_sql::factory();
    //$sql_counter->setDebug();
    $sql_counter->setQuery('SELECT counter FROM rex_aufgaben_user_settings WHERE user = '.$current_user);

    if ($sql_counter->getRows() > 0) {

      $ersetzen = '</i> Aufgaben <span class="label label-default">'.$counter.'</span></a>';
      $counter = $sql_counter->getValue('counter');

      if ($counter > 0) {
        $ersetzen = '</i> Aufgaben <span class="label label-danger">'.$counter.'</span></a>';
      } else {
        $ersetzen = '</i> Aufgaben <span class="label label-default">'.$counter.'</span></a>';
      }

      rex_extension::register('OUTPUT_FILTER',function(rex_extension_point $ep) use ($ersetzen){
        $suchmuster = '</i> Aufgaben';
        $ersetzen = $ersetzen;
        $ep->setSubject(str_replace($suchmuster, $ersetzen, $ep->getSubject()));
      });
    }
  }

  function send_mails($aktuelle_id, $aufgabe) {

    if ($aufgabe != '') {

      // Alle Admins E-Mail Adressen holen
      $mail_receiver = array();
      $sql_admin = rex_sql::factory();
      //$sql_admin->setDebug();
      $sql_admin->setTable('rex_user');
      $sql_admin->setWhere('admin = 1 AND email !=""');
      $sql_admin->select();
      if ($sql_admin->getRows()) {
        for($i=0; $i<$sql_admin->getRows(); $i++) {
          $mail_receiver[] = $sql_admin->getValue('email');
          $sql_admin->next();
        }
      }

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
      $sql_aufgabe->setQuery('SELECT * FROM rex_aufgaben_aufgaben '.$expand_query);

      if ($sql_aufgabe->getRows()) {

        // Eigentümer holen
        $sql_email_eigentuemer = rex_sql::factory();
        // $sql_email_eigentuemer->setDebug();
        $sql_email_eigentuemer->setQuery('SELECT email FROM rex_user WHERE id = '.$sql_aufgabe->getValue('eigentuemer').' AND email != ""');
        $mail_receiver[] = $sql_email_eigentuemer->getValue('email');

        // Updateuser holen
        $sql_email_updateuser = rex_sql::factory();
        // $sql_email_updateuser->setDebug();
        $sql_email_updateuser->setQuery('SELECT email FROM rex_user WHERE login = "'.$sql_aufgabe->getValue('updateuser').'" AND email != ""');
        $mail_receiver[] = $sql_email_updateuser->getValue('email');

        // Createuser holen
        $sql_email_createuser = rex_sql::factory();
        // $sql_email_createuser->setDebug();
        $sql_email_createuser->setQuery('SELECT email FROM rex_user WHERE login = "'.$sql_aufgabe->getValue('createuser').'" AND email != ""');
        $mail_receiver[] = $sql_email_createuser->getValue('email');

        // Doppelte Mail Empfänger entfernen
        $mail_adressen = '';
        $mail_adressen = array_unique($mail_receiver);

        // print_r($mail_adressen);

        // Mailinhalt
        $mail_titel         = $sql_aufgabe->getValue('titel');
        $mail_beschreibung  = $sql_aufgabe->getValue('beschreibung');
        $mail_eigentuemer   = $sql_aufgabe->getValue('eigentuemer');
        $mail_prio          = $sql_aufgabe->getValue('prio');
        $mail_status        = $sql_aufgabe->getValue('status');
        $mail_creatuser     = $sql_aufgabe->getValue('createuser');
        $mail_updateuser    = $sql_aufgabe->getValue('updateuser');
        $mail_finaldate     = $sql_aufgabe->getValue('finaldate');

        if(rex_addon::get('textile')->isAvailable()) {
          $text_beschreibung = str_replace('<br />', '', $mail_beschreibung);
          $text_beschreibung = rex_textile::parse($text_beschreibung);
          $text_beschreibung = str_replace('###', '&#x20;', $text_beschreibung);
        } else {
          $text_beschreibung = str_replace(PHP_EOL,'<br/>', $mail_beschreibung );
        }

        // Mails senden
        if (count($mail_adressen) == 0){
          echo "<div class='alert alert-success'>Es wurde keine E-Mail versendet.</div><br/>";
        } else {
          foreach($mail_adressen as $email_adresse) {

            $mail = new rex_mailer();

            $body  = "<h3>".$mail_titel."</h3>";
            $body  .= "<p>".$text_beschreibung."</b>";

            $text_body = $mail_titel."\n\n";
            $text_body .= $mail_beschreibung."\n\n";

            $mail->From = "no-reply@".$_SERVER['SERVER_NAME'];
            $mail->FromName = $_SERVER['SERVER_NAME'];

            if ($aufagen_art == 'new') {
              $mail->Subject = "(".$_SERVER['SERVER_NAME'].") Neue Aufgabe: ".$mail_titel;
            } else if ($aufagen_art == 'edit') {
              $mail->Subject = "(".$_SERVER['SERVER_NAME'].") Aufgabe geändert: ".$mail_titel;
            }

            $mail->Body    = $body;
            $mail->AltBody = $text_body;

            $mail->AddAddress($email_adresse, $email_adresse);

            if(!$mail->Send()) {
              echo "<div class='alert alert-danger'>E-Mail konnte nicht gesendet werden.</div>";
            } else {
              echo "<div class='alert alert-success'>E-Mail an <b>".$email_adresse."</b> wurde gesendet.</div>";
            }
          }
        }
        $mail_adressen = '';
        $mail_receiver = '';
      }
    }
  }
}

if ($this->getConfig('install') == 'true' && rex::getUser()) {
  $current_page = rex_be_controller::getCurrentPage();
  if ($current_page != 'aufgaben/aufgaben') {
    show_counter();
  }
}
