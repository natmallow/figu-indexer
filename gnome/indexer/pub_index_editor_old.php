<?php

$SECURITY->isLoggedIn();


use gnome\classes\model\PublicationIndex as PublicationIndex;
use gnome\classes\model\Publication as Publication;
use gnome\classes\model\Indices as Indices;

// require_once(__DIR__ . '/../includes/crystal/functions.php');

$lang = lang();

$PublicationIndex = new PublicationIndex();
$Publication = new Publication();
$Indices = new Indices();


$publication_id =
    $german =
    $english =
    $english_name =
    $german_name =
    $author =
    $date =
    $raw_html =
    $publication_type_id =
    $is_ready =
    $title =
    $publication_index_id =
    $notes = '';



$index_id = filter_input(INPUT_GET, 'index_id') ? filter_input(INPUT_GET, 'index_id') : "";
$publication_id = filter_input(INPUT_GET, 'publication_id') ? filter_input(INPUT_GET, 'publication_id') : "";

if ($index_id === "" || $publication_id === "") {
    $_SESSION['actionResponse'] = "Either Publication or Index was not selected, Try again";
    header("Location: indices.php");
    exit();
}

$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';

$indexRs =  $Indices->getIndex($index_id);
extract($indexRs);

// Select Statements:
$publication = $Publication->getPublication($publication_id);
$optionalFieldsAns = $Indices->getOptionalFieldsWithAnswer($index_id, $publication_id);
$publicationIndex = (object) $PublicationIndex->getIndexPublication($index_id, $publication_id);
$keywordsMeta = $PublicationIndex->getIndexKeywordMeta($index_id);


extract($publication);

$statusLookup = $PublicationIndex->getPublicationStatusLookup();

?>
<!DOCTYPE html>
<html>


<head>
    <meta charset="utf-8">
    <?php include __DIR__ . '../../includes/header.inc.php'; ?>
    <!-- font awesome  -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous" />


    <!-- <link rel="stylesheet" href="/css/fontawesome/fontawsome.min.css" /> -->
    <!-- <link rel="stylesheet" href="/css/bootstrap/bootstrap.min.css" /> -->
    <link rel="stylesheet" href="/css/dragger.css" />

    <!-- web components -->

    <script src="../assets/js/indexer.js"></script>
    <script src="../assets/js/spinner.js"></script>
    <script src="/js/lib/jquery/jquery.min.js"></script>
    <script src="/js/lib/jquery/jquery-ui.min.js"></script>
    <script src="/js/lib/popper/popper.min.js"></script>
    <script src="/js/lib/bootstrap/bootstrap.min.js"></script>

    <style id="eNum">
        /** test enumeration change */
        .eNum.on::before {
            content: attr(id);
            color: var(--rgba-primary-0);
            font-weight: bold;
        }

        .eNum::before {
            content: '';
        }

        .highlighter {
            color: <?= $text_color;
                    ?>;
            background-color: <?= $highlight_color;
                                ?>;
        }
    </style>
    <style>
        /* keyword block */
        k-word {
            border: solid 1px red;
            background-color: yellow;
        }
    </style>

    <script type="module" src="../assets/js/component-web/keyword.js"></script>
</head>

<body class="">


    <div id="wrapper">
        <div id="main">
            <div class="inner">
                <?php include '../../includes/title.inc.php'; ?>
                <div class="main-fix">
                    <h3>Publication : <strong><?= $publication_id ?></strong></h3>
                    <h3>Index title :
                        <strong>
                            <span style="width:fit-content; 
                                    color:<?= $text_color; ?>; 
                                    background-color:<?= $highlight_color; ?>;">
                                <?= $name; ?>
                            </span>
                        </strong>
                    </h3>
                    <div style="display: flex; justify-content: space-between;">
                        <div style="vertical-align: top;">
                            <label class="switch" style="margin: 0 6px 0 3px;">
                                <input type="checkbox" id="toggle-visability">
                                <span class="slider round"></span>
                            </label>
                            <div style="display:inline-block;vertical-align: inherit; font-size:large">Enumeration<span id="toggle-visability-tag"> - off</span></div>
                        </div>
                        <div>
                            <input type="radio" id="demo-priority-low" name="activeState" value="tracks" checked=""><label style="margin: 0 6px 0 3px;" for="demo-priority-low">Select
                                Track</label>
                            <input type="radio" id="demo-priority-normal" name="activeState" value="keywords"><label class="warn" style="margin: 0 6px 0 3px;" for="demo-priority-normal">Select
                                keywords</label>
                        </div>
                        <div>
                            <button class="button small" id="edit_row_btn">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <script>
                    const radios = document.querySelectorAll('input[name="activeState"]')
                    for (const radio of radios) {
                        radio.onclick = (e) => {
                            console.log(e.target.value);
                            const mainEle = document.querySelector('main');
                            mainEle.classList.toggle("keywords");
                        }
                    }
                </script>
                <main class="activate">
                    <section>
                        <div class="row">
                            <div class="col-12" id="publication_container">
                                <?= $raw_html ?>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
        <?php include '../includes/sidebar.inc.php'; ?>
    </div>



    <div style="position:fixed; bottom: 2px; right: 5px;">
        <button class="btn btn-outline-secondary m-0" id="to_top_btn">
            <i class="fas fa-rocket"></i>
        </button>
    </div>



    <!-- dragable and editable bootsttrap modal modal -->
    <!-- <div class="modal fade" id="dragable_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
        data-backdrop="static" data-keyboard="false"> -->



    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="row m-0 w-100">
                    <div class="col-md-12 px-4 p-2 dragable_touch d-block">
                        <h3 class="m-0 d-inline">Publication Indexer</h3>
                        <button type="button" onclick="closeModal()" class="close close_btn" data-dismiss="modal" aria-label="Close" data-backdrop="static" data-keyboard="false"><i class="fa fa-times"></i></button>
                    </div>
                    <div class="col-md-12 p-0">
                        <ul class="nav nav-tabs custom_tab_on_editor" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#row_seetings_general_tab" role="tab" aria-controls="home" aria-selected="true">Main</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="keywords-tab" data-toggle="tab" href="#keywords_tab" role="tab" aria-controls="keywords" aria-selected="false">Keywords</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="questions-tab" data-toggle="tab" href="#questions_tab" role="tab" aria-controls="questions" aria-selected="false">Yes or No</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="notes-tab" data-toggle="tab" href="#notes_tab" role="tab" aria-controls="notes" aria-selected="false">Notes</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="modal-body p-3">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="row_seetings_general_tab" role="tabpanel" aria-labelledby="home-tab">
                        <div class="form-group">
                            <label for="edit_project_name">Status</label>
                            <select name="publication_status" id="publication_status">
                                <?php foreach ($statusLookup as $row) : ?>
                                    <option value="<?= $row["publication_status_lookup"] ?>" <?= $row["publication_status_lookup"] == $publicationIndex->publication_index_status ? 'selected' : '' ?>>
                                        <?= $row["publication_status_lookup"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_project_name">Summary</label>
                            <textarea name="" id="summary" class="form-control"><?= trim($publicationIndex->summary) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_project_name">Sentences</label>
                            <textarea name="" id="track" class="form-control"><?= trim($publicationIndex->tracks) ?></textarea>
                        </div>

                    </div>
                    <div class="tab-pane fade" id="keywords_tab" role="tabpanel" aria-labelledby="keywords-tab">

                        <div class="form-group">
                            <div style="display:flex; padding-right:10px;padding-bottom: 10px;">
                                <label for="keyword-add" style="padding-right: .3em;">Keywords</label>
                                <input type="text" id="addkeywords" name="addkeywords" style="margin-right: 10px;" data-publication-index-id="<?= $publicationIndex->publication_index_id ?>" />
                                <button style="height: unset; padding: 0 1.25em" onclick="runAddKeywords()">
                                    <i class='fa fa-save fa-2x' style="radius: .375em; line-height: 1.25em"></i>
                                </button>
                            </div>
                            <div id="keywordsContainer">
                                <div id="keywordChips">
                                    <?php
                                    $keyWords = is_null($publicationIndex->keywords) ? [] : $publicationIndex->keywords;

                                    foreach ($keyWords as $word) :
                                        $dataSelected = [];
                                        $metahtml = '';
                                        foreach ($word->metas as $meta) {

                                            $dataSelected[] = $meta->id;
                                            $metahtml .= "<li class='sub-meta-$meta->id'>
                                                                    <strong>$meta->value</strong>
                                                                </li>";
                                        }
                                    ?>

                                        <div class="chip" id="chip-keyword-<?= $word->id ?>">
                                            <i class="fa fa-list-alt meta-control" data-keyword-id="<?= $word->id ?>" data-selected-meta="<?= implode(',', $dataSelected) ?>"></i>
                                            <?= $word->value ?>
                                            <span class="closebtn" onclick="keywordRemove(event, <?= $publicationIndex->publication_index_id ?>, <?= $word->id ?>)">&times;</span>

                                            <?= $metahtml ?>

                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- init hides  -->
                                <div id="meta-checkboxes" class="hide-disp" data-publication-index-id="<?= $publicationIndex->publication_index_id ?>" data-keyword-id="">
                                    <?php foreach ($keywordsMeta as $meta) : ?>
                                        <div>
                                            <input type="checkbox" id="meta_<?= $meta['indices_keyword_meta_id'] ?>" data-indices-keyword-meta-id="<?= $meta['indices_keyword_meta_id'] ?>" data-indices_id="<?= $index_id ?>" onchange="metaStage(event)">
                                            <label for="meta_<?= $meta['indices_keyword_meta_id'] ?>" style="font-size: .7em;padding-right:0px;" class="close-safe">
                                                <?= $meta['meta'] ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div id="keywordTextarea">
                                    <!-- <input type="text" class="form-control" id="edit_project_name" /> -->
                                    <textarea name="" id="keywordBlock" class="form-control"><?= trim(json_encode($publicationIndex->keywords)) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="questions_tab" role="tabpanel" aria-labelledby="questions-tab">
                        <div class="row">
                            <div class="col-6">Question?</div>
                            <div class="col-3">YES</div>
                            <div class="col-3">NO</div>
                        </div>
                        <?php foreach ($optionalFieldsAns as $row) : ?>

                            <div class="row">
                                <div class="col-6" style="margin: auto;"><?= $row["optional_field"] ?></div>
                                <div class="col-3">
                                    <input type="radio" value='1' id="rad-yes-<?= $row["indices_optional_field_id"] ?>" name="yesNo-<?= $row["indices_optional_field_id"] ?>" <?= $row["optional_field_value"] == 1 ? 'checked' : '' ?>>
                                    <label for="rad-yes-<?= $row["indices_optional_field_id"] ?>">Yes</label>
                                </div>
                                <div class="col-3">
                                    <input type="radio" value='0' id="rad-no-<?= $row["indices_optional_field_id"] ?>" name="yesNo-<?= $row["indices_optional_field_id"] ?>" <?= $row["optional_field_value"] == 0 ? 'checked' : '' ?>>
                                    <label for="rad-no-<?= $row["indices_optional_field_id"] ?>">No</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="tab-pane fade" id="notes_tab" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="form-group">
                            <label for="edit_project_name">Notes to others</label>
                            <!-- <input type="text" id="row_id"> -->
                            <textarea name="notes" id="notes" class="form-control"><?= trim($publicationIndex->notes) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <div class="row w-100">
                    <div class="col-6">
                        <button type="reset" class="btn" data-dismiss="modal" onclick="closeModal()">Close</button>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="btn" id="saveIndex">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- </div> -->
    <!-- dragable and editable bootsttrap modal modal END-->

    <script>
        const toggleEnumeration = document.querySelector("#toggle-visability");
        const para = document.querySelectorAll(".eNum");

        toggleEnumeration.addEventListener("click", function(e) {
            document.querySelector("#toggle-visability-tag").innerHTML = e.target.checked ? " - On" : " - Off"
            para.forEach((element) => {
                element.classList.toggle("on")
            })
        });

        ajaxPost = (jsonData, callback = '') => {
            // add loader here
            console.log(`callback is : ${callback}`)
            $.ajax({
                type: "POST",
                url: "publication_ajax.php",
                data: jsonData,
                dataType: 'json',
                cache: false,
                success: function(resp) {
                    spinnerRemove('keywords_tab');
                    console.log('Operation complete')
                    if (callback != '') {
                        callback(resp);
                    }
                },
                error: function(res) {
                    alert('Operation has failed try again or report to admin', res)
                    spinnerRemove('keywords_tab');
                }
            });
        }

        $("#to_top_btn").click(function() {
            // to top
            window.scroll({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
        });

        const closeModal = () => {
            $(".modal-dialog").hide();
        }

        $("#edit_row_btn").click(function() {
            $(".modal-dialog").show();
            // reset modal if it isn't visible
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 20,
                    left: 20,
                });
            }
            $(".modal-dialog").draggable({
                cursor: "move",
                // appendTo: "body",
                handle: ".dragable_touch",
                // containment: "parent",
                stop: function(event, ui) {
                    if (!isInViewport(this, [0, -300, -300, 0])) {
                        $(this).css({
                            top: 20,
                            left: 20,
                        });
                    }
                }
            });
        });


        /**
         * offSets[top, right, bottom, left]
         */
        const isInViewport = function(elem, offSets = [0, 0, 0, 0]) {

            const bounding = elem.getBoundingClientRect();
            return (
                bounding.top + offSets[0] >= 0 &&
                bounding.right + offSets[1] <= (window.innerWidth || document.documentElement.clientWidth) &&
                (bounding.bottom + offSets[2]) <= (window.innerHeight || document.documentElement.clientHeight) &&
                bounding.left + offSets[3] >= 0
            );
        };


        const bubbler = (ele) => {
            let eleArr = ele.id.match(/track_\d+_\d+\-(?:en|de)/gm);
            let topEle = ele.id.search(/publication_container/gm);
            if (topEle != -1) {
                return false;
            } else if (!eleArr) {
                return bubbler(ele.parentNode);
            }
            return eleArr;
        }

        const tracksHighlighter = () => {
            // get the tracks in from the input box
            const tracks = $('#track').val();
            if (tracks == '') return;
            const tracksArr = tracks.split(',');
            tracksArr.forEach((item) => {
                try {
                    let engTxt = document.getElementById(`${item}en`);
                    let gerTxt = document.getElementById(`${item}de`);
                    engTxt.classList.add('highlighter');
                    gerTxt.classList.add('highlighter');
                } catch (error) {
                    console.log(`${item} does not seem to be present `, error)
                }

            })
        }

        tracksHighlighter();

        const trackSelectHandler = e => {

            let tempId = bubbler(e.target);
            if (!tempId) return;
            let targets = tempId[0].slice(0, tempId[0].length - 2);
            const arr = [];
            let engTxt = document.getElementById(`${targets}en`);
            let gerTxt = document.getElementById(`${targets}de`);

            if (engTxt.classList.contains('highlighter')) {
                engTxt.classList.remove('highlighter');
                gerTxt.classList.remove('highlighter');
                trackRemove(targets);
            } else {
                engTxt.classList.add('highlighter');
                gerTxt.classList.add('highlighter');
                trackAdd(targets);
            }
        }

        const trackRemove = (track) => {
            let sentence = $('#track').val();
            let sentenceNew = sentence.replace(track, '');
            const rtnStr = sentenceNew.replace(/^[,]/, '').replace(/[,]+/g, ',')
            // sentenceNew = sentenceNew.match(/^[.,:!?]/) == true ? sentence :  ;
            $('#track').val(rtnStr);
        }

        const trackAdd = (track) => {
            let sentence = $('#track').val();
            if (sentence.trim().length <= 1) {
                $('#track').val(track)
            } else {
                $('#track').val((sentence + ',' + track).replace(/[,]+/g, ','));
            }
        }

        const keywordSelectHandler = (e) => {
            // check if exists in current keywords array
            // console.log(e)
            // e.target
            const selection = e.target.value.substring(
                e.target.selectionStart,
                e.target.selectionEnd
            )
            alert(selection)
        }

        // publication container
        const pubContainer = document.querySelector('#publication_container');

        // select section
        pubContainer.addEventListener('click', function(e) {

            trackSelectHandler(e);

            // keywordSelectHandler(e);
        });

        function getAbsPosition(element) {
            let parentOffset = $('#dragable_modal').offset();
            let position = $(element).position();
            let height = $(element).height();
            let xPos = position.left // - parentOffset.left;
            let yPos = position.top + height // - parentOffset.top;
            return {
                x: xPos,
                y: yPos
            };
        }

        attachEvent = (element, eventName, callback, event) => {
            if (element && eventName && element.getAttribute("listener") !== "true") {
                element.setAttribute("listener", "true");
                element.addEventListener(eventName, (event) => {
                    callback(event);
                });
            }
        };

        openMetaUI = (e) => {
            // console.log(e)
            const selectedMetas = $(e.target).attr('data-selected-meta').split(',');
            const metaContainer = document.getElementById('meta-checkboxes');
            const metaCheckboxes = metaContainer.getElementsByTagName("input");
            // get scroll offset
            const scrollPosMetaContainer = document.querySelector('#dragable_modal .modal-body');

            // move to correct position
            let pos = getAbsPosition(e.target);
            metaContainer.style.top = (pos.y + scrollPosMetaContainer.scrollTop) + 'px';
            metaContainer.style.left = pos.x + 'px';
            metaContainer.dataset.keywordId = $(e.target).attr('data-keyword-id');

            // reset all check boxes
            for (let i = 0; i < metaCheckboxes.length; i++) {
                metaCheckboxes[i].checked = false;
            }

            // make visible
            if (metaContainer.classList.contains('hide-disp')) {
                metaContainer.classList.remove('hide-disp');
            }

            // set selected check boxes
            for (let i = 0; i < selectedMetas.length; i++) {
                const ele = $('#meta_' + selectedMetas[i]);
                ele.prop("checked", true);
            }

            // stop from being highlighted
            e.stopPropagation();
        }

        const metaSelect = document.querySelectorAll('.meta-control');

        metaSelect.forEach(mBox => {
            attachEvent(mBox, "click", openMetaUI, event)
            // mBox.addEventListener('click', function(e) {
            //    return openMetaUI(e)
            // }, true)
        })

        document.addEventListener('click', function(e) {

            function cbBubbler(ele, cnt = 0) {

                let loops = cnt + 1;
                const topEle = ele.id?.search(/meta-checkboxes/gm);

                if (loops > 3)
                    return true;

                if (topEle == -1) {
                    return cbBubbler(ele.parentNode, loops);
                } else if (topEle != -1) {
                    return false;
                }
            }

            if (cbBubbler(e.target)) {
                let metaContainer = document.getElementById("meta-checkboxes");
                if (!metaContainer.classList.contains('hide-disp')) {
                    metaContainer.classList.add('hide-disp');
                }
            }
        })

        // right click add text to keywords
        pubContainer.addEventListener('contextmenu', function(event) {
            // return
            event.preventDefault();
            try {
                switch (event.which) {
                    case 3:
                        let s = window.getSelection();

                        const publication_index_id = '<?= $publicationIndex->publication_index_id ?>';

                        const range = s.getRangeAt(0);
                        const node = s.anchorNode;
                        const keyWords = $('#keywordBlock').val();

                        while (range.toString().indexOf(' ') != 0) {
                            range.setStart(node, (range.startOffset - 1));
                        }

                        range.setStart(node, range.startOffset + 1);

                        do {
                            range.setEnd(node, range.endOffset + 1);
                        } while (range.toString().indexOf(' ') == -1);

                        // remove extra space
                        range.setEnd(node, range.endOffset - 1);

                        const newWord = range.toString().trim().replace(/,+/g, '');
                        const hasWord = (str, word) => str.split(/,/).includes(word);
                        // this should be a hidden text field
                        const jsn = (keyWords != '') ? $.parseJSON(keyWords) : [];
                        console.log(publication_index_id, newWord);

                        if (!hasWord(keyWords, newWord) && !jsn.some(el => el.value == newWord)) {
                            const action = 'addKeyword';
                            const jsonData = {
                                action,
                                publication_index_id,
                                newWord
                            };
                            ajaxPost(jsonData, keywordsResponseHandler)
                        }

                        break;
                }
            } catch (error) {
                console.log('There was an error with right click: ', error);
            }
        });

        $('#addkeywords').on('keydown', function(e) {
            e.stopPropagation();
            if (e.keyCode == 13) {
                runAddKeywords();
            }
        });

        keywordRemoveJson = (keyword_id) => {
            const keyValObj = document.querySelector('#keywordBlock').value;
            let keyVals = JSON.parse(keyValObj);

            for (let i = 0; i < keyVals.length; ++i) {
                if (keyVals[i].id == keyword_id) {
                    keyVals.splice(i, 1);
                    break;
                }
            }

            document.querySelector('#keywordBlock').value = JSON.stringify(keyVals);
        }

        keywordRemove = (event, publication_index_id, keyword_id) => {
            const action = 'removeKeyword';
            const jsonData = {
                action,
                publication_index_id,
                keyword_id
            };
            ajaxPost(jsonData);
            keywordRemoveJson(keyword_id);
            const keyword = document.querySelector(`#chip-keyword-${keyword_id}`)
            keyword.remove();
        }

        spinnerRemove = (location) => {

            const elem = document.querySelector(`#${location}`).querySelector(`#spinnerSpan`);
            elem.parentNode.removeChild(elem);
        }

        spinnerAdd = (location) => {

            const node = document.createElement('span');
            const shadow = node.attachShadow({
                mode: 'open'
            });
            node.setAttribute('id', 'spinnerSpan')
            document.querySelector(`#${location}`).appendChild(node);

            shadow.innerHTML = ` 
                            <link rel="stylesheet" type="text/css" href="/css/bootstrap/bootstrap.min.css">
                            <style>
                                .spinner-container {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    z-index: 1000;
                                    background-color: rgb(60 200 200 / 15%);
                                    height: 100%;
                                    width: 100%;
                                    display: table-cell;
                                    vertical-align: middle;                                
                                }
                                .spinner-ctr {
                                    position: absolute;
                                    margin: auto;
                                    top: 0;
                                    bottom: 0;
                                    left: 0;
                                    right: 0;
                                    font-size: large;
                                    color: blue;
                                }
                            </style>
                            <div class="text-center spinner-container" id="spinner">
                                <div class="spinner-border spinner-ctr" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>`;
            // sheet.adoptedStyleSheets= [...document.adoptedStyleSheets] 

        }

        // here is the stage
        runAddKeywords = () => {
            if (document.querySelector(`#addkeywords`).value.trim() == '') {
                return;
            }
            spinnerAdd('keywords_tab');
            const action = 'addKeyword';
            const publication_index_id = $('#addkeywords').attr('data-publication-index-id');
            const newWord = $('#addkeywords').val();
            const jsonData = {
                action,
                publication_index_id,
                newWord
            };

            if (jsonData.newWord.trim() == '') {
                return;
            }
            document.querySelector(`#addkeywords`).value = '';
            ajaxPost(jsonData, keywordsResponseHandler)
        }

        keywordsResponseHandler = (response) => {
            // keywordsResponseHandler
            const keyWords = $('#keywordBlock').val();
            const jsn = (keyWords != '') ? $.parseJSON(keyWords) : [];

            jsn.push(...response.keywords);

            $('#keywordBlock').val(JSON.stringify(jsn));

            response.keywords.forEach((item) => {
                $("#keywordsContainer").append(`<div class="chip" id="chip-keyword-${item.id}">
                                                <i class='fa fa-list-alt meta-control' 
                                                data-keyword-id="${item.id}" 
                                                data-selected-meta="" 
                                                ></i>
                                                ${item.value}
                                                <span class="closebtn"
                                                    onclick="keywordRemove(event, ${response.publication_index_id}, ${item.id})">&times;</span>
                                            </div>`)
            })

            const metaSelect = document.querySelectorAll('.meta-control');

            metaSelect.forEach(mBox => {
                attachEvent(mBox, "click", openMetaUI, event)
                // mBox.addEventListener('click', function(e) {
                //    return openMetaUI(e)
                // }, true)
            })
        }

        metaStage = (event) => {
            const publication_index_id = $('#meta-checkboxes').attr('data-publication-index-id');
            const publication_keyword_id = $('#meta-checkboxes').attr('data-keyword-id');
            const indices_keyword_meta_id = event.currentTarget.dataset.indicesKeywordMetaId;
            const action = (event.currentTarget.checked) ? 'addKeywordMeta' : 'removeKeywordMeta';
            const jsonData = {
                action,
                publication_index_id,
                publication_keyword_id,
                indices_keyword_meta_id
            };
            ajaxPost(jsonData, metaResponseHandler);
        }

        metaResponseHandler = (response) => {
            const action = response.action;
            const keyword_id = response.keyword_id;
            const meta_id = response.meta_id.toString();

            const chip = document.querySelector(`#chip-keyword-${keyword_id} .meta-control`);
            const dataSelectedMeta = chip.dataset.selectedMeta.split(',');


            if (action == 'removeMeta') {
                chip.dataset['selectedMeta'] = dataSelectedMeta.filter((i) => i != meta_id).toString();
                const subMeta = document.querySelector(`#chip-keyword-${keyword_id} .sub-meta-${meta_id}`);
                subMeta.remove();

            } else {
                dataSelectedMeta.push(`${meta_id}`);
                chip.dataset['selectedMeta'] = dataSelectedMeta.toString();
                const metaCB = document.querySelector(`#meta_${meta_id}`);
                const MetaText = metaCB.nextElementSibling.innerText;
                const subMeta = document.querySelector(`#chip-keyword-${keyword_id}`);
                const el = document.createElement("li");
                el.classList.add(`sub-meta-${meta_id}`)
                el.innerHTML = `<strong>${MetaText}</strong>`
                subMeta.appendChild(el)
            }

        }


        const saveBtn = document.querySelector('#saveIndex');

        saveBtn.addEventListener('click', function(e) {
            const index_id = '<?= $index_id ?>';
            const publication_id = '<?= $publication_id ?>';
            const tracks = $('#track').val();
            const summary = $('#summary').val();
            const keyWords = $('#keywordBlock').val();
            const notes = $('#notes').val();
            const publicationStatus = $('#publication_status').val();
            const optionalFieldsAns = $('#questions_tab :radio:checked');
            let optionalFieldsArr = $.map(optionalFieldsAns, function(el) {
                return {
                    'publication_id': publication_id,
                    'indices_optional_field_id': el.id.slice(el.id.lastIndexOf("-") + 1, el.id.length),
                    'optional_field_value': el.value
                }
            });
            optionalFieldsArr = JSON.stringify(optionalFieldsArr),
                // console.log(optionalFieldsArr)
                jsonData = {
                    index_id,
                    publication_id,
                    tracks,
                    keyWords,
                    publicationStatus,
                    optionalFieldsArr,
                    notes,
                    summary
                };
            // console.log(jsonData)

            $.ajax({
                type: "POST",
                url: "publication_ajax.php", // "/gnome/upload_html.php",
                data: jsonData,
                dataType: 'json',
                cache: false,
                success: function(html) {
                    alert('save complete')
                },
                error: function(res) {
                    alert('no', res)
                }
            });

        });
    </script>

    <?php include __DIR__ . '/../includes/script.import.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>

</body>

</html>