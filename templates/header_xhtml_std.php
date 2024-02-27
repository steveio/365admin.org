<!doctype html>
<html lang="en">

<head>

<title><?= $this->Get("TITLE"); ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="description" content="<?= $this->Get("DESCRIPTION"); ?>" />
<meta name="keywords" content="<?= $this->Get("KEYWORDS"); ?>" />

<meta property="og:title" content="<?= $this->Get("TITLE"); ?>" />
<meta property="og:url" content="<?= $this->Get("URL"); ?>" />
<meta property="og:description" content="<?= $this->Get("DESCRIPTION"); ?>" />



<?= $this->Get("CSS_GENERIC"); ?>
<?= $this->Get("CSS_FONTS"); ?>

<?= $this->Get("JS_INCLUDE"); ?>

<script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=5bca02abddd6040011604f41&product=inline-share-buttons' async='async'></script>


<script type="text/javascript">
$(document).ready(function(){

<?= $this->Get("JS_ONLOAD"); ?>

});
</script>


</head>

<body>

<div class="container-fluid">


<header class="border-bottom">
<div class="container">
 
    <div class="row">
    	<div class="col-6">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <img src="<?= $this->Get("LOGO_URL"); ?>" alt="<?= $this->Get("TITLE"); ?>" border="0" />
        </a>
        </div>
        
        <? if ($oAuth->IsWebsite()) { ?>
        <div class="col-6">
            <div class="adbanner_web">
                <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
            </div>
            <div class="adbanner_mob">
            <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
            <!-- One World365 Mobile Banner Header -->
            <ins class="adsbygoogle"
                 style="display:inline-block;width:290px;height:70px"
                 data-ad-client="ca-pub-9874604497476880"
                 data-ad-slot="1198653468"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            </div>
       </div>
       <? } ?>
    </div>

	<div class="row">
        <div class="col-8">
        <?
        $oNav = $this->Get('TOP_NAV');
        print $oNav->Render();
        ?>
        </div>


        <? if ($oAuth->oUser->isValidUser) { ?>    
    	<div class="col-4">
            <div class="float-end">        
            <div class="dropdown">
              <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
              <ul class="dropdown-menu text-small">
                <a class="dropdown-item" href="/dashboard">dashboard</a>
                <? if ($oAuth->oUser->isAdmin) { ?>
                        <a class="dropdown-item" href="/company/add" title="add a new company">add company</a>
                        <a class="dropdown-item" href="/placement/add" title="add a new placement">add placement</a>
                        <a class="dropdown-item"  href="/user/" title="manage users">user</a>
                        <a class="dropdown-item" href="/activity-admin" title="activity admin">activity</a>
                <? } ?>
                <? if ($oAuth->oUser->isValidUser) { ?>
                        <a class="dropdown-item" href="/enquiry-report/" title="enquiry admin">enquiries</a>
                        <? if ($oAuth->oUser->isAdmin) { ?>
                        <a class="dropdown-item" href="/review-report/" title="reviews admin">reviews</a>
                        <?php } ?>
                <? } ?>
    
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/logout">Sign out</a></li>
              </ul>
          </div>
          </div>
        </div>
        <? } ?>

	</div>

</div>
</header>


<div class="container p-0 my-2">