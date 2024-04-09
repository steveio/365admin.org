<?


$oHeader = new Header();

$oHeader->SetTitle($oBrand->GetSiteTitle());
$oHeader->SetDesc($oBrand->GetSiteDescription());
$oHeader->SetKeywords("");

$oHeader->SetLogoUrl($oBrand->GetLogoUrl());


/* Load the generic (site wide) Javascript includes */

$oJsInclude = new JsInclude();
$oJsInclude->SetSrc("/includes/js/generic.js?ts=".date("Ymd"));
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
$oCssInclude->SetHref('/css/bootstrap.min.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);

$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/css/style.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);

$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/includes/js/daterangepicker/daterangepicker.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);

$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/includes/js/datatables/css/jquery.dataTables.min.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);

/*
$oCssInclude = new CssInclude();
$oCssInclude->SetHref('/assets/icons-1.11.3/font/bootstrap-icons.css');
$oCssInclude->SetMedia('screen');
$oHeader->SetCssInclude("CSS_GENERIC", $oCssInclude);
*/


require_once("./classes/navigation.class.php");
$oNav = new Nav();
$oNav->Setup();
$oHeader->SetNav('TOP_NAV', $oNav);


$oHeader->LoadTemplate("header_xhtml_std.php");


?>