<?php
$qry = rex_sql::factory();
$qry->setTable('rex_aufgaben');
$qry->select('*');

if (rex_post('addtodo') == "true")
{
    ob_end_clean();

    $sql_add = rex_sql::factory();
    $sql_add->setTable('rex_aufgaben');
    $sql_add->setWhere('id = ' . rex_post('id'));
    $sql_add->setValue('title', rex_post('title'));
    $sql_add->setValue('description', rex_post('description'));
    $sql_add->setValue('category', rex_post('category'));
    $sql_add->setValue('responsible', rex_post('responsible'));
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
    $sql_edit->setTable('rex_aufgaben');
    $sql_edit->setValue('status', rex_post('status'));
    $sql_edit->setValue('title', rex_post('title'));
    $sql_edit->setValue('description', rex_post('description'));
    $sql_edit->setValue('category', rex_post('category'));
    $sql_edit->setValue('responsible', rex_post('responsible'));
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
    $sql_delete->setTable('rex_aufgaben');
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

if (rex_post('updatecategory') == "true")
{
    ob_end_clean();

    $sql_update_category = rex_sql::factory();
    $sql_update_category->setTable('rex_aufgaben');
    $sql_update_category->setWhere('id = ' . rex_post('id'));
    $sql_update_category->setValue('category', rex_post('categoryid'));
    $sql_update_category->update();

    exit;
}

if (rex_post('updatestatus') == "true")
{
    ob_end_clean();

    $sql_update_status = rex_sql::factory();
    $sql_update_status->setTable('rex_aufgaben');
    $sql_update_status->setWhere('id = ' . rex_post('id'));
    $sql_update_status->setValue('status', rex_post('statusid'));
    $sql_update_status->update();

    exit;
}

if (rex_post('updateprio') == "true")
{
    ob_end_clean();

    $sql_update_prio = rex_sql::factory();
    $sql_update_prio->setTable('rex_aufgaben');
    $sql_update_prio->setWhere('id = ' . rex_post('id'));
    $sql_update_prio->setValue('prio', rex_post('prioid'));
    $sql_update_prio->update();

    exit;
}
?>

<div id="aufgaben" class="kanban-wrapper">
    <div class="container-fluid kanban-stage">
        <div id="sortableKanbanBoards" class="row">

            <?php
            $sql_categories = rex_sql::factory();
            // $sql_categories->setDebug();
            $sql_categories->setTable('rex_aufgaben_categories');
            $sql_categories->select('*');

            $sql_zustaendig = rex_sql::factory();
            // $sql_zustaendig->setDebug();
            $sql_zustaendig->setTable('rex_user');
            $sql_zustaendig->select('*');

            $sql_status = rex_sql::factory();
            // $sql_status->setDebug();
            $sql_status->setTable('rex_aufgaben_status');
            $sql_status->select('*');

            function getresponsible($responsibleId)
            {
                $sql_responsible = rex_sql::factory();
                // $sql_responsible->setDebug();
                $sql_responsible->setTable('rex_user');
                $sql_responsible->setWhere('id = ' . $responsibleId);
                $sql_responsible->select('*');

                    if ($sql_responsible->getRows() >= 1) {
                       return $sql_responsible->getValue('login');
                    } else {
                       return '--';
                    }
            }

            function getStatusLinks($currentid, $itemid)
            {
                $statussql = rex_sql::factory();
                // $statussql->setDebug();
                $statussql->setTable(rex::getTablePrefix() . 'aufgaben_status');
                $statussql->select();

                $currentclass = "";

                $status = "<hr/>";
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
                // $priosql->setDebug();
                $priosql->setTable(rex::getTablePrefix() . 'aufgaben');
                $priosql->setTable('rex_aufgaben');
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


            foreach ($sql_categories->getArray() as $category)
            {
                $sql_aufgaben = rex_sql::factory();
                // $sql_aufgaben->setDebug();
                $sql_aufgaben->setTable('rex_aufgaben');
                $sql_aufgaben->setWhere('category = ' . $category["id"]);
                $sql_aufgaben->select('*');
                $aufgaben_array = $sql_aufgaben->getArray();
                $color = "";
                ?>

                <div class="panel panel-default kanban-col" >
                    <div class="panel-heading" style="position: relative; border-bottom: 4px solid <?= $category["color"]; ?>;">
                        <span class="kanban-heading">
                            <?= $category["category"]; ?>
                        </span>
                    </div>
                    <div class="panel-body" data-categoryid="<?= $category["id"]; ?>" data-color="<?= $category["color"]; ?>">
                        <div class="kanban-centered">
                            <?php
                            for ($i = 0; $i < count($aufgaben_array); $i++)
                            {
                                $itemuid = "item" . uniqid();
                                $uid = "uid" . uniqid();
                                $aufgabe = $aufgaben_array[$i];
                                $description = $aufgabe["description"];
                                ?>
                            <article  style="position: relative; " class="kanban-entry grab" id="<?= $itemuid ?>" draggable="true" data-color="<?= $category["color"]; ?>" data-id="<?= $aufgabe["id"]; ?>" data-categoryid="<?= $category["id"]; ?>" data-item="<?= $itemuid ?>" data-status="<?= $aufgabe["status"] ?>" data-responsible="<?= $aufgabe["responsible"] ?>" data-title="<?= $aufgabe["title"] ?>" data-id="<?= $aufgabe["id"] ?>" data-description="<?= $aufgabe["description"] ?>" data-categoryname="<?= $category["category"]; ?>" >
                                <?= getPrioLinks($aufgabe["prio"], $aufgabe["id"]); ?>
                                    <a href="#" class="delete-kanban-entry"><i class="fa fa-2x fa-trash-o pull-right"></i></a>
                                    <a href="#" class="edit-kanban-entry" ><i class="fa fa-2x fa-pencil-square-o pull-right"></i></a>
                                    <div class="kanban-entry-inner">
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
                                            if (strlen($description))
                                            {
                                                ?>
                                                <h4>
                                                    <a class="expand collapsed" role="button" data-toggle="collapse" href="#<?= $uid ?>" aria-expanded="false" aria-controls="collapseExample">
                                                        <?= $aufgabe["title"] ?>
                                                    </a>
                                                </h4>
                                                <div class="collapse" id="<?= $uid ?>">
                                                    <div class="well">
                                                        <?php
                                                            if (rex_addon::get('textile')->isAvailable()) {
                                                              $description = str_replace('<br />', '', $description);
                                                              $description = rex_textile::parse($description);
                                                              $description = str_replace('###', '&#x20;', $description);
                                                            }
                                                            if (rex_addon::get('rex_markitup')->isAvailable()) {
                                                                  $description = rex_markitup::parseOutput('textile', $description);
                                                              }
                                                              echo $description;
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h4><?= $aufgabe["title"] ?></h4>
                                                <?php
                                            }

                                            echo getStatusLinks($aufgabe["status"], $aufgabe["id"]);
                                            ?>
                                            <hr>
                                            <span class="responsible">
                                                <?php echo $this->i18n('aufgaben_kanban_responsible'); ?>: <?= getresponsible($aufgabe["responsible"]); ?>
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
                        <a href="#" data-categoryname="<?= $category["category"]; ?>" data-categoryid="<?= $category["id"]; ?>" class="add-kanban-entry btn btn-default btn-raised "><i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo $this->i18n('aufgaben_kanban_aufgabe_hinzufuegen'); ?></a>
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
                <h4 class="modal-title"><?php echo $this->i18n('aufgaben_kanban_aufgabe_hinzufuegen'); ?></h4>
            </div>
            <div class="modal-body">
                <div id="missing-values" class="alert alert-danger" role="alert" style="display: none">
                    <?php echo $this->i18n('aufgaben_kanban_error_title'); ?>
                </div>
                <form>
                    <input type="hidden" name="addtodo" value="true" />
                    <fieldset>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-title"><?php echo $this->i18n('aufgaben_kanban_title'); ?></label>
                            </dt>
                            <dd>
                                <input id="rex-aufgaben-title" class="form-control" name="title" value="" type="text">
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-description"><?php echo $this->i18n('aufgaben_kanban_description'); ?></label>
                            </dt>
                            <dd>
                                <textarea id="rex-aufgaben-description" class="form-control" rows="6" name="description"></textarea>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-category-name"><?php echo $this->i18n('aufgaben_kanban_category'); ?></label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <input type="text" disabled="disabled" id="rex-aufgaben-category-name" name="category-name" size="1" id="rex-aufgaben-category" class="form-control" />
                                    <input type="hidden" name="category" size="1" id="rex-aufgaben-category" class="form-control" />
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label class="control-label" for="rex-aufgaben-responsible"><?php echo $this->i18n('aufgaben_kanban_responsible'); ?></label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="responsible" size="1" id="rex-aufgaben-responsible" class="form-control">
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
                                <label class="control-label" for="rex-aufgaben-status"><?php echo $this->i18n('aufgaben_kanban_status'); ?></label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="status" size="1" id="rex-aufgaben-status" class="form-control">
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
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->i18n('aufgaben_kanban_abort'); ?></button>
                <button type="button" class="btn btn-success" id="save-add-entry"><?php echo $this->i18n('aufgaben_kanban_save'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo $this->i18n('aufgaben_kanban_edit'); ?></h4>
            </div>
            <div class="modal-body">
                <div id="missing-values" class="alert alert-danger" role="alert" style="display: none">
                     <?php echo $this->i18n('aufgaben_kanban_error_title'); ?>
                </div>
                <form>
                    <input type="hidden" name="edittodo" value="true" />
                    <input type="hidden" id="entry-id" name="entry-id" value="" />
                    <fieldset>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-title"><?php echo $this->i18n('aufgaben_kanban_title'); ?></label>
                            </dt>
                            <dd>
                                <input id="rex-aufgaben-title" class="form-control" name="title" value="" type="text">
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-description"><?php echo $this->i18n('aufgaben_kanban_description'); ?></label>
                            </dt>
                            <dd>
                                <textarea id="rex-aufgaben-description" class="form-control" rows="6" name="description"></textarea>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-category-name"><?php echo $this->i18n('aufgaben_kanban_category'); ?></label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="category" size="1" id="rex-aufgaben-category-name" class="form-control">
                                        <?php
                                            foreach ($sql_categories->getArray() as $category)
                                            {
                                                var_dump($category);
                                                echo '<option value="' . $category["id"] . '">'.$category["category"] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt><label class="control-label" for="rex-aufgaben-responsible"><?php echo $this->i18n('aufgaben_kanban_responsible'); ?></label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="responsible" size="1" id="rex-aufgaben-responsible" class="form-control">
                                        <?php
                                            foreach ($sql_zustaendig->getArray() as $zustaendig)
                                            {
                                                // var_dump($zustaendig);
                                                echo '<option value="' . $zustaendig["id"] . '">' . $zustaendig["login"] . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                        <dl class="rex-form-group form-group">
                            <dt>
                                <label class="control-label" for="rex-aufgaben-status"><?php echo $this->i18n('aufgaben_kanban_status'); ?></label>
                            </dt>
                            <dd>
                                <div class="rex-select-style">
                                    <select name="status" size="1" id="rex-aufgaben-status" class="form-control">
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
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $this->i18n('aufgaben_kanban_abort'); ?></button>
                <button type="button" class="btn btn-success" id="save-edit-entry"><?php echo $this->i18n('aufgaben_kanban_save'); ?></button>
            </div>
        </div>
    </div>
</div>



<div id="confirm" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <?php echo $this->i18n('aufgaben_kanban_delete_confirm'); ?><br/>
                <strong class="delete-title"></strong>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete-entry"><?php echo $this->i18n('aufgaben_kanban_delete'); ?></button>
                <button type="button" data-dismiss="modal" class="btn btn-default"><?php echo $this->i18n('aufgaben_kanban_abort'); ?></button>
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
