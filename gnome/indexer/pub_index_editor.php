<?php

$SECURITY->isLoggedIn();


use gnome\classes\model\PublicationIndex as PublicationIndex;
use gnome\classes\model\Publication as Publication;
use gnome\classes\model\Indices as Indices;
use gnome\classes\service\IndicesKeywordService;


// require_once(__DIR__ . '/../includes/crystal/functions.php');

$lang = lang();

$PublicationIndex = new PublicationIndex();
$Publication = new Publication();
$Indices = new Indices();
$IndicesKeywordService = new IndicesKeywordService();

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
    $notes =
    $publicationType =
    $publicationName =
    $publicationAbbr = '';


$indices_id = filter_input(INPUT_GET, 'index_id') ? filter_input(INPUT_GET, 'index_id') : "";
$publication_id = filter_input(INPUT_GET, 'publication_id') ? filter_input(INPUT_GET, 'publication_id') : "";
$pub_type = filter_input(INPUT_GET, 'pub_type') ? filter_input(INPUT_GET, 'pub_type') : null;

if ($indices_id === "" || $publication_id === "") {
    $_SESSION['actionResponse'] = "Either Publication or Index was not selected, Try again";
    header("Location: indices.php");
    exit();
}

$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';
$description_html = '';

$indexHtmlRs =  $Indices->getIndex($indices_id);
extract($indexHtmlRs);

// Select Statements:
$publication = $Publication->getPublication($publication_id);
$optionalFieldsAns = $Indices->getOptionalFieldsWithAnswer($indices_id, $publication_id);
$publicationIndex = (object) $PublicationIndex->getIndexPublication($indices_id, $publication_id);
$keywordsMeta = $PublicationIndex->getIndexKeywordMeta($indices_id);
$listOfIndicies = $Indices->fetchIndices();

extract($publication);

$statusLookup = $PublicationIndex->getPublicationStatusLookup();

if (!is_null($pub_type)) {
    $publicationType = $Publication->getPublicationType($pub_type);
    $publicationName = $publicationType['name'];
    $publicationAbbr = $publicationType['abbreviation'];
}

?>
<!DOCTYPE html>
<html>


<head>
    <meta charset="utf-8">
    <?php include __DIR__ . '../../includes/head.inc.php'; ?>

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
            color: <?= $text_color ?>;
            background-color: <?= $highlight_color ?>;
        }
    </style>
    <style title="metaCss">
        [class*=sub-meta-] {
            display: 'none'
        }

        .chip:not(:has(li)) {
            display: inline-block;
        }
    </style>
    <style>
        /* Used for the shift select tracks */
        .text-select-active * {
            user-select: none;
            cursor: pointer;
        }

        /* Used for the shift select tracks */
        .keyword-select-active * {
            /* user-select: none; */
            cursor: crosshair;
        }
    </style>

    <!-- keyword class -->
    <script type="module" src="../assets/js/component-web/keyword.js"></script>
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>Publication : <strong><?= $publication_id ?></strong></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/indices.php">Indices (<?= $name ?>)</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/gnome/indexer/indexlinks.php?index_id=<?= $indices_id; ?>&lang=<?= lang(); ?>&pub_type=<?= $pub_type; ?>"><?= $publicationName; ?> (<?= $publicationAbbr; ?>)</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Editing (<?= $publication_id; ?>)
                    </li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="card sticky-sub">
                <h5 class="nat card-header">Featured</h5>
                <div class="card-body mt-2">
                    <div class="row">
                        <div class="col-4">
                            <h5 class="card-title">Index title : <span class="badge" style="width:fit-content; 
                                    color:<?= $text_color; ?>; 
                                    background-color:<?= $highlight_color; ?>;"><?= $name; ?></span>
                                <div class="btn-group">
                                    <button id="btnChangIndexer" class="btn btn-primarybtn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">

                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="btnChangIndexer">
                                        <?php foreach ($listOfIndicies as $key => $value) : ?>
                                            <li><a class="dropdown-item" value="<?= $value['indices_id'] ?>" href="?publication_id=<?= $publication_id ?>&index_id=<?= $value['indices_id'] ?>"><?= $value['name'] ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </h5>

                            <strong>Summary:</strong>
                            <?= $description_html; ?>

                        </div>

                        <div class="col-6">
                            <div class="alert alert-primary" role="alert">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="toggle-visability">
                                    <label class="form-check-label" for="toggle-visability">Enumeration<span id="toggle-visability-tag"> - off</span></label>
                                </div>
                            </div>
                            <div class="alert alert-primary" role="alert">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="tracks-show" value="showSelectedTracks" onchange="tracksHighlighter(this.checked)" checked="checked">
                                    <label class="form-check-label" for="tracks-show">Show Selected Tracks</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="keywords-show" value="showSelectedKeywords" onchange="Highlighter.toggleVisibility(this.checked)" checked="checked">
                                    <label class="form-check-label" for="keywords-show">Show Selected Keywords</label>
                                </div>
                            </div>
                            <div class="alert alert-primary" role="alert">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="select-keywords-radio" name="activeState" value="selectOff" checked="checked">
                                    <label class="form-check-label" for="select-keywords-radio">Select Off</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="select-tracks-radio" name="activeState" value="selectTracks">
                                    <label class="form-check-label" for="select-tracks-radio">Select Track</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="select-keywords-radio" name="activeState" value="selectKeywords">
                                    <label class="form-check-label" for="select-keywords-radio">Select keywords</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-2 text-end">
                            <button class="btn btn-primary" id="edit_row_btn">
                                <i class="ri-edit-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body" style="padding-top:20px;">
                            <div id="publication_container" class="">
                                <?= $raw_html ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>



    <!-- dragable and editable bootsttrap modal modal -->
    <!-- <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2"
        data-backdrop="static" data-keyboard="false"> -->



    <div id="dragable_modal" class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header w-100">
                <div class="row m-0 w-100">
                    <div class="col-md-12 px-4 p-2 dragable_touch d-flex justify-content-between align-items-center">
                        <h3 class="m-0 d-inline">Publication Indexer</h3>
                        <button type="button" onclick="closeModal()" class="btn-close btn-close-white  d-inline" data-dismiss="modal" aria-label="Close" data-backdrop="static" data-keyboard="false">
                            <!-- <i class="ri-close-circle-line"></i> -->
                        </button>
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
                    <!-- Main TRACKS PANNEL -->
                    <div class="tab-pane fade show active" id="row_seetings_general_tab" role="tabpanel" aria-labelledby="home-tab">

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Select</label>
                            <div class="col-sm-10">
                                <select class="form-select" aria-label="Index Status" deluminate_imagetype="unknown" name="publication_status" id="publication_status">
                                    <?php foreach ($statusLookup as $row) : ?>
                                        <option value="<?= $row["publication_status_lookup"] ?>" <?= $row["publication_status_lookup"] == $publicationIndex->publication_index_status ? 'selected' : '' ?>>
                                            <?= $row["publication_status_lookup"] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_project_name" class="form-label">Summary</label>
                            <textarea name="" id="summary" class="form-control"><?= trim($publicationIndex->summary) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_project_name" class="form-label">Sentences</label>
                            <textarea name="" id="track" class="form-control" disabled="disabled"><?= trim($publicationIndex->tracks) ?></textarea>
                        </div>

                    </div>
                    <!-- KEYWORDS PANNEL -->
                    <div class="tab-pane fade keywords-tab" id="keywords_tab" role="tabpanel" aria-labelledby="keywords-tab">

                        <div class="row mb-3 sticky-sub-nav">
                            <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="add-keyword-tooltip"><i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" title="Add a single word or multiple words separated by a ( , )"></i></span>
                                    <input type="text" class="form-control" id="addkeywords" name="addkeywords" placeholder="Keywords" data-publication-index-id="<?= $publicationIndex->publication_index_id ?>" aria-describedby="add-keyword-tooltip">
                                    <button class="btn btn-primary" onclick="Highlighter.runAddKeyword()" data-bs-toggle="tooltip" title="Add keywords">
                                        + <i class='bx bxs-save'></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="filter-tooltip"><i class="bi bi-info-circle-fill" data-bs-toggle="tooltip" title="To filters keywords just start typing"></i></span>
                                    <input type="text" class="form-control" onkeyup="filterKeywordsHandler(this);" value="" placeholder="Filter" aria-label="Filter Keywords" aria-describedby="filter-tooltip">
                                </div>
                            </div>
                            <div class="col-md-1">
                                <span data-bs-toggle="dropdown">
                                    <button class="btn btn-success" aria-expanded="false" data-bs-toggle="tooltip" title="keyword Options">
                                        <i class='bi bi-gear'></i>
                                    </button>
                                </span>
                                <ul class="dropdown-menu">
                                    <li class="form-check">
                                        <input type="checkbox" class="form-check-input" id="showMeta" checked onchange='showMetaHandler(this);'>
                                        <label class="form-check-label" for="showMeta">
                                            Show Meta
                                        </label>
                                    </li>
                                    <li class="form-check">
                                        <input type="checkbox" class="form-check-input" id="filterMetaOnly" checked onchange='filterMetaOnlyHandler(this);'>
                                        <label class="form-check-label" for="filterMetaOnly">
                                            Filter Meta Only
                                        </label>
                                    </li>
                                    <li class="form-check">
                                        <input type="checkbox" class="form-check-input" id="referenceKeywordOnly" checked onchange='filterReferenceKeywordHandler(this);'>
                                        <label class="form-check-label" for="referenceKeywordOnly">
                                            Show Ref Keywords Only
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>




                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Master Keyword list
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">


                                        <div id="masterKeywordsContainer">
                                            <div id="masterKeywordList">
                                                <?php
                                                    $mKeyWords = $IndicesKeywordService->getIndicesMasterKeywords($indices_id);
                                                    $mKeyWords = is_null($mKeyWords) ? [] : $mKeyWords;

                                                    $keywordsArray = [];
                                                    foreach ($mKeyWords as $word) {
                                                        $keywordsArray[] = $word['value'];
                                                    }
                                                    sort($keywordsArray);
                                                    echo implode(', ', $keywordsArray);
                                                 ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <fieldset class="publication-keyword-container">
                            <legend>Keyword list for <?= $publication_id ?></legend>
                            <div id="keywordsContainer">
                                <div id="keywordChips">
                                    <!----    keywords as chips    --->
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

                                        <div class="chip --nf" id="chip-keyword-<?= $word->id ?>" data-chip-val="<?= $word->value ?>">
                                            <i class="bi bi-menu-button-wide-fill meta-control" data-keyword-id="<?= $word->id ?>" data-selected-meta="<?= implode(',', $dataSelected) ?>"></i>
                                            <span class="word-jump"><?= $word->value ?></span>
                                            <?php if (!$word->locked) : ?>
                                                <span class="closebtn" onclick="Highlighter.runRemoveKeyword(<?= $publicationIndex->publication_index_id ?>, <?= $word->id ?>)">&times;</span>
                                            <?php else : ?>
                                                <span class="lock-btn" data-bs-toggle="tooltip" title="Cant be deleted here. Part of master keyword list."><i class="ri-lock-2-fill"></i></span>
                                            <?php endif; ?>
                                            <?= $metahtml ?>
                                        </div>
                                    <?php endforeach; ?>
                                    <!----    keywords as chips    --->
                                </div>

                                <!-- init hides  -->

                                <div id="meta-checkboxes" class="hide-disp" data-publication-index-id="<?= $publicationIndex->publication_index_id ?>" data-keyword-id="">
                                    <?php foreach ($keywordsMeta as $meta) : ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="meta_<?= $meta['indices_keyword_meta_id'] ?>" data-indices-keyword-meta-id="<?= $meta['indices_keyword_meta_id'] ?>" data-indices_id="<?= $indices_id ?>" onchange="metaStage(event)">
                                            <label class="form-check-label" for="meta_<?= $meta['indices_keyword_meta_id'] ?>">
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
                        </fieldset>
                    </div>
                    <!-- QUESTIONS PANNEL -->
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
                    <!-- NOTES PANNEL -->
                    <div class="tab-pane fade" id="notes_tab" role="tabpanel" aria-labelledby="notes-tab">
                        <div class="form-group">
                            <label for="edit_project_name">Notes to others</label>
                            <!-- <input type="text" id="row_id"> -->
                            <textarea name="notes" id="notes" class="form-control"><?= trim($publicationIndex->notes) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="reset" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-primary" id="saveIndex">Save changes</button>
            </div>
        </div>
    </div>

    <!-- dragable and editable bootsttrap modal modal END-->
    <script>
        // the main html container for the publication
        const publicationContainer = document.querySelector('#publication_container');
        const keywordContainer = document.querySelector('#keywordsContainer');
        const publicationIndexId = '<?= $publicationIndex->publication_index_id ?>';
        const publicationId = '<?= $publication_id ?>';

        // reference done once on loading
        const ALLTRACKIDS = [...document.querySelectorAll("track-span")].map(ele => ele.id);

        // these are for the track click handler when the select track is 
        let lastClickedTrack = "";

        let isShiftKeyDown = false;
        let isCtrlOrCmdKeyDown = false; // Can represent Control on Windows or Command on macOS



        window.addEventListener("keydown", (e) => {
            isShiftKeyDown = e.shiftKey;
            isCtrlOrCmdKeyDown = e.ctrlKey || e.metaKey; // Check for Control or Command key
        });

        window.addEventListener("keyup", (e) => {
            if (e.key === "Shift") {
                isShiftKeyDown = false;
            }
            if (e.key === "Control" || e.key === "Meta") { // Check for Control on Windows or Command on macOS
                isCtrlOrCmdKeyDown = false;
            }
        });


        function escapeRegExp(str) {
            return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }


        const ajaxPost = (jsonData, callback = '') => {
            // add loader here
            // console.log(`callback is : ${callback}`)
            $.ajax({
                type: "POST",
                url: "publication_ajax.php",
                data: jsonData,
                dataType: 'json',
                cache: false,
                success: function(resp) {
                    spinnerRemove('keywords_tab');
                    console.log('Post operation complete')
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

        class KeywordHighlighter {

            static KeywordHighlighter = self;

            currKeywordId = '';
            currKeywordIdPointer = 0;
            currKeywordIdRef = [];

            constructor(node, ajaxP) {
                this.node = node;
                this.ajax = ajaxP;
                this.wrapperTag = 'k-word';
                this.attrWord = 'data-word';
                this.attrWordId = 'data-keyword-id';

                this._addKeyword = this._addKeyword.bind(this);
                this._removeKeyword = this._removeKeyword.bind(this);
                this.toggleVisibility = this.toggleVisibility.bind(this);
                this.selectKeywordHandler = this.selectKeywordHandler.bind(this);
                this.runRemoveKeyword = this.runRemoveKeyword.bind(this);
                this.highlightWords = this.highlightWords.bind(this);
                this.keywordAddResponseHandler = this.keywordAddResponseHandler.bind(this);
                this.keywordRemoveResponseHandler = this.keywordRemoveResponseHandler.bind(this);
                this.keywordRemoveResponseHandler = this.keywordRemoveResponseHandler.bind(this);
            }

            initKeywords() {
                // we need to highlight those keyword from the master keyword list


                // selects the keywords in the main document 
                const keyWords = JSON.parse(document.querySelector('#keywordBlock').value);

                // orders the array by value length
                const orderedKeyValues = keyWords.sort((a, b) => b.value.length - a.value.length);



                orderedKeyValues.forEach(word => {
                    this.highlightWords([word]);
                });
            }

            keywordFocus(keywordId = null, offset = 320) {
                // console.log(keywordId);
                if (keywordId === null) return;

                const scrollInView = () => {

                    // console.log(Highlighter.currKeywordId, 'looking for me') 
                    const wordRefs = publicationContainer.querySelectorAll(`[data-keyword-id="${Highlighter.currKeywordId}"]`);
                    // console.log(`wordRefs length: ${wordRefs.length}`); // Check how many elements were found
                    if (wordRefs.length > 0) {
                        const pointer = this.currKeywordIdPointer % wordRefs.length;
                        // console.log(`pointer: ${pointer}`); // Check the calculated pointer value
                        const targetElement = wordRefs[pointer];
                        // console.log(targetElement); // Verify the target element
                        if (targetElement) { // Check if targetElement is not undefined
                            targetElement.classList.add('--current-hl');
                            const elementPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                            const offsetPosition = elementPosition - offset;
                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                        } else {
                            console.error('Target element is undefined');
                        }
                    } else {
                        console.error('No elements found with the specified data-keyword-id');
                    }
                }

                if (this.currKeywordId != keywordId) {
                    this.currKeywordId = keywordId;
                    this.currKeywordIdPointer = 0;

                } else {
                    this.currKeywordIdPointer++;
                }

                scrollInView();

            }

            toggleVisibility(bool) {
                document.querySelectorAll('k-word').forEach((kWordElement) => {
                    kWordElement.toggle(); // Call the toggle method on each instance
                });
            }

            highlightWords(words) {

                // console.log(words)

                // Get all text nodes in the node
                // seems wasteful but a new object must be created for each word search this is because the reference breaks
                // this causes alot of momentary over head
                this.textNodes = this.getTextNodes(this.node);

                // Loop through each text node
                this.textNodes.forEach(textNode => {
                    // Loop through each word
                    words.forEach(word => {
                        // Create a regular expression for the word
                        const regex = new RegExp(`\\b${escapeRegExp(word.value)}\\b`, 'gi');

                        // Find all matches of the word in the text node
                        let match;
                        while ((match = regex.exec(textNode.nodeValue)) !== null) {
                            console.log(match[0])
                            // prevents nesting of wrapperTag (k-word) element
                            if (textNode.parentNode.tagName.toLowerCase() === this.wrapperTag) continue;

                            document.getElementById(`chip-keyword-${word.id}`).classList.remove("--nf");

                            const wordNode = document.createElement(this.wrapperTag);
                            wordNode.setAttribute(this.attrWord, word.value.toLowerCase());
                            wordNode.setAttribute(this.attrWordId, word.id);
                            wordNode.textContent = match[0];

                            // Replace the matched text with the wrapped element
                            const startIndex = match.index;
                            const endIndex = match.index + match[0].length;
                            const range = document.createRange();
                            range.setStart(textNode, startIndex);
                            range.setEnd(textNode, endIndex);
                            range.deleteContents();
                            range.insertNode(wordNode);

                            // Move the start index to after the wrapped element
                            regex.lastIndex = startIndex + wordNode.textContent.length;
                        }
                    });
                });
            }

            // attempts to grab the word that was clicked then calls run add the keyword  
            _addKeyword(event) {

                // use event.target instead of e.target for consistency with event parameter name
                const s = window.getSelection();
                const range = s.getRangeAt(0);
                const node = s.anchorNode;
                const keyWords = document.querySelector('#keywordBlock').value;

                // const nodemain = document.querySelector('#publication_container');

                const clickedWord = event.target;
                const wrapperTag = 'k-word';

                // use early return instead of if statement with empty body
                if (clickedWord.tagName.toLowerCase() === wrapperTag) return;

                // check to see if text is pure
                const selectedHTML = range.cloneContents();
                const serializer = new XMLSerializer();
                const string = serializer.serializeToString(selectedHTML);
                const hasHtml = /<[^>]*>/g.test(string);

                if (hasHtml) {
                    alert("Illegal selection \nSelections Cannot Overlap Elements.");
                    s.removeAllRanges();
                    return;
                }

                // use a for loop instead of two while loops
                for (let startOffset = range.startOffset; startOffset !== 0; startOffset--) {
                    try {
                        range.setStart(node, startOffset - 1);
                    } catch (e) {
                        try {
                            range.setStart(node, startOffset);
                        } catch (error) {
                            console.log('invalid range - setStart')
                            return;
                        }
                    }

                    if (range.toString().search(/\s/) === 0) {
                        range.setStart(node, startOffset);
                        break;
                    }
                }

                for (let endOffset = range.endOffset; endOffset < node.length; endOffset++) {
                    try {
                        range.setEnd(node, endOffset + 1);
                    } catch (e) {
                        try {
                            range.setEnd(node, endOffset);
                        } catch (error) {
                            console.log('invalid range - setEnd')
                            return;
                        }
                    }

                    if (range.toString().search(/\s/) !== -1) {
                        range.setEnd(node, endOffset);
                        break;
                    }
                }

                const newWord = range.toString().trim().replace(/[,.;?!()]/g, '');

                if (newWord == '') return;
                s.removeAllRanges();

                // ajax add new word
                this.runAddKeyword(newWord)
            }

            // get the information from the clicked k-word element 
            _removeKeyword(event) {
                // const word = event.target.dataset.word;
                const wordId = event.target.dataset.keywordId;
                this.runRemoveKeyword(publicationIndexId, wordId)
            }


            runAddKeyword(newWord = null) {
                if (document.querySelector(`#addkeywords`).value.trim() == '' && newWord === null) {
                    return;
                }
                spinnerAdd('keywords_tab');
                const action = 'addKeyword';
                const publication_index_id = publicationIndexId;
                // what action to take
                const newKeyWord = (newWord === null) ? $('#addkeywords').val() : newWord;
                const jsonData = {
                    action,
                    publication_index_id,
                    newKeyWord
                };

                if (jsonData.newKeyWord.trim() == '') {
                    return;
                }

                // clears the input box
                document.querySelector(`#addkeywords`).value = '';
                this.ajax(jsonData, this.keywordAddResponseHandler)
            }


            /**
             * takes a json response loops through it and adds the response to the meta-keyword container
             * @response json
             */
            keywordAddResponseHandler(response) {

                // Assuming attachEvent is defined elsewhere and correctly attaches events
                const keywordBlock = document.getElementById('keywordBlock');
                let keywords = keywordBlock.value ? JSON.parse(keywordBlock.value) : [];

                keywords.push(...response.keywords);
                keywordBlock.value = JSON.stringify(keywords);

                const keywordsContainer = document.getElementById("keywordChips");
                let htmlToAdd = "";

                // Json to HTML mapping
                response.keywords.forEach((item) => {
                    htmlToAdd += `<div class="chip new-meta" id="chip-keyword-${item.id}" data-chip-val="${item.value}">
                        <i class="bi bi-menu-button-wide-fill meta-control"
                           data-keyword-id="${item.id}" 
                           data-selected-meta=""></i>
                           <span class="word-jump">${item.value}</span>
                        <span class="closebtn" data-publication-index-id="${response.publication_index_id}" data-keyword-id="${item.id}">&times;</span>
                      </div>`;
                });

                // Convert the string to HTML nodes and prepend them all at once
                const range = document.createRange();
                const documentFragment = range.createContextualFragment(htmlToAdd);
                keywordsContainer.prepend(documentFragment);

                // Attach click events to close buttons and meta-controls
                document.querySelectorAll("#keywordsContainer .new-meta").forEach(keywordChip => {
                    // Remove the 'new-meta' class to mark it as initialized
                    keywordChip.classList.remove('new-meta');

                    // attach meta menu listner 
                    const metaControl = keywordChip.querySelector('.meta-control');
                    if (metaControl) {
                        attachEvent(metaControl, "click", openMetaUI);
                    }

                    // Directly attach an event listener to the close button
                    const closeButton = keywordChip.querySelector('.closebtn');
                    if (closeButton) {
                        closeButton.addEventListener('click', function(event) {
                            // TODO ref to inti object should be singleton 
                            Highlighter.runRemoveKeyword(this.getAttribute('data-publication-index-id'), this.getAttribute('data-keyword-id'));
                        });
                    }
                });

                // order value from the response rs ensures correct highlighting 
                const orderedKeyValues = response.keywords.sort((a, b) => b.value.length - a.value.length);

                // highlight keywords on page
                orderedKeyValues.forEach(word => {
                    this.highlightWords([word]);
                });

            }

            selectKeywordHandler(event) {

                // check if tracks are visible
                if (!showSelectedKeywords()) {
                    document.getElementById('keywords-show').checked = true;
                    this.toggleVisibility(true)
                    return;
                }

                if (event.target.tagName.toLowerCase() == this.wrapperTag) {
                    this._removeKeyword(event);
                } else {
                    this._addKeyword(event);
                }
            }

            // returns a list of all text nodes from the main container
            getTextNodes(node) {
                const textNodes = [];

                function getTextNodesHelper(node) {
                    if (node.nodeType === Node.TEXT_NODE) {
                        textNodes.push(node);
                    } else {
                        const childNodes = node.childNodes;
                        for (let i = 0; i < childNodes.length; i++) {
                            getTextNodesHelper(childNodes[i]);
                        }
                    }
                }

                getTextNodesHelper(node);
                return textNodes;
            }

            keywordRemoveResponseHandler(response) {

                const keyword_id = response.keyword_id;
                // remove json reference
                const keyValObj = document.querySelector('#keywordBlock').value;
                let keyVals = JSON.parse(keyValObj);

                for (let i = 0; i < keyVals.length; ++i) {
                    if (keyVals[i].id == keyword_id) {
                        keyVals.splice(i, 1);
                        break;
                    }
                }

                document.querySelector('#keywordBlock').value = JSON.stringify(keyVals);

                // removes gray chip
                const keyword = document.querySelector(`#chip-keyword-${keyword_id}`)
                keyword.remove();

                // removes k-word
                const wordNodes = document.querySelectorAll(`[data-keyword-id="${keyword_id}"]`);
                wordNodes.forEach(wordNode => {
                    const textNode = document.createTextNode(wordNode.textContent);
                    wordNode.parentNode.replaceChild(textNode, wordNode);
                    // joins all parent nodes
                    wordNode.parentNode.normalize();
                });
            }

            // removes keyword refenrence from publication 
            runRemoveKeyword(publication_index_id, keyword_id) {
                console.log(publication_index_id, keyword_id, ' <------------- runRemoveKeyword');
                const action = 'removeKeyword';
                const jsonData = {
                    action,
                    publication_index_id,
                    keyword_id
                };
                this.ajax(jsonData, this.keywordRemoveResponseHandler);
            }
        }

        const Highlighter = new KeywordHighlighter(publicationContainer, ajaxPost)
        Highlighter.initKeywords();
    </script>
    <script>
        // radio buttons that select the function to be used
        const radios = document.querySelectorAll('input[name="activeState"]')

        // array of event listeners stored as objects
        let eventListeners = [];

        const showTrackNumbers = () => document.querySelector('#toggle-visability').checked;
        const showSelectedTracks = () => document.querySelector('#tracks-show').checked;
        const showSelectedKeywords = () => document.querySelector('#keywords-show').checked;
        const selectedAction = () => document.querySelector('input[name = "activeState"]:checked').value; // "none" | "tracks" | "keywords"



        // eventListeners is a global that holds all event listeners
        function removeAllListeners() {
            for (let {
                    node,
                    event,
                    handler
                }
                of eventListeners) {
                node.removeEventListener(event, handler);
            }
            eventListeners = [];
        }

        function addListener(node, event, handler) {
            node.addEventListener(event, handler);
            eventListeners.push({
                node,
                event,
                handler
            });
        }

        //Allows the user to select or not select text
        function toggleContainerClass(cssClass = "") {

            if (!publicationContainer) return; // Guard clause if publicationContainer is not defined

            // removes all css from 
            const isSelectTextDisabled = publicationContainer.className = "";

            if (cssClass != "") {
                publicationContainer?.classList.add(cssClass);
            }
        }


        // attach radio on change listeners
        for (const radio of radios) {
            radio.onclick = (e) => {
                removeAllListeners();
                toggleContainerClass();
                switch (e.target.value) {
                    case 'selectTracks':

                        addListener(publicationContainer, 'click', trackSelectHandler);
                        // disable select
                        toggleContainerClass('text-select-active');
                        break;
                    case 'selectKeywords':

                        addListener(publicationContainer, 'click', Highlighter.selectKeywordHandler);
                        toggleContainerClass('keyword-select-active');
                        break;
                    default:
                        break;
                }

            }
        }
    </script>
    <script src="./../assets/js/enumeration-toggle.js"></script>
    <script>
        /** 
         * filters keyword values based on input case 
         * Hides or shows keywords
         */
        const filterKeywordsHandler = (e) => {
            const filter = e.value;
            const chips = [
                ...document.querySelectorAll('#keywordChips div.chip')
            ]
            // console.log(chips)
            chips.forEach((element) => {
                // element.dataset.chipVal
                if (!element.dataset.chipVal?.trim().startsWith(filter)) {
                    element.classList.add("d-none")
                } else {
                    element.classList.remove("d-none")
                }
            })
        }

        /**
         * turns on the ability to set MetaData
         */
        const showMetaHandler = (checkbox) => {

            if (!checkbox.checked) {

                for (const sheet of document.styleSheets) {
                    if (sheet.title === 'metaCss') {
                        // return sheet;
                        sheet.deleteRule(0);
                        sheet.insertRule("[class*=sub-meta-] { display:none}", 0);
                    }
                }

            } else {
                for (const sheet of document.styleSheets) {
                    if (sheet.title === 'metaCss') {
                        // return sheet;
                        sheet.deleteRule(0);
                        sheet.insertRule("[class*=sub-meta-] { display:block}", 0);
                    }
                }
            }
        }

        /**
         * Shows keywords that are in the publication only 
         */
        const filterReferenceKeywordHandler = (checkbox) => {

            const notFoundKeywords = document.querySelectorAll('.--nf');
            if (!checkbox.checked) {
                notFoundKeywords.forEach((ele) => ele.classList?.add("d-none"));
            } else {
                notFoundKeywords.forEach((ele) => ele.classList?.remove("d-none"));
            }

        }

        /**
         * Toggles only the keywords that have meta data associated with them
         */
        const filterMetaOnlyHandler = (checkbox) => {

            if (!checkbox.checked) {

                for (const sheet of document.styleSheets) {
                    if (sheet.title === 'metaCss') {
                        // return sheet;
                        sheet.deleteRule(1);
                        sheet.insertRule(".chip:not(:has(li)){ display: none;}", 1);
                    }
                }

            } else {
                for (const sheet of document.styleSheets) {
                    if (sheet.title === 'metaCss') {
                        // return sheet;
                        sheet.deleteRule(1);
                        sheet.insertRule(".chip:not(:has(li)){ display: inline-block;}", 1);
                    }
                }
            }
        }

        /**
         * Closes the modal
         */
        const closeModal = () => {
            $(".modal-dialog").hide();
        }

        $("#edit_row_btn").click(function() {
            $(".modal-dialog").show();
            // reset modal if it isn't visible
            if (!$(".modal.in").length) {
                $(".modal-dialog").css({
                    top: 80,
                    left: 320,
                });
            }
            $(".modal-dialog").draggable({
                cursor: "move",
                // appendTo: "body",
                handle: ".dragable_touch",
                containment: "body",
                stop: function(event, ui) {
                    if (!isInViewport(this, [0, -300, -300, 0])) {
                        $(this).css({
                            top: 80,
                            left: 320,
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

        const tracksHighlighter = (action = true) => {

            // get the tracks in from the input box
            const tracks = $('#track').val();
            if (tracks == '') return;
            const tracksArr = tracks.split(',');

            if (action) {
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
            } else {
                Array.from(document.querySelectorAll('.highlighter')).forEach(
                    (el) => el.classList.remove('highlighter')
                );
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

        const trackSelectHandler = (e) => {

            // check if tracks are visible
            if (!showSelectedTracks()) {
                document.getElementById('tracks-show').checked = true;
                tracksHighlighter(this.checked)
                return;
            }

            let tracks = [];
            let tracksLang = ['en', 'de']; // something that should be dynamic
            let tempId = bubbler(e.target); // on clicking the element bubbles up to where the track id is
            if (!tempId) return; // if not found escape
            // get the root track
            let track = tempId[0].slice(0, -2);
            // check if shift is down 
            if (isShiftKeyDown && lastClickedTrack != '' && (lastClickedTrack != track)) {

                const selectDirection = +lastClickedTrack.slice(6, -1).replace("_", ".") >
                    +track.slice(6, -1).replace("_", ".") ? "down" : "up";
                // what direction will we go in up or down?

                let [startValue, endValue] = selectDirection == 'down' ? [track, lastClickedTrack] : [lastClickedTrack, track];

                const startIndex = ALLTRACKIDS.indexOf(startValue + tracksLang[0]);
                const endIndex = ALLTRACKIDS.indexOf(endValue + tracksLang[0]);

                for (let i = startIndex; i <= endIndex; i++) {
                    let trackPush = ALLTRACKIDS[i].slice(0, -2);
                    tracks.push(trackPush);
                }

            } else {
                tracks.push(track);
            }

            // these will be the action needed
            const uniqueTracks = [...new Set(tracks)];

            // determine what we are going to do based on the lastClickedTrack value
            // this will do the opposite of its current state ie toggle highlighter
            let isHighlighted = document.getElementById(`${uniqueTracks[0]}${tracksLang[0]}`)?.classList.contains('highlighter');

            // this will do the same as the first track that was clicked for multiple updates
            if (tracks.length > 1) {
                isHighlighted = !document.getElementById(`${lastClickedTrack}${tracksLang[0]}`)?.classList.contains('highlighter');
            }

            // console.log(isHighlighted, "isHighlighted")     
            if (isHighlighted === true) {
                for (let i = 0; i < uniqueTracks.length; i++) {
                    tracksLang.forEach(lang => {
                        document.getElementById(`${uniqueTracks[i]}${lang}`).classList.remove('highlighter');
                    })
                    trackRemove(uniqueTracks[i]);
                }
            } else {
                for (let i = 0; i < uniqueTracks.length; i++) {
                    tracksLang.forEach(lang => {
                        document.getElementById(`${uniqueTracks[i]}${lang}`).classList.add('highlighter');
                    })
                    trackAdd(uniqueTracks[i]);
                }
            }

            // clean up last track Clicked
            lastClickedTrack = track;
        }


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

        /**
         * Attaches an event listener to an element if it doesn't already have one for that event.
         * This prevents adding the same event listener multiple times to an element.
         *
         * @param {HTMLElement} element - The DOM element to which the event listener will be attached.
         * @param {string} eventName - The name of the event to listen for (e.g., 'click', 'mouseover').
         * @param {Function} callback - The function to be called when the event is triggered.
         * @param {Event} event - The event object that will be passed to the callback function. 
         *                        This parameter is not used in the function definition and 
         *                        could be removed to avoid confusion.
         */
        const attachEvent = (element, eventName, callback, event) => {
            if (element && eventName && element.getAttribute("listener") !== "true") {
                element.setAttribute("listener", "true");
                element.addEventListener(eventName, (event) => {
                    callback(event);
                });
            }
        };

        const openMetaUI = (e) => {
            // console.log(e)
            const selectedMetas = e.target.dataset.selectedMeta.split(',');
            const metaContainer = document.getElementById('meta-checkboxes');
            const metaCheckboxes = metaContainer.getElementsByTagName("input");

            // get scroll offset
            const scrollPosMetaContainer = document.querySelector('#dragable_modal .modal-body');

            // move to correct position
            let pos = getAbsPosition(e.target);
            metaContainer.style.top = `${pos.y + scrollPosMetaContainer.scrollTop}px`;
            metaContainer.style.left = `${pos.x}px`;
            metaContainer.dataset.keywordId = e.target.dataset.keywordId;

            // reset all check boxes
            for (let i = 0; i < metaCheckboxes.length; i++) {
                metaCheckboxes[i].checked = false;
            }

            // make visible
            if (metaContainer.classList.contains('hide-disp')) {
                metaContainer.classList.remove('hide-disp');
            }

            // set selected check boxes
            selectedMetas.forEach(meta => {
                const checkbox = document.getElementById(`meta_${meta}`);
                if (checkbox) checkbox.checked = true;
            });

            // stop from being highlighted
            e.stopPropagation();
        }

        const metaSelect = document.querySelectorAll('.meta-control');

        metaSelect.forEach(mBox => {
            attachEvent(mBox, "click", openMetaUI, event)
        })

        /**
         * attaches a listener that closes the metacheck box
         */
        document.addEventListener('click', function(e) {
            const target = e.target;
            let metaContainer = document.getElementById("meta-checkboxes");
            if (!metaContainer.contains(target) && !metaContainer.classList.contains('hide-disp')) {
                metaContainer.classList.add('hide-disp');
            }

            // remove any focused word
            document.querySelectorAll('.--current-hl').forEach(element => {
                element.classList.remove('--current-hl');
            });

            // attach word jump here
            if (target.classList.contains('word-jump')) {
                // get the word keyword id
                const keywordChipId = target.parentNode.id;
                let keywordId = keywordChipId.split("-").pop();
                Highlighter.keywordFocus(keywordId);
            }
        })

        document.getElementById('addkeywords').addEventListener('keydown', function(e) {
            e.stopPropagation();
            if (e.keyCode == 13) {
                runAddKeyword();
            }
        });


        const spinnerRemove = (location) => {

            const elem = document.querySelector(`#${location}`).querySelector(`#spinnerSpan`);
            try {
                elem.parentNode.removeChild(elem);
            } catch (error) {
                // continue 
            }

        }

        const spinnerAdd = (location) => {

            const node = document.createElement('span');
            const shadow = node.attachShadow({
                mode: 'open'
            });
            node.setAttribute('id', 'spinnerSpan')
            document.querySelector(`#${location}`).prepend(node);

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
                                    pointer-events: all;                  
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

        const metaStage = (event) => {
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

        const metaResponseHandler = (response) => {
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


        document.getElementById('saveIndex').addEventListener('click', function(e) {
            spinnerAdd('dragable_modal');
            const action ='update-publication';
            const indices_id = '<?= $indices_id ?>';
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
                    action,
                    indices_id,
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
                url: "publication_ajax.php",
                data: jsonData,
                dataType: 'json',
                cache: false,
                success: function(html) {
                    alert('save complete');
                    spinnerRemove('dragable_modal');
                    // update tracks with new sort order
                    $('#track').val(html?.tracks);
                },
                error: function(res) {
                    alert('no', res);
                    spinnerRemove('dragable_modal');
                }
            });

        });

        const init = () => {
            tracksHighlighter();
        }
        init();
    </script>

    <?php include __DIR__ . '/../includes/script.import.inc.php'; ?>
    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '../../includes/footer.inc.php'; ?>
</body>

</html>