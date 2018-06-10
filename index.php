<!DOCTYPE html>
<?php
	session_start();
	//setcookie('user', "microsofthater2", time()+(3600*24*365), "/"); 
	include "include/config.php";
	if(isset($_COOKIE['user']) and $_COOKIE['user']!="") {	
		include "include/db_connect.php";
		include_once "include/functions.php";
		$user = $_COOKIE['user'];
		$username=md5($user);
		$getid = $db->query("SELECT session FROM `last_fm_users` WHERE username LIKE '$username'"); 
		$sk=$getid->fetch_assoc()['session'];
		$getid = $db->query("SELECT sig FROM `last_fm_users` WHERE username LIKE '$username'"); 
		$sig=$getid->fetch_assoc()['sig'];
     	$_SESSION['user']=$user;
     	$_SESSION['session']=$sk;	
		$_SESSION['sig']=$sig;
		if($user!="") {
			$methode="method=user.getInfo&user=".$user;
			$out_user = post($methode, $api_key);
			if($out_user!='{"error":6,"message":"User not found","links":[]}') {
				$decode_Info_User=json_decode($out_user);
				$user_info_forimage_array = get_object_vars($decode_Info_User)['user'];
				$user_name_info = get_object_vars($decode_Info_User)['user']->name;
				$totalTracks = get_object_vars($decode_Info_User)['user']->playcount;
				$starttime = get_object_vars($decode_Info_User)['user']->registered->unixtime;
				$user_info_forimage = get_object_vars($user_info_forimage_array)['image'];
				$userimage = get_object_vars($user_info_forimage[1]);
				$getimage = $db->query("SELECT `name` FROM `last_fm_user_pics` WHERE user LIKE '$user'"); 
				$getimage_row = $getimage->fetch_assoc()['name'];
				if(isset($getimage_row)and $getimage_row!="") {
					$image="user_pics/".$getimage_row.".png";
				}
				else {
					$image="pic/empty.png";
				}
			}
		}
	}
	else {
	
echo 	'<script language="JavaScript">
			<!-- hide 
			var password;
			var pass="baum";
			password=prompt();
			if (password  != pass) {
				window.location="rip.html";			
			}  	
    	-->
    	</script>';
	}

?>
<html lang="de">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    	<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    	<meta name="description" content="">
    	<meta name="author" content="Dominik Eichler" >
		<link rel="icon" href="favicon.png">
		<title><?php echo (isset($user) and $user!="") ? $user : 'Startseite';?></title>
		<!-- Bootstrap core CSS -->
    	<link href="css/bootstrap.css" rel="stylesheet">
		<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
		<link href="css/ie10-viewport-bug-workaround.css" rel="stylesheet">
		<!-- Custom styles for this template -->
		<link href="css/dashboard.css" rel="stylesheet">
 		<link href="css/main.css" rel="stylesheet">
  		<script src="js/jquery.js"></script>
    	<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    	<script src="js/bootstrap.js"></script>
    	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    	<script src="js/ie10-viewport-bug-workaround.js"></script>
    	
    	
		<script type="text/javascript">
		  $(document).ready(function(){
		  	var users = {};
		  	var user;
			function getCookie(cname) {
			    var name = cname + "=";
			    var decodedCookie = decodeURIComponent(document.cookie);
			    var ca = decodedCookie.split(';');
			    for(var i = 0; i <ca.length; i++) {
			        var c = ca[i];
			        while (c.charAt(0) == ' ') {
			            c = c.substring(1);
			        }
			        if (c.indexOf(name) == 0) {
			            return c.substring(name.length, c.length);
			        }
			    }
			    return "";
			}
			if (getCookie("user")!="") {
				user=getCookie("user");
				new_user(users, user);
				users[user]["RecentTracks"].stat=0;
			} 


  		   
			function userob(pagein, stopload, method, stat) {
  				this.pagein=pagein;
  				this.stopload=stopload;
  				this.max_count=$("#"+method+"_1_1").text();
  				this.stat=stat;
 				
			}
 		   
		    $(window).scroll(function() {
		      if ($(window).scrollTop()+200 >= ($(document).height() - ($(window).height()))) {
					if($('.user.cont:visible').length > 0){
	          		var cl = $('.user.cont:visible').attr("class").split(' ');	
	          		if (!users[user]) {
							users[user]={};
						
	     		 		}
	          		if (!users[user][cl[2]]) {
	          			users[user]["RecentTracks"]=new userob(1, false, "RecentTracks", 0);
	          			users[user][cl[2]]=new userob(1, false, cl[2], 0); // user/methode -> neues objekt anlegen, mit eigenschaften der
	     		 		}
		     		 	if (users[user][cl[2]].stopload == false) {  
			    	    	users[user][cl[2]].stopload = true;
		          		var el=$('.'+cl[2]+'.cont'+'.'+cl[1]);   		 // 2 klassen .a.b
							max_count = users[user][cl[2]].max_count; 
							users[user][cl[2]].pagein=(users[user][cl[2]].pagein)+1;
			 	 			$.post("include/get.php",{
						   	0: cl[1],
						      1: "35",
						      2: users[user][cl[2]].pagein, //holen 
						      3: cl[2],
						      4: "false",
						      5: max_count,
						   },
						   function (data) {
								if (data.replace(" \n", "") == 1) {
								}
								else{
									$( el ).children().append( data);
									users[user][cl[2]].stopload = false;
								}
							});
			 			}
			       }
			    }
			  });
			  $( "#logout" ).click(function() {
			  		var th= this;
			  		user=getCookie("user");
					$.post("include/logout.php",{
		   			0: user,
		   		},
		   		function (data) {
						if (data.replace(" \n", "") == 1) {
						}
						else{
							$(th).parent().hide();
							$( "#login" ).show();
						}
					});

			  	});
			  $( "#user_nav" ).click(function() {
			  		user=getCookie("user");
			  		var element=$('.RecentTracks.cont'+'.'+user); 
					document.title = user;  		 // 2 klassen .a.b
					if(element.length > 0){
						change("user", "RecentTracks");		
						element.show();
					}
			  	});
			  	function getcontent(user, method, users) { 
			  	
			  	 	$("#loading").show();
	    			$.post("include/get.php",{
		   			0: user,
		      		1: "35",
		      		2: "1",
		      		3: method,
		      		4: "false",
		      		5: "false",
		   		},
		   		function (data) {
						if (data.replace(" \n", "") == 1) {
						}
						else{
							change("user", method);
							$( ".table-responsive" ).append( data);
							if (method=="RecentTracks") {
								users[user][method].stat=0;
							}
							$("#loading").hide();
						}
					});
	  			}
				  	
				function change(bar, method) { 	
				 	$( ".active" ).removeClass('active');
					$('.'+method).parent().addClass('active');
					$('.cont').hide();
					$('.bar').hide();
					$( "."+bar+".bar, .sr" ).show(); 	
				}
			   function new_user(users, user) { 	
				 	if (!users[user]) {
						users[user]={};
						users[user]["RecentTracks"]=new userob(1, false, "RecentTracks", 1);
	     		 	}
					var element=$('.RecentTracks.cont'+'.'+user); 
					document.title = user;  		 // 2 klassen .a.b
					if(element.length > 0){
						change("user", "RecentTracks");		
						element.show();
					}
					else {
						 getcontent(user, "RecentTracks", users);
						 
					}
				}	
	
				$( "form" ).submit(function( event ) {
					user=$( "#user_input_value" ).val().trim();
					new_user(users, user);
					$("#explr").attr("href", "http://explr.fm?username="+user);
	   			event.preventDefault();
	   			return user;
				});
	
				$( "a" ).click(function() {
					var name;
					var that=this;
					if ((name=that.className.split(' '))[0]=="user") {
						var element=$('.'+name[1]+'.cont'+'.'+user);   		 // 2 klassen .a.b
						if(element.length > 0){
							change("user", name[1]);
							element.show();
						}
						else {
							getcontent(user, name[1]);
						}
					}
					else {
						if ((name=that.className.split(' '))[0]=="group_link") {
							document.title = "LDKF-Gruppe";
							var element=$(".group.cont#artist_week");
							if (element.length >0) {
								change("group", "group_link, .artist_week");
								element.show();
							}
							else {
								$("#loading").show();
								$("#explr").attr("href", "http://explr.fm");			
								$.post("include/artist_week.php",{
					   			0: true,
					      	},
					   		function (data) {
									if (data.replace(" \n", "") == 1) {
									}
									else{
										change("group", "group_link, .artist_week");
										$( ".table-responsive" ).append( data);
										$("#loading").hide();
									}
								});	
								$("#loading").show();
								$.post("include/getmembers.php",{
					   			0: true,
					      	},
					   		function (data) {
									if (data.replace(" \n", "") == 1) {
									}
									else{
										$( "#userlist" ).append( data);
										$("#loading").hide();
									}
								});
							}
						}
						else {
							if ((name=that.className.split(' '))[0]=="group") {
								var element=$('#'+name[1]+'.cont'+'.group');   		 // 2 klassen .a.b
								if(element.length > 0){
									change("group", "group_link, ."+name[1]);
									element.show();
								}
								else {
									$("#loading").show();
									$.post("include/"+name[1]+".php",{
					   				0: name[1],
						      	},
						   		function (data) {
										if (data.replace(" \n", "") == 1) {
										}
										else{
											change("group", "group_link, ."+name[1]);
											$( ".table-responsive" ).append( data);
											$("#loading").hide();
										}
									});
								}
							}
							else {
								
								
							}
						}
					}

					if (that.id=="userlist_nav") {
						if ($("#userlist:visible").length > 0) {
							$( "#userlist").hide();
							$(that).parent().removeClass('active');
							$( "#group_bar").show(); 
						}
						else {
							$( "#group_bar").hide();
							$(that).parent().addClass('active');
							$( "#userlist").show(); 
						}
						
					}
				});		    	
				setInterval(function(){  
  				  	if($('.user.cont').length > 0){
						$.each(users, function(us, value) {
							if(users[us]["RecentTracks"].stat==0){
								users[us]["RecentTracks"].stat=1;
								$.post("include/get.php",{
	  								0: us,
			      				1: "1",
			      				2: "1",
			      				3: "RecentTracks",
			      				4: "true",
			      				5: $(".last."+us).attr("title") ,
	 							},
								function (data) {
									if (data.replace(" \n", "") == 1) {
									}
									else{
										$(".last."+us).attr("class", us);
										$( "tr.del."+us ).replaceWith( "" );
										$( "tr.repl."+us ).replaceWith( data );
	
									}
									users[us]["RecentTracks"].stat=0;
								});
							}
							else {
								
							}
						});
					}
  				}, 20000);
	   		

		    	$("#userlist").on("click",".mem", function(){
		    		user=this.id;
  					new_user(users, user);
  					$("#explr").attr("href", "http://explr.fm?username="+user);
				});
		 	})
		 			  
		</script>
	</head>
	<body style="font-family: ubuntu-m;">
		<nav class="navbar navbar-inverse navbar-fixed-top">
      	<div class="container-fluid">
        		<div class="navbar-header">
          		<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            		<span class="sr-only">Toggle navigation</span>
            		<span class="icon-bar"></span>
            		<span class="icon-bar"></span>
            		<span class="icon-bar"></span>
          		</button>
          		<a class="navbar-brand" style="display:<?php echo (isset($user) and $user!="") ? 'none' : 'block'; ?>" href="http://ldkf.de" target="_blank">ldkf.de</a> <!-- this-->
          		<div style="vertical-align:middle; padding-top: 6px; display:<?php echo (isset($user) and $user!="") ? 'block' : 'none'; ?>">	<!-- or that-->  
          			<img style="border-radius: 19px;" width="38px" src="<?php echo (isset($image)) ? $image :'' ?>">
          		</div>         
        		</div>
        		<div id="navbar" class="navbar-collapse collapse">
		        	<ul class="nav navbar-nav navbar-left" >
		        		<li style=" display:<?php echo (isset($user) and $user!="") ? 'block' : 'none'; ?>"><a id="user_nav" href="#"><?php if(isset($user)){echo $user;} ?></a></li>
					</ul>
		         <ul class="nav navbar-nav navbar-right">
		         	<li><a href="https://scrobbler.ldkf.de" target="_blank">Scrobbler</a></li>
		          	<li><a class="group_link" href="#">Gruppe</a></li>
						<li style="display:<?php echo (isset($user) and $user!="") ? 'block' : 'none';?>"><a id="logout" href="#">Logout</a></li>
						<li id="login" style="display:<?php echo (isset($user) and $user!="") ? 'none' : 'block';?>"><a target="" href="http://www.last.fm/api/auth?api_key=<?php echo $api_key; ?>&cb=https://lastfm.ldkf.de/login.php">Login</a></li>
		         	<li><a href="http://explr.fm<?php echo (isset($user)) ? '?username='.$user : "";?>" id="explr" target="_blank">Explr.fm</a></li><!-- ?username=username-->		        
		        	</ul>
		         <form class="navbar-form navbar-right" id="ff">
		         	<div class="input-group">
      					<input type="text" class="form-control" size="12" id="user_input_value" placeholder="Benutzer suchen..." required>
      					<span class="input-group-btn">
        						<button class="btn btn-default start" type="submit" id="user_input">Los!</button>
      					</span>
    					</div>
		         </form>
	        	</div>
			</div>
		</nav>
		<div id="loading" style="display:none; width:100%; height:100%; opacity:0.7;  z-index: 2; position:absolute; font-size:15px; background-color:black; color:white; padding:40px;"></div>
		<div class="container-fluid" style=" z-index: 1;">
	   	<div class="row sr" style="display:<?php echo (isset($user) and $user!="") ? 'block' : 'none'?>">
	      	<div class="col-sm-3 col-md-2 sidebar" style="display:block; z-index: 1;">
	         	<ul class="nav nav-sidebar bar user" id="user_bar" style="display:<?php echo (isset($user) and $user!="") ? 'block' : 'none'; ?>">
		            <li <?php echo ( isset($user) and $user!="" and isset($_SESSION['user']) ) ? ' class="active"' : '' ?>><a class="user RecentTracks" href="#">Zuletzt gehört</a></li>
		        		<li><a class="user LovedTracks" href="#">Lieblingslieder</a></li>
		        		<li><a class="user TopArtists" href="#">Top K&uuml;nstler</a></li>
						<li><a class="user TopAlbums" href="#">Top Alben</a></li>
						<li><a class="user TopTracks" href="#">Top Titel</a></li>	
	          	</ul>
	          	<ul class="nav nav-sidebar bar group" id="group_users" style="display:none;">
	          		<li class=""><a id="userlist_nav" href="#">Mitglieder</a></li>
	          	</ul>
	          	<ul class="nav nav-sidebar bar" id="userlist" style="display:none;">

	          	</ul>
	          	<ul class="nav nav-sidebar bar group" id="group_bar" style="display:none">
		            <li><a class="group artist_week" href="#">Künstler (Woche)</a></li>
		        		<li><a class="group artist_all" href="#">Künstler (Gesamt)</a></li>
		        		<li><a class="group tracks_week" href="#">Titel (Woche)</a></li>
						<li><a class="group tracks_all" href="#">Titel (Gesamt)</a></li>
	          	</ul>
	          	<div style="">
	          		<a  href="impressum.html" target="_blank">Impressum</a>
						<a target="_blank" href="datenschutzerklaerung.html" >Datenschutz</a>
					</div>
	       	</div>
	        	<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2" style="margin-top:20px;"> <!-- hier ausblenden / neues div einfügen -->
	         	

	         	<div class="table-responsive">
	         		<?php
		         		if(isset($user) and $user!="" and isset($_SESSION['user'])) {
						   	include "include/get.php";
		         		}
	         		?>
	         		<!-- thats the interesting part-->
					</div>
				</div>
			</div>
			<?php 
				if(!isset($user) or $user=="") {
					echo '
						<div id="welcome" class="jumbotron cont" style="margin-top: 40px; text-align:center; max-width: 730px; margin-right: auto; margin-left: auto;">
	     					<p style="font-size:30pt; margin-bottom:30px">Willkommen auf lastfm.ldkf.de</p>
				       	<p class="lead">Hier kannst du Scrobbels und Infos von <a href="https://www.last.fm/de/" target="_blank">last.fm</a> abrufen und
				       	&uuml;bersichtlich darstellen lassen.
							Außerdem kannst du die Charts der ldkf-Gruppe anschauen.       		
				       	</p>
				       	<p>Du kannst direkt anfangen indem du entweder auf einen Link in der Navigationsleiste klickst oder dort nach einem Lastfm-Benutzer suchst.
				       	</p>
						<a  href="impressum.html" target="_blank">Impressum</a>
						<a target="_blank" href="datenschutzerklaerung.html" >Datenschutz</a>

				     	</div>

		
					';	
				}	
			?>
			
		</div>
		<script type="text/javascript">
    			window.cookieconsent_options = {"message":"This website uses cookies to ensure you get the best experience on our website","dismiss":"Got it!","learnMore":"More info","link":null,"theme":"dark-bottom"};
			</script>
			<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.9/cookieconsent.min.js"></script>
			<script type="text/javascript">
        $(document).ready(function(){
            $('body').on('hidden.bs.modal', '.modal', function () {
                $(this).removeData('bs.modal');
            });   
            
           var cl= $( ".love" );
				$('body').on('click', '.love', function() {
		   		var cont=$(this).attr("title");
		   		var mbid=$(this).parent().attr("class");
		   		var splits=cont.split('_');
		   		var action=splits[0];
		   		var artist=splits[1];
		   		var track=splits[2];
		   		var that=$( this );
					$.post( "include/do.php", { f: action, artist: artist,  track: track} ).done(function( data ) {
		    			$("."+mbid).find( "img" ).attr("src", "pic/"+data.replace(" \n", "")+".png");
		  			});
		  			if (action==="love") {
		  				$("."+mbid).find("label").attr("title", "unlove_"+artist+"_"+track);
		  			}
		  			else {
		  				$("."+mbid).find("label").attr("title", "love_"+artist+"_"+track);
		  			}
		   		
				});
				$('body').on('mouseover', '.love', function() {
					var action=$(this).attr("title").split("_")[0];
					if (action==="love") {
						$( this ).find( "img" ).attr("src", "pic/love.png");
					}
					else {
						$( this ).find( "img" ).attr("src", "pic/nolove.png");
					}

				});

				$('body').on('mouseout', '.love', function() {
			  		var action=$(this).attr("title").split("_")[0];
		   		if (action==="love") {
		   			if ($( this ).find( "img" ).attr("src")==="pic/love.png") {
		   				$( this ).find( "img" ).attr("src", "pic/nolove.png");
		   			}
						
					}
					else {
						if ($( this ).find( "img" ).attr("src")==="pic/nolove.png") {
							$( this ).find( "img" ).attr("src", "pic/love.png");
						}
					}
		  		});
				
				
          });
        	
      </script>
	</body>
</html>