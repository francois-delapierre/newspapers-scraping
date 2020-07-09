<?php

require("tFPDF/tfpdf.php");
require("SimpleHTMLDom/simple_html_dom.php");


$articles = [];

if(isset($_POST['editeur']) AND isset($_POST['date']) AND isset($_POST['urls']))
{
  if($_POST['editeur']!="" AND $_POST['date']!="" AND $_POST['urls']!="")
  {
  $editeur = $_POST['editeur'];
  $date = $_POST["date"];

  $nom_date = "$editeur du $date";

  $articles = $_POST['urls'];
  $articles = preg_split("/[\s,]+/", $articles);




class PDF extends tFPDF
{

  protected $col = 0; // Colonne courante
  protected $y0;      // Ordonnée du début des colonnes



function Header()
{
    global $nom_date;
    // Arial gras 15
    $this->SetFont('Arial','B',8);
    // Calcul de la largeur du titre et positionnement
    $w = $this->GetStringWidth($nom_date)+6;
    $this->SetX((210-$w));
    $this->Cell(0,10,$nom_date);
        // Couleurs du cadre, du fond et du texte
    $this->SetDrawColor(0,80,180);
    $this->SetFillColor(230,230,0);
    $this->SetTextColor(220,50,50);
    // Epaisseur du cadre (1 mm)
    $this->SetLineWidth(1);
    // Titre
    //$this->Cell($w,9,$journal,1,1,'C',true);

    $image="https://www.gnakrylive.com/images/headers/logo-gnakrylive.png";
    $size = getimagesize($image);
    $largeur=$size[0];
    $hauteur=$size[1];
    $ratio=20/$hauteur;	//hauteur imposée de 120mm
    $newlargeur=$largeur*$ratio;
    $posi=(210-$newlargeur)/2;	//210mm = largeur de page
    $this->Image($image, $posi, 5, 0,20);

    // Saut de ligne
    $this->Ln(20);

    // Sauvegarde de l'ordonnée
    $this->y0 = $this->GetY();

}

function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Arial italique 8
    $this->SetFont('Arial','I',8);
    // Couleur du texte en gris
    $this->SetTextColor(128);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
}



function SetCol($col)
{
    // Positionnement sur une colonne
    $this->col = $col;
    $x = 10+$col*65;
    $this->SetLeftMargin($x);
    $this->SetX($x);
}

function AcceptPageBreak()
{
    // Méthode autorisant ou non le saut de page automatique
    if($this->col<2)
    {
        // Passage à la colonne suivante
        $this->SetCol($this->col+1);
        // Ordonnée en haut
        $this->SetY($this->y0);
        // On reste sur la page
        return false;
    }
    else
    {
        // Retour en première colonne
        $this->SetCol(0);
        // Saut de page
        return true;
    }
}



function TitreArticle($num, $libelle)
{
  // Add a Unicode font (uses UTF-8)
    $this->AddFont('AverBold','','AverBold.ttf',true);
    $this->SetFont('AverBold','',14);
    // Couleur de fond
    $this->SetFillColor(112,173,71);
    // Titre
    $this->Cell(0,2,"",0,1,'L',true);
    $this->MultiCell(0,6,$libelle,0,"C");


    $this->Cell(0,2,"",0,1,'L',true);
    // Saut de ligne
    $this->Ln(4);

    // Sauvegarde de l'ordonnée
    $this->y0 = $this->GetY();

}


function ImageArticle($url_img)
{

    $image=$url_img;
    $size = getimagesize($image);
    $largeur=$size[0];
    $hauteur=$size[1];
    $ratio=100/$hauteur;	//hauteur imposée de 120mm
    $newlargeur=$largeur*$ratio;
    $posi=(210-$newlargeur)/2;	//210mm = largeur de page
    $this->Image($image, $posi, 50, 0,100);
    $this->Cell(0,110,"",0,2);

    // Sauvegarde de l'ordonnée
    $this->y0 = $this->GetY();
}


function CorpsArticle($fichier)
{
    // Lecture du fichier texte
    $txt = $fichier;

    // Add a Unicode font (uses UTF-8)
    $this->AddFont('Aver','','Aver.ttf',true);
    $this->SetFont('Aver','',11);

    // Sortie du texte justifié
    $this->MultiCell(60,8,$txt);

    // Saut de ligne
    $this->Ln();

    // Retour en première colonne
    $this->SetCol(0);
}

function AjouterArticle($num, $titre, $fichier,$url_img)
{
    $this->AddPage();
    $this->TitreArticle($num,$titre);
    $this->ImageArticle($url_img);
    $this->CorpsArticle($fichier);
}
}

$pdf = new PDF();

$pdf->SetTitle($nom_date);
$pdf->SetAuthor($editeur);

for($x=0;$x<sizeof($articles)-1;$x++)
{
  $html = file_get_html($articles[$x]);

  if( ($html->find('h2[itemprop=headline]')) && ($html->find('main div div img',0)) && ($html->find('div[itemprop=articleBody] p')))
    {
        $title = $html->find('h2[itemprop=headline]');
        $image = $html->find('main div div img',0);

        $titre  = $title[0]->innertext;
        $titre = nl2br($titre);
        $titre = strip_tags($titre);
        $titre = trim($titre);

        $img    = "<img src='https://www.gnakrylive.com".$image->src."'/>'";
        $url_img= "https://www.gnakrylive.com".$image->src;

        $texte = "";
        $article_body = $html->find('div[itemprop=articleBody] p');

        for($i=0;$i<sizeof($article_body);$i++)
        {
          $texte = $texte.$article_body[$i]->plaintext;
        }

        $pdf->AjouterArticle($x,$titre,$texte,$url_img);
      }
}


$pdf->Output("I","$nom_date.pdf");
}
else {
  echo "Vous n'avez pas renseigné toutes les données du formulaire. Merci de retourner à la page précédente.";
}
}

else {
  echo "Vous n'avez pas renseigné toutes les données du formulaire. Merci de retourner à la page précédente.";
}


?>
