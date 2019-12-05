<?


$oHeader = new Header();

$oHeader->SetTitle($oBrand->GetSiteTitle());
$oHeader->SetDesc($oBrand->GetSiteDescription());
$oHeader->SetKeywords("");

$oHeader->SetLogoUrl($oBrand->GetLogoUrl());


/* Load the generic (site wide) Javascript includes */

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/generic.js?&update=true");
$oHeader->SetJsInclude($oJsInclude);


$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/jquery-3.3.1.js");
$oHeader->SetJsInclude($oJsInclude);

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/jquery/plugins/jquery.cookie.js");
$oHeader->SetJsInclude($oJsInclude);



/* load the generic site wide CSS */
$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/css/stylesheet.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);

$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/css/fonts.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_FONTS", $oCssInclude);



$oHeader->LoadTemplate("header_xhtml_std.php");



?>
