<?php
require_once 'config.php';

// ID parametresini al
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Gallery öğesini getir
try {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE HTML>
<!--
	Hyperspace by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html>
	<head>
		<title><?php echo htmlspecialchars($item['title']); ?> - Hyperspace</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Header -->
			<header id="header">
				<a href="index.php" class="title">Hyperspace</a>
				<nav>
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="index.php#one">Gallery</a></li>
						<li><a href="index.php#two">What we do</a></li>
						<li><a href="index.php#three">Contact</a></li>
					</ul>
				</nav>
			</header>

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Main -->
					<section id="main" class="wrapper">
						<div class="inner">
							<h1 class="major"><?php echo htmlspecialchars($item['title']); ?></h1>
							<?php if ($item['brief']): ?>
								<h2><?php echo htmlspecialchars($item['brief']); ?></h2>
							<?php endif; ?>

							<?php if ($item['image_url']): ?>
								<span class="image fit">
									<img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" />
								</span>
							<?php endif; ?>

							<?php if ($item['description']): ?>
								<p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
							<?php endif; ?>

							<ul class="actions">
								<li><a href="index.php#one" class="button">← Back to Gallery</a></li>
							</ul>
						</div>
					</section>

			</div>

		<!-- Footer -->
			<footer id="footer" class="wrapper alt">
				<div class="inner">
					<ul class="menu">
						<li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
					</ul>
				</div>
			</footer>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/jquery.scrolly.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>


