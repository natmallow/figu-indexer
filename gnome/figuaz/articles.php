<?php

$SECURITY->isLoggedIn();

$lang = lang();

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
            ':name' =>  $_POST['name'],
            ':description' =>  $_POST['description'],
            // ':sort_value' =>  $_POST['sort_value'],
            ':is_active' =>  $_POST['is_active'] != '1' ? '0' : '1',
            ':image' =>  $_POST['image'],
            ':summary' =>  $_POST['summary'],
            ':id_parent' =>  $_POST['id_parent'],
            ':image_description' =>  $_POST['image_description'],
            ':id'  =>  $_POST['id_sections'],
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
            ':name' =>  $_POST['name'],
            ':description' =>  $_POST['description'],
            // ':sort_value' =>  $_POST['sort_value'],
            ':is_active' =>  $_POST['is_active'] != '1' ? '0' : '1',
            ':image' =>  $_POST['image'],
            ':summary' =>  $_POST['summary'],
            ':id_parent' =>  $_POST['id_parent'],
            ':image_description' =>  $_POST['image_description'],
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
                $msg = "The Article<strong>%s</strong> - Has been removed!";
                gnome\classes\model\Ajax::factory()->toggleArticles('is_deleted', $_GET['id'], $msg);
                break;
            case 'togglePublish':
                $msg = "The Article: <strong>%s</strong> - published has changed!";
                gnome\classes\model\Ajax::factory()->toggleArticles('is_published', $_GET['id'], $msg);
                break;
            case 'toggletoSidebar':
                $msg = "The Article<strong>%s</strong> - sidebar location has been edited!";
                gnome\classes\model\Ajax::factory()->toggleArticles('is_on_sidebar', $_GET['id'], $msg);
                break;
        }
    }
}

$articles = gnome\classes\model\Article::factory()->getSectionArticles();

?>
<!doctype html>

<html>

<head>
    <?php include __DIR__ . '/includes/head.inc.php'; ?>
</head>

<body class="">
    <?php include __DIR__ . '/includes/topnav.inc.php'; ?>
    <?php include 'includes/sidebar.inc.php'; ?>
    <main id="main" class="main">


        <?php include 'includes/title.inc.php'; ?>

        <section class="section">
            <header class="py-3 mb-4 border-bottom">
                <div class="col-12">
                    <span class="fs-4">You are editing Articles <small>(in)</small> <span style="color:darkorange;"><?php echo (lang() == 'en') ? 'English' : 'Spanish' ?></span></span>
                </div>
            </header>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-between align-items-center">
                    <span class="fs-2 fw-bold">Available Articles</span>
                    <a href="/gnome/article.php?action=add&lang=<?= $lang ?>" class="btn btn-primary btn-sm" style="height: fit-content;">Create New Article</a>
                </div>
                <div class="col-md-12">
                    <?php include 'includes/head-resp.inc.php'; ?>
                </div>

                <?php
                $title = '';
                $cTitle = '';
                for ($i = 0; $i < count($articles); $i++) :

                    if ($title != $articles[$i]['name']) :
                ?>
                        <div class="col-md-3">
                            <h4><?= $articles[$i]['name'] ?></h4>

                        <?php
                        $title = $articles[$i]['name'];
                    endif;
                        ?>
                        <div class="card mb-2 --card">
                            <div class="card-body">
                                <h5 class="card-title text-nowrap overflow-hidden text-truncate">
                                    <a href="/gnome/preview.php?id_articles=<?= $articles[$i]["id_articles"] ?>&action=view&lang=<?= $lang ?>" class="link" target="preview">
                                        <?= is_null($articles[$i]["top_title"]) ? 'needed' : $articles[$i]["top_title"]; ?>
                                    </a>
                                </h5>
                                <h6 class="card-subtitle small mb-2">(<?= $articles[$i]["title"] ?>)</h6>
                                <div class="card-text mb-3">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" onclick="toggleGen('<?php echo '/gnome/articles.php?id=' . $articles[$i]['id_articles'] . '&action=toggletoSidebar' ?>')" id="is_on_sidebar<?= $articles[$i]["id_articles"] ?>" <?php if ($articles[$i]["is_on_sidebar"] == '1') echo "checked='checked'"; ?>>
                                        <label for="is_on_sidebar<?= $articles[$i]["id_articles"] ?>">add to sidebar</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" onclick="toggleGen('<?php echo '/gnome/articles.php?id=' . $articles[$i]['id_articles'] . '&action=togglePublish' ?>')" id="is_published<?= $articles[$i]["id_articles"] ?>" <?php if ($articles[$i]["is_published"] == '1') echo "checked='checked'"; ?>>
                                        <label for="is_published<?= $articles[$i]["id_articles"] ?>">publish</label>
                                    </div>
                                </div>

                                <div class="card-footer d-flex justify-content-between">
                                    <a title="Delete <?= $articles[$i]["title"] ?>" onclick="return confirm('Confirm Delete');" href="/gnome/articles.php?id=<?= $articles[$i]["id_articles"] ?>&action=remove" class="btn btn-outline-danger btn-sm">Remove</a>
                                    <a href="/gnome/article.php?id=<?= $articles[$i]["id_articles"] ?>&action=edit&lang=<?= $lang ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                </div>
                            </div>
                        </div>

                        <?php

                        if (
                            (isset($articles[$i + 1]['name']) && $title != $articles[$i + 1]['name'])
                            || (!isset($articles[$i + 1]['name']))

                        ) {
                            echo '</div>';
                        };



                        ?>

                    <?php endfor; ?>
                        </div>
        </section>

    </main>

    <?php include __DIR__ . '/../includes/script.image.inc.php'; ?>
    <?php include __DIR__ . '/../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/includes/footer.inc.php'; ?>    
    

    </body>

</html>