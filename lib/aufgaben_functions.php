<?php

class rex_aufgaben {


  // Beispiel
  var $zaehler = 0;

  public function test($var) {
    if (!empty($var)) {
      $this->zaehler += $var;
    }
  }

  /*
  $aufgaben = new rex_aufgaben();

  $aufgaben->test(1);
  echo $aufgaben->zaehler; // ausgabe 1
  $aufgaben->test(10);
  echo $aufgaben->zaehler; // ausgabe 11
  */

  // SHOW COUNTER
  public function show_counter() {

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


  // MAILS
  function send_mails($email_adressen, $aktuelle_id, $aufgabe, $betreff) {

    if ($aufgabe != '') {

      // var_dump($email_adressen);

      $mail_receiver = $email_adressen;

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

        /*
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
        */
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

        $sql_status_name = rex_sql::factory();
        // $sql_status_name->setDebug();
        $sql_status_name->setQuery('SELECT status FROM rex_aufgaben_status WHERE id = '.$mail_status);
        $mail_status = $sql_status_name->getValue('status');


        $sql_eigentuemer_name = rex_sql::factory();
        $sql_eigentuemer_name->setQuery('SELECT login FROM rex_user WHERE id = '.$mail_eigentuemer);
        $mail_eigentuemer = $sql_eigentuemer_name->getValue('login');


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

            // E-Mail Adresse nochmal prüfen
            $sql_email_pruefung = rex_sql::factory();
            // $sql_email_pruefung->setDebug();
            $sql_email_pruefung->setQuery('SELECT email FROM rex_user WHERE email = "'.$email_adresse.'"');
            if ($sql_email_pruefung->getRows() > 0) {

              $mail = new rex_mailer();

              $body  = "<h3>".$mail_titel."</h3>";
              $body  .= '<hr/>';
              $body  .= '<p>Status: <b>'.$mail_status.'</b> | Zuständig: <b>'.$mail_eigentuemer.'</b> | Prio: <b>'.$mail_prio.'</b>';
              $body  .= '<p>Aktualisiert von: <b>'.$mail_updateuser.'</b> | Erstellt von: <b>'.$mail_creatuser.'</b> | Zieldatum: <b>'.$mail_finaldate.'</b>';
              $body  .= '<hr/>';
              $body  .= "<p><b></b>".$text_beschreibung."</b>";

              $text_body = $mail_titel."\n\n";
              $text_body .= $mail_beschreibung."\n\n";

              $mail->From = "no-reply@".$_SERVER['SERVER_NAME'];
              $mail->FromName = $_SERVER['SERVER_NAME'];

              $mail->Subject = $betreff.$mail_titel;

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
        }
        $mail_adressen = '';
        $mail_receiver = '';
      }
    }
  }

}





