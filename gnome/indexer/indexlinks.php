<?php

$SECURITY->isLoggedIn();


// phpinfo();

// $redis = new Redis(); // Instantiate a Redis object directly.

// try {
//     // Attempt to connect to Redis server.
//     $redis->connect('127.0.0.1', 6379); // Assuming Redis is running on localhost at the default port.

//     // Check if the Redis server is accessible.
//     if ($redis->ping()) {
//         echo "Successfully connected to Redis.";
//     } else {
//         echo "Connection to Redis failed.";
//     }
// } catch (Exception $e) {
//     // Handle connection errors.
//     echo "Redis connection error: " . $e->getMessage();
// }

// exit;

use gnome\classes\model\Indices;
use gnome\classes\model\PublicationIndex;
use gnome\classes\model\Publication;

$lang = lang();

$Indices = new Indices();
$PublicationIndex = new PublicationIndex();
$indices_id = filter_input(INPUT_GET, 'index_id');
$pub_type = filter_input(INPUT_GET, 'pub_type') ? filter_input(INPUT_GET, 'pub_type') : null;
$publicationName = null;
$publicationAbbr = null;
$publicationIndexRs = [];

// $indices_id = ''; passed in
$name = '';
$description_html = '';
$highlight_color = '';
$text_color = '';

if ($indices_id) {
    $SECURITY->indexPermission($indices_id)?->hasRightAccess('can_write', 'Author access needed');

    // gets indices information html css color
    $indexRs =  $Indices->getIndex($indices_id);
    extract($indexRs);

    // if (!is_null($pub_type)) {
    //     // this is the main call
    //     $publicationIndexRs = $PublicationIndex->getIndexPublications($indices_id, $pub_type);
    // }

    $Publication = new Publication();
    $publicationTypes = $Publication->getPublicationTypes();

    if (!is_null($pub_type)) {
        $publicationType = $Publication->getPublicationType($pub_type);
        $publicationName = $publicationType['name'];
        $publicationAbbr = $publicationType['abbreviation'];
    }
} else {
    $_SESSION['actionResponse'] = "No index was selected";
    header("Location: ./indices.php?lang=$lang");
    exit();
}

?>
<!DOCTYPE html>
<html>


<head>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>
    <script src="../assets/ag-grid/@31.2.0/ag-grid-community.min.js"></script>
    <!-- <script src="../assets/ag-grid/@31.2.0/ag-grid-enterprise.min.js"></script> -->
    <link rel="stylesheet" href="../assets/ag-grid/@31.2.0/ag-grid.css">
    <link rel="stylesheet" href="../assets/ag-grid/@31.2.0/ag-theme-balham.css">
    <style>
        /* Hide resize handles and pseudo-elements in the header cell */
        .no-resize-handle .ag-header-cell-resize,
        .no-resize-handle::before {
            display: none !important;
        }

        /* Optional: target the specific first column header more accurately */
        .ag-header-cell[col-id="0"] .ag-header-cell-resize,
        .ag-header-cell[col-id="0"]::before {
            display: none !important;
        }
    </style>
    <script>
        <?= returnStrToCss(); ?>
    </script>
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
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
                        <a href="/gnome/indexer/indices.php">Indices (<?= $name ?>)</a>
                    </li>
                    <?php if (is_null($pub_type)) : ?>
                        <li class="breadcrumb-item active">
                            Select Publication Type
                        </li>
                    <?php else : ?>
                        <li class="breadcrumb-item active">
                            <?= $publicationName; ?> (<?= $publicationAbbr; ?>)
                        </li>
                    <?php endif; ?>

                </ol>
            </nav>
        </div>
        <!-- End Page Title -->
        <section class="section" id="wrapper">

            <header class="main">
                <h4>You are viewing: <?= $name; ?></h4>
                <!-- <div>
                    <h2 class="p-3" style="width:fit-content; border-radius:6px; 
                                    color:<?= $text_color; ?>; 
                                    background-color:<?= $highlight_color; ?>;">
                        
                    </h2>
                </div> -->
            </header>


            <div class="row">
                <div class="col-12 mt-2 mb-3">
                    <?php
                    $selectedPublicationType = 'Select publication type';
                    $htmlPublicationType = '';

                    foreach ($publicationTypes as $key => $value) {
                        if ($value['publication_type_id'] == $pub_type) {
                            $selectedPublicationType = $value['name'];
                        }
                        $htmlPublicationType .= "<li class='form-check'>
                            <a href=\"/gnome/indexer/indexlinks.php?index_id={$indices_id}&lang={$lang}&pub_type={$value['publication_type_id']}\" data-toggle=\"tooltip\" data-original-title=\"{$value['abbreviation']}\">{$value['name']}</a>
                        </li>";
                    }
                    ?>

                    <div class="dropdown">
                        <a class="btn btn-outline-dark btn-lg dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $selectedPublicationType; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <?php echo $htmlPublicationType; ?>
                        </ul>
                    </div>

                    <div class="mt-3"><strong>To start:</strong>
                        <ol>
                            <li>Select a publication type.</li>
                            <li>See status | Start editing | Continue editing
                                <button class="btn btn-sm" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-sanitize="false" data-bs-html="true" data-bs-content='<div class="popover fs-6" role="tooltip">
                                            <div class="popover-body" >
                                                <img src="/gnome/assets/img/how-to-publications-info.jpg" /><br>
                                                <a href="/gnome/indexer/publications_index.php?lang=en">To Publications Index</a>
                                            </div></div>'>
                                    <i class="bi bi-info-circle-fill"></i>
                                </button>
                            </li>
                        </ol>
                    </div>





                </div>
            </div>
            <div class="row">
                <div class="col-12 mt-2 mb-3">
                    <strong>Search State:</strong> <span id="logContainer"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mt-2 mb-3">
                    <div class="legend"><strong>Status Legend:</strong>
                        <div class="not-started"><button class="btn btn-link" onClick="setFilterStatus('Not Started')">Not Started</button></div>
                        <div class="inprogress"><button class="btn btn-link" onClick="setFilterStatus('In progress')">In progress</button></div>
                        <div class="needs-review"><button class="btn btn-link" onClick="setFilterStatus('Review needed')">Review needed</button></div>
                        <div class="being-reviewed"><button class="btn btn-link" onClick="setFilterStatus('Review in progress')">Review in progress</button></div>
                        <div class="no-ref-found"><button class="btn btn-link" onClick="setFilterStatus('Finished no ref found')">Finished no ref found</button></div>
                        <div class="finished"><button class="btn btn-link" onClick="setFilterStatus('Finished')">Finished</button></div>
                        <div class="clear"><button class="btn btn-link" onClick="clearFilter()">Clear Filter</button></div>
                    </div>
                </div>
            </div>
            <div class="row gx-2 gy-2">
                <?php if (is_null($pub_type)) : ?>
                    <div class="col-12">

                        <div class="mb-5 mt-5" style="text-align:center">
                            <strong>Select a publication type to begin</strong><br>
                            <div class="dropdown">
                                <a class="btn btn-outline-dark btn-lg dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo $selectedPublicationType; ?>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <?php echo $htmlPublicationType; ?>
                                </ul>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>
                <?php foreach ($publicationIndexRs as $key => $value) : ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <div class="card mb-0 p-2 <?php echo strToCss($value['indexing_status']); ?>">
                            <div class="d-flex justify-content-between align-items-center">

                                <strong>
                                    <a href="./pub_view_index.php?publication_id=<?= $value['publication_id'] ?>&index_id=<?= $indices_id ?>&pub_type=<?= $pub_type ?>" class="">
                                        <?php echo $value['publication_id']; ?>
                                    </a>
                                </strong>


                                <?php
                                if ($value['indexing_status'] != "Not Started") :
                                ?>
                                    <a href="./pub_index_editor.php?publication_id=<?= $value['publication_id'] ?>&index_id=<?= $indices_id ?>&pub_type=<?= $pub_type ?>" class="update-btn <?php echo strToCss($value['indexing_status']); ?>">edit</a>
                                <?php elseif ($value['is_ready'] != 1) : ?>
                                    <span class="nt-ready">Not ready!</span>
                                <?php else : ?>
                                    <a onClick="initEditing('<?= $value['publication_id'] ?>','<?= $indices_id ?>','<?= $pub_type ?>')" href="javascript:return false;" class="update-btn <?php echo strToCss($value['indexing_status']); ?>">Start</a>
                                <?php endif; ?>

                            </div>
                            <div class="pt-2">
                                <div class="dynamic-sm"><span>Status:</span> <br>
                                    <?php echo $value['indexing_status']; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>





            <div class="my-grid-container" style="position: relative; height: 600px; width: 100%;">
                <div class="dropdown" id="agGridMenu" style="position: absolute; right: 10px; top: 10px; z-index: 1000; display: none;">
                    <button class="btn btn-secondary" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class='bi bi-gear'></i>
                    </button>



                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li>
                            <button type="button" data-bs-toggle="tooltip" data-bs-original-title="Main keyword search across publications" class="search-publication-btn dropdown-item">
                                Run Keyword Search
                            </button>
                        </li>
                        <!-- <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li> -->
                    </ul>
                </div>
                <div id="myGrid" style="height: 600px; " class="ag-theme-balham"></div>
            </div>


        </section>
    </main>


    <!-- pop up dialog start -->
    <div class="modal modal-dialog-scrollable modal-lg fade" id="modalMasterKeywordSearch" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="linkIndexModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalMasterKeywordSearchLabel">
                        Search publications using Master Keyword list
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalMasterKeywordSearchBody">
                    You are about to search the through the selected publication.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <button type="button" class="btn btn-primary" id="proceedButton">Confirm</button>

                    <button class="btn btn-primary d-none" id="submit-btn-load" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Loading...
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- pop up dialog end -->



    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>

    <script>
        // locate modal body
        const keywordSearchModal = $('#modalMasterKeywordSearchBody');

        // this function updates the publication status to inprogress
        const initEditing = (_publication_id, _index_id, _pub_type) => {
            const action = 'update-publication';
            const indices_id = _index_id;
            const publication_id = _publication_id;
            const pub_type = _pub_type;
            const tracks = '';
            const summary = '';
            const keyWords = '';
            const notes = '';
            const publicationStatus = 'In progress';
            const optionalFieldsAns = '';
            const optionalFieldsArr = '';

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

            fetch('publication_ajax.php', { // Adjusted URL from your original comment
                    method: 'POST', // Set the method to POST
                    headers: {
                        'Content-Type': 'application/json', // Set content type to JSON
                    },
                    body: JSON.stringify(jsonData), // Stringify jsonData for the request body
                    cache: 'no-cache' // Equivalent to jQuery's cache: false
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json(); // Process the response to JSON
                })
                .then(data => {
                    console.log('save complete');
                    // Redirecting using the response data
                    window.location.href = `/gnome/indexer/pub_index_editor.php?publication_id=${publication_id}&index_id=${indices_id}&pub_type=${pub_type}`;
                })
                .catch(error => {
                    console.error('Failed to save', error);
                });


        }
    </script>


    <script>
        // // new attempt
        // const socket = new WebSocket('ws://localhost:8080');

        // socket.onopen = function(event) {
        //     console.log("Connected to WebSocket server.");
        // };

        // socket.onmessage = function(event) {
        //     console.log("Message received from the server:", event.data);

        //     // If the message is in JSON format, you might need to parse it
        //     try {
        //         const message = JSON.parse(event.data);
        //         console.log("Parsed message:", message);
        //     } catch (error) {
        //         console.error("Error parsing message:", event.data);
        //     }
        // };

        // socket.onerror = function(error) {
        //     console.error("WebSocket error:", error);
        // };

        // socket.onclose = function(event) {
        //     console.log("WebSocket connection closed:", event);
        // };


        // const logContainer = document.getElementById('logContainer');
        // let lastTimestamp = 0;
        // let retryCount = 0;

        // async function fetchLogFile() {
        //     try {
        //         const response = await fetch(`long_poll_ajax.php?timestamp=${lastTimestamp}`);
        //         if (!response.ok) {
        //             console.error('Fetch failed with status:', response.status);
        //             if (response.status === 404) {
        //                 retryCount++;
        //                 let retryDelay = 0;

        //                 if (retryCount === 1) {
        //                     retryDelay = 5000; // Wait 5 seconds
        //                 } else if (retryCount === 2) {
        //                     retryDelay = 10000; // Wait 10 seconds
        //                 } else {
        //                     console.log("Stopping retries after 3 failed attempts");
        //                     return; // Stop trying after 3 attempts
        //                 }

        //                 setTimeout(fetchLogFile, retryDelay);
        //             } else {
        //                 setTimeout(fetchLogFile, 1000); // Retry after a short delay for other errors
        //             }
        //             return;
        //         }
        //         const data = await response.json();
        //         if (data.log) {
        //             logContainer.textContent = data.log;
        //         }
        //         lastTimestamp = data.timestamp;
        //         retryCount = 0; // Reset retry count on success
        //         fetchLogFile();
        //     } catch (error) {
        //         console.error('Error fetching log file:', error);
        //         setTimeout(fetchLogFile, 1000); // Retry after a short delay if there's an error
        //     }
        // }
    </script>
    <script type="text/javascript" charset="utf-8">
        selectedRows = [];
        /*
                class CustomHeader extends HTMLElement {
                    constructor() {
                        super();
                        // You can replace the [?] with an actual icon using <i class="your-icon-class"></i> or <img src="your-icon-url"/>
                        this.innerHTML = `<span class="ag-header-cell-label">
                                            <span class="ag-header-cell-text"></span>
                                            <span class="custom-header-icon" title="Indicates if the item is active (1: active, 0 or null: inactive)">[?]</span>
                                        </span>`;
                    }

                    // Method to set the params, could be used to customize text, tooltip, etc.
                    init(params) {
                        this.querySelector('.ag-header-cell-text').textContent = params.displayName;
                        // You can further manipulate the element here, e.g., set different tooltips based on params
                    }
                }
        */

        class CustomHeader {
            init(params) {
                this.params = params;
                this.eGui = document.createElement('div');
                this.eGui.className = 'custom-header-cell no-resize-handle';
                this.eGui.style.display = 'flex';
                this.eGui.style.alignItems = 'center';
                this.eGui.innerHTML = '<input type="checkbox" id="selectAllChk" class="cb-header" />';
                this.checkbox = this.eGui.querySelector('#selectAllChk');

                this.checkbox.addEventListener('change', () => {
                    if (this.checkbox.checked) {
                        this.params.api.forEachNode((node) => {
                            if (node.data.is_ready === 1) {
                                node.setSelected(true);
                            }
                        });
                    } else {
                        this.params.api.forEachNode((node) => {
                            if (node.data.is_ready === 1) {
                                node.setSelected(false);
                            }
                        });
                    }
                });

                this.params.api.addEventListener('selectionChanged', () => this.updateState());
                this.params.api.addEventListener('modelUpdated', () => this.updateState());
            }

            updateState() {
                const allSelectable = [];
                const allSelected = [];
                this.params.api.forEachNode((node) => {
                    if (node.data.is_ready === 1) {
                        allSelectable.push(node);
                        if (node.isSelected()) {
                            allSelected.push(node);
                        }
                    }
                });
                this.checkbox.checked = allSelectable.length > 0 && allSelected.length === allSelectable.length;
                this.checkbox.indeterminate = allSelected.length > 0 && allSelected.length < allSelectable.length;
            }

            getGui() {
                return this.eGui;
            }
        }


        class CheckboxHeader extends HTMLElement {
            constructor() {
                super();
                this.innerHTML = `
                    <div class="custom-header-checkbox">
                        <input type="checkbox" id="headerCheckbox" />
                        <label for="headerCheckbox">Select All</label>
                    </div>`;
            }

            // Initialization and event binding
            init(params) {
                this.checkbox = this.querySelector('#headerCheckbox');
                this.params = params;
                this.checkbox.addEventListener('change', this.onCheckboxChanged.bind(this));
            }

            onCheckboxChanged() {
                if (this.checkbox.checked) {
                    // Logic to select all rows, for example:
                    this.params.api.selectAll();
                } else {
                    // Logic to deselect all rows
                    this.params.api.deselectAll();
                }
            }

            // Ensure to clean up, especially if you're adding event listeners
            disconnectedCallback() {
                this.checkbox.removeEventListener('change', this.onCheckboxChanged.bind(this));
            }
        }

        function actionCellRenderer(params) {
            // Assuming `params.data` contains the row data
            const {
                indexing_status,
                publication_id,
                is_ready
            } = params.data;
            const indices_id = "<?= $indices_id ?>"; // Set this to your actual indices_id
            const pub_type = "<?= $pub_type ?>"; // Set this to your actual pub_type

            // Function to generate CSS class based on indexing_status
            function strToCss(status) {
                // Implement the logic similar to your PHP strToCss function
                // For example, replace spaces with dashes and convert to lowercase
                return status.replace(/\s+/g, '-').toLowerCase();
            }

            if (indexing_status !== "Not Started") {
                return `<a href="./pub_index_editor.php?publication_id=${publication_id}&index_id=${indices_id}&pub_type=${pub_type}" class="update-btn">Edit</a>`;
            } else if (is_ready !== 1) {
                return `<span class="nt-ready">Not ready!</span>`;
            } else {
                // Note: You need to ensure the initEditing function is defined and accessible
                return `<a onClick="initEditing('${publication_id}','${indices_id}','${pub_type}')" href="javascript:void(0);" class="update-btn ${strToCss(indexing_status)}">Start</a>`;
            }
        }

        function keywordSearchCellRenderer(params) {
            // Assuming `params.data` contains the row data
            const {
                indexing_status,
                publication_id,
                is_ready
            } = params.data;
            const indices_id = "<?= $indices_id ?>"; // Set this to your actual indices_id
            const pub_type = "<?= $pub_type ?>"; // Set this to your actual pub_type

            // Function to generate CSS class based on indexing_status
            function strToCss(status) {
                // Implement the logic similar to your PHP strToCss function
                // For example, replace spaces with dashes and convert to lowercase
                return status.replace(/\s+/g, '-').toLowerCase();
            }

            if (indexing_status !== "Not Started") {
                return `<a href="./pub_index_editor.php?publication_id=${publication_id}&index_id=${indices_id}&pub_type=${pub_type}" class="update-btn">edit</a>`;
            } else if (is_ready !== 1) {
                return `<span class="nt-ready">Not ready!</span>`;
            } else {
                // Note: You need to ensure the initEditing function is defined and accessible
                return `<a onClick="initEditing('${publication_id}','${indices_id}','${pub_type}')" href="javascript:void(0);" class="update-btn ${strToCss(indexing_status)}">Start</a>`;
            }
        }

        function publicationView(params) {
            // Assuming `params.data` contains the row data
            const {
                indexing_status,
                publication_id,
                is_ready
            } = params.data;
            const indices_id = "<?= $indices_id ?>"; // Set this to your actual indices_id
            const pub_type = "<?= $pub_type ?>"; // Set this to your actual pub_type

            return `<strong>
                        <a href="./pub_view_index.php?publication_id=${publication_id}&index_id=${indices_id}&pub_type=${pub_type}" >
                            ${publication_id}
                        </a>
                    </strong>`;

        }

        function onSelectionChanged(event) {
            selectedRows = event.api.getSelectedRows();
            // console.log(selectedRows);
        }




        // Grid API: Access to Grid API methods
        let gridApi;

        const columnDefs = [{
                headerCheckboxSelection: false, // Disable ag-Grid's own checkbox
                checkboxSelection: params => params.data.is_ready === 1,
                headerComponent: 'CustomHeader',
                width: 50,
                lockPosition: true,
                suppressHeaderMenuButton: true,
                suppressMovable: true,
                editable: false,
                resizable: false, // Disable resizing for this column
            },
            {
                headerName: 'Publication',
                cellRenderer: publicationView,
                flex: 1,
            },
            {
                headerName: 'Status',
                headerTooltip: "Indicates if the item is active (1: active, 0 or null: inactive)", // Tooltip text
                field: 'indexing_status',
                width: 200,
                flex: 1,
                filter: 'agSetColumnFilter',
                filterParams: {
                    values: [
                        'Not Started',
                        'In progress',
                        'Review needed',
                        'Review in progress',
                        'Finished no ref found',
                        'Finished'
                    ]
                }
            },
            {
                headerName: 'Search State',
                field: 'keyword_search_status',
                // Adjust as necessary for your layout
                resizable: false,
                flex: 1,
                width: 200,
                cellStyle: {
                    textAlign: 'left'
                },
            },
            {
                headerName: 'keywords',
                field: 'keywords_found',
                // Adjust as necessary for your layout
                resizable: true,
                flex: 1,
                width: 200,
                cellStyle: {
                    textAlign: 'left'
                },                
                cellRenderer: (params) => {
                    
                    return `<span title="${params.value}">${params.value}</span>`;
                }
            },
            {
                headerName: 'Actions',
                cellRenderer: actionCellRenderer,
                // Adjust as necessary for your layout
                flex: 1,
                cellStyle: {
                    textAlign: 'left'
                },
            },

        ];

        const gridOptions = {
            rowData: [],
            columnDefs: columnDefs,
            components: {
                checkboxHeader: CheckboxHeader,
                CustomHeader: CustomHeader,
            },
            rowSelection: 'multiple',
            getRowClass: function(params) {
                return strToCss(params.data.indexing_status);
            },
            rowHeight: 42,
            onSelectionChanged: onSelectionChanged,
            onGridReady: function(event) {
                // attach options menu
                document.getElementById('agGridMenu').style.display = 'block';
                gridApi = event.api;
            },
            getRowId: (params) => params.data.publication_id,
        };



        // Create Grid: Create new grid within the #myGrid div, using the Grid Options object
        gridApi = agGrid.createGrid(document.querySelector("#myGrid"), gridOptions);

        // const eGridDiv = document.querySelector('#myGrid');
        // new agGrid.createGrid(eGridDiv, gridOptions);


        // loads data on to the page
        const datasource = {
            getRows(params) {
                // console.log(JSON.stringify(params.request, null, 1));
                // return;
                fetch('./indexlinks_ajax.php', {
                        method: 'POST',
                        body: JSON.stringify(params.request),
                        headers: {
                            "Content-Type": "application/json;"
                        }
                    })
                    .then(httpResponse => httpResponse.json())
                    .then(data => {
                        gridApi.setGridOption('rowData', data.data)
                        // gridOptions.api.setRowData(response.data);
                        // setGridOption 
                    })
                    // .then(fetchLogFile())
                    .catch(error => {
                        console.error(error);
                        // params.failCallback();
                    })
            }
        };
        <?php if (!is_null($pub_type)) : ?>

            const params = {
                request: {
                    action: "table-data",
                    indices_id: "<?= $indices_id ?>",
                    pub_type: "<?= $pub_type ?>"
                }
            }
            datasource.getRows(params);

        <?php endif; ?>

        // @selected number of publications that have been checked 
        function getMasterKeyWords(selectedCount = '0') {
            const requestData = {
                action: 'get-master-keywords',
                indices_id: `<?= $indices_id ?>`
            };

            fetch('indexlinks_ajax.php', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData),
                    cache: 'no-cache'
                }).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).then(jsonData => {
                    let html = `<div class="mb-2">You have selected <strong>${selectedCount}</strong> publication(s) to search.`;
                    html += ` The following keyword(s) will be included in the search.</div>`;
                    if (jsonData && jsonData.data && (jsonData.data.length > 0)) {

                        html += `<div class="chip-container mb-3">`;
                        jsonData.data.forEach(element => {
                            html += `<div class="chip">${element.value}</div>`;
                        });
                        html += `</div>
                                <div class="text-center mb-2">If these are not the keywords you want <a href='index_detail.php?index_id=<?= $indices_id ?>&action=edit&lang=<?= lang() ?>'>Click here</a> to edit</div>
                                <h5 class="text-center">Press 'Confirm' to proceed.</h5>
                                `;


                    } else {
                        document.getElementById("proceedButton").disabled = true;
                        html = `No keywords found to add keywords to the index master list.<br>
                    <a href='index_detail.php?index_id=<?= $indices_id ?>&action=edit&lang=<?= lang() ?>'>Click here</a> to add some keywords.`;
                    }

                    keywordSearchModal.html(html); // Ensure keywordSearchModal is properly defined or accessible in this context.
                })
                .catch(error => {
                    console.error('Failed to load indices', error);
                })
        }





        // pop up       
        keywordModal = new bootstrap.Modal(document.getElementById('modalMasterKeywordSearch'));
        document.querySelector(".search-publication-btn").addEventListener("click", function(e) {
            e.preventDefault();

            // get the assoiated keyword master list
            document.getElementById("proceedButton").disabled = false;

            if (selectedRows?.length > 0) {
                getMasterKeyWords(selectedRows.length)
            } else {
                keywordSearchModal.html(`Check mark the publication(s) to be searched.`)
                document.getElementById("proceedButton").disabled = true;
            }

            // Show the confirm delete modal
            keywordModal.show();
        });

        // expects a value pair [{"publication_id":###, "keyword_search_status":''}]
        function updateStatus(searchArr) {

            for (let i = 0; i < searchArr; i++) {
                // select row
                const rowNode = gridApi.getRowNode(searchArr[i].publication_id);

                if (rowNode) {
                    rowNode.setDataValue('keyword_search_status', searchArr[i].keyword_search_status);
                }
            }

        }


        function setFilterStatus(status) {
            gridApi.setFilterModel({
                indexing_status: {
                    type: 'equals',
                    filter: status
                }
            });
            gridApi.onFilterChanged();
        }

        function clearFilter() {
            gridApi.setFilterModel(null);
            gridApi.onFilterChanged();
        }




        function checkSearchStatus(last_checked = null) {

            const requestData = {
                action: 'run-master-keyword-search-status',
                indices_id: `<?= $indices_id ?>`,
                last_checked: last_checked
            };

            fetch('indexlinks_ajax.php', {
                    method: 'POST',
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(requestData),
                    cache: 'no-cache'
                }).then(response => response.json())
                .then(data => {
                    // If all searches are complete, stop polling
                    if (data.message == 'No search in progress') {
                        clearInterval(pollingInterval);
                        document.getElementById('logContainer').innerHTML = "All searches completed";
                    } else {
                        console.log(data.data.last_checked);
                        console.log(data.data);
                        document.getElementById('logContainer').innerHTML = "A Search is currently inprogress";
                        // data.data
                        // update the grid

                        // updateStatus()
                    }

                }).catch(error => {
                    console.error('Error checking search status', error);
                });
        }

        // Start the polling function every 15 seconds (test at 1.5 seconds)
        const pollingInterval = setInterval(checkSearchStatus, 1500);

        function toggleVisibility(elementId) {
            const element = document.getElementById(elementId);
            if (element) {
                element.classList.toggle('d-none');
            } else {
                console.warn(`Element with ID '${elementId}' not found.`);
            }
        }

        function runMasterKeywordSearch(selectedCount = '0') {
            const proceedButton = document.getElementById("proceedButton");

            // Disable button
            proceedButton.disabled = true;

            // Toggle visibility of elements
            toggleVisibility("submit-btn-load");
            toggleVisibility("proceedButton");

            const requestData = {
                action: 'run-master-keyword-search',
                indices_id: `<?= $indices_id ?>`,
                publication_ids: selectedRows.map(item => item.publication_id)
            };

            console.log('Sending request:', requestData);

            fetch('indexlinks_ajax.php', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify(requestData),
                cache: 'no-cache'
            }).then(response => {
                console.log('Received response:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text(); // Get the raw text response first
            }).then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text); // Manually parse JSON
                    console.log('Parsed JSON:', data);
                    keywordModal.hide();
                    checkSearchStatus(data.data.last_checked); // Start polling for status updates
                } catch (error) {
                    console.error('Failed to parse JSON:', error);
                    throw new Error('Invalid JSON response');
                }
            }).catch(error => {
                alert('Search failed');
                console.error('Search failed', error);
            }).finally(() => {
                console.log('Finally block executed');
                // Re-enable button and toggle visibility of elements
                proceedButton.disabled = false;
                toggleVisibility("submit-btn-load");
                toggleVisibility("proceedButton");
            });
        }




        document.querySelector("#proceedButton").addEventListener("click", function(e) {
            e.preventDefault();
            runMasterKeywordSearch()
        })
    </script>


</body>

</html>