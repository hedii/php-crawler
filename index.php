<?php require 'Crawler.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>php-crawler.dev | index.php</title>
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">

	<!-- css -->
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	<script>
		setInterval(function(){
			window.scrollTo(0, document.body.scrollHeight);
		},100);
	</script>
	
</head>
<body>

	<header id="site-header">
		<nav class="navbar navbar-default" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="index.php">php-crawler</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav navbar-right">
						<li><a href="index.php">Home</a></li>
						<li><a href="emails.php">Emails</a></li>
					</ul>
				</div><!-- .navbar-collapse -->
			</div><!-- .container-fluid -->
		</nav><!-- .navbar -->
	</header><!-- #site-header -->

	<main id="site-content" role="main">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<form method="post" action="">
						<div class="form-group">
							<label for="url">Please enter an URL</label>
							<input type="url" class="form-control" id="url" name="url" placeholder="http://example.com">
						</div>
						<input type="submit" class="btn btn-default">
					</form>
				</div>
			</div><!-- .row -->
			<div class="row">
				<div class="col-md-12">
					<?php 
						if (isset($_POST['url'])) {
							
							$crawler = new Crawler();
							
							$url = $_POST['url'];
							
							$crawler->crawl_urls($url);
							
						}
					?>
				</div>
			</div><!-- .row -->
		</div><!-- .container -->
	</main><!-- #site-content -->

	<footer id="site-footer" role="contentinfo">
	</footer><!-- #site-footer -->

	<!-- js -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/script.js"></script>

</body>
</html>