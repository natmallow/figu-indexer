<?php
$SECURITY->isLoggedIn();

use gnome\classes\DBConnection;
use gnome\classes\MessageResource;
use gnome\model\Event;

$database = new DBConnection();


require_once(__DIR__ . '/../includes/crystal/functions.php');

$lang = lang();
$action = action();


$event_id = '';
$name = '';
$description_html = '';
$event_time_start = '';
$event_time_end = '';
$event_date = '';

$Event = new Event();

function updateLinkTable($database, $insertId) {

    // remove all 

    $sqlDelete = "DELETE FROM `link_articles_sections` WHERE event_id = :insertId;";
    $pdoc = $database->dbc->prepare($sqlDelete);
    $pdoc->execute([':insertId' => $insertId]);
    // echo $database->showquery($sqlDelete, [':insertId' => $insertId]).'<br>';
    // link articles to sections
    if (!empty($_POST['sections'])) {

        // add all 
        $sqlAddlinks = "INSERT INTO `link_articles_sections`
            (`id_sections`,
            `event_id`)
            VALUES (:id_sections, :event_id)";

        $pdoc = $database->dbc->prepare($sqlAddlinks);

        foreach ($_POST['sections'] as $k) {
            $pdoc->execute([':id_sections' => $k, ':event_id' => $insertId]);
        }

        // echo $database->showquery($sqlAddlinks, [':id_sections' => $k, ':event_id' => $insertId,]) . '<br>';

        $pdoc = null;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (filter_input(INPUT_POST, 'action') == 'edit') {

        $Event->updateEvent($_POST, $isMakeTemplate);
        $_SESSION['actionResponse'] = 'Edit Complete!';
        header("Location: ./events.php?lang=$lang");
        exit();
    } elseif (filter_input(INPUT_POST, 'action') == 'add') {

        $sqlAdd = "INSERT INTO event
        (
            `name`, 
            `description_html`, 
            `event_time_start`,
            `event_time_end`,
            `event_date`,
            `updated_by`,
            `created_by`
        )
        VALUES
        (
            :name, 
            :description_html, 
            :event_time_start,        
            :event_time_end,
            :event_date,
            :updated_by,
            :created_by
        );";

        $pdoc = $database->dbc->prepare($sqlAdd);

        $sqlBind = [
            ':name' => $_POST['name'],
            ':description_html' => $_POST['description_html'],
            ':event_time_start' => $_POST['event_time_start'],
            ':event_time_end' => $_POST['event_time_end'],
            ':event_date' => date("Y-m-d H:i:s", strtotime($_POST['event_date'])),
            ':updated_by' => $_SESSION['username'],
            ':created_by' => $_SESSION['username']
        ];

        $pdoc->execute($sqlBind);

        $insertId = $database->lastInsertId();


        $_SESSION['actionResponse'] = $_POST['name'] . ' Has Been Created';
        header("Location: ./events.php?lang=$lang");
        exit();
    }
}

function getSections($database) {
    // section load
    $idArticles = (!empty(filter_input(INPUT_GET, 'id'))) ? filter_input(INPUT_GET, 'id') : '';

    $sqlSection = "SELECT DISTINCT s.name, s.id_sections,
        (SELECT IFNULL( 
            (SELECT 1 
                FROM articles a 
                INNER JOIN link_articles_sections l ON a.event_id = l.event_id  
                WHERE l.id_sections = s.id_sections AND l.event_id = :id) , 0
            )
        ) AS is_selected  
        FROM sections s LEFT JOIN
        link_articles_sections l on l.id_sections = s.id_sections;";

    $sqlSectionPDO = $database->dbc->prepare($sqlSection);
    $sqlSectionPDO->execute([':id' => $idArticles]);
    $sections = $sqlSectionPDO->fetchAll();
    $sqlSectionPDO = null;
    return $sections;
}

// $sections = getSections($database);
// Select Statements:
// individual
// $article = null;
if (!empty(filter_input(INPUT_GET, 'id'))) {

    $id = filter_input(INPUT_GET, 'id');

    $event = $Event->getEvent($id);
    extract($event[0]);

    // $event_id = $event_id_primary;
    $pdoc = null;
}
?>

<html>


    <head>
        <!-- <link rel="stylesheet" type="text/css" href="assets/content-tools.min.css"> -->
        <?php include __DIR__ . '/includes/header.inc.php'; ?>
        <link rel="stylesheet" href="assets/jodit/jodit.min.css">
        <script src="assets/jodit/jodit.js"></script>
    </head>

    <body class="">
        <div id="wrapper">
            <div id="main">
                <div class="inner">
                    <?php include '../includes/title.inc.php'; ?>
                    <section style="padding-block-end: 120px;">
                        <header class="main">
                            <h2>You are editing <small>(in)</small> <span style="color:darkorange;"><?php echo ($lang == 'en') ? 'English' : 'Spanish' ?></span></h2>
                        </header>
                        <div class="row gtr-200">

                            <div class="col-12 col-12-medium">
                                <?php
                                if ($_SESSION['actionResponse'] != '') {
                                    echo "<div class='notification'>$_SESSION[actionResponse]</div>";
                                }

                                $_SESSION['actionResponse'] = '';
                                ?>
                            </div>
                        </div>
                        <?php if ($action != '' && ($action == 'edit' || $action == 'add')) : ?>
                            <form id="articleFrom" method="POST">
                                <div class="row gtr-200">


                                    <div class="col-12 col-12-medium col-12-small">
                                        <input type="hidden" name="action" value="<?= $action ?>">
                                        <input type="hidden" name="event_id" value="<?= $event_id ?>">
                                        <label for="name">Event Name:</label>
                                        <input type="text" name="name" id="title" value="<?= htmlentities($name) ?>" required minlength="4" maxlength="250">
                                    </div>



<!--                                    <div class="col-6 col-md-6">
                                        <label for="event_date">Date of Event:</label>
                                        <input type="date" name="event_date" id="event_date" value="<?= $event_date ?>" required style="padding: 7px 1em;">
                                    </div>

                                    <div class="col-3 col-3-medium">
                                        <label for="author">Start Time:</label>
                                        <input type="time" name="event_time_start" id="author" value="<?= $event_time_start ?>" required minlength="4" maxlength="150">
                                    </div>
                                    <div class="col-3 col-3-medium">
                                        <label for="author">End Time:</label>
                                        <input type="time" name="event_time_end" id="author" value="<?= $event_time_end ?>" required minlength="4" maxlength="150">
                                    </div>-->


                                    <div class="col-12 col-12-medium col-12-small">
                                        <label for="description_html">Content:</label>

                                        <textarea rows="4" cols="20" name="description_html" form="articleFrom" id="description_html"><?= $description_html ?></textarea>

                                                                    <!-- <div id="editor"><?= $description_html ?></div> -->
                                    </div>

                                    <div class="col-12">
                                        <label for="link_download_internal">Link to downloadable file (pdf, doc, zip, etc):</label>
                                    </div>
                                    <div class="col-2 col-2-medium col-3-small">
                                        <button id="openFileModal" data-only="file" type="button">Attach file</button>
                                    </div>


                                    <div class="col-12 col-12-medium">
                                        <hr><br>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div style="text-align: left">
                                            <a href="/gnome/events.php" class="button">Cancel</a>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <div style="text-align: right">
                                            <button type="submit">
                                                <?php echo ($action === 'add') ? 'Save' : 'Update'; ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php else : ?>
                            <div class="row gtr-200">
                                <div class="col-12 col-12-medium">
                                    Invalid action
                                </div>
                            </div>
                        <?php endif ?>
                    </section>
                </div>
            </div>
            <?php include 'includes/sidebar.inc.php'; ?> 
        </div>
    </div>
</div>
<!-- The Modal -->
<div id="filesModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content-basic">
        <span class="close-x" id="closeModal">&times;</span>
        <div class="box alt">
            <div class="row gtr-50 gtr-uniform" id='modalData'>

            </div>
        </div>
    </div>

</div>
<?php include __DIR__ . '/../includes/script.image.inc.php'; ?>
<?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
<script>
        function factory() {
            initModal('filesModal', 'openModal', 'closeModal', 'modalData', 'section-image', ['imageSelectHandler']);
            initModal('filesModal', 'openFileModal', 'closeModal', 'modalData', 'link_download_internal', ['docSelectHandler']);
        }

        window.onload = factory();
</script>
<script>
    var editor = new Jodit('#description_html', {
        filebrowser: {
            ajax: {
                url: 'assets/connector/index.php'
            },
            uploader: {
                url: 'assets/connector/index.php?action=fileUpload',
            }
        }
    });
    ;
</script>
</body>

</html>