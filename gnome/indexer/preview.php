<?php


$article = gnome\classes\model\Article::factory()->getArticleFull($_GET['id_articles'],lang());
extract($article[0]);

?>

<html>

<head>


	<?php include __DIR__ . '/includes/header.inc.php'; ?>

</head>

<body>
	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<div class="row">
					<div class="col-12 col-12-medium" style="text-align: right;">
						You are viewing - <?= $primary_title ?>
						<hr>
					</div>

					<?php if (is_null($top_title)) : ?>
						<div class="col-12 col-12-medium">
							<section>
								<header class="main">
									<h2>
										No translation exists
									</h2>
								</header>
								<p>
									<ol>
										<li>Close this page</li>
										<li>Click on the language you want</li>
										<li>Click on Articles</li>
										<li>Find Article you want Click Edit</li>
										<li>Add Content and Save</li>
										<li>Then you can click on the article preview</li>	
									</ol>
								</p>							
							</section>
						</div>
					<?php else : ?>
						<div class="col-12 col-12-medium">
							<section>
								<header class="main">
									<h1>
										<?= $top_title ?>
									</h1>
								</header>
								<span class="image main">
									<img src="/media<?= $image ?>" alt="">
								</span>
								<?= $content_html ?>
							</section>
						</div>
					<?php endif ?>




				</div>
			</div>
		</div>
</body>

</html>