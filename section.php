<?php

$title = 'This is a test';

?>

<html>


<head>
	<script src="js/lib/jquery/jquery.min.js"></script>
	<script src="js/lib/browser/browser.min.js"></script>
	<script src="js/lib/breakpoints/breakpoints.min.js"></script>
	<script src="js/util.js"></script>
	<link rel="stylesheet" type="text/css" href="css/custom.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>

<body class="">


	<div id="wrapper">
		<div id="main">
			<div class="inner">
				<?php include 'includes/title.inc.php'; ?>
				<section>
					<header class="main">
						<h1><?=$title; ?></h1>
					</header>

				</section>
			</div>
		</div>
		<?php include 'includes/sidebar.inc.php'; ?>
	</div>
	<?php include 'includes/script.inc.php'; ?>
</body>

</html>