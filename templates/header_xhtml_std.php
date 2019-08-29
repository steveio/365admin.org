<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" >

<head>

<title><?= $this->Get("TITLE"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="<?= $this->Get("DESCRIPTION"); ?>" />
<meta name="keywords" content="<?= $this->Get("KEYWORDS"); ?>" />



<?= $this->Get("CSS_GENERIC"); ?>
<?= $this->Get("CSS_FONTS"); ?>

<!--[if !IE]>-->
<?= $this->Get("CSS_NOT_IE"); ?>
<!--<![endif]-->

<!--[if IE 6]>
<?= $this->Get("CSS_IE6"); ?>
<![endif]-->

<!--[if IE 7]>
<?= $this->Get("CSS_IE7"); ?>
<![endif]-->

<!--[if IE 8]>
<?= $this->Get("CSS_IE8"); ?>
<![endif]-->


<?= $this->Get("JS_INCLUDE"); ?>


<script type="text/javascript">
$(document).ready(function(){

<?= $this->Get("JS_ONLOAD"); ?>

});
</script>


</head>


<body>


<!-- BEGIN Page Wrap (closed in footer) -->
<div class="MainWrapper">
   

<!-- BEGIN Header -->
<div class="header">

	<div class="heading logo">
		<div class="col five clear" >
			<div class="col two-sm">
				<p class="logo">
				<img src="<?= $this->Get("LOGO_URL"); ?>" alt="<?= $this->Get("TITLE"); ?>" border="0" />
				</p>
			</div>
			
			<? if ($oAuth->IsValidUser()) { ?>
			<!-- BEGIN admin nav -->
			<div class="col three">
			<div class="right-align clear pad-r pad-t">
			        <? if ($oAuth->oUser->isAdmin) { ?>
			                <a class="nav_link" href="<? $_CONFIG['url'] ?>/company/add" title="add a new company">company</a>  <img src="/images/red_cube.gif" border="0">
			                <a class="nav_link" href="<? $_CONFIG['url'] ?>/placement/add" title="add a new placement">placement</a>  <img src="/images/red_cube.gif" border="0">
			                <a class="nav_link" href="<? $_CONFIG['url'] ?>/article-manager" title="manage articles">article</a>  <img src="/images/red_cube.gif" border="0">
			                <a class="nav_link" href="<?= $oBrand->GetWebsiteUrl(); ?>/user/" title="manage users">user</a>  <img src="/images/red_cube.gif" border="0">
			        <? } ?>
			        <? if ($oAuth->oUser->isAdmin) { ?>
			                <br />
			                <a class="nav_link" href="<?= $oBrand->GetWebsiteUrl(); ?>/link_admin.php" title="link admin">links</a>  <img src="<?= $_CONFIG['url']; ?>/images/red_cube.gif" border="0">
			                <a class="nav_link" href="<?= $oBrand->GetWebsiteUrl(); ?>/website_admin.php" title="website admin">website</a>  <img src="<?= $_CONFIG['url']; ?>/images/red_cube.gif" border="0">
			                <a class="nav_link" href="<?= $_CONFIG['url']; ?>/activity-admin" title="activity admin">activity</a>  <img src="<?= $_CONFIG['url']; ?>/images/red_cube.gif" border="0">
			                <br />
			        <? } ?>
			        <? if ($oAuth->oUser->isValidUser) { ?>
			                <a class="nav_link" href="<? $_CONFIG['url'] ?>/enquiry-report/" title="enquiry admin">enquiries</a>  <img src="<?= $_CONFIG['url']; ?>/images/red_cube.gif" border="0">
			                <? if ($oAuth->oUser->isAdmin) { ?>
			                <a class="nav_link" href="<? $_CONFIG['url'] ?>/review-report/" title="reviews admin">reviews</a>  <img src="<?= $_CONFIG['url']; ?>/images/red_cube.gif" border="0">
			                <?php } ?>
			        <? } ?>
			        <a class="nav_link" title="Edit Company Profile / Post Placements" href="<?= $_CONFIG['url']; ?>/dashboard/">dashboard</a>  <img src="<?= $_CONFIG['url']; ?>/images/red_cube.gif" border="0">
			
			</div> 
			</div>
			<!--  END admin nav -->
			<? } ?>
		</div>
	</div>


</div><!-- END Header -->

<div class="ContentWrapper clear">
<hr />
