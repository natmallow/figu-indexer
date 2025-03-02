<?php
$SECURITY->isLoggedIn();

use gnome\classes\model\Template;


// functions include in autoloader
$lang = lang();
// $page = page();
// $limit = (int) limit();
// $starting_limit = ($page-1)*$limit;

function update(DBConnection $db, $column, $msg) {
    # code...
    $sqlUpdate = $db->dbc->prepare(
            "UPDATE events SET $column = IF ($column, 0, 1) WHERE id_events= :id"
    );

    $sqlUpdate->execute([
        ':id' => $_GET['id']
    ]);
    // echo $sqlUpdate->rowCount();
    $sqlUpdate = null;

    $sqlFetch = $db->dbc->prepare("SELECT title FROM events WHERE id_events= :id");
    $result = $sqlFetch->execute([':id' => $_GET['id']]);

    $fetch = $sqlFetch->fetchAll();

    $_SESSION['actionResponse'] = sprintf($msg, $fetch[0][0]);
    $sqlFetch = null;
    header("Location: ./events.php");
    exit();
}

$Template = new Template();

$name = '';
$idsection = '';
$description = '';
$is_active = 0;
$image = '';
$image_description = '';
$summary = '';
$id_sections = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $is_active = isset($_POST['is_active']) ? 1 : 0;
    if ($_POST['action'] == 'edit') {

        $sqlUpdate = $database->dbc->prepare("
                    UPDATE sections SET 
                        name = :name, 
                        description = :description, 
                        is_active = :is_active, 
                        image = :image,
                        summary = :summary, 
                        id_parent = :id_parent,
                        image_description = :image_description
                    WHERE id_sections= :id");

        $sqlUpdate->execute([
            ':name' => $_POST['name'],
            ':description' => $_POST['description'],
            // ':sort_value' =>  $_POST['sort_value'],
            ':is_active' => $_POST['is_active'] != '1' ? '0' : '1',
            ':image' => $_POST['image'],
            ':summary' => $_POST['summary'],
            ':id_parent' => $_POST['id_parent'],
            ':image_description' => $_POST['image_description'],
            ':id' => $_POST['id_sections'],
        ]);

        // echo $sqlUpdate->rowCount();

        $sqlUpdate = null;
        $_SESSION['actionResponse'] = 'Edit Complete! <br><br>';
        header("Location: ./sections.php");
        exit();
    } elseif ($_POST['action'] == 'add') {

        $sqlAdd = $database->dbc->prepare("
        INSERT INTO `sections`
        (
        `name`,
        `description`,
        `is_active`,
        `image`,
        `summary`,
        `id_parent`,
        `image_description`)
        VALUES
        (
        :name,
        :description,
        :is_active,
        :image,
        :summary,
        :id_parent,
        :image_description)");

        $sqlAdd->execute([
            ':name' => $_POST['name'],
            ':description' => $_POST['description'],
            // ':sort_value' =>  $_POST['sort_value'],
            ':is_active' => $_POST['is_active'] != '1' ? '0' : '1',
            ':image' => $_POST['image'],
            ':summary' => $_POST['summary'],
            ':id_parent' => $_POST['id_parent'],
            ':image_description' => $_POST['image_description'],
        ]);

        // echo $sqlAdd->rowCount();

        $sqlAdd = null;
        $_SESSION['actionResponse'] = $_POST['name'] . 'Has Been Added';
        header("Location: ./sections.php");
        exit();
    }
}


// single actions
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['id']) && !empty($_GET['action'])) {
        switch ($_GET['action']) {
            case 'remove':
                $repMsg = "The Article<br><strong>%s</strong> - Has been removed!";
                update($database, 'is_deleted', $repMsg);
                break;
            case 'togglePublish':
                $repMsg = "The Article<br><strong>%s</strong> - Has been published!";
                update($database, 'is_published', $repMsg);
                break;
            case 'toggletoSidebar':
                $repMsg = "The Article<br><strong>%s</strong> - Has been added to side bar published!";
                update($database, 'is_on_sidebar', $repMsg);
                break;
        }
    }
}
// Select Statements:
// individual
// $section = null;




$templates = $Template->getAll();

?>

<html>


    <head>
        <?php include __DIR__ . '/includes/header.inc.php'; ?>
    </head>

    <body class="">
        <div id="wrapper">
            <div id="main">
                <div class="inner">
                    <?php include '../includes/title.inc.php'; ?>
                    <section>
                        <header class="main">
                            <h2 style="display: inline-block;">You are editing Template <small>(in)</small> <span style="color:darkorange;"><?php echo ($lang == 'en') ? 'English' : 'Spanish' ?></span></h2>

                        </header>

                        <div class="row gtr-200">
                            <div class="col-4 col-5-medium">
                                Events - <a href="/gnome/template.php?action=add&lang=<?= $lang ?>" class="button small" style="line-height: 4em;">Create New Template</a>
                                <hr>
                            </div>
                            <div class="col-8 col-7-medium align-center">
                                <div class='' id="notification">
                                    <?php
                                    if ($_SESSION['actionResponse'] != '') {
                                        echo "<div class='notification fadeOut'>$_SESSION[actionResponse]</div>";
                                    }

                                    $_SESSION['actionResponse'] = '';
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php
                        $title = '';
                        $cTitle = '';
                        for ($i = 0; $i < count($templates); $i++) :

//style="display: flex; flex-wrap: wrap; justify-content: space-between"
                            ?>
                            <div class="row alt-rows" style="">
                                <div class="col-10"  >
                                    <?= is_null($templates[$i]["name"]) ? 'needed' : $templates[$i]["name"]; ?>
                                </div>
                                <div class="col-2" style="text-align: right; padding-right: 5px;" >
                                    <a href="/gnome/template.php?id=<?= $templates[$i]["email_template_id"] ?>&action=edit&lang=<?= $lang ?>" style="padding-top: .4em;">Edit</a> 
                                </div>
                            </div>
                            <?php
                        endfor;

//                        $total_records = $row[0];
//                        $total_pages = ceil($total_records / $limit);
                        
                        $pageLink = '<div class="pagination"> %s </div>';
                        $pageLinks = '';
                        
                        for ($i = 1; $i <= $Template->total_pages; $i++) {
                            if($i == $page){
                               $pageLinks .= "<strong> $i </strong> ";
                            } else {
                               $pageLinks .= "<a href='events.php?".getPagin($i)."' class='pagin'> $i </a>"; 
                            }
                        }
                        
                        $pageLinks = rtrim($pageLinks, ', ');
                        ?>             
                <div class="row">
                    <div class="off-10 col-2 pagination" style="text-align: right"><?php printf($pageLink, $pageLinks); ?></div> 
                </div>
                </section>
            </div>
        </div>
        <?php include 'includes/sidebar.inc.php'; ?>
    </div>
    <?php include __DIR__ . '/includes/script.inc.php'; ?>
    <?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
</body>

</html>