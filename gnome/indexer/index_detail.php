<?php
$SECURITY->isLoggedIn();


use gnome\classes\model\Indices;
use gnome\classes\model\IndicesLink;
use gnome\classes\model\User;
use gnome\classes\service\IndicesKeywordService;

$Indices = new Indices();
$User = new User();
$IndicesLink = new IndicesLink();
$IndicesKeywordService = new IndicesKeywordService();

$lang = lang();

$ownerName = '';
$ownerId = '';
$indexId = '';
$indices_id = '';
$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';
$optionalFields = [];
$metaFields = [];

$indexId = filter_input(INPUT_GET, 'index_id');


$yesNoTmpDeleteVars = $metaTagTmpDeleteVars = array('{{indicesId}}', '{{saveVal}}', '{{html}}');
$yesNoTmpDelete = '<div class="col-md-6 p-1 d-flex gap-1">
    <input type="text" 
        data-indicesid="{{indicesId}}" 
        value="{{saveVal}}"
        class="form-control" 
        disabled>
    <button class="yes-no-delete delete btn btn-danger" 
        data-indices-optional-field-id="{{html}}">
            <i class="ri-delete-back-2-fill"></i>
    </button>
</div>';


$metaTagTmpDelete = '<div class="col-md-6 p-1 d-flex gap-1">
    <input type="text" 
        data-indicesid="{{indicesId}}" 
        value="{{saveVal}}" 
        class="meta-input-disabled form-control"
        disabled>
    <button class="meta-delete delete btn btn-danger" 
        data-indices-keyword-meta-id="{{html}}">
            <i class="ri-delete-back-2-fill"></i>
    </button>
</div>';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (filter_input(INPUT_POST, 'action') == 'add') {
        $id = $Indices->addIndex();

        // $Indices->addIndexPermission($id, $_SESSION['username'], '1', '1', '1');
        header("Location: ./indices.php?lang=" . lang());
        exit();
    }

    if (filter_input(INPUT_POST, 'action') == 'edit') {
        $indexId = $Indices->updateIndex();
        header("Location: ./index_detail.php?index_id=$indexId&action=edit&lang=" . lang());
        exit();
    }
}



if (!is_null($indexId)) {

    $index =  $Indices->getIndex($_GET['index_id']);
    extract($index);
    $optionalFields = $Indices->getOptionalFields($_GET['index_id']);
    $metaFields =  $Indices->getMetaFields($_GET['index_id']);

    $SECURITY->indexPermission($indexId)?->hasRightAccess('can_admin', 'Admin access needed');

    // $ownerName = first and last, $ownerId == $user_id
    extract($Indices->getIndexOwner($indexId));

    // only the admin 
    $adminUsers = $Indices->getIndexAdminUsers($indexId);
    $currentUsers = $Indices->getIndexUsers($indexId);
    $availableUsers = $Indices->getAvailableUsers($indexId);

    $linkedIndices = $IndicesLink->getLinkedIndex($indexId);
}

?>
<!DOCTYPE html>
<html>

<head>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>
    <link rel="stylesheet" href="../assets/jodit/jodit.min.css">
    <script src="../assets/jodit/jodit.min.js"></script>
</head>

<body class="">
    <?php include __DIR__ . '/../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">

        <?php include '../includes/title.inc.php'; ?>
        <div class="pagetitle">
            <h1>Indices</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/indices.php">Indices</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Editing (<?= $name ?>)
                    </li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section" id="wrapper">

            <div class="row">
                <div class="col-md-12">
                    <?php include '../includes/head-resp.inc.php'; ?>
                </div>
            </div>

            <?php if (!empty($_GET['action']) && ($_GET['action'] == 'edit' || $_GET['action'] == 'add')) : ?>
                <form id="sectionForm" method="POST">
                    <input type="hidden" name="action" value="<?= $_GET['action'] ?>">
                    <input type="hidden" name="lang" value="<?= $lang ?>">
                    <input type="hidden" name="indices_id" value="<?= $indices_id ?>">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label for="fname">Name (index name) :</label>
                            <input class="form-control" type="text" name="name" id="name" value="<?= $name ?>" required minlength="4" maxlength="555">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="fname">Owner:</label>
                            <input class="form-control" type="text" disabled value="<?= $ownerName != '' ? $ownerName : 'Owner not set' ?>">
                        </div>

                        <div class="col-md-8 mb-2">
                            <label for="description_html" class="mb-3">Description of Index:</label>
                            <textarea rows="4" cols="50" name="description_html" form="sectionForm" id="description_html"><?= $description_html ?></textarea>
                        </div>

                        <div class="col-md-4 mb-2">

                            <?php if ($indices_id) : ?>
                                <div class="flex-blk mb-1">
                                    <label for="description_html">Associated indices:</label>
                                    <button type="button" data-bs-toggle="tooltip" data-bs-original-title="Add or Remove linked indices" class="link-index-btn btn btn-success"><i class="bi bi-link-45deg"></i></button>
                                </div>

                                <aside class="pl-2" id="associatedIndices">
                                </aside>
                            <?php endif ?>

                        </div>
                            
                        <!-- Keyword add section start -->


                        <?php if ($indices_id != '') : ?>

                            <div class="col-md-12 mb-2">
                                <!-- keyword input box -->
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="add-keyword-tooltip"><i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" title="Add a single word or multiple words separated by a ( , )"></i></span>
                                    <input type="text" class="form-control" id="addkeywords" name="addkeywords" placeholder="Keywords" data-index-id="<?= $indexId ?>" aria-describedby="add-keyword-tooltip">
                                    <button class="btn btn-primary" type="button" onclick="KeyWordCls.linkKeyword()" data-bs-toggle="tooltip" title="Add keywords">
                                        + <i class='bx bxs-save'></i>
                                    </button>
                                </div>
                                <!-- keyword display -->
                                <div id="keywordsContainer">
                                    <div id="keywordChips">
                                        <!----    keywords as chips    --->
                                        <?php
                                        $keyWords = $IndicesKeywordService->getIndicesMasterKeywords($indices_id);
                                        $keyWords = is_null($keyWords) ? [] : $keyWords;

                                        foreach ($keyWords as $word) :
                                            $word = (object) $word;
                                        ?>


                                            <div class="chip" id="chip-keyword-<?= $word->id ?>" data-chip-val="<?= $word->value ?>">
                                                <?= $word->value ?>
                                                <span class="closebtn" onclick="KeyWordCls.unlinkKeyword(event, <?= $indices_id ?>, <?= $word->id ?>)">&times;</span>
                                            </div>
                                        <?php endforeach; ?>
                                        <!----    keywords as chips    --->
                                    </div>
                                </div>

                            </div>
                            <!-- Keyword add section end -->


                            <!-- Bootstrap Confirmation Modal -->
                            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                                            <!-- Updated close button -->
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Are you sure you want to unlink this keyword?
                                        </div>
                                        <div class="modal-footer">
                                            <!-- Updated cancel button -->
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary" id="confirmAction">Confirm</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        <?php else : ?>
                            <div class="col-md-12"><strong>Please save index to add keywords</strong></div>
                        <?php endif; ?>

                        <script>
                            // Keyword add Js Code
                            const ajaxPost = (jsonData, callback = '') => {
                                // add loader here
                                // console.log(`callback is : ${callback}`)
                                $.ajax({
                                    type: "POST",
                                    url: "indices_ajax.php",
                                    data: jsonData,
                                    dataType: 'json',
                                    cache: false,
                                    success: function(resp) {
                                        console.log('Post operation complete')
                                        if (callback != '') {
                                            callback(resp);
                                        }
                                    },
                                    error: function(res) {
                                        alert('Operation has failed try again or report to admin', res)
                                    }
                                });
                            }

                            class Keyword {

                                constructor(ajaxP) {
                                    // this.node = node;
                                    this.ajax = ajaxP;
                                }

                                unlinkKeyword(e, indicesId, keywordId) {
                                    // Show the confirmation modal
                                    $('#confirmationModal').modal('show');

                                    // Find the confirm button by its ID and attach a click event listener
                                    document.getElementById('confirmAction').addEventListener('click', () => {
                                        // Define the request object
                                        const requestObj = {
                                            action: 'unlink-keyword',
                                            indicesId: indicesId,
                                            keyWordId: keywordId
                                        };

                                        // Call the AJAX function with the request object
                                        this.ajax(requestObj, (response) => {
                                            // Assuming the operation was successful, remove the keyword chip
                                            document.getElementById(`chip-keyword-${keywordId}`).remove();
                                        });

                                        // Hide the modal after confirming
                                        $('#confirmationModal').modal('hide');
                                    }, {
                                        once: true
                                    }); // Ensure the event listener is added once to prevent multiple bindings


                                }

                                // adds keyword if not present then links it
                                linkKeyword() {
                                    const newKeyWords = (document.getElementById("addkeywords").value).trim();
                                    if (newKeyWords == '') return;

                                    const requestObj = {
                                        "action": "link-keyword",
                                        "keywords": newKeyWords,
                                        "indices_id": <?= json_encode($indexId ?: null) ?>
                                    };
                                    this.ajax(requestObj, this.linkKeywordResponseHandler);
                                }

                                // link work callback
                                /**
                                 * takes a json response loops through it and adds the response to the meta-keyword container
                                 * @response json
                                 */
                                linkKeywordResponseHandler(response) {

                                    const keywordsContainer = document.getElementById("keywordChips");
                                    let htmlToAdd = "";

                                    // Json to HTML mapping
                                    response.keywords.forEach((item) => {
                                        // check if exists
                                        if (document.getElementById(`chip-keyword-${item.id}`) === null) {
                                            htmlToAdd += `<div class="chip new-meta" id="chip-keyword-${item.id}" data-chip-val="${item.value}">
                                                           ${item.value}
                                                        <span class="closebtn" data-indices-id="${response.indices_id}" data-keyword-id="${item.id}">&times;</span>
                                                    </div> `;
                                        }

                                    });

                                    if (htmlToAdd == "") return;

                                    // Convert the string to HTML nodes and prepend them all at once
                                    const range = document.createRange();
                                    const documentFragment = range.createContextualFragment(htmlToAdd);
                                    keywordsContainer.prepend(documentFragment);

                                    // Attach click events to close buttons and meta-controls
                                    document.querySelectorAll("#keywordsContainer .new-meta").forEach(keywordChip => {
                                        // Remove the 'new-meta' class to mark it as initialized
                                        keywordChip.classList.remove('new-meta');

                                        // Directly attach an event listener to the close button
                                        const closeButton = keywordChip.querySelector('.closebtn');
                                        if (closeButton) {
                                            closeButton.addEventListener('click', function(event) {
                                                // TODO ref to inti object should be singleton 
                                                KeyWordCls.unlinkKeyword(event, this.getAttribute('data-indices-id'), this.getAttribute('data-keyword-id'));
                                            });
                                        }
                                    });

                                    //emptys input
                                    document.getElementById('addkeywords').value = '';

                                }
                            }

                            const KeyWordCls = new Keyword(ajaxPost);
                        </script>












                        <?php if ($indices_id != '') : ?>
                            <div class="col-md-4 mb-2 unselectable-text">
                                <label for="">Available Users</label>
                                <div class="input-group mb-3">
                                    <!-- <select class="form-select form-select-sm text-end" size="7" multiple id="select1"> -->
                                    <ul id="list1" class="list-group">
                                        <?php foreach ($availableUsers as $row) : ?>
                                            <?php if ($row["user_id"] != $ownerId) : ?>
                                                <li class="list-group-item" draggable="true" data-value="<?= $row["user_id"] ?>">
                                                    <?= $row["name_first"] ?>
                                                    <?= $row["name_last"] ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                    <!-- <a href="#" class="btn btn-success d-flex align-items-center" id="add">&gt;&gt;</a> -->
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 unselectable-text">
                                <label for="">Regular Users</label>
                                <div class="input-group mb-3">
                                    <!-- <a href="#" class="btn btn-danger d-flex align-items-center" id="remove">&lt;&lt;</a> -->
                                    <!-- <select class="form-select form-select-sm text-end" size="7" multiple id="select2"> -->
                                    <ul id="list2" class="list-group">
                                        <?php foreach ($currentUsers as $row) : ?>
                                            <?php if ($row["user_id"] != $ownerId) : ?>
                                                <li class="list-group-item" draggable="true" data-value="<?= $row["user_id"] ?>">
                                                    <?= $row["name_first"] ?>
                                                    <?= $row["name_last"] ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                    <!-- <a href="#" class="btn btn-success d-flex align-items-center" id="addAdmin" style="float:right">&gt;&gt;</a> -->
                                </div>
                            </div>
                            <div class="col-md-4 mb-2 unselectable-text">
                                <label for="">Admin users</label>
                                <div class="input-group mb-3">
                                    <!-- <a href="#" class="btn btn-danger d-flex align-items-center" id="removeAdmin">&lt;&lt;</a> -->
                                    <!-- <select class="form-select form-select-sm" size="7" multiple id="select3"> -->
                                    <ul id="list3" class="list-group">
                                        <?php foreach ($adminUsers as $row) : ?>
                                            <?php if ($row["user_id"] != $ownerId) : ?>
                                                <li class="list-group-item" draggable="true" data-value="<?= $row["user_id"] ?>">
                                                    <?= $row["name_first"] ?>
                                                    <?= $row["name_last"] ?>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php else : ?>
                            <div class="col-md-12 mb-2"><strong>Please save index to add/remove permissions</strong></div>
                        <?php endif; ?>




                        <div class="col-md-3 mb-2">
                            <div class="input-group mb-4 mt-3 d-flex justify-content-between align-items-center">
                                <label for="text_color">Select index text color</label>
                                <input type="color" id="text_color" name="text_color" value="<?= $text_color ?: '#000000' ?>">
                            </div>
                            <div class="input-group mb-4 d-flex justify-content-between align-items-center">
                                <label for="highlight_color">Select index highlight color</label>
                                <input type="color" id="highlight_color" name="highlight_color" value="<?= $highlight_color ?: '#ffffff' ?>">
                            </div>
                        </div>
                        <div class="col-md-9 mb-2">
                            <div class="contact-report">
                                <div><b>Example</b></div>
                                <div>
                                    <span id="example_text" style="color:<?= $text_color ?>; background-color:<?= $highlight_color ?>">
                                        Certainly,
                                        but we have kept an eye on you, because you have been preoccupied
                                        with these problems in other personalities for
                                        many thousands of years, and because you think and act in a real and
                                        honest way, and because you have already often carried out such a
                                        mission in your former lives, even though great mysteries surround
                                        this for us.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row yes-no-section mb-2">
                        <?php if ($indices_id != '') : ?>
                            <div class="col-md-12 flex-blk">
                                <h3>Yes or No Question:</h3>
                                <button class="yes-no-add-btn btn btn-success btn-sm">
                                    Add Question
                                </button>
                            </div>
                        <?php else : ?>
                            <div class="col-md-12">Please save index to add questions</div>
                        <?php endif; ?>

                        <div class="row yes-no-container">
                            <?php foreach ($optionalFields as $row) :

                                $yesNoTmpDeleteVals = array($row["indices_id"], $row["optional_field"], $row["indices_optional_field_id"]);
                                $output = str_replace($yesNoTmpDeleteVars, $yesNoTmpDeleteVals, $yesNoTmpDelete);

                                echo $output;

                            endforeach; ?>
                        </div>
                    </div>
                    <div class="row meta-section mb-5">
                        <?php if ($indices_id != '') : ?>
                            <div class="col-md-12 flex-blk">
                                <h3>List of possible meta data for index keywords:</h3>
                                <button class="meta-add-btn btn btn-success btn-sm">
                                    Add Meta Data
                                </button>
                            </div>
                        <?php else : ?>
                            <div class="col-md-12">Please save index to add meta data</div>
                        <?php endif; ?>

                        <div class="row meta-container">
                            <?php foreach ($metaFields as $row) :

                                $metaDeleteVals = array($row["indices_id"], $row["meta"], $row["indices_keyword_meta_id"]);
                                $output = str_replace($metaTagTmpDeleteVars, $metaDeleteVals, $metaTagTmpDelete);

                                echo $output;

                            endforeach; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 flex-blk mb-4">
                            <button class="btn btn-secondary" onclick="reset()">Cancel</button>
                            <button class="btn btn-primary" type="submit">Save</button>
                        </div>
                    </div>

                </form>
            <?php else : ?>
                <div class="row">
                    <div class="col-md-12">
                        No index was selected
                    </div>
                </div>
            <?php endif ?>
        </section>
    </main>

    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-dialog-scrollable modal-lg fade" id="linkIndexModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="linkIndexModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="linkIndexModalLabel">Link Indices</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="linkedIndexBody">
                    load via ajax
                </div>
            </div>
        </div>
    </div>




    <script>
        // loads the wysiwyg
        try {
            var editor = new Jodit("#description_html", {
                editorCssClass: 'default-jodit',
                "buttons": "source,bold,|,underline,italic,|,|,,|,font,fontsize,brush,|,,undo,redo,selectall,cut,copy,paste,copyformat"
            });
        } catch (err) {}

        // indices_id
        indicesId = <?= $indices_id ?>


        fetchLinkedIndices = function() {

            const assIn = $('#associatedIndices');
            const data = {
                "action": 'fetch-linked',
                "indices_id": `${indicesId}`
            }
            $.ajax({
                type: "POST",
                url: "index_detail_ajax.php",
                data: data,
                dataType: 'html',
                cache: false,
                success: function(resp) {
                    // console.log(resp)
                    // update Associated index area
                    assIn.html(resp);
                },
                error: function(res) {
                    alert('Failed to load indices', res);
                }
            });
        }

        // only fetch links 

        if (indicesId)
            fetchLinkedIndices();

        // loads the link index
        $(document).ready(function() {

            // attach listener for the check boxes that will be returned in the ajax
            document.getElementById('linkedIndexBody').addEventListener('change', function(event) {
                // Check if the clicked element is a checkbox
                if (event.target && event.target.type === 'checkbox') {
                    // Handle the checkbox change event
                    console.log('Checkbox with value ' + event.target.value + ' is ' + (event.target.checked ? 'checked' : 'unchecked'));
                    // Add your logic here
                    const action = (event.target.checked ? 'add-link-index' : 'remove-link-index');

                    const data = {
                        "action": action,
                        "indices_group_id": `${indicesId}`,
                        "indices_id": (event.target.value).toString(),
                    }
                    $.ajax({
                        type: "POST",
                        url: "index_detail_ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        success: function(resp) {
                            console.log(resp)
                            // call         
                            fetchLinkedIndices()
                        },
                        error: function(res) {
                            alert('Failed to load indices', res);
                        }
                    });

                }
            });

            

            // requires id=wrapper 
            $("#wrapper").on("click", ".link-index-btn", function(e) {
                e.preventDefault();

                // locate modal body
                const linkModalBody = $('#linkedIndexBody');
                const data = {
                    "action": 'get-all-indices',
                    "indices_id": `${indicesId}`
                }

                // Get modal element and create Bootstrap modal instance
                var linkModalElem = document.getElementById('linkIndexModal');
                var linkModal = new bootstrap.Modal(linkModalElem);
                linkModal.show();

                // When the modal is hidden, check if a descendant still has focus and blur it.
                linkModalElem.addEventListener('hidden.bs.modal', function() {
                    if (document.activeElement && linkModalElem.contains(document.activeElement)) {
                        document.activeElement.blur();
                    }
                });

                // linkModal.hide();
                $.ajax({
                    type: "POST",
                    url: "index_detail_ajax.php", // "/gnome/upload_html.php",
                    data: data,
                    dataType: 'html',
                    cache: false,
                    success: function(html) {
                        linkModalBody.html(`${html}`);
                        // attach listeners to the html

                    },
                    error: function(res) {
                        alert('Failed to load indices', res);
                    }
                });
            })

        });

        // inits the color pick section
        $(document).ready(function() {

            document.getElementById('highlight_color').addEventListener('change', function(event) {
                var val = event.target.value;
                var div = document.getElementById('example_text');
                div.style.backgroundColor = val;
            });

            document.getElementById('text_color').addEventListener('change', function(event) {
                var val = event.target.value;
                var div = document.getElementById('example_text');
                div.style.color = val;
            });

        });

        // dynamically add yes-no questions fields
        $(document).ready(function() {

            let yesNoMaxFields = 30;
            let x = 1;
            const yesNoWrapper = $(".yes-no-container");
            const yesNoAddBtn = $(".yes-no-add-btn");

            $(yesNoAddBtn).click(function(e) {
                e.preventDefault();
                if (x < yesNoMaxFields) {
                    x++;
                    $(yesNoWrapper).append(
                        `
                        <div class="col-md-6 p-1 d-flex gap-1">
                        <input type="text" data-indicesid="<?= $indices_id ?>" class="yes-no-input form-control" />
                        <button class="yes-no-save btn btn-primary">Save</button>
                        </div>
                    `
                    ); //add input box
                } else {
                    alert('You Reached the limits')
                }
            });

            // requires id=wrapper 
            $("#wrapper").on("click", ".yes-no-save", function(e) {
                e.preventDefault();
                const pDiv = $(this).parent('div');
                const saveVal = pDiv.find('input[type=text]').val();
                const indicesId = pDiv.find('input[type=text]').attr('data-indicesid');
                const data = {
                    "action": 'add-option',
                    "indices_id": indicesId,
                    "optional_field": saveVal
                }

                $.ajax({
                    type: "POST",
                    url: "indices_ajax.php", // "/gnome/upload_html.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    success: function(html) {

                        const values = {
                            '{{indicesId}}': indicesId,
                            '{{saveVal}}': saveVal,
                            '{{html}}': html
                        }



                        const output = `<?= $yesNoTmpDelete ?>`.replace(/{{indicesId}}|{{saveVal}}|{{html}}/g, function(match) {
                            return values[match];
                        });

                        pDiv.replaceWith(`${output}`);

                    },
                    error: function(res) {
                        alert('no', res)
                    }
                });
            })

            // requires id=wrapper 
            $("#wrapper").on("click", ".yes-no-delete", function(e) {
                e.preventDefault();

                const pDiv = $(this).parent('div');
                const indicesOptionalFieldId = $(this).attr('data-indices-optional-field-id');

                // Show the confirm delete modal
                var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                deleteModal.show();

                // Handle the confirm delete button click
                $('#confirmDeleteButton').on('click', function() {
                    const data = {
                        "action": 'delete-option',
                        "indices_optional_field_id": indicesOptionalFieldId
                    }

                    $.ajax({
                        type: "POST",
                        url: "indices_ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        success: function(html) {
                            pDiv.remove();
                        },
                        error: function(res) {
                            console.log('no', res);
                        }
                    });

                    deleteModal.hide();
                    x--;
                });
            });


        });

        // dynamically add meta
        $(document).ready(function() {
            let metaMaxFields = 10;
            const metaWrapper = $(".meta-container");
            const metaAddButton = $(".meta-add-btn");


            $(metaAddButton).click(function(e) {
                e.preventDefault();

                $(metaWrapper).append(`
                        <div class="col-md-6 p-1 d-flex gap-1">
                        <input type="text" data-indicesid="<?= $indices_id ?>" class="meta-input form-control" />
                        <button class="meta-save btn btn-primary">Save</button>
                        </div>
                    `); //add input box
            });

            $(metaWrapper).on("click", ".meta-save", function(e) {
                e.preventDefault();
                const pDiv = $(this).parent('div');
                const saveVal = pDiv.find('input[type=text]').val();
                const indicesId = pDiv.find('input[type=text]').attr('data-indicesid');
                const data = {
                    "action": 'add-meta',
                    "indices_id": indicesId,
                    "meta": saveVal
                }

                $.ajax({
                    type: "POST",
                    url: "indices_ajax.php", // "/gnome/upload_html.php",
                    data: data,
                    dataType: 'json',
                    cache: false,
                    success: function(html) {

                        const values = {
                            '{{indicesId}}': indicesId,
                            '{{saveVal}}': saveVal,
                            '{{html}}': html
                        }

                        const output = `<?= $metaTagTmpDelete ?>`.replace(/{{indicesId}}|{{saveVal}}|{{html}}/g, function(match) {
                            return values[match];
                        });

                        pDiv.replaceWith(`${output}`);
                    },
                    error: function(res) {
                        alert('no', res)
                    }
                });
            })

            $("#wrapper").on("click", ".meta-delete", function(e) {

                e.preventDefault();

                const pDiv = $(this).parent('div');
                const indicesMetaId = $(this).attr('data-indices-keyword-meta-id');

                // Show the confirm delete modal
                var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                deleteModal.show();

                // Handle the confirm delete button click
                $('#confirmDeleteButton').on('click', function() {

                    const data = {
                        "action": 'delete-meta',
                        "indices_keyword_meta_id": indicesMetaId
                    }

                    $.ajax({
                        type: "POST",
                        url: "indices_ajax.php",
                        data: data,
                        dataType: 'json',
                        cache: false,
                        success: function(html) {
                            pDiv.remove();
                        },
                        error: function(res) {
                            console.log('no', res)
                        }
                    });
                })
            })
        });
    </script>
    <script>
        const ajaxFunction = function(data) {
            $.ajax({
                type: "POST",
                url: "indices_ajax.php",
                data: data,
                dataType: 'json',
                cache: false,
                success: function(html) {
                    console.log('Change success')
                },
                error: function(res) {
                    alert('Permission failed', res)
                }
            });
        }

        $('#addAdmin').click(function() {

            const selectedAdd = $('#select2 option:selected');

            for (let i = 0; i < selectedAdd.length; i++) {
                const data = {
                    "action": 'save-permission',
                    "indices_id": `${indicesId}`,
                    "user_id": selectedAdd[i].value,
                    "can_admin": '1'
                }
                ajaxFunction(data);
            }

            return !selectedAdd.remove().appendTo('#select3');
        });

        $('#add').click(function() {

            const selectedAdd = $('#select1 option:selected');

            for (let i = 0; i < selectedAdd.length; i++) {
                // console.log('----------', selectedAdd[i].value)
                const data = {
                    "action": 'save-permission',
                    "indices_id": `${indicesId}`,
                    "user_id": selectedAdd[i].value,
                    "can_admin": '0'
                }
                ajaxFunction(data);
            }

            return !selectedAdd.remove().appendTo('#select2');
        });

        $('#remove').click(function() {
            const selectedRemove = $('#select2 option:selected');
            for (let i = 0; i < selectedRemove.length; i++) {
                // console.log('----------', selectedRemove[i].value)
                const data = {
                    "action": 'remove-permission',
                    "indices_id": `${indicesId}`,
                    "user_id": selectedRemove[i].value
                }
                ajaxFunction(data);
            }
            return !selectedRemove.remove().appendTo('#select1');

        });

        $('#removeAdmin').click(function() {
            const selectedAdminRemove = $('#select3 option:selected');
            for (let i = 0; i < selectedAdminRemove.length; i++) {
                const data = {
                    "action": 'save-permission',
                    "indices_id": `${indicesId}`,
                    "user_id": selectedAdminRemove[i].value,
                    "can_admin": '0'
                }
                ajaxFunction(data);
            }
            return !selectedAdminRemove.remove().appendTo('#select2');

        });

        // set global drag
        let dragged;

        /* events fired on the draggable target */
        document.addEventListener("drag", function(event) {
            // nothing set
        }, false);

        document.addEventListener("dragstart", function(event) {
            // store a ref. on the dragged elem
            dragged = event.target;
            // make it half transparent
            event.target.style.opacity = .5;
        }, false);

        document.addEventListener("dragend", function(event) {
            // reset the transparency
            event.target.style.opacity = 1;
        }, false);

        /* events fired on the drop targets */
        document.addEventListener("dragover", function(event) {
            if (event.target.classList.contains("list-group") || event.target.classList.contains("list-group-item")) {
                let targetElement = event.target.classList.contains("list-group-item") ? event.target.parentElement : event.target;
                targetElement.style.background = "orange";
                targetElement.style.cursor = "pointer";
            }
            // prevent default to allow drop
            event.preventDefault();
        }, false);

        document.addEventListener("dragenter", function(event) {
            if (event.target.classList.contains("list-group") || event.target.classList.contains("list-group-item")) {
                let targetElement = event.target.classList.contains("list-group-item") ? event.target.parentElement : event.target;
                targetElement.style.background = "orange";
                targetElement.style.cursor = "pointer";
            }
        }, false);

        document.addEventListener("dragleave", function(event) {
            // reset background of potential drop target when the draggable element leaves it
            if (event.target.className == "list-group") {
                event.target.style.background = "";
            }

        }, false);

        document.addEventListener("drop", function(event) {
            // prevent default action (open as link for some elements)
            event.preventDefault();
            // move dragged elem to the selected drop target
            if (event.target.className == "list-group" || event.target.className == "list-group-item") {
                event.target.style.background = "";
                const oldParent = dragged.parentNode;
                const newParent = (event.target.className == "list-group") ? event.target : event.target.parentNode;
                const userId = dragged.getAttribute('data-value');

                if (newParent !== oldParent) {
                    if (event.target.className == "list-group") {
                        event.target.appendChild(dragged);
                    } else if (event.target.className == "list-group-item") {
                        event.target.parentNode.appendChild(dragged);
                    }
                } else {
                    console.log("no action taken");
                    return;
                }

                let canAdmin = 0;
                let action = 'save-permission';

                if (newParent.id == 'list3') {
                    canAdmin = 1;
                }

                if (newParent.id == 'list1') {
                    action = 'remove-permission';
                }

                const data = {
                    "action": action,
                    "indices_id": <?= json_encode($indexId) ?>,
                    "user_id": userId,
                    "can_admin": canAdmin
                }

                ajaxFunction(data);
            }
        }, false);
    </script>
</body>

</html>