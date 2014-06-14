<?php
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
define('FPDF_FONTPATH','./');
require_once('fpdf.php');

class PDF extends FPDF
{
//*****************************************************
var $widths;
var $aligns;
var $pos;

function NbLines($w,$txt)
{
    //Computes the number of lines a MultiCell of width w will take
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}

function CheckPageBreak($h)
{
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function Row($data,$fill,$border)
{
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;

	//Issue a page break first if needed
    //$this->CheckPageBreak($h);
	if($this->GetY()+$h>$this->PageBreakTrigger){
		if ($border=='LR'){
			$this->SetX($this->pos);
			$this->Line($this->GetX(),$this->GetY(),$this->GetX()+array_sum($this->widths),$this->GetY());
		}
		$this->AddPage($this->CurOrientation);
	}
	
	//set table alignment
	$this->SetX($this->pos);
	//set table borders
	if ($fill==1){
	$this->SetFillColor(235,235,235);
	}
	else{
	$this->SetFillColor(255,255,255);
	}

	//Draw the cells of the row
    for($i=0;$i<count($data);$i++){
		//row number lines
		$wc=$this->Nblines($this->widths[$i],$data[$i]);
		//$data fill
		if ($wc < $nb){
			for ($k=1;$k<=$nb;$k++){
				$data[$i].="\n";
			}
		}
	
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a,1);
		
		//Draw the border
		if ($border==1){
        $this->Rect($x,$y,$w,$h);
		}
		else{
			for ($j = 0; $j <= strlen($border); $j++){
				if (substr($border,$j,1)=='L'){
					$this->Line($x,$y,$x,$y+$h);
				}
				elseif (substr($border,$j,1)=='R'){
					$this->Line($x+$w,$y,$x+$w,$y+$h);
				}
				elseif (substr($border,$j,1)=='T'){
					$this->Line($x,$y,$x+$w,$y);
				}
				elseif (substr($border,$j,1)=='B'){
					$this->Line($x,$y+$h,$x+$w,$y+$h);
				}
			}
		}
		
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
}

function SetWidths($w)
{
    //Set the array of column widths
    $this->widths=$w;
}

function SetPos($p){
		$x=$this->w;
	
	if ($p=='C'){
	$this->pos=($x-array_sum($this->widths))/2;
	}
	elseif($p=='L'){
	$this->pos=10;
	}
	elseif($p=='R'){
	$this->pos=($x-array_sum($this->widths))-10;
	}
}

function SetAligns($a)
{
    //Set the array of column alignments
    $this->aligns=$a;
}
//****************************************************************
//Page header
function Header()
{
	global $title,$session,$e,$lang,$title_link;
	
	
	//Logo
    //$this->Image('crm.png',10,8,10,'','','http://www.crm-ctt.com');
	$this->Ln(10);
	$this->Image('crm.jpg',10,8,30,8,'JPG','http://www.crm-ctt.com');
    //Arial bold 15
    //$this->Ln(15);
    $this->SetFont('Arial','',8);
    //Move to the right
    //$this->Cell(20);
    //Title
    $this->Cell(0,10,$title,0,0,'L',0,$title_link);
    //Line break
    //$this->Ln(20);
	$this->Ln(8);
}
function PutLink($URL,$txt)
{
    //Put a hyperlink
    $this->SetTextColor(0,0,255);
    $this->SetStyle('U',true);
    $this->Write(5,$txt,$URL);
    $this->SetStyle('U',false);
    $this->SetTextColor(0);
}
function SetStyle($tag,$enable)
{
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B','I','U') as $s)
        if($this->$s>0)
            $style.=$s;
    $this->SetFont('',$style);
}
//Page footer
function Footer()
{
    //Position at 1.5 cm from bottom
    $this->SetY(-15);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
    //Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

	var $outlines=array();
	var $OutlineRoot;

	function Bookmark($txt,$level=0,$y=0)
	{
		if($y==-1)
			$y=$this->GetY();
		$this->outlines[]=array('t'=>$txt,'l'=>$level,'y'=>$y,'p'=>$this->PageNo());
	}

	function _putbookmarks()
	{
		$nb=count($this->outlines);
		if($nb==0)
			return;
		$lru=array();
		$level=0;
		foreach($this->outlines as $i=>$o)
		{
			if($o['l']>0)
			{
				$parent=$lru[$o['l']-1];
				//Set parent and last pointers
				$this->outlines[$i]['parent']=$parent;
				$this->outlines[$parent]['last']=$i;
				if($o['l']>$level)
				{
					//Level increasing: set first pointer
					$this->outlines[$parent]['first']=$i;
				}
			}
			else
				$this->outlines[$i]['parent']=$nb;
			if($o['l']<=$level and $i>0)
			{
				//Set prev and next pointers
				$prev=$lru[$o['l']];
				$this->outlines[$prev]['next']=$i;
				$this->outlines[$i]['prev']=$prev;
			}
			$lru[$o['l']]=$i;
			$level=$o['l'];
		}
		//Outline items
		$n=$this->n+1;
		foreach($this->outlines as $i=>$o)
		{
			$this->_newobj();
			$this->_out('<</Title '.$this->_textstring($o['t']));
			$this->_out('/Parent '.($n+$o['parent']).' 0 R');
			if(isset($o['prev']))
				$this->_out('/Prev '.($n+$o['prev']).' 0 R');
			if(isset($o['next']))
				$this->_out('/Next '.($n+$o['next']).' 0 R');
			if(isset($o['first']))
				$this->_out('/First '.($n+$o['first']).' 0 R');
			if(isset($o['last']))
				$this->_out('/Last '.($n+$o['last']).' 0 R');
			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]',1+2*$o['p'],($this->h-$o['y'])*$this->k));
			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root
		$this->_newobj();
		$this->OutlineRoot=$this->n;
		$this->_out('<</Type /Outlines /First '.$n.' 0 R');
		$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
		$this->_out('endobj');
	}

	function _putresources()
	{
		parent::_putresources();
		$this->_putbookmarks();
	}

	function _putcatalog()
	{
		parent::_putcatalog();
		if(count($this->outlines)>0)
		{
			$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
			$this->_out('/PageMode /UseOutlines');
		}
	}
	function CreateIndex(){
	//Index title
	$this->SetFontSize(20);
	$this->Cell(0,5,'Index',0,1,'C');
	$this->SetFontSize(15);
	$this->Ln(10);

	$size=sizeof($this->outlines);
	$PageCellSize=$this->GetStringWidth('p. '.$this->outlines[$size-1]['p'])+2;
	for ($i=0;$i<$size;$i++){
		//Offset
		$level=$this->outlines[$i]['l'];
		if($level>0)
			$this->Cell($level*8);

		//Caption
		$str=$this->outlines[$i]['t'];
		$strsize=$this->GetStringWidth($str);
		$avail_size=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-4;
		while ($strsize>=$avail_size){
			$str=substr($str,0,-1);
			$strsize=$this->GetStringWidth($str);
		}
		$this->Cell($strsize+2,$this->FontSize+2,$str);

		//Filling dots
		$w=$this->w-$this->lMargin-$this->rMargin-$PageCellSize-($level*8)-($strsize+2);
		$nb=$w/$this->GetStringWidth('.');
		$dots=str_repeat('.',$nb);
		$this->Cell($w,$this->FontSize+2,$dots,0,0,'R');

		//Page number
		$this->Cell($PageCellSize,$this->FontSize+2,'p. '.$this->outlines[$i]['p'],0,1,'R');
	}
	}
}

function hex2rgb($hex) {
    preg_replace('/([0-9a-f]{2})/ie', '$rgb[] = hexdec(\1)', $hex);
    return $rgb;
}
function line() {
		global $pdf;
	    $pdf->SetFillColor(128,0,0);
		$pdf->Cell(0,.2,"",0,1,'L',1);
		$pdf->Ln(1);
}


?>
