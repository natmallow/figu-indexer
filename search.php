<?php


$dbconfig = require(__DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'crystal' . DIRECTORY_SEPARATOR . 'settings.config.php');          // Define db configuration arrays here
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'crystal' . DIRECTORY_SEPARATOR . 'db.connect.php');             // Include this file

// echo $_GET['id_articles'].'<br>';
$id_articles = $_GET['id_articles'];

$database = new DBConnection();

require_once(__DIR__ . '/includes/crystal/functions.php');  

$lang = lang();

$sql = "SELECT a.*, ab.* FROM articles a 
		RIGHT JOIN articles_body ab ON (a.id_articles = ab.id_articles AND ab.language = :lang ) 
		WHERE a.id_articles = :id_articles AND is_published = '1' AND is_deleted = '0';";

// section=$1&idarticle=$2 [L,QSA]
$pdoc = $database->dbc->prepare($sql);
$pdoc->execute([':lang' => $lang, ':id_articles' =>  $id_articles]);


extract($pdoc->fetchAll()[0]);

// title`,
// content_html`,
// image`,
// summary`,
// is_published`,
// link_internal`,
// link_external`,
// created_by`,
// updated_by`,
// created_date`,
// updated_date`,
// read`,
// sections_id_sections`

?>

<html>


<head>

	<base href="/">
	<?php include 'includes/header.inc.php'; ?>

</head>

<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<?php include 'includes/title.inc.php'; ?>
				<section>
					<header class="main">
						<h1>
							<?= $title ?>
						</h1>
					</header>
					<span class="image main">
						<img src="media/<?= $image ?>" alt="">
					</span>
					<?= $content_html ?>
				</section>
			</div>
		</div>
		<?php include 'includes/sidebar.inc.php'; ?>
	</div>
	<?php include 'includes/script.nav.inc.php'; ?>
</body>

</html>