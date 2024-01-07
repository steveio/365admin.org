<?


$oFooter = new Footer();



$oFooter->SetBrand($oBrand->GetName());
$oFooter->SetDesc($oBrand->GetSiteDescription());
$oFooter->SetCopyright($oBrand->GetName() . " &copy;  2007 - ".date('Y'));


$oFooter->LoadTemplate("footer.php");


?>
