<?php
session_start();
$error_login = false;

if (!isset($_SESSION['loggedIn'])) {
    $_SESSION['loggedIn'] = false;
}

if (isset($_POST['password'])) {
    if (sha1($_POST['password']) == $password) {
        $_SESSION['loggedIn'] = true;
        $error_login = false;
    } else {
       	$error_login = true; 
    }
} 

if (!$_SESSION['loggedIn']): ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Over Monitor</title>
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.css" />
    <link rel="stylesheet" href="css/style.css" />
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>

    <!-- iOS Scripts -->
	<meta name="apple-mobile-web-app-title" content="Over Monitor">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0, width=device-width" />
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=2.0" media="(device-height: 568px)" />
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png"/>
    <!-- /iOS Scripts  -->
    
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    
</head>
<body>

<div data-role="page" id="login" class="access-body" >


    <p class="logo">
    Over Monitor
    </p>

    <!-- Start content -->
    <div data-role="content">
	<?php
	if($error_login){
			print('<p align="center">Wrong password, try again</p>');
		}else
		{
			print('<p align="center">You need to login</p>');
		}
	
	?>
	   	
    	<form method="post" data-ajax="false">
      			<input type="password" name="password" id="password" value="" /> <br>
      			<input type="submit" name="submit" value="Login">
    	</form>

	<?php
	if($password == "aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d"){
			print('<p align="center">Default password is: <i>hello</i> if you want to modify or disable view the README file.</p>');
		}
	?>
	
	
    </div>
    <!-- end content -->

    <!-- footer -->
    <div data-theme="a" data-role="footer" data-position="fixed">
        <p class="copyright">Powered by <a href="http://www.andreadraghetti.it/">Andrea Draghetti</a>
            <br>
            <?php
            print('Version: ' . $om_version);
            ?>
        </p>
    </div>
    <!-- /footer -->

</div>
<!-- end page -->
</body>
</html>

<?php
exit();
endif;
?>