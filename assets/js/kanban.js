var redaxo = redaxo || {};
redaxo.Kanban = redaxo.Kanban || (function (jQuery)
{
    var jRexLoader;

    var Kanban = function ()
    {
        var jKanbanStage = jQuery('.kanban-stage');
        var jKanbanCol = jQuery('.panel-body');
        var jChangePrioTrigger = jQuery(".change-prio");
        var jChangeStatusTrigger = jQuery(".change-status");
        var jDeleteEntryTrigger = jQuery(".delete-kanban-entry");
        var jAddEntryTrigger = jQuery(".add-kanban-entry");
        var jEditEntryTrigger = jQuery(".edit-kanban-entry");
        var jDraggableContainer = jQuery(".grab");
        var jDropContainer = jQuery(".panel-body");

        jQuery.extend(true, this,
        {
            jKanbanStage: jKanbanStage,
            jKanbanCol: jKanbanCol,
            jChangePrioTrigger: jChangePrioTrigger,
            jChangeStatusTrigger: jChangeStatusTrigger,
            jDeleteEntryTrigger: jDeleteEntryTrigger,
            jAddEntryTrigger: jAddEntryTrigger,
            jEditEntryTrigger: jEditEntryTrigger,
            jDraggableContainer: jDraggableContainer,
            jDropContainer: jDropContainer
        });

        initView.call(this);
    };

    var getRexLoader = function getRexLoader()
    {
        var that = this;

        var checkExist = setInterval(function ()
        {
            if (jQuery('#rex-js-ajax-loader').length)
            {
                jRexLoader =  jQuery('#rex-js-ajax-loader');
                attachEventHandler.call(that);
                clearInterval(checkExist);
            }
        }, 100);
    };


    var initView = function initView()
    {
        getRexLoader.call(this);
        initKanban.call(this);
    };


    // init kanban view
    var initKanban = function initKanban()
    {
        var kanbanColCount = parseInt(this.jKanbanCol.length);

        this.jKanbanCol.css('max-height', (window.innerHeight - 150) + 'px');
        this.jKanbanStage.css('min-width', (kanbanColCount * 325) + 'px');
    };

    var kanbanAjax = function kanbanAjax(postData, successCallback)
    {
        jQuery.ajax(
        {
            method: "POST",
            url: kanbanAjaxUrl,
            data: postData,
            beforeSend: function (xhr)
            {
                jRexLoader.addClass('rex-visible');
            },
            success: function (data, textStatus, jqXHR)
            {
                successCallback();
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                jRexLoader.removeClass('rex-visible');
                console.error("#ERROR: jqXHR, textStatus, errorThrown", jqXHR, textStatus, errorThrown);
            }
        });
    };


    // change kanban entry prio
    var changePrioHandler = function changePrioHandler()
    {
        var jThis = jQuery(this);
        var jPrioWrapper = jThis.parent();
        var jPrioItems = jPrioWrapper.find(".change-prio");
        var id = jThis.data("id");
        var thisPrioID = jThis.data("prioid");
        var hasClass = jThis.hasClass("current");

        if (!hasClass)
        {
            prioId = thisPrioID;
        }

        var postSuccessCallback = function postSuccessCallback()
        {
            if (hasClass)
            {
                jThis.removeClass("current");
            }
            else
            {
                jPrioItems.removeClass("current");

                if (prioId > 0)
                {
                    jThis.addClass("current");
                }
            }

            jRexLoader.removeClass('rex-visible');
        };

        kanbanAjax({"updateprio": "true", "prioid": prioId, "id": id}, postSuccessCallback);
    };


    // change kanban entry status
    var changeStatusHandler = function changeStatusHandler()
    {
        var jThis = jQuery(this);
        var jStatusWrapper = jThis.parent();
        var jStatusItems = jStatusWrapper.find(".change-status");
        var id = jThis.data("id");
        var thisStatusID = jThis.data("statusid");
        var hasClass = jThis.hasClass("current");
        var statusId = thisStatusID;

        var postSuccessCallback = function postSuccessCallback()
        {
            if (hasClass)
            {
                jThis.addClass("current");
            }
            else
            {
                jStatusItems.removeClass("current");
                jThis.addClass("current");
            }

            jRexLoader.removeClass('rex-visible');
        };

        kanbanAjax({"updatestatus": "true", "statusid": statusId, "id": id}, postSuccessCallback);
    };


    // delete kanban entry
    var deleteEntryHandler = function deleteEntryHandler(event)
    {
        event.preventDefault();

        var jThis = jQuery(this);
        var jEntry = jThis.closest("article.kanban-entry");
        var id = jEntry.data("id");
        var title = jEntry.data("title");
        var item = jEntry.data("item");
        var jItem = jQuery("#" + item);
        var jDeleteEntryModal = jQuery('#confirm');
        var jConfirmItemTitle = jDeleteEntryModal.find(".delete-title");

        jConfirmItemTitle.text(title);

        var postSuccessCallback = function postSuccessCallback()
        {
            jItem.slideUp("fast", function ()
            {
                jItem.remove();
                jRexLoader.removeClass('rex-visible');
            });
        };

        jDeleteEntryModal.modal().one('click', '#delete-entry', function (event)
        {
            kanbanAjax({"deletetodo": "true", "id": id}, postSuccessCallback);
        });
    };


    // add kanban entry
    var addEntryHandler = function addEntryHandler(event)
    {
        event.preventDefault();

        var jThis = jQuery(this);
        var jAddEntryModal = jQuery("#add-modal");
        var kategorieId = jThis.data("kategorieid");
        var kategorieName = jThis.data("kategoriename");
        var jForm = jAddEntryModal.find("form");
        var jTitle = jForm.find("#rex-aufgaben-aufgaben-titel");
        var jDescription = jForm.find("#rex-aufgaben-aufgaben-beschreibung");
        var jCategoryID = jForm.find("#rex-aufgaben-aufgaben-kategorie");
        var jCategoryName = jForm.find("#rex-aufgaben-aufgaben-kategorie-name");
        var jErrorContainer = jQuery("#missing-values");

        console.log(jThis);
        console.log("kategorieId", kategorieId);

        jErrorContainer.hide();

        jTitle.val("");
        jDescription.val("");
        jCategoryName.val(kategorieName);
        jCategoryID.val(parseFloat(kategorieId));

        var checkForm = function checkForm(event)
        {
            var formData = jForm.serialize();

            if (jTitle.val() === "" || jCategoryName.val() === "")
            {
                jErrorContainer.slideDown();
                jAddEntryModal.modal().one('click', '#save-add-entry', checkForm);
            }
            else
            {
                jErrorContainer.slideUp();

                var postSuccessCallback = function postSuccessCallback()
                {
                    jAddEntryModal.modal('hide');
                    window.location.reload();
                };

                kanbanAjax(formData, postSuccessCallback);
            }
        };

        jAddEntryModal.modal().one('click', '#save-add-entry', checkForm);
    };


    // edit kanban entry
    var editEntryHandler = function editEntryHandler(event)
    {
        event.preventDefault();

        var jThis = jQuery(this);
        var jEntry = jThis.closest("article.kanban-entry");
        var jEditEntryModal = jQuery("#edit-modal");
        var kategorieId = jEntry.data("kategorieid");
        var id = jEntry.data("id");
        var titel = jEntry.data("title");
        var beschreibung = jEntry.data("beschreibung");
        var eigentuemer = jEntry.data("eigentuemer");
        var status = jEntry.data("status");
        var jForm = jEditEntryModal.find("form");
        var jTitle = jForm.find("#rex-aufgaben-aufgaben-titel");
        var jId = jForm.find("#entry-id");
        var jDescription = jForm.find("#rex-aufgaben-aufgaben-beschreibung");
        var jStatus = jForm.find("#rex-aufgaben-aufgaben-status");
        var jEigentuemer = jForm.find("#rex-aufgaben-aufgaben-eigentuemer");
        var jCategory = jForm.find("#rex-aufgaben-aufgaben-kategorie");
        var jErrorContainer = jQuery("#missing-values");

        console.log(id);
        console.log(jEntry.data());

        jErrorContainer.hide();

        jId.val(id);
        jTitle.val(titel);
        jDescription.val(beschreibung);
        jCategory.val(kategorieId);
        jStatus.val(status);
        jEigentuemer.val(eigentuemer);

        var checkForm = function checkForm(event)
        {
            var formData = jForm.serialize();

            if (jTitle.val() === "" || jCategory.val() === "")
            {
                jErrorContainer.slideDown();
                jEditEntryModal.modal().one('click', '#save-edit-entry', checkForm);
            }
            else
            {
                jErrorContainer.slideUp();

                var postSuccessCallback = function postSuccessCallback()
                {
                    jEditEntryModal.modal('hide');
                    window.location.reload();
                };

                kanbanAjax(formData, postSuccessCallback);
            }
        };

        jEditEntryModal.modal().one('click', '#save-edit-entry', checkForm);
    };


    // draggable settings
    var draggableSettings = {
        appendTo: 'body',
        helper: function ()
        {
            var dom = [];
            var jThis = jQuery(this);
            var color = jThis.data("color");

            dom.push('<div id="kanban-drag-helper" style="font-size: 40px; color: ' + color + ';"><i class="fa fa-sticky-note" aria-hidden="true"></i></div>');

            return $(dom.join(''));
        },
        cursorAt: {right: 15, bottom: 20},
        cursor: "move"
    };


    // droppable settings
    var droppableSettings = {
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)",
        over: function (event, ui)
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

            if (draggedkategorieid === kategorieid)
            {
                return false;
            }

            var postSuccessCallback = function postSuccessCallback()
            {
                jRexLoader.removeClass('rex-visible');
                jThis.find(".placeholder").remove();
                jDraggedItem.appendTo(jAppendContainer);
                jDraggedItem.data("kategorieid", kategorieid);
                jDraggedItem.find(".kanban-entry-inner").css({"border-color": color});
            };

            kanbanAjax({"updatekategorie": "true", "id": id, "kategorieid": kategorieid}, postSuccessCallback);
        }
    };


    var attachEventHandler = function attachEventHandler()
    {
        this.jChangePrioTrigger.on("click", changePrioHandler);
        this.jChangeStatusTrigger.on("click", changeStatusHandler);
        this.jDeleteEntryTrigger.on("click", deleteEntryHandler);
        this.jAddEntryTrigger.on("click", addEntryHandler);
        this.jEditEntryTrigger.on("click", editEntryHandler);
        this.jDraggableContainer.draggable(draggableSettings);
        this.jDropContainer.droppable(droppableSettings);
    };

    return Kanban;

}(jQuery));
