<?php

$lang = lang();
$id_sections = $_GET['id_sections'];

$section = gnome\classes\model\Section::factory()->getSection($id_sections, $lang);
// extract($section);
if ($section && is_array($section)) {
    extract($section);
} else {
    // Handle the error, e.g., log it, show a message, etc.
    error_log('Failed to get section data');
    // Optionally, you can initialize $section with a default value to prevent further errors
    $section = [];
}


$articles = gnome\classes\model\Article::factory()->getArticles($id_sections, $lang);
$displayLinks = '';
foreach ($articles as $key => $row) {
	$urlTitle = str_replace(' ', '-', htmlentities($row['title']));

	if ($row['is_external_only'] != '1') {
		$displayLinks .= "<li><a href=\"/$lang/article/$row[id_articles]/$urlTitle\">$row[title]</a></li>";
	} else {
		$displayLinks .= "<li><a href=\"$row[link_external]\" target='_blank'>$row[title]</a> - external </li>";
	}
}

?>

<html>

<head>
	<base href="/">
	<?php include 'includes/header.inc.php'; ?>
</head>

<body class="">
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<?php include 'includes/title.inc.php'; ?>
				<section>
					<header class="main">
						<h1><?= $name; ?></h1>
					</header>
					<div class="row gtr-200">
						<div class="col-6 col-12-medium">
							<p>
								<?= $description; ?>
							</p>
							<div class="row">
								<div class="col-12 col-12-small">
									<h4>Articles</h4>
									<ul class="alt">
										<?= $displayLinks ?>
									</ul>
								</div>

								<!--<h4>Icons</h4>
									 <ul class="icons">
										<li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
										<li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
										<li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
										<li><a href="#" class="icon brands fa-github"><span class="label">Github</span></a></li>
										<li><a href="#" class="icon brands fa-dribbble"><span class="label">Dribbble</span></a></li>
										<li><a href="#" class="icon brands fa-tumblr"><span class="label">Tumblr</span></a></li>
									</ul> -->

							</div>
							<!-- <h3>Blockquote</h3> -->

						</div>
						<div class="col-6 col-12-medium">
							<span class="image main">
								<figure>
									<img src="/media/<?= $image; ?>" alt="<?= $image; ?>">
									<figcaption style="text-align:center"><?= $image_description ?></figcaption>
								</figure>
							</span>
							<blockquote>
								<?= $summary; ?>
							</blockquote>
						</div>
				</section>
			</div>
		</div>
		<?php include 'includes/sidebar.inc.php'; ?>
	</div>
	<?php include 'includes/script.nav.inc.php'; ?>
</body>

</html>