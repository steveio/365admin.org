<?


$oHeader = new Header();

$oHeader->SetTitle($oBrand->GetSiteTitle());
$oHeader->SetDesc($oBrand->GetSiteDescription());
$oHeader->SetKeywords("");

$oHeader->SetLogoUrl($oBrand->GetLogoUrl());


/* Load the generic (site wide) Javascript includes */

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/generic.js");
$oHeader->SetJsInclude($oJsInclude);


$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/jquery-3.7.1.min.js");
$oHeader->SetJsInclude($oJsInclude);

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/jquery/plugins/jquery.cookie.js");
$oHeader->SetJsInclude($oJsInclude);

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/datatables/js/jquery.dataTables.min.js");
$oHeader->SetJsInclude($oJsInclude);

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/daterangepicker/moment.min.js");
$oHeader->SetJsInclude($oJsInclude);

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/daterangepicker/daterangepicker.js");
$oHeader->SetJsInclude($oJsInclude);

$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/includes/js/daterangepicker/daterangepicker.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);

$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/includes/js/datatables/css/jquery.dataTables.min.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);



$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/css/fonts.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_FONTS", $oCssInclude);



$oHeader->LoadTemplate("header_xhtml_std.php");



?>