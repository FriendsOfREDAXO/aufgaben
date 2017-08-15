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
      $sql_aufgabe->setQuery('SELECT * FROM rex_aufgaben '.$expand_query);

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

        // creatuser holen
        $sql_email_creatuser = rex_sql::factory();
        // $sql_email_creatuser->setDebug();
        $sql_email_creatuser->setQuery('SELECT email FROM rex_user WHERE login = "'.$sql_aufgabe->getValue('creatuser').'" AND email != ""');
        $mail_receiver[] = $sql_email_creatuser->getValue('email');
        */
        // Doppelte Mail Empfänger entfernen
        $mail_adressen = '';
        $mail_adressen = array_unique($mail_receiver);

        // print_r($mail_adressen);

        // Mailinhalt
        $mail_titel         = $sql_aufgabe->getValue('title');
        $mail_beschreibung  = $sql_aufgabe->getValue('description');
        $mail_prio          = $sql_aufgabe->getValue('prio');
        $mail_status        = $sql_aufgabe->getValue('status');

        $eigentuemer_realname = $sql_aufgabe->getValue('responsible');      
        $eigentuemer_sql = rex_sql::factory();
        $eigentuemer_sql->setQuery("SELECT name FROM rex_user WHERE login = '$eigentuemer_realname'");
        $mail_eigentuemer  = $eigentuemerr_sql->getValue('name');  
      
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

        $sql_status_name = rex_sql::factory();
        // $sql_status_name->setDebug();
        $sql_status_name->setQuery('SELECT status FROM rex_aufgaben_status WHERE id = '.$mail_status);
        $mail_status = $sql_status_name->getValue('status');


        $sql_eigentuemer_name = rex_sql::factory();
        $sql_eigentuemer_name->setQuery('SELECT login FROM rex_user WHERE id = '.$mail_eigentuemer);
        $mail_eigentuemer = $sql_eigentuemer_name->getValue('name');


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

              $body = '
              <table style="border-collapse:collapse;border-spacing:0;border-color:#ccc; width: 100%; text-align: left;">
  <tr>
    <td style="font-family:Arial, sans-serif; padding:20px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#f0f0f0;font-weight:bold;vertical-align:top" colspan="4">
      <h2>'.$mail_titel.'</h2>
    </td>
  </tr>
  <tr>
    <td style="font-family:Arial, sans-serif; font-size: 14px;padding:8px 3px 8px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top" colspan="4">
      '.$text_beschreibung.'
    </td>
  </tr>
  <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#f0f0f0;font-weight:bold;vertical-align:top" colspan="4"> </td>
  </tr>
  <tr>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top"> Prio </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top;"> <b>'.$mail_prio.'</b> </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top"> Status </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top;"> <b>'.$mail_status.'</b> </td>
  </tr>
  <tr>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top"> Zuständig </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top;"> <b>'.$mail_eigentuemer.'</b> </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top"> Erstellt von </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top;"> <b>'.$mail_creatuser.'</b> </td>
  </tr>
  <tr>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top"> Fälligkeitsdatum </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top;"> <b>'.$mail_finaldate.'</b> </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top"> Aktualisiert von </td>
    <td style="font-family:Arial, sans-serif; font-size: 12px;padding:3px 3px 3px 8px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:#ccc;color:#333;background-color:#fff;vertical-align:top;"> <b>'.$mail_updateuser.'</b> </td>
  </tr>
</table>';

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





