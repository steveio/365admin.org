<!doctype html>
<html lang="en">

<head>

<title><?= $this->Get("TITLE"); ?></title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?= $this->Get("DESCRIPTION"); ?>" />
<meta name="keywords" content="<?= $this->Get("KEYWORDS"); ?>" />


<link href="/bootstrap-5.3.2-dist/css/bootstrap.min.css" rel="stylesheet" integrity="" crossorigin="anonymous">



<?= $this->Get("CSS_GENERIC"); ?>
<?= $this->Get("CSS_FONTS"); ?>


<?= $this->Get("JS_INCLUDE"); ?>


<script type="text/javascript">
$(document).ready(function(){

<?= $this->Get("JS_ONLOAD"); ?>

});
</script>


</head>

<body>

<!-- BEGIN Page Wrap (closed in footer) -->
<div class="container-fluid">


<header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/dashboard" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <img src="<?= $this->Get("LOGO_URL"); ?>" alt="<?= $this->Get("TITLE"); ?>" border="0" />
        </a>


        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="#" class="nav-link px-2 link-secondary"></a></li>
        </ul>

      <? if ($oAuth->oUser->isValidUser) { ?>
        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input type="search" class="form-control" placeholder="Search..." aria-label="Search">
        </form>

        <div class="dropdown text-end">
          <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            Menu
          </a>
          <ul class="dropdown-menu text-small">
            <a class="dropdown-item" href="/dashboard">dashboard</a>
            <? if ($oAuth->oUser->isAdmin) { ?>
                    <a class="dropdown-item" href="/company/add" title="add a new company">company</a>
                    <a class="dropdown-item" href="/placement/add" title="add a new placement">placement</a>
                    <a class="dropdown-item" href="/article-manager" title="manage articles">article</a>
                    <a class="dropdown-item"  href="/user/" title="manage users">user</a>
            <? } ?>
            <? if ($oAuth->oUser->isAdmin) { ?>
                    <a class="dropdown-item" href="/link_admin.php" title="link admin">links</a>
                    <a class="dropdown-item" href="/website_admin.php" title="website admin">website</a>
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
    <? } ?>


    </div>
  </header>


<div class="container-fluid">
<hr />
