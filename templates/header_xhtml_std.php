<!doctype html>
<html lang="en">

<head>

<title><?= $this->Get("TITLE"); ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="google-site-verification" content="lNJMDbpe5zfy_6ap1x7GMmPiu7cCTQ_fNQW0ToD7SSU" />
<meta name="description" content="<?= $this->Get("DESCRIPTION"); ?>" />
<meta name="keywords" content="<?= $this->Get("KEYWORDS"); ?>" />

<meta property="og:title" content="<?= $this->Get("TITLE"); ?>" />
<meta property="og:url" content="<?= $this->Get("URL"); ?>" />
<meta property="og:description" content="<?= $this->Get("DESCRIPTION"); ?>" />

<script type="application/ld+json">
<?= $this->Get("JSONLD"); ?>
</script>

<link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">

<?= $this->Get("CSS_GENERIC"); ?>
<?= $this->Get("CSS_FONTS"); ?>

<?= $this->Get("JS_INCLUDE"); ?>


<? if (!$oAuth->oUser->isValidUser) { ?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KFVBLN089G"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-KFVBLN089G');
</script>


<? if (!$oAuth->IsValidUser()) { ?>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9874604497476880" crossorigin="anonymous"></script>
<script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=5bca02abddd6040011604f41&product=inline-share-buttons' async='async'></script>
<? } ?>

<script type="text/javascript">
$(document).ready(function(){

<?= $this->Get("JS_ONLOAD"); ?>

});
</script>

<? } // end is valid user ?>

</head>

<body>

<div class="container-fluid">


<header class="border-bottom">
<div class="container">
 
<div class="row">
    <div class="col-8">
	<div class="col-lg-4 col-sm-12">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <img src="<?= $this->Get("LOGO_URL"); ?>" alt="<?= $this->Get("TITLE"); ?>" border="0" />
        </a>
        </div>
    </div>        


    <? if ($oAuth->oUser->isValidUser) { ?>
    <div class="col-4 my-3">
        <div class="float-end">
        <div class="dropdown">
          <a href="#" class="btn btn-light d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Admin</a>
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

<div class="row my-3">
   <div class="col-12">
   <?
   $oNav = $this->Get('TOP_NAV');
   print $oNav->Render();
   ?>
</div>



</div>
</header>


<div class="container p-0 my-2">
