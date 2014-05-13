<?php
	session_start();
	if(!isset($_SESSION['userid']) && !isset($_SESSION['username'])){
		if(isset($_POST['userid']) && isset($_POST['txtUserName'])){
			$_SESSION['userid'] = $_POST['userid'];
			$_SESSION['username'] = $_POST['txtUserName'];
			header( 'Location: index.php' ) ;
		}
	}else{
		header( 'Location: index.php' );		
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>WALL</title>
		<link href="assets/css/bootstrap.min.css" rel="stylesheet"><!-- bootstrap style -->
		<link href="assets/css/main.css" rel="stylesheet"><!-- main style -->
		<style>
			body{
				background: #f8f8f8;
				padding-top: 50px;
			}
		</style>
	</head>
	<body>
	
		<div class="container">
			<form id="login" role="form" action="login.php" method="POST">
				<div class="form-group" id="form-group">
					<h2>Login</h2>
					<input type="hidden" name="txtUserName" id="txtUserName">
					<input type="text" class="form-control" id="txtLogin" name="userid" placeholder="User ID">
					<input type="submit" id="btnLogin" name="btnLogin" class="btn btn-primary btn-block">
				</div>
			</form>
		</div>
		
		<script src="http://webdev.com:8080/socket.io/socket.io.js"></script>
		<script>
			try{
				var socket = io.connect('http://webdev.com:8080');
			}catch(e){
				console.log('Cannot connect to io');
			}
			
			if(socket !== undefined){			
				document.getElementById('txtLogin').addEventListener('keyup', function(event){
					if(event.which === 13){
						document.getElementById('btnLogin').click();
					}
				});
			
				document.getElementById('btnLogin').addEventListener('click', function(e){
					e.preventDefault();
					
					var userid = document.getElementById('txtLogin');
					
					socket.emit('login', {
						userid: userid.value,
					});
					
				});
					
				socket.on('username', function(data){
					// console.log(data);
					if(data.length === 1){
						var username = document.getElementById('txtUserName');
						username.value = data[0].name;
						document.getElementById('login').submit();
					}else{
						var element = document.getElementById('form-group');
						
						element.className += " has-error";
						alert('Invalid ID');
					}
				});					
				
			}
		</script>
	
	</body>
</html>