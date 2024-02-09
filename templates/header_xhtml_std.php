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
<meta property="og:url" content="<?= $_REQUEST['page_url'] ?>" />
<meta property="og:description" content="<?= $this->Get("DESCRIPTION"); ?>" />


<link href="/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">



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

<!-- BEGIN Page Wrap (closed in footer) -->
<div class="container-fluid">


<header class="border-bottom">
    <div class="container">
 
     <div class="row">
        <a href="/dashboard" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <img src="<?= $this->Get("LOGO_URL"); ?>" alt="<?= $this->Get("TITLE"); ?>" border="0" />
        </a>
      </div>


     <div class="row my-1">
      <? if ($oAuth->oUser->isValidUser) { ?>
	<div class="col-12">
        <div class="float-end">
        <div class="dropdown">
          <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Menu</a>
  	  <!--<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-ibs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Menu</a>-->

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
