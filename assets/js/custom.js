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
