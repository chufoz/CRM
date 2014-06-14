<?
/* ********************************************************************
 * CRM 
 * Copyright (c) 2001-2004 Hidde Fennema (hidde@it-combine.com)
 * Licensed under the GNU GPL. For full terms see http://www.gnu.org/
 *
 * This file does several things :)
 *
 * Check http://www.crm-ctt.com/ for more information
 **********************************************************************
 */
//qlog("Including fgrph.php");
function tekenGrafiek($arrX, $arrY, $aankoopArr, $AreDates, $desc, $imgWidth, $imgHeight){
	global $title, $CRM_VERSION, $in_line, $filename_to_disk_to_use;
	

	//tijden

	$minX = getMinimum($arrX);
	$maxX = getMaximum($arrX);

	//waarden
	$minY = getMinimum($arrY);
	$maxY = getMaximum($arrY);

	
	$yDiff = $maxY - $minY;

	if ($yDiff == 0){
		$yDiff = 5;
	}
	$minY -= ($yDiff / 25);
	$maxY += ($yDiff / 25);
	$maxY = sprintf("%.2f", $maxY);

	$begintijd = strtotime($minX);
	$eindtijd = strtotime($maxX);

	$tijdSpan = $eindtijd - $begintijd;

	$xSpan = sizeof($arrX);
	$ySpan = $maxY - $minY;

	if (!$in_line) {
		qlog("SENDING IMAGE PNG HEADER");
		header ("Content-type: image/png");
	}
	


	if (!$im) {
		$im = ImageCreate ($imgWidth, $imgHeight) or die ("Cannot Initialize new GD image stream");		    
	}

	$firstColor = ImageColorAllocate ($im, 255, 255, 255);		// Eerste kleur moet wit zijn.
	$lineColor = ImageColorAllocate ($im, 254, 170, 87);		// Kleur van grafiek zelf
	$lineColorshadow = ImageColorAllocate ($im, 254, 254, 254);		// Kleur van grafiek zelf

	$textColor = ImageColorAllocate ($im, 71, 72, 77);		// Kleur van de tekst
	$purColor  = ImageColorAllocate ($im, 0, 120, 0);		// Kleur van lijn aankoopprijs
	$gridColor = ImageColorAllocate ($im, 173, 184, 217);	// Kleur van het raster
	$bgColor   = ImageColorAllocate ($im, 255, 255, 255);	// Achtergrondkleur	
	$ShadowColor = ImageColorAllocate ($im, 180, 180, 180);		// Kleur van grafiek zelf
//	$ShadowColor = ImageColorAllocate ($im, 0, 0, 0);		// Kleur van grafiek zelf

	if (sizeof($arrY) < 2){
		imagerectangle($im, 0, 0, $imgWidth - 1, $imgHeight - 1, $textColor);
			imagestring($im, 5, 10, $imgHeight/2, "Not enough data to build graph", $textColor);
	}
	else{
	
		$onderrand = imagefontwidth(1) * 11;
		
		$leftoffset= imagefontwidth(1) * strlen($maxY) + 5;
		
		imagefilledrectangle($im, $leftoffset,0,$imgWidth, 20,$bgColor);	
		imagefilledrectangle($im, 0,0,$leftoffset, $imgHeight,$bgColor);
		imagefilledrectangle($im, 0,$imgHeight - $onderrand,$imgWidth, $imgHeight,$bgColor);
		imagefilledrectangle($im, $imgWidth-20,0,$imgWidth, $imgHeight,$bgColor);

		// Tekenen van de koerswaarden links naast de grafiek
		$i=0;
		$step = $ySpan / (($imgHeight - 25 - $onderrand) / 25);
		for ($y = $imgHeight - 4 - $onderrand; $y > 15; $y -= 25){
			$label = sprintf("%.2f", $minY + $step * $i);
			$label = round($label);
			if (($label<0)||($label==-0)) { $label=0;}
			imagestring($im, 1, 1, $y, $label , $textColor);
			$i++;
		}
		// Data onderaan grafiek
		$timeStamp = $begintijd;
		$step = $tijdSpan / (($imgWidth - 25 - $leftoffset) / 25);
		if ($AreDates) {
				for ($x=$leftoffset - 6; $x<=$imgWidth - 25; $x+=25){
						
					$label = strftime("%d-%m-%Y", $timeStamp);


					imagestringup($im, 1, $x+2, $imgHeight - 0, $label, $textColor);
					
					$timeStamp += $step;
		//			$timestamp = $arrY[$];
					
					if ($timeStamp > $eindtijd){
						$timeStamp = $eindtijd;
					}
				}
		} else {
			// Bottom values are not dates	
		}
		$xRes = ($imgWidth - 25 - $leftoffset) / $xSpan;
		$yRes  = ($imgHeight - 25 - $onderrand) / $ySpan;

		ImageString ($im, 2, $leftoffset, 5,  $desc, $textColor);
		ImageStringup ($im, 2, $imgWidth - imagefontheight(1) - 6, $imgHeight - $onderrand, $CRM_VERSION, $ShadowColor);

		//	Tekenen van het raster
		for ($x=$leftoffset; $x<=$imgWidth - 25; $x+=25){	// Teken verticale lijnen
			imageline($im, $x, 20, $x, $imgHeight - $onderrand, $gridColor);
		}
		for ($y=($imgHeight - $onderrand); $y>=25; $y-=25){	// Teken horizontale lijnen
			imageline($im, $leftoffset, $y, $x, $y, $gridColor);
		}
		//tekenen van omtrek
		imagerectangle($im, $leftoffset, $y, $imgWidth - 20, $imgHeight - $onderrand, $textColor);

		$labelSpacing = 25;		// + imagefontheight(1);
		$x = $leftoffset;		// Tekenen van de koersgrafiek zelf
		for ($i=0; $i<sizeof($arrX); $i++){
			$dat = strtotime($arrX[$i]);
			$dag = strftime("%a", $dat);
			// geef lijnkleur afhankelijk van dag
			
			$color = $lineColor;
			
			$val = $arrY[$i];
			$val -= $minY;
			$val *= $yRes;
			$y = $imgHeight - $val - $onderrand;

			if ($doAankoop){
				$aval = $aankoopArr[$i];
				$aval -= $minY;
				$aval *= $yRes;
				$ay = $imgHeight - $aval - $onderrand;
			}
	
			if ($x == $leftoffset){		// begin van de grafiek
				$xold = $x+10 ;
				$yold = $y+10;
				if ($doAankoop)
					$ayold = $ay;
			}
			else{
				
				imageline($im, $xold+5, $yold+3, $x+5, $y+3, $ShadowColor);	// Schaduw
				if ($y<0) { $y=0; }
				imageline($im, $xold+1, $yold, $x, $y, $color);	// koersgrafiek
			}
			$xold = $x;
			$yold = $y;
			if ($doAankoop){
				$ayold = $ay;
			}
			$x += $xRes;
		}



	}	

	if ($filename_to_disk_to_use<>"") {
		qlog("Writing to $filename_to_disk_to_use");
		ImagePng ($im,$filename_to_disk_to_use);
	} else {
		ImagePng ($im);
	}
}

function getMinimum($arr)
{	
		@sort($arr);
		return $arr[0];
}

function getMaximum($arr)
{	@rsort($arr);
	return $arr[0];    
}


?>