<?php
$func = rex_request('func', 'string');

if ($func == 'delete') {
    $id = (rex_request('id', 'int'));
    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_kategorien');
    $sql->setWhere('id = ' . $id);
    if ($sql->delete()) {
        echo '<div class="alert alert-success">Die Kategorie wurde gelöscht.</div>';
    }

    $func = '';
}

if ($func == '') {
    $list = rex_list::factory("SELECT * FROM " . rex::getTablePrefix() . "aufgaben_kategorien ORDER BY kategorie ASC");
    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage('<div class="alert alert-info" role="alert"><strong>Keine Kategorie vorhanden.</strong><br/>Bitte legen Sie eine Kategroie an bzw. Ifnormieren Ihren Administrator.</div>');

    // icon column
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="Kategorie hinzufügen"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
    $list->setColumnLabel('kategorie', 'Kategorie');

    $list->setColumnLabel('farbe', 'Farbe');
    $list->setColumnLayout('farbe', ['<th>###VALUE###</th>', '<td data-title="Aufgaben" class="td_aufgaben"><span class="simplecolorpicker icon" title="###VALUE###" style="background-color: ###VALUE###;" role="button" tabindex="0"></span></td>']);


    $delete = 'deleteCol';
    $list->addColumn($delete, '<i class="rex-icon rex-icon-delete"></i> löschen', -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams($delete, ['id' => '###id###', 'func' => 'delete']);
    $list->addLinkAttribute($delete, 'data-confirm', rex_i18n::msg('delete') . ' ?');
    $list->removeColumn('id');
    $content = '<div id="aufgaben">' . $list->get() . '</div>';
    $fragment = new rex_fragment();
    $fragment->setVar('content', $content, false);
    $content = $fragment->parse('core/page/section.php');
    echo $content;
}
elseif ($func == 'edit' || $func == 'add') {
    $fieldset = $func == 'edit' ? 'Kategorie editieren' : 'Kategorie hinzufügen';
    $id = rex_request('id', 'int');
    $form = rex_form::factory(rex::getTablePrefix() . 'aufgaben_kategorien', '', 'id=' . $id);
    $field = $form->addTextField('kategorie');
    $field->setLabel('Kategorie');
    $field->getValidator()->add('notEmpty', 'Bitte geben Sie eine Kategorie an.');

    $field = $form->addSelectField('farbe');
    $field->setPrefix('<div class="colorpicker">');
    $field->setSuffix('</div>');
    $field->setLabel('Farbe');
    $select =$field->getSelect();
    $select->setSize(1);
    $select->addOption('#000000','#000000');
    $select->addOption('#808080','#808080');
    $select->addOption('#C0C0C0','#C0C0C0');
    $select->addOption('#0000FF','#0000FF');
    $select->addOption('#008080','#008080');
    $select->addOption('#00FFFF','#00FFFF');
    $select->addOption('#800080','#800080');
    $select->addOption('#800000','#800000');
    $select->addOption('#FF0000','#FF0000');
    $select->addOption('#FF00FF','#FF00FF');
    $select->addOption('#008000','#008000');
    $select->addOption('#00FF00','#00FF00');
    $select->addOption('#808000','#808000');
    $select->addOption('#FFFF00','#FFFF00');
    if ($field->getValue()== "") {
        $field->setValue('#000000');
    }

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
?>
<script>
 $('.colorpicker select').simplecolorpicker({
  picker: true
}).on('change', function() {
  $(document.body).css('background-color', $('select[name="colorpicker"]').val());
});
</script>

