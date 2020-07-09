<?php require("SimpleHTMLDom/simple_html_dom.php");
$html = file_get_html("https://www.gnakrylive.com/actualite");
$liste_urls = [];
?>
<html>
<head>
</head>
<body>
  <header>
    <h1>Générateur de PDF pour la presse en ligne</h1>
  </header>
  <h2>GnakryLive</h2>
  <form method="POST" action="generatePDF.php">
    <label for="editeur">Nom du journal : </label><input type="text" name="editeur" id="editeur" value="GnakryLive"><br />
    <label for="date">Date de parution : </label><input type="date" name="date" id="date"><br />
    <label for="urls">URLs des articles : </label><textarea name="urls" id="urls" cols="100" rows="20" ><?php $urls = $html->find('.nav-stacked li a');
    for($x=0;$x<10;$x++)
    {
    array_push($liste_urls,$urls[$x]->href);
    echo "https://www.gnakrylive.com".$urls[$x]->href."\r";
  }
  ?>
</textarea><br />
    <input type="submit">
  </form>

</body>
</html>
