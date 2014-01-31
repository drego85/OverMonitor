<?php

error_reporting(0);

//Check Version
$om_version = '1.2';
$rm_version = file_get_contents('http://www.overmonitor.org/version.txt');

$aStats = array();

$sDistroName = '';
$sDistroVer  = '';

// OS, Kernel and CPU
foreach (glob("/etc/*_version") as $filename) 
{
    list( $sDistroName, $dummy ) = explode( '_', basename($filename) );

    $sDistroName = ucfirst($sDistroName);
    $sDistroVer  = trim( file_get_contents($filename) );
    
    $aStats['distro'] = "$sDistroName $sDistroVer";
    break;
}

if( !$aStats['distro'] )
{
    if( file_exists( '/etc/issue' ) )
    {
        $lines = file('/etc/issue');
        $aStats['distro'] = trim( $lines[0] );
    }
    else
    {
        $output = NULL;
        exec( "uname -om", $output );
        $aStats['distro'] = trim( implode( ' ', $output ) );
    }
}

$cpu = file( '/proc/cpuinfo' );
$vendor = NULL;
$model = NULL;
$cores = 0;

foreach( $cpu as $line )
{
    if( preg_match( '/^vendor_id\s*:\s*(.+)$/i', $line, $m ) )
    {
        $vendor = $m[1];
    }
    else if( preg_match( '/^model\s+name\s*:\s*(.+)$/i', $line, $m ) )
    {
        $model = $m[1];
    }
    else if( preg_match( '/^processor\s*:\s*\d+$/i', $line ) )
    {
        $cores++;
    }
}

$aStats['cpu']    = "$vendor, $model";
$aStats['cores']  = $cores;
$aStats['kernel'] = trim(file_get_contents("/proc/version"));

$aStats['php'] = phpversion();
$aStats['apache'] = $_SERVER['SERVER_SOFTWARE'];


//Uptime
$aStats['uptime'] = trim( file_get_contents("/proc/uptime") );

//Load Average

$load = file_get_contents("/proc/loadavg");
$load = explode( ' ', $load );

$aStats['load'] = $load[0].', '.$load[1].', '.$load[2];

//Memory

$memory = file( '/proc/meminfo' );
foreach( $memory as $line )
{
    $line = trim($line);
    
    if( preg_match( '/^memtotal[^\d]+(\d+)[^\d]+$/i', $line, $m ) )
    {
        $aStats['total_memory'] = $m[1];
    }
    else if( preg_match( '/^memfree[^\d]+(\d+)[^\d]+$/i', $line, $m ) )
    {
        $aStats['free_memory'] = $m[1];
    }
}

//Partitions

$aStats['hd'] = array();

foreach( file('/proc/mounts') as $mount )
{
    $mount = trim($mount);
    if( $mount && $mount[0] == '/' )
    {
        $parts = explode( ' ', $mount );
        if( $parts[0] != $parts[1] )
        {
            $device = $parts[0];
            $folder = $parts[1];
            $total  = disk_total_space($folder) / 1024;
            $free   = disk_free_space($folder) / 1024;

            if( $total > 0 )
            {
                $used   = $total - $free;
                $used_perc = ( $used * 100.0 ) / $total;
                $free_perc =  ( $free * 100.0 ) / $total;

                $aStats['hd'][] = array
                (
                    'dev' => $device,
                    'total' => $total,
                    'used' => $used,
                    'free' => $free,
                    'used_perc' => $used_perc,
                    'free_perc' =>  $free_perc,
                    'mount' => $folder
                );
            }
        }
    }
}

//Traffic Meter


$ifname = NULL;

if( file_exists('/etc/network/interfaces') )
{
    foreach( file('/etc/network/interfaces') as $line )
    {
        $line = trim($line);

        if( preg_match( '/^iface\s+([^\s]+)\s+inet\s+.+$/', $line, $m ) && $m[1] != 'lo' )
        {
            $ifname = $m[1];
            break;
        }
    }
}
else
{
    foreach( glob('/sys/class/net/*') as $filename )
    {
        if( $filename != '/sys/class/net/lo' && file_exists( "$filename/statistics/rx_bytes" ) && trim( file_get_contents("$filename/statistics/rx_bytes") ) != '0' )
		{
			$parts = explode( '/', $filename );
            $ifname = array_pop( $parts );
        }
    }
}



if( $ifname != NULL )
{
    $aStats['net_rx'] = trim( file_get_contents("/sys/class/net/$ifname/statistics/rx_bytes") );
    $aStats['net_tx'] = trim( file_get_contents("/sys/class/net/$ifname/statistics/tx_bytes") );
}
else
{
    $aStats['net_rx'] = 0;
    $aStats['net_tx'] = 0;
}

$uptime = $aStats['uptime'];
$uptime = explode(" ", $uptime);
$idletime=$uptime[1];
$uptime=$uptime[0];

//Uptime Day Calculate
$day=86400;
$days=floor($uptime/$day);
$utdelta=$uptime-($days*$day);

//Uptime Hour Calculate
$hour=3600;
$hours=floor($utdelta/$hour);
$utdelta-=$hours*$hour;

//Uptime Minute Calculate
$minute=60;
$minutes=floor($utdelta/$minute);

//Uptime Seconds Calculate
$utdelta-=round($minutes*$minute,2);

//Log for Memory and Load Average
$actual_time = time();
$actual_day = date('Y.m.d', $actual_time);
$actual_day_chart = date('d/m/y', $actual_time);
$actual_hour = date('H:i:s', $actual_time);
$yesterday = date("Y.m.d", strtotime("yesterday"));

$myFile = "log/log_daily.txt";
$fh = fopen($myFile, 'r');
$theData = fread($fh, 10);
fclose($fh);

//Create a new log file at the new the day
if($theData == $actual_day)
{
	$myFile = "log/log_daily.txt";
	$fh = fopen($myFile, 'a+');
	$stringData = $actual_day . ' ' . $actual_hour . ' ' . $load[0] . ' ' . $load[1] . ' ' . $load[2] . ' ' . intval(($aStats['total_memory']-$aStats['free_memory'])/1024) . "\r\n";
	fwrite($fh, $stringData);
	fclose($fh);
}
else
{

	rename("chart/chart_la.png",  "chart/" . $yesterday . "_chart_la.png");
   	rename("chart/chart_memory.png",  "chart/" . $yesterday . "_chart_memory.png");
   	
	$myFile = "log/log_daily.txt";
	$fh = fopen($myFile, 'w');
	$stringData = $actual_day . ' ' . $actual_hour . ' ' . $load[0] . ' ' . $load[1] . ' ' . $load[2] . ' ' . intval(($aStats['total_memory']-$aStats['free_memory'])/1024) . "\r\n";
	fwrite($fh, $stringData);
	fclose($fh);
}

// Read Log for Chart
$lines_chart = file('log/log_daily.txt');
$la1_chart = array();
$la5_chart = array();
$la10_chart = array();
$time_chart = array();
$memory_chart = array();

foreach ($lines_chart as $line)
{
	$sector = explode(" ", $line);

	$time_chart[] = substr($sector[1], 0, -3);
	$la1_chart[] = $sector[2];
	$la5_chart[] =  $sector[3];
	$la10_chart[] =  $sector[4];
	$memory_chart[] = ($sector[5]/1024);
}
 

//Libraries pChart
include("class/pData.class.php");
include("class/pDraw.class.php");
include("class/pImage.class.php");


//Chart Load Average
$Data_ChartLA = new pData();
$Data_ChartLA->addPoints($la1_chart,"Serie1");
$Data_ChartLA->setSerieDescription("Serie1","1 min");
$Data_ChartLA->setSerieOnAxis("Serie1",0);

$Data_ChartLA->addPoints($la5_chart,"Serie2");
$Data_ChartLA->setSerieDescription("Serie2","5 min");
$Data_ChartLA->setSerieOnAxis("Serie2",0);

$Data_ChartLA->addPoints($la10_chart,"Serie3");
$Data_ChartLA->setSerieDescription("Serie3","10 min");
$Data_ChartLA->setSerieOnAxis("Serie3",0);

$Data_ChartLA->addPoints($time_chart,"Absissa");
$Data_ChartLA->setAbscissa("Absissa");

$Data_ChartLA->setAxisPosition(0,AXIS_POSITION_LEFT);
$Data_ChartLA->setAxisName(0,"");
$Data_ChartLA->setAxisUnit(0,"");

$Picture_ChartLA = new pImage(400,230,$Data_ChartLA,TRUE);
$Picture_ChartLA->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

$Picture_ChartLA->setFontProperties(array("FontName"=>"fonts/GeosansLight.ttf","FontSize"=>14));
$TextSettings = array("Align"=>TEXT_ALIGN_BOTTOMLEFT
, "R"=>0, "G"=>0, "B"=>0);
$Picture_ChartLA->drawText(15,30,"Load Average " . $actual_day_chart,$TextSettings);

$Picture_ChartLA->setShadow(FALSE);
$Picture_ChartLA->setGraphArea(20,40,380,200);
$Picture_ChartLA->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"fonts/pf_arma_five.ttf","FontSize"=>6));

$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
, "Mode"=>SCALE_MODE_FLOATING
, "LabelingMethod"=>LABELING_ALL
, "GridR"=>255, "GridG"=>255, "GridB"=>255, "LabelSkip"=>15, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>0, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>NONE);
$Picture_ChartLA->drawScale($Settings);

$Picture_ChartLA->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

$Config = "";
$Picture_ChartLA->drawSplineChart($Config);

$Config = array("R"=>0, "G"=>0, "B"=>0, "Alpha"=>50, "AxisID"=>0, "Ticks"=>4, "Caption"=>"Threshold");
$Picture_ChartLA->drawThreshold(1,$Config);

$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
, "Mode"=>LEGEND_HORIZONTAL
);
$Picture_ChartLA->drawLegend(275,16,$Config);
$Picture_ChartLA->Render("chart/chart_la.png");

//Chart Memory

$Data_ChartRAM = new pData();
$Data_ChartRAM->addPoints($memory_chart,"Serie1");
$Data_ChartRAM->setSerieDescription("Serie1","Memory");
$Data_ChartRAM->setSerieOnAxis("Serie1",0);


$Data_ChartRAM->addPoints($time_chart,"Absissa");
$Data_ChartRAM->setAbscissa("Absissa");

$Data_ChartRAM->setAxisPosition(0,AXIS_POSITION_LEFT);
$Data_ChartRAM->setAxisName(0,"");
$Data_ChartRAM->setAxisUnit(0,"");

$Picture_ChartRAM = new pImage(400,230,$Data_ChartRAM,TRUE);
$Picture_ChartRAM->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));

$Picture_ChartRAM->setFontProperties(array("FontName"=>"fonts/GeosansLight.ttf","FontSize"=>14));
$TextSettings = array("Align"=>TEXT_ALIGN_BOTTOMLEFT
, "R"=>0, "G"=>0, "B"=>0);
$Picture_ChartRAM->drawText(15,30,"Memory " . $actual_day_chart,$TextSettings);

$Picture_ChartRAM->setShadow(FALSE);
$Picture_ChartRAM->setGraphArea(30,40,380,200);
$Picture_ChartRAM->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"fonts/pf_arma_five.ttf","FontSize"=>6));

$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
, "Mode"=>SCALE_MODE_FLOATING
, "LabelingMethod"=>LABELING_ALL
, "GridR"=>255, "GridG"=>255, "GridB"=>255, "LabelSkip"=>15, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>0, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>NONE);
$Picture_ChartRAM->drawScale($Settings);

$Picture_ChartRAM->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));

$Config = "";
$Picture_ChartRAM->drawSplineChart($Config);

$Config = array("R"=>0, "G"=>0, "B"=>0, "Alpha"=>50, "AxisID"=>0, "Ticks"=>4, "Caption"=>"Threshold");
$Picture_ChartRAM->drawThreshold(1,$Config);

$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>6, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
, "Mode"=>LEGEND_HORIZONTAL
);
$Picture_ChartRAM->drawLegend(275,16,$Config);
$Picture_ChartRAM->Render("chart/chart_memory.png");




?>

