<?php
$qry = rex_sql::factory();
$qry->setTable('rex_aufgaben_aufgaben');
$qry->select('*');

if (rex_post('addtodo') == "true")
{
    ob_end_clean();

    $sql_add = rex_sql::factory();
    $sql_add->setTable('rex_aufgaben_aufgaben');
    $sql_add->setWhere('id = ' . rex_post('id'));
    $sql_add->setValue('titel', rex_post('titel'));
    $sql_add->setValue('beschreibung', rex_post('beschreibung'));
    $sql_add->setValue('kategorie', rex_post('kategorie'));
    $sql_add->setValue('eigentuemer', rex_post('eigentuemer'));
    $sql_add->setValue('updatedate', date('Y-m-d H:i:s'));
    $sql_add->setValue('createdate', date('Y-m-d H:i:s'));
    $sql_add->setValue('createuser', rex::getUser()->getName());
    $sql_add->setValue('updateuser', rex::getUser()->getName());
    $sql_add->setValue('observer', '');
    $sql_add->setValue('finaldate', '');
    $sql_add->setValue('status', rex_post('status'));

    if ($sql_add->insert())
    {
        http_response_code(200);
    }
    else
    {
        http_response_code(500);
    }
    exit;
}

if (rex_post('edittodo') == "true")
{
    ob_end_clean();

    $sql_edit = rex_sql::factory();
    $sql_edit->setTable('rex_aufgaben_aufgaben');
    $sql_edit->setValue('status', rex_post('status'));
    $sql_edit->setValue('titel', rex_post('titel'));
    $sql_edit->setValue('beschreibung', rex_post('beschreibung'));
    $sql_edit->setValue('kategorie', rex_post('kategorie'));
    $sql_edit->setValue('eigentuemer', rex_post('eigentuemer'));
    $sql_edit->setValue('updatedate', date('Y-m-d H:i:s'));
    $sql_edit->setValue('updateuser', rex::getUser()->getName());
    $sql_edit->setWhere('id=' . rex_post('entry-id'));

    if ($sql_edit->update())
    {
        http_response_code(200);
    }
    else
    {
        http_response_code(500);
    }

    exit;
}

if (rex_post('deletetodo') == "true")
{
    ob_end_clean();

    $sql_delete = rex_sql::factory();
    $sql_delete->setTable('rex_aufgaben_aufgaben');
    $sql_delete->setWhere('id = ' . rex_post('id'));

    if ($sql_delete->delete())
    {
        http_response_code(200);
    }
    else
    {
        http_response_code(500);
    }
    exit;
}

if (rex_post('updatekategorie') == "true")
{
    ob_end_clean();

    $sql_update_kategorie = rex_sql::factory();
    $sql_update_kategorie->setTable('rex_aufgaben_aufgaben');
    $sql_update_kategorie->setWhere('id = ' . rex_post('id'));
    $sql_update_kategorie->setValue('kategorie', rex_post('kategorieid'));
    $sql_update_kategorie->update();

    exit;
}

if (rex_post('updatestatus') == "true")
{
    ob_end_clean();

    $sql_update_status = rex_sql::factory();
    $sql_update_status->setTable('rex_aufgaben_aufgaben');
    $sql_update_status->setWhere('id = ' . rex_post('id'));
    $sql_update_status->setValue('status', rex_post('statusid'));
    $sql_update_status->update();

    exit;
}

if (rex_post('updateprio') == "true")
{
    ob_end_clean();

    $sql_update_prio = rex_sql::factory();
    $sql_update_prio->setTable('rex_aufgaben_aufgaben');
    $sql_update_prio->setWhere('id = ' . rex_post('id'));
    $sql_update_prio->setValue('prio', rex_post('prioid'));
    $sql_update_prio->update();

    exit;
}
?>

<div class="kanban-wrapper">
    <div class="container-fluid kanban-stage">
        <div id="sortableKanbanBoards" class="row">

            <?php
            $sql_kategorien = rex_sql::factory();
            $sql_kategorien->setTable('rex_aufgaben_kategorien');
            $sql_kategorien->select('*');

            $sql_zustaendig = rex_sql::factory();
            $sql_zustaendig->setTable('rex_user');
            $sql_zustaendig->select('*');

            $sql_status = rex_sql::factory();
            $sql_status->setTable('rex_aufgaben_status');
            $sql_status->select('*');

            function getEigentuemer($eigentuemerId)
            {
                $sql_eigentuemer = rex_sql::factory();
                $sql_eigentuemer->setTable('rex_user');
                $sql_eigentuemer->setWhere('id = ' . $eigentuemerId);
                $sql_eigentuemer->select('*');

                return $sql_eigentuemer->getValue('login');
            }

            function getStatusLinks($currentid, $itemid)
            {
                $statussql = rex_sql::factory();
                //$sql->setDebug();
                $statussql->setTable(rex::getTablePrefix() . 'aufgaben_status');
                $statussql->select();

                $currentclass = "";

                $status = "<hr>";
                $status .= "<div class='status'>";
                for ($i = 0; $i < $statussql->getRows(); $i++)
                {
                    if($currentid == $statussql->getValue('id'))
                    {
                        $currentclass = "current";
                    }
                    else
                    {
                        $currentclass = "";
                    }

                    if($statussql->getValue('id') == 6)
                    {
                        $currentclass .= " done";
                    }

                    $status .= "<a href='#' class='change-status ".$currentclass."' data-id='".$itemid."' data-statusid='".$statussql->getValue('id')."' title='".  $statussql->getValue('status') ."'><i class='rex-icon ".$statussql->getValue('icon')."'></i></a>";
                    $statussql->next();
                }
                $status .= "</div>";

                return $status;
            }


            function getPrioLinks($currentid, $itemid)
            {
                $priosql = rex_sql::factory();
                //$sql->setDebug();
                $priosql->setTable(rex::getTablePrefix() . 'aufgaben_aufgaben');
                $priosql->setTable('rex_aufgaben_aufgaben');
                $priosql->setWhere('id = ' . $itemid);
                $priosql->select('*');
                $prioArray = $priosql->getArray();
                $currentclass = "";

                $prio = "<div class='prio-wrapper'>";
                $prio .= "<a href='#' class='change-prio' data-id='" . $itemid . "' data-prioid='0'><i class='fa fa-star-o' aria-hidden='true'></i></a>";
                for ($i = 0; $i < 3; $i++)
                {
                    if ($i+1 == $prioArray[0]["prio"])
                    {
                        $currentclass = "current";
                    }
                    else
                    {
                        $currentclass = "";
                    }

                    $prio .= "<a href='#' class='change-prio " . $currentclass . "' data-id='" . $itemid . "' data-prioid='" . ($i+1) . "'><i class='fa fa-star' aria-hidden='true'></i></a>";
                }
                $prio .= "</div>";

                return $prio;
            }


            foreach ($sql_kategorien->getArray() as $kategorie)
            {
                $sql_aufgaben = rex_sql::factory();
                $sql_aufgaben->setTable('rex_aufgaben_aufgaben');
                $sql_aufgaben->setWhere('kategorie = ' . $kategorie["id"]);
                $sql_aufgaben->select('*');
                $aufgaben_array = $sql_aufgaben->getArray();
                $color = "";
                ?>

                <div class="panel panel-default kanban-col">
                    <div class="panel-heading" style="position: relative; border-bottom: 5px solid <?= $kategorie["farbe"]; ?>; color: <?= $kategorie["farbe"]; ?>">
                        <span class="kanban-heading">
                            <?= $kategorie["kategorie"]; ?>
                        </span>
                    </div>
                    <div class="panel-body" data-kategorieid="<?= $kategorie["id"]; ?>" data-color="<?= $kategorie["farbe"]; ?>">
                        <div class="kanban-centered">

                            <?php
                            for ($i = 0; $i < count($aufgaben_array); $i++)
                            {
                                $itemuid = "item" . uniqid();
                                $uid = "uid" . uniqid();
                                $aufgabe = $aufgaben_array[$i];
                                $beschreibung = $aufgabe["beschreibung"];
                                ?>
                            <article class="kanban-entry grab" id="<?= $itemuid ?>" draggable="true" data-color="<?= $kategorie["farbe"]; ?>" data-id="<?= $aufgabe["id"]; ?>" data-kategorieid="<?= $kategorie["id"]; ?>" data-item="<?= $itemuid ?>" data-status="<?= $aufgabe["status"] ?>" data-eigentuemer="<?= $aufgabe["eigentuemer"] ?>" data-title="<?= $aufgabe["titel"] ?>" data-id="<?= $aufgabe["id"] ?>" data-beschreibung="<?= $aufgabe["beschreibung"] ?>" data-kategoriename="<?= $kategorie["kategorie"]; ?>">
                                <?= getPrioLinks($aufgabe["prio"], $aufgabe["id"]); ?>
                                    <a href="#" class="delete-kanban-entry"><i class="fa fa-2x fa-trash-o pull-right"></i></a>
                                    <a href="#" class="edit-kanban-entry" ><i class="fa fa-2x fa-pencil-square-o pull-right"></i></a>
                                    <div class="kanban-entry-inner" style="border-left: 5px solid <?= $kategorie["farbe"]; ?>">
                                        <div class="kanban-label">
                                            <?php
                                            if (strlen($aufgabe['finaldate']))
                                            {
                                                ?>
                                                <div class="date">
                                                    <?= date('d.m.Y', strtotime($aufgabe['finaldate'])) ?>
                                                </div>

                                                <?php
                                            }
                                            if (strlen($beschreibung))
                                            {
                                                ?>
                                                <h4>
                                                    <a class="expand collapsed" role="button" data-toggle="collapse" href="#<?= $uid ?>" aria-expanded="false" aria-controls="collapseExample">
                                                        <?= $aufgabe["titel"] ?>
                                                    </a>
                                                </h4>
                                                <div class="collapse" id="<?= $uid ?>">
                                                    <div class="well">
                                                        <?= $beschreibung ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h4><?= $aufgabe["titel"] ?></h4>
                                                <?php
                                            }

                                            echo getStatusLinks($aufgabe["status"], $aufgabe["id"]);
                                            ?>
                                            <hr>
                                            <span class="label label-default">
                                                <?= getEigentuemer($aufgabe["eigentuemer"]); ?>
                                            </span>
                                        </div>
                                    </div>
                                </article>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="#" data-kategoriename="<?= $kategorie["kategorie"]; ?>" data-kategorieid="<?= $kategorie["id"]; ?>" class="add-kanban-entry btn btn-default btn-raised "><i class="fa fa-plus-circle" aria-hidden="true"></i> Aufgabe Hinzufügen</a>
                    </div>
                </div>

                <?php
            }

?>


        </div>
    </div>
</div>


<div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Aufgabe hinzufügen</h4>
            </div>
            <div class="modal-body">
                <div id="missing-values" class="alert alert-danger" role="alert" style="display: none">
                    Bitte einen Titel angeben.
                    <br>
                    Bitte eine Kategorie auswählen.
                </div>
                <form>
                    <input type="hidden" name="addtodo" value="true" />
                    <fieldset>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-titel">Titel</label>
                            </dt>
                            <dd>
                                <input id="rex-aufgaben-aufgaben-titel" class="form-control" name="titel" value="" type="text">
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-beschreibung">Beschreibung</label>
                            </dt>
                            <dd>
                                <textarea id="rex-aufgaben-aufgaben-beschreibung" class="form-control" rows="6" name="beschreibung"></textarea>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-kategorie-name">Kategorie</label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <input type="text" disabled="disabled" id="rex-aufgaben-aufgaben-kategorie-name" name="kategorie-name" size="1" id="rex-aufgaben-aufgaben-kategorie" class="form-control" />
                                    <input type="hidden" name="kategorie" size="1" id="rex-aufgaben-aufgaben-kategorie" class="form-control" />
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label class="control-label" for="rex-aufgaben-aufgaben-eigentuemer">Zuständig</label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="eigentuemer" size="1" id="rex-aufgaben-aufgaben-eigentuemer" class="form-control">
                                        <?php
                                        foreach ($sql_zustaendig->getArray() as $zustaendig)
                                        {
                                            var_dump($zustaendig);
                                            echo '<option value="' . $zustaendig["id"] . '">' . $zustaendig["login"] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-status">Status</label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="status" size="1" id="rex-aufgaben-aufgaben-status" class="form-control">
                                        <option value="0" selected="selected">Bitte wählen</option>
                                        <?php
                                            foreach ($sql_status->getArray() as $status)
                                            {
                                                var_dump($status);
                                                echo '<option value="' . $status["id"] . '">' . $status["status"] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="save-add-entry">Speichern</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Aufgabe bearbeiten</h4>
            </div>
            <div class="modal-body">
                <div id="missing-values" class="alert alert-danger" role="alert" style="display: none">
                    Bitte einen Titel angeben.
                    <br>
                    Bitte eine Kategorie auswählen.
                </div>
                <form>
                    <input type="hidden" name="edittodo" value="true" />
                    <input type="hidden" id="entry-id" name="entry-id" value="" />
                    <fieldset>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-titel">Titel</label>
                            </dt>
                            <dd>
                                <input id="rex-aufgaben-aufgaben-titel" class="form-control" name="titel" value="" type="text">
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-beschreibung">Beschreibung</label>
                            </dt>
                            <dd>
                                <textarea id="rex-aufgaben-aufgaben-beschreibung" class="form-control" rows="6" name="beschreibung"></textarea>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-kategorie-name">Kategorie</label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="kategorie" size="1" id="rex-aufgaben-aufgaben-kategorie-name" class="form-control">
                                        <?php
                                            foreach ($sql_kategorien->getArray() as $kategorie)
                                            {
                                                var_dump($kategorie);
                                                echo '<option value="' . $kategorie["id"] . '">' . $kategorie["kategorie"] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label class="control-label" for="rex-aufgaben-aufgaben-eigentuemer">Zuständig</label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="eigentuemer" size="1" id="rex-aufgaben-aufgaben-eigentuemer" class="form-control">
                                        <?php
                                            foreach ($sql_zustaendig->getArray() as $zustaendig)
                                            {
                                                var_dump($zustaendig);
                                                echo '<option value="' . $zustaendig["id"] . '">' . $zustaendig["login"] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-aufgaben-status">Status</label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="status" size="1" id="rex-aufgaben-aufgaben-status" class="form-control">
                                        <option value="0" selected="selected">Bitte wählen</option>
                                        <?php
                                            foreach ($sql_status->getArray() as $status)
                                            {
                                                var_dump($status);
                                                echo '<option value="' . $status["id"] . '">' . $status["status"] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="save-edit-entry">Speichern</button>
            </div>
        </div>
    </div>
</div>



<div id="confirm" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                Möchtest du die Aufgabe <strong class="delete-title"></strong> wirklich löschen?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete-entry">Löschen</button>
                <button type="button" data-dismiss="modal" class="btn btn-default">Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<script>
    var kanbanAjaxUrl = "<?= rex_url::currentBackendPage()?>";

    jQuery(document).ready(function ()
    {
        //prepend bootstrap modal mobile behaviour..
        var jBody = jQuery("body");

        var jConfirmModal = jQuery("#confirm").detach();
        jBody.append(jConfirmModal);

        var jEditModal = jQuery("#edit-modal").detach();
        jBody.append(jEditModal);

        var jAddModal = jQuery("#add-modal").detach();
        jBody.append(jAddModal);

        new redaxo.Kanban();
    });
</script>
