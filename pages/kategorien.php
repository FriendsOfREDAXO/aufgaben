<?php
$func = rex_request('func', 'string');

if ($func == 'delete') {
    $id = (rex_request('id', 'int'));
    $sql = rex_sql::factory();

    // $sql->setDebug();

    $sql->setTable('rex_aufgaben_kategorien');
    $sql->setWhere('id = ' . $id);
    if ($sql->delete()) {
        echo '<div class="alert alert-success">Die Kategorie wurde gelÃ¶scht.</div>';
    }

    $func = '';
}

if ($func == '') {
    $list = rex_list::factory("SELECT * FROM " . rex::getTablePrefix() . "aufgaben_kategorien ORDER BY kategorie ASC");
    $list->addTableAttribute('class', 'table-striped');
    $list->setNoRowsMessage('<div class="alert alert-info" role="alert"><strong>Keine Kategorie vorhanden.</strong><br/>Bitte legen Sie eine Kategroie an bzw. Ifnormieren Ihren Administrator.</div>');

    // icon column
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="Kategorie hinzufÃ¼gen"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-file-text-o"></i>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);
    $list->setColumnLabel('kategorie', 'Kategorie');
    $list->setColumnLabel('farbe', 'Farbe');
    $delete = 'deleteCol';
    $list->addColumn($delete, '<i class="rex-icon rex-icon-delete"></i> lÃ¶schen', -1, ['', '<td class="rex-table-action">###VALUE###</td>']);
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
    $fieldset = $func == 'edit' ? 'Kategorie editieren' : 'Kategorie hinzufÃ¼gen';
    $id = rex_request('id', 'int');
    $form = rex_form::factory(rex::getTablePrefix() . 'aufgaben_kategorien', '', 'id=' . $id);
    $field = $form->addTextField('kategorie');
    $field->setLabel('Kategorie');
    $field->getValidator()->add('notEmpty', 'Bitte geben Sie eine Kategorie an.');
    $field = $form->addTextField('farbe');
    $field->setLabel('Farbe');
    $field->getValidator()->add('notEmpty', 'Bitte geben Sie  einnen Fabwert ( #123 oder #123456 ) an.');
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
