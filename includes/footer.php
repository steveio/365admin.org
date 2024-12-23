<?

$oArticle = new Article;
$oArticle->SetFetchMode(FETCHMODE__FULL);
$oArticle->Get($oBrand->GetWebsiteId(),"/footer");

$oFooter = new Template();

$oFooter->Set('CONTENT',$oArticle->GetDescFull());
$oFooter->Set('COPYRIGHT',$oBrand->GetName() . " &copy;  2007 - ".date('Y'));

$oFooter->LoadTemplate('footer.php');

?>
