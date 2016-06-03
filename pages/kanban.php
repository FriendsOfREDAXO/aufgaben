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
                                <article class="kanban-entry grab" data-color="<?= $kategorie["farbe"]; ?>" id="<?= $itemuid ?>" draggable="true" data-id="<?= $aufgabe["id"]; ?>" data-kategorieid="<?= $kategorie["id"]; ?>">
                                            
                                <?= getPrioLinks($aufgabe["prio"], $aufgabe["id"]); ?>
                                    <a href="#" class="delete-kanban-entry" data-item="<?= $itemuid ?>" data-title="<?= $aufgabe["titel"] ?>" data-id="<?= $aufgabe["id"] ?>"><i class="fa fa-2x fa-minus-circle pull-right"></i></a>
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
                                        <option value="" selected="selected">Bitte wählen</option>
                                        <option value="3">123</option>
                                        <option value="1" selected="selected">rex5</option>
                                        <option value="2">test</option>
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
                                        <option value="1">Offen</option>
                                        <option value="2">Wird bearbeitet</option>
                                        <option value="3">Frage</option>
                                        <option value="4">Warten auf etwas</option>
                                        <option value="5">Auf später verschoben</option>
                                        <option value="6">Erledigt</option>
                                    </select>
                                </div>
                            </dd>
                        </dl>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                <button type="button" class="btn btn-success" id="save-entry">Speichern</button>
            </div>
        </div>
    </div>
</div>



<div id="confirm" class="modal modal-static fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <strong class="delete-title"></strong>
                <br>
                <br>
                Möchtest du die Aufgabe wirklich löschen?
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete">Löschen</button>
                <button type="button" data-dismiss="modal" class="btn">Abbrechen</button>
            </div>
        </div>
    </div>
</div>

<style>
    .kanban-wrapper{width:100%;position:relative;overflow-X:scroll}.kanban-wrapper a:hover,.kanban-wrapper a:active,.kanban-wrapper a:focus,.kanban-wrapper a:link,.kanban-wrapper a:visited{outline:0;outline:0 !important;outline-style:none}.kanban-wrapper h4{color:#324050}.kanban-wrapper h4 a{color:#324050;display:block;position:relative;padding-left:17px}.kanban-wrapper h4 a:hover,.kanban-wrapper h4 a:focus,.kanban-wrapper h4 a:active{text-decoration:none}.kanban-wrapper h4 a.expand:after{content:"\f139";font-family:FontAwesome;font-size:14px;position:absolute;left:0;top:2px;height:15px;width:15px}.kanban-wrapper h4 a.expand.collapsed:after{content:"\f13a"}.kanban-wrapper .kanban-heading{font-size:16px;font-weight:bold}.kanban-wrapper img{vertical-align:middle}.kanban-wrapper .img-responsive{display:block;height:auto;max-width:100%}.kanban-wrapper .img-thumbnail{background-color:#fff;border:1px solid #ededf0;display:inline-block;height:auto;line-height:1.428571429;max-width:100%;moz-transition:all .2s ease-in-out;o-transition:all .2s ease-in-out;padding:2px;transition:all .2s ease-in-out;webkit-transition:all .2s ease-in-out}.kanban-wrapper .kanban-centered{position:relative;margin-bottom:30px}.kanban-wrapper .kanban-centered:before,.kanban-wrapper .kanban-centered:after{content:" ";display:table}.kanban-wrapper .kanban-centered:after{clear:both}.kanban-wrapper .kanban-centered:before{content:'';position:absolute;display:block;width:2px;top:20px;bottom:20px}.kanban-wrapper .kanban-entry{position:relative;margin:10px 8px;clear:both;-moz-box-shadow:rgba(50,50,50,0.5) 1px 1px 2px;-webkit-box-shadow:rgba(50,50,50,0.5) 1px 1px 2px;box-shadow:rgba(50,50,50,0.5) 1px 1px 2px}.kanban-wrapper .kanban-entry:after{clear:both}.kanban-wrapper .kanban-entry:before,.kanban-wrapper .kanban-entry:after{content:" ";display:table}.kanban-wrapper .kanban-entry:after{clear:both}.kanban-wrapper .kanban-entry.begin{margin-bottom:0}.kanban-wrapper .kanban-entry.left-aligned{float:left}.kanban-wrapper kanban-entry.left-aligned .kanban-entry-inner{margin-left:0;margin-right:-18px}.kanban-wrapper .kanban-time{left:auto;right:-100px;text-align:left}.kanban-wrapper .kanban-icon{float:right}.kanban-wrapper .kanban-label{margin-left:0;width:100%}.kanban-wrapper .kanban-label:after{left:auto;right:0;margin-left:0;margin-right:-9px;-moz-transform:rotate(180deg);-o-transform:rotate(180deg);-webkit-transform:rotate(180deg);-ms-transform:rotate(180deg);transform:rotate(180deg)}.kanban-wrapper .kanban-entry-inner{position:relative}.kanban-wrapper .kanban-entry-inner:before,.kanban-wrapper .kanban-entry-inner:after{content:" ";display:table}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner:after{clear:both}.kanban-wrapper .kanban-entry-inner:before,.kanban-wrapper .kanban-entry-inner:after{content:" ";display:table}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-time{position:absolute;left:-100px;text-align:right;padding:10px;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-time>span{display:block}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-time>span:first-child{font-size:15px;font-weight:bold}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-time>span:last-child{font-size:12px}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label{position:relative;background:#f5f5f6;padding:0.75em;-webkit-background-clip:padding-box;-moz-background-clip:padding;background-clip:padding-box}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label h2,.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label p{color:#737881;font-family:"Noto Sans",sans-serif;font-size:12px;margin:0;line-height:1.428571429}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label p+p{margin-top:15px}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label h2{font-size:16px;margin-bottom:10px}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label h2 a{color:#303641}.kanban-wrapper .kanban-centered .kanban-entry .kanban-entry-inner .kanban-label h2 span{-webkit-opacity:.6;-moz-opacity:.6;opacity:.6;-ms-filter:alpha(opacity=60);filter:alpha(opacity=60)}.kanban-wrapper .modal-static{position:fixed;top:50% !important;left:50% !important;margin-top:-100px;margin-left:-100px;overflow:visible !important}.kanban-wrapper .modal-static,.kanban-wrapper .modal-static .modal-dialog,.kanban-wrapper .modal-static .modal-content{width:200px;height:150px}.kanban-wrapper .modal-static .modal-dialog,.kanban-wrapper .modal-static .modal-content{padding:0 !important;margin:0 !important}.kanban-wrapper .kanban-col{width:300px;margin-right:20px;float:left}.kanban-wrapper .panel-body{padding:15px 0 0 0;overflow-y:auto}.kanban-wrapper .grabbing{cursor:-moz-grabbing;cursor:-webkit-grabbing}.kanban-wrapper .panel-heading{cursor:context-menu}.kanban-wrapper .panel-heading i{cursor:pointer}.kanban-wrapper .status{display:table;width:100%}.kanban-wrapper .status .change-status{text-align:center;display:table-cell;width:16.66666666666667%}.kanban-wrapper .status .change-status.current i{color:black;padding:3px}.kanban-wrapper .status .change-status.current.done i{color:#3CB594}.kanban-wrapper hr{border-top:1px solid #DFE3E9;margin:10px 0}.kanban-wrapper .kanban-entry .prio-wrapper{position:absolute;right:2px;top:-8px;z-index:50;filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);opacity:0;-moz-transition-property:all;-o-transition-property:all;-webkit-transition-property:all;transition-property:all;-moz-transition-duration:0.3s;-o-transition-duration:0.3s;-webkit-transition-duration:0.3s;transition-duration:0.3s;-moz-transition-timing-function:ease-in-out;-o-transition-timing-function:ease-in-out;-webkit-transition-timing-function:ease-in-out;transition-timing-function:ease-in-out}.kanban-wrapper .kanban-entry .prio-wrapper .current{color:#F0AD4E}.kanban-wrapper .kanban-entry .prio-wrapper .change-prio{padding:2px}.kanban-wrapper .kanban-entry .delete-kanban-entry{position:absolute;right:-5px;bottom:-5px;font-size:10px;color:#C9302C;z-index:50;filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);opacity:0;-moz-transition-property:all;-o-transition-property:all;-webkit-transition-property:all;transition-property:all;-moz-transition-duration:0.3s;-o-transition-duration:0.3s;-webkit-transition-duration:0.3s;transition-duration:0.3s;-moz-transition-timing-function:ease-in-out;-o-transition-timing-function:ease-in-out;-webkit-transition-timing-function:ease-in-out;transition-timing-function:ease-in-out}.kanban-wrapper .kanban-entry .delete-kanban-entry i{filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=50);opacity:0.5}.kanban-wrapper .kanban-entry .delete-kanban-entry:hover i{filter:progid:DXImageTransform.Microsoft.Alpha(enabled=false);opacity:1}.kanban-wrapper .kanban-entry:hover .prio-wrapper{filter:progid:DXImageTransform.Microsoft.Alpha(enabled=false);opacity:1}.kanban-wrapper .kanban-entry:hover .delete-kanban-entry{filter:progid:DXImageTransform.Microsoft.Alpha(enabled=false);opacity:1}.kanban-wrapper #drop-helper{font-size:30px;color:black}
</style>
<script>
    var kanbanCol = jQuery('.panel-body');
    kanbanCol.css('max-height', (window.innerHeight - 150) + 'px');

    var kanbanColCount = parseInt(kanbanCol.length);
    jQuery('.kanban-stage').css('min-width', (kanbanColCount * 350) + 'px');

    jQuery(".change-prio").on("click", function ()
    {
        var jThis = jQuery(this);
        var jPrioWrapper = jThis.parent();
        var jPrioItems = jPrioWrapper.find(".change-prio");
        var id = jThis.data("id");
        var thisPrioID = jThis.data("prioid");
        var hasClass = jThis.hasClass("current");
        
        if(!hasClass)
        {
            prioId = thisPrioID;
        }

        jQuery.ajax(
        {
            method: "POST",
            url: "<?= rex_url::currentBackendPage() ?>",
            data: {"updateprio": "true", "prioid": prioId, "id": id},
            beforeSend: function (xhr)
            {
                jRexLoader.addClass('rex-visible');
            },
            success: function (data, textStatus, jqXHR)
            {
                if(hasClass)
                {
                    jThis.removeClass("current");
                }
                else
                {
                    jPrioItems.removeClass("current");
                    
                    if(prioId > 0)
                    {
                        jThis.addClass("current");
                    }
                }
                
                jRexLoader.removeClass('rex-visible');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                jRexLoader.removeClass('rex-visible');
                console.error("ERROR", textStatus, errorThrown);
            }
        });
    });
    
    
    jQuery(".change-status").on("click", function ()
    {
        var jThis = jQuery(this);
        var jStatusWrapper = jThis.parent();
        var jStatusItems = jStatusWrapper.find(".change-status");
        var id = jThis.data("id");
        var thisStatusID = jThis.data("statusid");
        var hasClass = jThis.hasClass("current");
        var statusId = thisStatusID;

        jQuery.ajax(
        {
            method: "POST",
            url: "<?= rex_url::currentBackendPage() ?>",
            data: {"updatestatus": "true", "statusid": statusId, "id": id},
            beforeSend: function (xhr)
            {
                jRexLoader.addClass('rex-visible');
            },
            success: function (data, textStatus, jqXHR)
            {
                if(hasClass)
                {
                    jThis.addClass("current");
                }
                else
                {
                    jStatusItems.removeClass("current");
                    jThis.addClass("current");
                }
                
                jRexLoader.removeClass('rex-visible');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                jRexLoader.removeClass('rex-visible');
                console.error("ERROR", textStatus, errorThrown);
            }
        });
    });

    jQuery(".grab").draggable(
    {
        appendTo: 'body',
        helper: function() 
        {
            var dom = [];
            var jThis = jQuery(this);
            var color = jThis.data("color");

            dom.push('<div style="font-size: 40px; color: '+color+';"><i class="fa fa-sticky-note" aria-hidden="true"></i></div>');

            return $(dom.join(''));
        },
        cursorAt: {right: 15, bottom: 20},
        cursor: "move"
    });
    
    
    jQuery(".delete-kanban-entry").on("click", function (event)
    {
        event.preventDefault();
        
        var jThis = jQuery(this);
        var id = jThis.data("id");
        var title = jThis.data("title");
        var item = jThis.data("item");
        var jItem = jQuery("#" + item);
        var jConfirm = jQuery('#confirm');
        var jConfirmItemTitle = jConfirm.find(".delete-title");
        
        jConfirmItemTitle.text(title);
                
        jConfirm.modal().one('click', '#delete', function (event) 
        {
            jQuery.ajax(
            {
                method: "POST",
                url: "<?= rex_url::currentBackendPage() ?>",
                data: {"deletetodo": "true", "id": id},
                beforeSend: function (xhr)
                {
                    jRexLoader.addClass('rex-visible');
                },
                success: function (data, textStatus, jqXHR)
                {
                    jItem.slideUp("fast", function ()
                    {
                        jItem.remove();
                        jRexLoader.removeClass('rex-visible');
                    });
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    jRexLoader.removeClass('rex-visible');
                    console.error("ERROR", textStatus, errorThrown);
                }
            });
        });
    });
    
    jQuery(".add-kanban-entry").on("click", function (event)
    {
        event.preventDefault();
        
        var jThis = jQuery(this);
        var jModal = jQuery("#add-modal");
        var kategorieId = jThis.data("kategorieid");
        var kategorieName = jThis.data("kategoriename");
        var jForm = jModal.find("form");
        var jTitle = jForm.find("#rex-aufgaben-aufgaben-titel");
        var jDescription = jForm.find("#rex-aufgaben-aufgaben-beschreibung");
        var jCategoryID = jForm.find("#rex-aufgaben-aufgaben-kategorie");
        var jCategoryName = jForm.find("#rex-aufgaben-aufgaben-kategorie-name");
        var jErrorContainer = jQuery("#missing-values");
            
        jErrorContainer.hide();
            
        jTitle.val("");
        jDescription.val("");
        jCategoryName.val(kategorieName);
        jCategoryID.val(parseFloat(kategorieId));

        var checkForm = function checkForm (event) 
        {
            var data = jForm.serialize();
            console.log(data);

            if (jTitle.val() === "" || jCategoryName.val() === "") 
            {
                jErrorContainer.slideDown();
                jModal.modal().one('click', '#save-entry', checkForm);
            }
            else
            {
                jErrorContainer.slideUp();
                
                jQuery.ajax(
                {
                    method: "POST",
                    url: "<?= rex_url::currentBackendPage() ?>",
                    data: data,
                    beforeSend: function (xhr)
                    {
                        jRexLoader.addClass('rex-visible');
                    },
                    success: function (data, textStatus, jqXHR)
                    {
                        //jRexLoader.removeClass('rex-visible');
                        jModal.modal('hide');
                        window.location.reload();
                    },
                    error: function (jqXHR, textStatus, errorThrown)
                    {
                        jRexLoader.removeClass('rex-visible');
                        console.error("ERROR", textStatus, errorThrown);
                    }
                });                
            }
        };

        jModal.modal().one('click', '#save-entry', checkForm);
    });

    var jRexLoader = jQuery('#rex-js-ajax-loader');

    jQuery(".panel-body").droppable(
    {
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)",
        over: function( event, ui ) 
        {
            var jThis = jQuery(this);
            var jHelperItem = jQuery(ui.helper);
            var color = jThis.data("color");
            jHelperItem.css({"color": color});
        },
        drop: function (event, ui)
        {
            var jThis = jQuery(this);
            var kategorieid = jThis.data("kategorieid");
            var jAppendContainer = jThis.find(".kanban-centered");
            var jDraggedItem = jQuery(ui.draggable);
            var id = jDraggedItem.data("id");
            var draggedkategorieid = jDraggedItem.data("kategorieid");
            var color = jThis.data("color");

            if(draggedkategorieid === kategorieid)
            {
                return false;
            }

            jQuery.ajax(
            {
                method: "POST",
                url: "<?= rex_url::currentBackendPage() ?>",
                data: {"updatekategorie": "true", "id": id, "kategorieid": kategorieid},
                beforeSend: function (xhr)
                {
                    jRexLoader.addClass('rex-visible');
                },
                success: function (data, textStatus, jqXHR)
                {
                    jRexLoader.removeClass('rex-visible');
                    jThis.find(".placeholder").remove();
                    jDraggedItem.appendTo(jAppendContainer);
                    jDraggedItem.find(".kanban-entry-inner").css({"border-color": color});
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    jRexLoader.removeClass('rex-visible');
                    console.error("ERROR", textStatus, errorThrown);
                }
            });
        }
    });
</script>
