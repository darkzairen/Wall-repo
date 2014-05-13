<?php
	session_start();
	if(!isset($_SESSION['userid']) && !isset($_SESSION['username'])){
		header( 'Location: login.php' ) ;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>ISD Wall</title>
		<link href="assets/css/bootstrap.min.css" rel="stylesheet"><!-- bootstrap style -->
		<link href="assets/css/main.css" rel="stylesheet"><!-- main style -->
		<link href="assets/css/ekko-lightbox.min.css" rel="stylesheet"><!-- main style -->
		<link href="assets/css/lightbox-dark.css" rel="stylesheet"><!-- main style -->
	</head>
	<body>
				
		<div id="wrapper" class="container">
			<div class="row">
				<div class="col-md-8">
					<div class="chat-messages">
					</div>
				</div>
				<div class="col-md-4">
					<div id="sidebar" class="affix">
						<nav class="navbar navbar-inverse" role="navigation">
							<div class="navbar-header">
								<a class="navbar-brand" href="#">ISD Wall</a>
							</div>
							<p class="navbar-text navbar-right"><a href="#" class="navbar-link">@<?php echo $_SESSION['username'] ?></a>&nbsp;|&nbsp;<a href="logout.php" class="navbar-link">logout</a></p>
						</nav>
						<div class="chat">
							<input type="hidden" class="chat-name" readonly value="<?php echo $_SESSION['username'] ?>" />
							<textarea placeholder="Type your message..." maxlength="200"></textarea>
							<div class="bar">
								<span class="bar-fill" id="pb"><!--<span class="bar-fill-text" id="pt"></span>--></span>
							</div>
							<form action="upload.php" method="post" encytype="multipart/form-data" id="upload" class="upload" >
								<input type="file" id="file" name="file[]" class="filestyle" data-input="false" data-classButton="btn btn-primary btn-xs" data-classIcon="glyphicon-picture" data-buttonText="Choose Image" required multiple >
								&nbsp;
								<div class="btn-group">
									<button type="submit" id="submit" name="submit" class="btn btn-default btn-xs">
										<span class="glyphicon glyphicon-upload"></span> Upload
									</button>
									<button type="reset" id="reset" name="reset" class="btn btn-default btn-xs">
										<span class="glyphicon glyphicon-refresh"></span> Clear
									</button>
								</div>
							</form>
							<div class="chat-status">Status: <span>Idle</span></div>
						</div>
						<br/>
						
					</div>
				</div>
			</div>
		</div>
					
		<script src="http://webdev.com:8080/socket.io/socket.io.js"></script>
		<script src="assets/js/main.js"></script>
		<script src="assets/js/upload.js"></script>
		<script src="assets/js/jquery-1.11.0.min.js"></script><!-- jQuery Plugin -->
		<script src="assets/js/bootstrap.min.js"></script><!-- bootstrap Plugin -->
		<script src="assets/js/ekko-lightbox.min.js"></script><!-- lightbox Plugin -->
		<script src="assets/js/bootstrap-filestyle.min.js"></script><!-- lightbox Plugin -->
		<script>
			var f = document.getElementById('file'),
				pb = document.getElementById('pb'),
				aUploadedFiles = [];
		
			document.getElementById('submit').addEventListener('click', function(e){
				e.preventDefault();				
								
				if(f.files.length > 0){
					app.uploader({
						files: f,
						progressBar: pb,
						processor: 'upload.php',
						
						
						finished: function(data){
							for(x = 0; x < data.succeeded.length; x = x + 1){
								aUploadedFiles[x] = data.succeeded[x].file;
							}
						},
						
						error: function(){
							console.log('not working');
						}
					});
				}else{
					alert('Please choose a file.');
				}
			});
			
			document.getElementById('reset').addEventListener('click', function(){
				pb.style.width = 0;
				aUploadedFiles = [];
				$(".bootstrap-filestyle .badge").remove();
			});
			
			
			$(document).ready(function(){
				$(document).delegate('*[data-toggle="lightbox"]', 'click', function(event) {
					event.preventDefault();
					$(this).ekkoLightbox();
				}); 
			});
		</script>
		
		
	</body>
</html>