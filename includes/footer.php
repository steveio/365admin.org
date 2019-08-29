<?


$oFooter = new Footer();



$oFooter->SetBrand($oBrand->GetName());
$oFooter->SetDesc($oBrand->GetSiteDescription());
$oFooter->SetCopyright("&copy;  ". $oBrand->GetName() . " ".date('Y'));


$oFooter->LoadTemplate("footer.php");


?>
