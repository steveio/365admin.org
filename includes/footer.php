<?


$oFooter = new Footer();



$oFooter->SetBrand($oBrand->GetName());
$oFooter->SetDesc($oBrand->GetSiteDescription());
$oFooter->SetCopyright("&copy;  ". $oBrand->GetName() . " 2007 - ".date('Y'));


$oFooter->LoadTemplate("footer.php");


?>
