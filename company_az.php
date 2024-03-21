<?php
print $oHeader->Render();
?>

<div class="container">
<div class="align-items-center justify-content-center">

<div class="row">
<?

// DISPLAY COMPANY A-Z
$letters = range("A","Z");
print "<div class='row-12'>";
print "<h1>Company A-Z :</h1>";
foreach($letters as $letter) {
	if ($letter == strtoupper($_REQUEST['letter'])) {
		$css_style = "color: red;";
	} else {
		$css_style = "color: #CCCCCC;";
	}
	print "<a style='font-size: 1.6em; letter-spacing: 6px; $css_style' title='Display Companies Beginning : $letter' href='".$_CONFIG['url']."/company/a-z/".strtolower($letter)."'>".$letter ."</a>";
}

if ($_REQUEST['letter'] != "") {
	
	$db->query("select title,desc_short,url_name from company where title like '".strtoupper($_REQUEST['letter'])."%';");
	
	$aCompany = $db->getObjects();

	$cols = array_chunk($aCompany, ceil(count($aCompany)/3));
	
	print "<div class=\"row my-3\">";

	print "<p class='small'>Results : </p>";

	foreach ($cols as $arr){
	    print "<div class=\"col-4\">";
	    foreach($arr as $c)
	    {
    		print "<a class='c_title_sm' href='".$_CONFIG['url']."/company/".$c->url_name."' title='".$c->title." :".$c->desc_short." '>".stripslashes($c->title)."</a><br>";
	    }
	    print "</div>";
	}
	print "</div>";
}
print "</div>";



function cmp($a, $b) {
  if ($a->title == $b->title) {
    return 0;
  } else {
    return ($a->title > $b->title) ? 1 : -1; // reverse order
  }
}

print "</div>";


/*
print "<div id='profile' style='margin-top: 10px;'>";
print "<h2>Featured Companies</h2>";
$aAll = array_merge($aComp['SPONSORED'],$aComp['ENHANCED'],$aComp['BASIC']);
usort($aAll, 'cmp');

foreach ($aAll as $c) {
	
	$oProfile = new CompanyProfile();
	$oProfile->SetFromArray($c);		
	$oProfile->GetImages();

	print "<div style='float: left; height: 86px; width: 580px; margin: 10px 20px 10px 0px;'>";
	print "<div style='width: 240px; height: 80px; float:left;'>";
	print "<a href='".$oProfile->GetProfileUrl()."' title='".$oProfile->GetTitle().":".$oProfile->GetDescShort()."'>";
	if (is_object($oProfile->GetImage(0,LOGO_IMAGE))) {
		print $oProfile->GetImage(0,LOGO_IMAGE)->GetHtml("_sm",$oProfile->GetTitle(),'',$outputSize = FALSE);
	}
	print "</a></div>";
	print "<a class='c_title_sm' href='".$oProfile->GetProfileUrl()."' title='".$oProfile->GetTitle().":".$oProfile->GetDescShort()."'>".$oProfile->GetTitle()."</a>";
	print "<p class='c_desc_sm'>".$oProfile->GetDescShort(140)."</p>";
	print "</div>";
	

}
print "</div>";	
*/

?>
</div>
</div>
<?

print $oFooter->Render();

?>
