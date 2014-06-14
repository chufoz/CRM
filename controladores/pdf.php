<?php
require('fpdf/fpdf.php');
//*************************************************************************************************
class PDF extends FPDF
{
var $widths;
var $aligns;
var $pos;
var $heads;
var $headsize;
var $showhead;
var $tborder;
var $header;
var $cellheight;
var $headalign;
var $font="Helvetica";

var $fillcolor1=array('r'=>'235','g'=>'235','b'=>'235');
var $fillcolor2=array('r'=>'255','g'=>'255','b'=>'255');

function SetWidths($w){
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
	elseif(is_numeric($p)){
	$this->pos=$p;
	}
}

function SetAligns($a){
    //Set the array of column alignments
    $this->aligns=$a;
}

function SetHeads($a,$s,$sh,$tb){
    //Set the headers of table columns
    $this->heads=$a;
    $this->headsize=$s;
    $this->showhead=$sh;
    $this->tborder=$tb;
}

function Row($data,$fill,$border){
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=$this->cellheight*$nb;

    //Issue a page break first if needed
    //$this->CheckPageBreak($h);
    if($this->GetY()+$h>$this->PageBreakTrigger){
	    if ($border=='LR'){
		    $this->SetX($this->pos);
		    $this->Line($this->GetX(),$this->GetY(),$this->GetX()+array_sum($this->widths),$this->GetY());
		    if ($this->showhead==0){
			    $border.="T";
		    }
	    }
	    $this->AddPage($this->CurOrientation);
	    $this->TableHeader();
    }

    //set table alignment
    $this->SetX($this->pos);
    //set table borders
    if ($fill==1){
	if (isset($this->fillcolor1)){
	    $this->SetFillColor($this->fillcolor1['r'], $this->fillcolor1['g'], $this->fillcolor1['b']);
	}
	else{
	    $this->SetFillColor(235,235,235);
	}
    }
    else{
	if (isset($this->fillcolor2)){
	    $this->SetFillColor($this->fillcolor2['r'], $this->fillcolor2['g'], $this->fillcolor2['b']);
	}
	else{
	    $this->SetFillColor(255,255,255);
	}
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
        $this->MultiCell($w,$this->cellheight,$data[$i],0,$a,1);
		
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

function CheckPageBreak($h){
    //If the height h would cause an overflow, add a new page immediately
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}

function NbLines($w,$txt){
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

function TableHeader(){
	if ($this->showhead==1){
		$ca=$this->aligns;
		$cf=$this->CurrentFont;
		$ha = isset($this->headalign) ? $this->headalign : 'C';
		$a=array_fill(0,count($this->heads),$ha);
		$this->SetAligns($a);
		$this->SetFont($this->font,'B',$this->headsize);
		$fill=$this->tborder==3 ? 1 : 0;
		$border = $this->tborder != 0 ? '1' : '0';
		$this->Row($this->heads,$fill,$border);
		$this->SetAligns($ca);
		$this->SetFont($this->font,'',$this->headsize);
		if ($this->tborder!=1){
			$this->Ln(0.2);
		}
		unset($this->headalign);
	}
}

//Cabecera de pagina
function Header(){
    if ($this->header==false){
	return;
    }
    
    if(PHP_OS=="WINNT"){
	setlocale (LC_TIME,"esm");
    }
    else{
	setlocale (LC_TIME,"es_MX","es_ES");
	$pmam = strtoupper(date("a"));
    }
    $datenow = strtoupper(strftime("%A %d/%b/%Y,  %I:%M %p  "));//date("D d M,Y h:i a");

    $this->Line(10,10,$this->w-10,10);

    if ($this->pageno()==1){
	$img="logo.jpg";
	$this->Line(10,22,$this->w-10,22);
	//$this->Image($img1,10,12,50,15);
	if (file_exists($img)){
	    $this->Image($img,10,11,0,10);
	}
	$this->SetFont($this->font,'',8);
	$this->Ln(2);
	$this->MultiCell(0,5,"\n"."Nombre de la empresa",0,'R');
	
	$this->Ln(6);
	$this->SetFont($this->font,'',8);
	$this->MultiCell(0,5,$datenow.$pmam,0,'R');
	$this->SetFont($this->font,'',10);
	
	$this->Ln(5);
    }
}

//Pie de pagina
function Footer(){
    if ($this->header==false){
	return;
    }
    //Posici&oacute;n: a 1,5 cm del final
    $this->SetY(-15);
    //foot line
    $this->Line(10,$this->h-12,$this->w-10,$this->h-12);
    //Arial italic 8
    $this->SetFont($this->font,'',8);
    //N&uacute;mero de p&aacute;gina
    $this->Cell(0,10,$this->PageNo().' de {nb}',0,0,'R');
}

/**
 * Metodo de la clase para desplegar texto
 * @param string $text
 * @param int $size
 * @param string $align
 */
function texto($text,$size,$align){
    $this->SetFont($this->font,'',$size);
    $this->MultiCell(0,$this->cellheight,$text,0,$align);
}

/**
 * Genera tabla pasando datos
 * @param array $data Datos de la tabla
 * @param string $tit Titulo de la tabla
 * @param int $size_tit Tama&ntilde;o de la fuente del titulo
 * @param array $align Alineacion de columnas 'C','R','J'
 * @param int $size Tama&ntilde;o de la fuente de los registros
 * @param bool $showhead Mostrar encabezados (0,1)
 * @param array $width Ancho de las columnas
 * @param int $border Bordes de la tabla <br>
 *              0.- Sin bordes <br>
 *              1.- Bordes sin sombreado <br>
 *              2.- Solo bordes laterales, con sombreado intercalado <br>
 *              3.- Solo bordes laterales
 * @param array $pos Posicion de la tabla ('C','R','L')
 * @param int $sizehead Tama&ntildeo del encabezado;
 */
function tabla($data,$tit,$size_tit,$align,$size,$showhead,$width,$border,$pos,$sizehead=''){
    $num_fields=count($data[0]);

    $this->SetWidths($width);
    $this->SetPos($pos);
    $this->SetAligns($align);
    $this->SetFont('Helvetica','',$size);

    $head=array_keys($data[0]);
    $sh = isset($sizehead) ? $sizehead : $size;
    $this->SetHeads($head,$sh,$showhead,$border);

    //Titulo de la tabla
    if ($tit!=""){
	$this->SetX($this->pos);
	$this->SetFont('Helvetica','B',$size_tit);
	$this->MultiCell(array_sum($width),$this->cellheight,$tit,0,'C');
	$this->SetFont('Helvetica','',$size);
    }

    //Encabezados de la tabla
    $this->TableHeader();
    $this->SetFont('Helvetica','',$size);

    //registros
    $fill=0;
    $i=0;
    foreach($data as $row){
	if ($border==0){
	    $bordes='0';
	    $fill=0;
	}
	elseif($border==1){
	    $bordes='1';
	    $fill=0;
	}
	elseif($border==2){
	    if ($i==0 and $showhead==0){
		$bordes='LRT';
	    }
	    else{
		$bordes='LR';
	    }
	    $fill=!$fill;
	}
	elseif($border==3){
	    if ($i==0 and $showhead==0){
		$bordes='LRT';
	    }
	    else{
		$bordes='LR';
	    }
	    $fill=0;
	}
	$this->Row(array_values($row),$fill,$bordes);
	$i++;
    }

    if ($border!=0){
	$this->SetX($this->pos);
	$x=$this->GetX();
	$y=$this->GetY();
	$w=array_sum($width);
	$this->Ln(0.2);
	$this->Line($x,$y,$x+$w,$y);
	$this->Ln(0.2);
    }
}

function hr($x1, $x2){
    $this->SetDrawColor(73, 104, 134);
    $this->SetLineWidth(0.3);
    $y = $this->GetY();
    $this->Line($x1,$y,$x2,$y);
    $this->SetDrawColor(0, 0, 0);
    $this->SetLineWidth(0.2);
}


}
//*************************************************************************************************
function plantilla(){
    $pdf=new PDF('P','mm','Letter');
    $pdf->header=true;
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->font="Helvetica";
    $pdf->SetMargins(10,15,10);
    $pdf->cellheight=3.5;
    return $pdf;
}
?>
