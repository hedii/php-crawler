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
	
	<!-- js -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/script.js"></script>	
	
	<!-- font -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
</head>

<body>
	<div class="counter">
		<h4>Statistics</h4>
		<table>
			<tbody>
				<tr>
					<td>Total emails found: </td>
					<td><div id="total_email" class="statistic">0</div></td>
				</tr>
				<tr>
					<td>Total URLs stored: </td>
					<td><div id="total_url" class="statistic">0</div></td>
				</tr>			
				<tr>
					<td>Visited URLs: </td>
					<td><div id="visited_url" class="statistic">0</div></td>
				</tr>
				<tr>
					<td>Not visited URLs: </td>
					<td><div id="not_visited_url" class="statistic">0</div></td>
				</tr>
				<tr>
					<td>Crawled URLs for emails: </td>
					<td><div id="crawled_for_email_url" class="statistic">0</div></td>
				</tr>
				<tr>
					<td>Not yet crawled URLs for emails: </td>
					<td><div id="not_crawled_for_email_url" class="statistic">0</div></td>
				</tr>
				<tr>
					<td>Average system load: </td>
					<td><div id="system_load" class="statistic">0</div></td>
				</tr>				
			</tbody>
		</table>
		<br />
		
		<h4>Total URLs</h4>
		<div class="progress">
			<div id="progress_visited_url" class="progress-bar progress-bar-success" style="width: 0%">
				<span id="visited_url_percent">visited</span>
			</div>
			<div id="progress_non_visited_url" class="progress-bar progress-bar-danger" style="width: 0%">
				<span id="not_visited_url_percent">non-visited</span>
			</div>
		</div>
		
		<h4>URLs crawled for emails</h4>
		<div class="progress">
			<div id="progress_crawled_for_email_url" class="progress-bar progress-bar-success" style="width: 0%">
				<span id="crawled_for_email_url_percent">crawled for email</span>
			</div>
			<div id="progress_non_crawled_for_email_url" class="progress-bar progress-bar-danger" style="width: 0%">
				<span id="not_crawled_for_email_url_percent">non-crawled for email</span>
			</div>
		</div>		
	</div>

	<script>
		setInterval(function(){			
			// focus on the bottom of the page
			// window.scrollTo(0, document.body.scrollHeight);
			
			$.ajax({
				url: 'datas/total_email.php',
				type: 'GET',
				success: function(data) {
					var total_email = data;
					$('#total_email').html(total_email);
				}
			});
			
			$.ajax({
				url: 'datas/total_url.php',
				type: 'GET',
				success: function(data) {
					var total_url = data;
					$('#total_url').html(total_url);
					
					$.ajax({
						url: 'datas/visited_url.php',
						type: 'GET',
						success: function(data) {
							var visited_url = data;
							var not_visited_url = total_url - visited_url;
							$('#visited_url').html(visited_url);
							$('#not_visited_url').html(not_visited_url);
							
							/* Progress bar for urls */
							var visited_url_percent     = visited_url / total_url * 100;
							visited_url_percent         = Math.round(visited_url_percent * 100) / 100;
							var not_visited_url_percent = not_visited_url / total_url * 100;
							not_visited_url_percent     = Math.round(not_visited_url_percent * 100) / 100;
							$('#progress_visited_url').css('width', visited_url_percent + '%');
							$('#progress_non_visited_url').css('width', not_visited_url_percent + '%');
							$('#visited_url_percent').html(visited_url_percent + '% visited');
							$('#not_visited_url_percent').html(not_visited_url_percent + '% non-visited');
							
						}
					});
					
					$.ajax({
						url: 'datas/crawled_for_email_url.php',
						type: 'GET',
						success: function(data) {
							var crawled_for_email_url = data;
							var not_crawled_for_email_url = total_url - crawled_for_email_url;
							$('#crawled_for_email_url').html(crawled_for_email_url);
							$('#not_crawled_for_email_url').html(not_crawled_for_email_url);
							
							/* Progress bar for crawled for email url */
							var crawled_for_email_url_percent     = crawled_for_email_url / total_url * 100;
							crawled_for_email_url_percent         = Math.round(crawled_for_email_url_percent * 100) / 100;
							var not_crawled_for_email_url_percent = not_crawled_for_email_url / total_url * 100;
							not_crawled_for_email_url_percent         = Math.round(not_crawled_for_email_url_percent * 100) / 100;
							$('#progress_crawled_for_email_url').css('width', crawled_for_email_url_percent + '%');
							$('#progress_non_crawled_for_email_url').css('width', not_crawled_for_email_url_percent + '%');
							$('#crawled_for_email_url_percent').html(crawled_for_email_url_percent + '% crawled for email');
							$('#not_crawled_for_email_url_percent').html(not_crawled_for_email_url_percent + '% not crawled for email');							
						}
					});					
				}
			});
			
			$.ajax({
				url: 'datas/system_load.php',
				type: 'GET',
				success: function(data) {
					$('#system_load').html(data + '%');
				}
			});
			
		},2000);
	</script>

	<header id="site-header">
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
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
					<div class="page-header">
						<h1>php crawler</h1>
					</div>
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

</body>
</html>
