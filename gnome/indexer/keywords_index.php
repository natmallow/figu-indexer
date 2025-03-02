<?php
$SECURITY->isLoggedIn();

use gnome\classes\model\Keyword;

$Keyword = new Keyword();


$alphaArr = [
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
    'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r',
    's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
];

$filterAlpha = filter_input(INPUT_GET, 'filter_alpha') ?? 'a';
// $filterAlpha = in_array($filterAlpha, $alphaArr)? $filterAlpha : 'a'; 

$page = '1'; //filter_input(INPUT_GET, 'page') ?? '1';  
$ipp = filter_input(INPUT_GET, 'ipp') ?? '10';
$paginationFilters = "page=$page&ipp=$ipp";



$keywordTypes = ['ignored', 'used'];
$filterKeyType = filter_input(INPUT_GET, 'filter_keytype');
$filterKeyType = in_array($filterKeyType, $keywordTypes) ? $filterKeyType : 'ignored'; //ignored

if ($filterKeyType ==  'used') {
    list($keywordsObj, $paginator) = $Keyword->getUsedKeywords($filterAlpha);
} else {
    list($keywordsObj, $paginator) = $Keyword->getIgnoreKeywords($filterAlpha);
}

// var_dump($keywordsObj);

// var_dump($paginator);

?>
<!DOCTYPE html>
<html>

<head>
    <?php include __DIR__ . '/../includes/head.inc.php'; ?>
</head>

<body class="">
    <?php include __DIR__ . '../../includes/topnav.inc.php'; ?>
    <?php include '../includes/sidebar.inc.php'; ?>
    <main id="main" class="main">


        <?php include '../includes/title.inc.php'; ?>

        <div class="pagetitle">
            <h1>Keywords</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/gnome/index.php">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Keywords
                    </li>
                </ol>
            </nav>
        </div>
        <div class='' id="notification">
            <?php
            if ($_SESSION['actionResponse'] != '') {
                echo "<div class='notification fadeOut'>$_SESSION[actionResponse] </div>";
            }

            // $_SESSION['actionResponse'] = '';
            ?>
        </div>
        <section>


            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h4 class="card-subtitle text-muted">Filters</h4>
                    </div>
                    <div class="col">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-light m-0 <?= $filterKeyType == 'used' ? 'active' : '' ?>">
                                <a href="?<?= $paginationFilters; ?>&filter_alpha=<?= $filterAlpha; ?>&filter_keytype=used">
                                    keywords
                                </a>
                            </label>
                            <label class="btn btn-light m-0 <?= $filterKeyType == 'ignored' ? 'active' : '' ?>">
                                <a href="?<?= $paginationFilters; ?>&filter_alpha=<?= $filterAlpha; ?>&filter_keytype=ignored">
                                    Ignored keywords
                                </a>
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="word_search" placeholder="Word search">
                            <div class="input-group-append">
                                <a id="submitSearch" class="btn btn-outline-secondary m-0">Search</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" style="padding-top:12px">
                        <?php foreach ($alphaArr as $key => $value) : ?>
                            <a href="?<?= $paginationFilters; ?>&filter_alpha=<?= $value; ?>&filter_keytype=<?= $filterKeyType; ?>" class="btn btn-light <?= $filterAlpha == $value ? 'active' : '' ?>">
                                <?= $value; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="row" id="publication_keywords_display" style="padding: 8px 0;">
                    <div class="col-12">
                        <h4 class="card-subtitle mt-2 mb-2 text-muted">
                            <?= $filterKeyType == 'ignored' ? 'List of ignored keywords' : 'Keywords' ?> :
                        </h4>
                        <?php
                        $keyWords = is_null($keywordsObj) ? [] : $keywordsObj;

                        if (count($keyWords) == 0) {
                            echo '<h3 style="color:red;">No data found for query</h3>';
                        }

                        foreach ($keyWords as $word) :
                        ?>
                            <div class="chip" id="keyword_<?= $word["keyword_id"] ?>">
                                <?= $word["keyword"] ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-12" style="padding: 20px 0">

                    <?= $paginator->display_pagination(); ?>
                </div>
            </div>
        </section>
    </main>



    <?php include __DIR__ . '/../../includes/script.nav.inc.php'; ?>
    <?php include __DIR__ . '/../includes/footer.inc.php'; ?>

    <script>
        wordSearchHandler = () => {
            let wordSearch = document.getElementById('word_search').value;

            if (!wordSearch.trim()) {
                // console.log('yes I am in');
                return alert('please enter search word');
            }

            //  window.location = `?<?= $paginationFilters; ?>&filter_alpha=${wordSearch}&filter_keytype=<?= $filterKeyType; ?>`
            location.search = `<?= $paginationFilters; ?>&filter_alpha=${wordSearch}&filter_keytype=<?= $filterKeyType; ?>`
        }

        document.querySelector("#submitSearch").addEventListener("click", wordSearchHandler);
    </script>
</body>

</html>