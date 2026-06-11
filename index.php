<?php
require_once 'config.php';

// Gallery verilerini çek
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    $gallery_items = $stmt->fetchAll();
} catch (PDOException $e) {
    $gallery_items = [];
}

// What We Do verilerini çek
try {
    $stmt = $pdo->query("SELECT * FROM what_we_do ORDER BY created_at ASC");
    $services = $stmt->fetchAll();
} catch (PDOException $e) {
    $services = [];
}

// Contact Info verilerini çek
try {
    $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
    $contact_info = $stmt->fetch();
} catch (PDOException $e) {
    $contact_info = null;
}

// Descriptions verilerini çek
try {
    $stmt = $pdo->query("SELECT * FROM descriptions");
    $descriptions_raw = $stmt->fetchAll();
    $descriptions = [];
    foreach ($descriptions_raw as $desc) {
        $descriptions[$desc['section_key']] = $desc;
    }
} catch (PDOException $e) {
    $descriptions = [];
}

// Contact Form Submission Handling
if (isset($_POST['contact_submit'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Validate inputs
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email)) $errors[] = 'Email is required';
    if (empty($title)) $errors[] = 'Title is required';
    if (empty($message)) $errors[] = 'Message is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, title, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $title, $message]);

            // Return success response for AJAX
            http_response_code(200);
            exit;
        } catch (PDOException $e) {
            // Return error response for AJAX
            http_response_code(500);
            exit;
        }
    } else {
        // Return error response for AJAX
        http_response_code(400);
        exit;
    }
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
		<title>Hyperspace by HTML5 UP</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	</head>
	<body class="is-preload">

		<!-- Sidebar -->
			<section id="sidebar">
				<div class="inner">
					<nav>
						<ul>
							<li><a href="#intro">Welcome</a></li>
							<li><a href="#one">Who we are</a></li>
							<li><a href="#two">What we do</a></li>
							<li><a href="#three">Get in touch</a></li>
						</ul>
					</nav>
				</div>
			</section>

		<!-- Wrapper -->
			<div id="wrapper">

				<!-- Intro -->
					<section id="intro" class="wrapper style1 fullscreen fade-up">
						<div class="inner">
							<h1>Hyperspace</h1>
							<?php if (isset($descriptions['intro']) && !empty($descriptions['intro']['content'])): ?>
								<p><?php echo nl2br(htmlspecialchars($descriptions['intro']['content'])); ?></p>
							<?php else: ?>
							<p>Just another fine responsive site template designed by <a href="http://html5up.net">HTML5 UP</a><br />
							and released for free under the <a href="http://html5up.net/license">Creative Commons</a>.</p>
							<?php endif; ?>
							<ul class="actions">
								<li><a href="#one" class="button scrolly">Learn more</a></li>
							</ul>
						</div>
					</section>

				<!-- One -->
					<section id="one" class="wrapper style2 spotlights">
						<?php foreach ($gallery_items as $index => $item): ?>
						<section>
							<a href="detail.php?id=<?php echo $item['id']; ?>" class="image">
								<img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" data-position="center center" />
							</a>
							<div class="content">
								<div class="inner">
									<h2><?php echo htmlspecialchars($item['title']); ?></h2>
									<p><?php echo htmlspecialchars($item['brief'] ?? ''); ?></p>
									<ul class="actions">
										<li><a href="detail.php?id=<?php echo $item['id']; ?>" class="button">Learn more</a></li>
									</ul>
								</div>
							</div>
						</section>
						<?php endforeach; ?>

						<?php if (empty($gallery_items)): ?>
						<section>
							<div class="content">
								<div class="inner">
									<h2>Henüz Galeri Öğesi Yok</h2>
									<p>Galeri öğeleri yakında eklenecektir.</p>
								</div>
							</div>
						</section>
						<?php endif; ?>
					</section>

				<!-- Two -->
					<section id="two" class="wrapper style3 fade-up">
						<div class="inner">
							<h2>What we do</h2>
							<?php if (isset($descriptions['what_we_do_intro']) && !empty($descriptions['what_we_do_intro']['content'])): ?>
								<p><?php echo nl2br(htmlspecialchars($descriptions['what_we_do_intro']['content'])); ?></p>
							<?php else: ?>
							<p>Phasellus convallis elit id ullamcorper pulvinar. Duis aliquam turpis mauris, eu ultricies erat malesuada quis. Aliquam dapibus, lacus eget hendrerit bibendum, urna est aliquam sem, sit amet imperdiet est velit quis lorem.</p>
							<?php endif; ?>
							<div class="features">
								<?php foreach ($services as $service): ?>
								<section>
									<?php if ($service['icon_class']): ?>
										<span class="icon solid major <?php echo htmlspecialchars($service['icon_class']); ?>"></span>
									<?php endif; ?>
									<h3><?php echo htmlspecialchars($service['title']); ?></h3>
									<p><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
								</section>
								<?php endforeach; ?>

								<?php if (empty($services)): ?>
								<section>
									<span class="icon solid major fa-info-circle"></span>
									<h3>Hizmetler Yakında</h3>
									<p>Hizmet bilgilerimiz yakında eklenecektir.</p>
								</section>
								<?php endif; ?>
							</div>
						</div>
					</section>

				<!-- Three -->
					<section id="three" class="wrapper style1 fade-up">
						<div class="inner">
							<h2>Get in touch</h2>
							<?php if (isset($descriptions['contact_intro']) && !empty($descriptions['contact_intro']['content'])): ?>
								<p><?php echo nl2br(htmlspecialchars($descriptions['contact_intro']['content'])); ?></p>
							<?php else: ?>
							<p>Phasellus convallis elit id ullamcorper pulvinar. Duis aliquam turpis mauris, eu ultricies erat malesuada quis. Aliquam dapibus, lacus eget hendrerit bibendum, urna est aliquam sem, sit amet imperdiet est velit quis lorem.</p>
							<?php endif; ?>
							<div class="split style1">
								<section>
									<form method="post" action="" id="contactForm">
										<div class="fields">
											<div class="field half">
												<label for="name">Name</label>
												<input type="text" name="name" id="name" required />
											</div>
											<div class="field half">
												<label for="email">Email</label>
												<input type="email" name="email" id="email" required />
											</div>
											<div class="field">
												<label for="title">Title</label>
												<input type="text" name="title" id="title" required />
											</div>
											<div class="field">
												<label for="message">Message</label>
												<textarea name="message" id="message" rows="5" required></textarea>
											</div>
										</div>
										<ul class="actions">
											<li><input type="submit" name="contact_submit" value="Send Message" class="primary" /></li>
										</ul>
									</form>

									<script>
										document.getElementById('contactForm').addEventListener('submit', function(e) {
											e.preventDefault();

											var formData = new FormData(this);

											// AJAX ile form gönderimi
											fetch('contact_handler.php', {
												method: 'POST',
												body: formData
											})
											.then(response => response.json())
											.then(data => {
												if (data.success) {
													// Başarı mesajı göster
													alert('Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağız.');
													this.reset();
												} else {
													// Hata mesajı göster
													alert('Mesaj gönderilirken bir hata oluştu: ' + data.message);
													console.error('Hata:', data.message);
												}
											})
											.catch(error => {
												console.error('Hata:', error);
												alert('Mesaj gönderilirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.');
											});
										});
									</script>
								</section>
								<section>
									<ul class="contact">
										<?php if ($contact_info && $contact_info['address']): ?>
										<li>
											<h3>Address</h3>
											<span><?php echo nl2br(htmlspecialchars($contact_info['address'])); ?></span>
										</li>
										<?php endif; ?>

										<?php if ($contact_info && $contact_info['email']): ?>
										<li>
											<h3>Email</h3>
											<a href="mailto:<?php echo htmlspecialchars($contact_info['email']); ?>"><?php echo htmlspecialchars($contact_info['email']); ?></a>
										</li>
										<?php endif; ?>

										<?php if ($contact_info && $contact_info['phone']): ?>
										<li>
											<h3>Phone</h3>
											<span><?php echo htmlspecialchars($contact_info['phone']); ?></span>
										</li>
										<?php endif; ?>

										<?php
										$social_links = [];
										if ($contact_info) {
											if ($contact_info['twitter_url']) $social_links['twitter'] = ['url' => $contact_info['twitter_url'], 'icon' => 'fa-twitter', 'label' => 'Twitter'];
											if ($contact_info['facebook_url']) $social_links['facebook'] = ['url' => $contact_info['facebook_url'], 'icon' => 'fa-facebook-f', 'label' => 'Facebook'];
											if ($contact_info['github_url']) $social_links['github'] = ['url' => $contact_info['github_url'], 'icon' => 'fa-github', 'label' => 'GitHub'];
											if ($contact_info['instagram_url']) $social_links['instagram'] = ['url' => $contact_info['instagram_url'], 'icon' => 'fa-instagram', 'label' => 'Instagram'];
											if ($contact_info['linkedin_url']) $social_links['linkedin'] = ['url' => $contact_info['linkedin_url'], 'icon' => 'fa-linkedin-in', 'label' => 'LinkedIn'];
										}
										?>

										<?php if (!empty($social_links)): ?>
										<li>
											<h3>Social</h3>
											<ul class="icons">
												<?php foreach ($social_links as $social): ?>
												<li><a href="<?php echo htmlspecialchars($social['url']); ?>" class="icon brands <?php echo $social['icon']; ?>" target="_blank"><span class="label"><?php echo htmlspecialchars($social['label']); ?></span></a></li>
												<?php endforeach; ?>
											</ul>
										</li>
										<?php endif; ?>
									</ul>
								</section>
							</div>
						</div>
					</section>

			</div>

		<!-- Footer -->
			<footer id="footer" class="wrapper style1-alt">
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