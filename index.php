<?php
	session_start();
	
	if(isset($_COOKIE[('user']) and $_COOKIE['user']!=""){
		header('Location: lastfm.php');
	}


?>


<html>
	<head>
		<link rel="icon" href="favicon.png">
		<link href="https://msn.ldkf.de/css/bootstrap.min.css" rel="stylesheet">
    	<link href="https://msn.ldkf.de/css/main.css" rel="stylesheet">
    	<link href="https://msn.ldkf.de/css/bootstrap-theme.min.css" rel="stylesheet">
		<title>Startseite</title>
		<style>
			.overlay-content{
				vertical-align: middle;	
				margin: 0px auto;	
				z-index: 2;
				position: relative;
				position: relative;
				top: 50%;
				-webkit-transform: translateY(-50%);
				-ms-transform: translateY(-50%);
				transform: translateY(-50%);				
			}
			html, body {
				width: 100%;
				height: 100%;
				margin: 0;
				padding: 0;
				overflow: hidden;
			}
			#hintergrund {
				min-width: 100%;
				min-height: 100%;
				position: absolute;
				z-index: 0;
			}
			.middle{
				width:410px; 
				padding:30px; 
				background-color: rgba(0, 0, 0, 0.6);
				border-radius: 6px;
			}
		</style>
	</head>
	<body style="">
		<div>
			<img id="hintergrund" src="pic/bg.jpg"/>
		</div>
		<div id="content" class="main-content" role="main" style="height:100%">
			<div class="overlay-content">
				<div class="container middle">
					<form class="form-signin" method="post" action="lastfm.php">
						<label for="input_username" class="sr-only">Username</label>
						<input id="id_username" style="margin-bottom:5px;" autocomplete="on" type="text" name="username" class="form-control" placeholder="<?php
							if(isset($_COOKIE["login"])){
								$val_name=$_COOKIE["login"];
								echo $val_name.'" value="'.$val_name;
							}
							else{
								echo 'LastFm Benutzername';											
							}
      				?>" required>
      				<select name="method" class="form-control" style="margin-bottom:5px;">
							<option selected value="2">Gescrobbelte Lieder anzeigen</option>
							<option value="5">Lieblingslieder anzeigen</option>
							<option value="6">Top K&uuml;nstler anzeigen</option>
   						<option value="7">Top Titel anzeigen</option>
						</select>
						<input type="hidden" name="start" value="1">
   					<button class="btn btn-lg btn-primary btn-block" type="submit">Los geht's!</button>
   					<?php
   						if(isset($error) and $error!="") {
								echo '<h3 style="color:red">'.$error.'</h3>';      	
   						}
   					?>
   					</form>
   					<br>
						<form class="form-signin" method="post" action="lastfm.php" style="padding-top:10px;">
        					<button class="btn btn-lg btn-primary btn-block">LDKF-Bot zu Telegram hinzuf&uuml;gen</button>
        					<input type="hidden" name="method" value="1">
        				</form>
        				<form class="form-signin" method="post" action="lastfm.php" style="margin-top:10px;">
							<button class="btn btn-lg btn-primary btn-block">Gruppeninformationen anzeigen</button>
							<input type="hidden" name="method" value="4">
        					<?php
      	     				if(isset($error) and $error!="") {
									echo '<h3 style="color:red">'.$error.'</h3>';      	
      						}
      					?>
      				</form>
      			</div>
					<script type="text/javascript">
    					window.cookieconsent_options = {"message":"This website uses cookies to ensure you get the best experience on our website","dismiss":"Got it!","learnMore":"More info","link":null,"theme":"dark-bottom"};
					</script>
					<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
			  </div>
			</div>
		</div>  
	</body>
</html> 