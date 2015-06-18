<?php
include	'functions.php';

//Authentication Script, comment the following two lines to disable authentication
//Write your password by encrypting it sha1() below, default is 'hello'
$password = 'aaf4c61ddcc5e8a2dabede0f3b482cd9aea9434d';
include	'access.php';
?>
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
    
		<!-- Balloon Scrypt -->
	    <link rel="stylesheet" href="balloon/style/add2home.css">
	    <script type="text/javascript">
	        var addToHomeConfig = {
	            touchIcon:true,
	        };
	    </script>
	    <script type="application/javascript" src="balloon/src/add2home.js"></script>
	    <!-- /Balloon Scrypt -->
    <!-- /iOS Scripts  -->
    
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  
	<!-- End JavaScript -->
    
</head>
<body>


<!-- Home -->
<div data-role="page" id="home" class="homepage-body" >
    <!-- header -->
    <div data-role="header" class="homepage-header"  >
        Welcome!
    </div>
    <!--/header -->


    <p class="logo">
    Over Monitor
    </p>
    

    <!-- Start content -->
    <div data-role="content">
        <ul data-role="listview" id="listview" data-inset="true">

            <li><a href="#status" data-transition="slidefade"><img src="img/display.png" alt="Status" class="ui-li-icon">Status</a></li>
            <li><a href="#statistics" data-transition="slidefade"><img src="img/stats.png" alt="Statistics" class="ui-li-icon">Statistics</a></li>
            <li><a href="#about" data-transition="slidefade"><img src="img/info.png" alt="About" class="ui-li-icon">About</a></li>
            <?php
            	if($_SESSION['loggedIn']){
				print('<li><a href="#logout" data-transition="slidefade"><img src="img/info.png" alt="Logout" class="ui-li-icon">Log Out</a></li>');
				}
			?>
        </ul>
		<img src="img/shadow.png" class="shadow" alt="shadow">
		
		<!-- social icons -->
        <p class="social">
            Stay Connected!<br />

            <?php
            if($om_version != $rm_version) {
                print('<br>New update available: ' . $rm_version . '<br><br>');
            }
            ?>

            <a href="http://www.overmonitor.org"><img src="img/social-icons/github.png" alt="GitHub"></a>
            <a href="mailto:drego85@draghetti.it"><img src="img/social-icons/email.png" alt="eMail"></a>


        </p>
        <!-- /social icons -->

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

<!-- Status -->
<div data-role="page" id="status">
    <!-- header -->
    <div data-theme="c" data-role="header" data-position="fixed">
        <a data-transition="slidefade" data-role="button" data-theme="a" href="#home" data-icon="home" data-iconpos="left" class="ui-btn-left">Home</a>
        <h3>
            Server Status
        </h3>
    </div>
    <!--/header -->

    <!-- Start content -->
    <div data-role="content">
        <div data-role="collapsible-set">
            <div data-role="collapsible">
                <h3>
                    Sistem Info
                </h3>
                <p>
                    <?php
                    print ('' . $aStats['distro']  . '<br><br>');
                    print ('' . $aStats['kernel']  . '<br><br>');
                    print ('' . $aStats['cpu'] . ' ' . $aStats['cores'] . ' Core' . '<br><br>');
                    print('PHP ' . $aStats['php'] . ' - ' . $aStats['apache'] . '<br>');
                    ?>
                </p>
            </div>
            <div data-role="collapsible">
                <h3>
                    Uptime
                </h3>
                <p>
                    <?php
                    print ('<strong>' . $days . '</strong> days, <strong>' . $hours . '</strong> hours and <strong>' . $minutes . '</strong> minutes' . '<br>');
                    ?>
                </p>
            </div>
            <div data-role="collapsible" data-collapsed="false">
                <h3>
                    Load Average
                </h3>
                <p>
                    <?php
                    print ('<strong>1 min:</strong> ' . $load[0] . '<br>');
                    print ('<strong>5 min:</strong> ' . $load[1] . '<br>');
                    print ('<strong>10 min:</strong> ' . $load[2] . '<br>');
                    ?>
                </p>
            </div>
            <div data-role="collapsible">
                <h3>
                    Memory
                </h3>
                <p>
                    <?php
                    print ('<strong>Total:</strong> ' . intval($aStats['total_memory']/1024) . 'Mb' . '<br>');
                    print ('<strong>Active:</strong> ' . intval(($aStats['total_memory']-$aStats['free_memory'])/1024) . 'Mb' . '<br>');
                    print ('<strong>Free:</strong> ' . intval($aStats['free_memory']/1024) . 'Mb' . '<br>');
                    ?>
                </p>
            </div>
            <div data-role="collapsible">
                <h3>
                    Partitions
                </h3>
                <p>
                    <?php
                    $totalhd = count($aStats['hd']);
                    for ($actualhd = 0; $actualhd < $totalhd; ++$actualhd) {
                        print ('<p class="partitions">');
                        print ('<strong>' . $aStats['hd'][$actualhd][dev] . '</strong>' .'<br>');
                        print (intval($aStats['hd'][$actualhd][free_perc]) . '% of free space' . '<br>');
                        if ($aStats['hd'][$actualhd][used] < 1048576){
                        	print (round(intval($aStats['hd'][$actualhd][used]/1024,2)) . 'Mb used up ' . intval($aStats['hd'][$actualhd][total]/1024) . 'Mb'  . '<br>');
                        } else {
                        	print ((round(intval($aStats['hd'][$actualhd][used]/1024)/1024,2)) . 'Tb used up ' . intval(($aStats['hd'][$actualhd][total]/1024)/1024) . 'Tb'  . '<br>');
                        }
                        print ('</p>');
                    }
                    ?>
                </p>
            </div>
            <div data-role="collapsible">
                <h3>
                    Traffic Meter
                </h3>
                <p>
                    <?php
                    print ('<strong>Received:</strong> ' . round(((($aStats['net_rx'] / 1024) / 1024) / 1024),3 ) . 'Gb*' . '<br>');
                    print ('<strong>Sent:</strong>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp ' . round(((($aStats['net_tx'] / 1024) / 1024) / 1024),3 ) . 'Gb*' . '<br>');
                    ?>
                    <br>
                    *traffic since last server restart.
                </p>
            </div>
        </div>
    </div>
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

<!-- Statistics -->
<div data-role="page" id="statistics">
    <!-- header -->
    <div data-theme="c" data-role="header" data-position="fixed">
        <a data-transition="slidefade" data-role="button" data-theme="a" href="#home" data-icon="home" data-iconpos="left" class="ui-btn-left">Home</a>
        <h3>
            Statistics
        </h3>
    </div>
    <!-- Start content -->
    <div data-role="content">

        <h3>Load Average</h3>

        <!-- Load Average Chart -->
        <div class="box la-page"></div>
        <!-- /banner image -->

        <h3>Memory</h3>

        <!-- Memory Chart -->
        <div class="box memory-page"></div>
        <!-- / Memory Chart -->

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

<!-- About -->
<div data-role="page" id="about">
    <!-- header -->
    <div data-theme="c" data-role="header" data-position="fixed">
        <a data-transition="slidefade" data-role="button" data-theme="a" href="#home" data-icon="home" data-iconpos="left" class="ui-btn-left">Home</a>
        <h3>
            About
        </h3>
    </div>
    <!-- Start content -->
    <div data-role="content">
        <p><strong>Over Monitor</strong> is a Web App for Smartphone that can check the status of your server. The application was created to monitor your server even when you not have of your PC, with a smartphone you can check the status of the server. </p>

        <h3>Features:</h3>
        <ul>
            <li>System Information (Distribution, Kernel and CPU)</li>
            <li>Uptime</li>
            <li>Load Average</li>
            <li>Memory (Total, Freed, Used)</li>
            <li>Partitions</li>
            <li>Traffic Meter</li>
        </ul>

        Also every day generate the chart with Load Average and Memory, you can see the graph of the day in the "Statistics" section and graphs passed in the folder "/chart".

        <h3>Credits:</h3>

        Andrea Draghetti is the creator of the project, I want thank:

        <ul>
            <li>Simone Margaritelli to support for coding;</li>
            <li>Matteo Spinelli (cubiq.org) for floating balloon.</li>
        </ul>


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

<!-- Logout -->
<div data-role="page" id="logout">
    <!-- header -->
    <div data-theme="c" data-role="header" data-position="fixed">
        <h3>
            Logout
        </h3>
    </div>
    <!-- Start content -->
    <div data-role="content">
        <p>You have <strong>logged out!</strong></p>

       <?php
   		 	session_start();
    		$_SESSION['loggedIn'] = false;
	
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
