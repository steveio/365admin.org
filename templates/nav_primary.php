<!-- BEGIN NAV -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<div class="container-fluid">
<div class="collapse navbar-collapse" id="navbarSupportedContent">
<ul class="navbar-nav me-auto mb-2 mb-lg-0">

<?
$aSection = $this->Get('SECTIONS');
foreach($aSection as $oSection) {
?>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?= $oSection->GetTitle(); ?>
        </a>
		<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
    	<?
    	foreach($oSection->GetSubSections() as $oSubSection) {
    		if ($oSubSection->HasSubSections()) {
    		?>
    			<li class="dropdown-item">
    			<a tabindex="-1" href="<?= $oSubSection->GetLink(); ?>"><?= $oSubSection->GetTitle(); ?></a>
    			<ul class="dropdown-menu level2">
    			<?php 
    			foreach($oSubSection->GetSubSections() as $oSubSection) {
    			?>
    				<li><a tabindex="-1" href="<?= $oSubSection->GetLink(); ?>"><?= $oSubSection->GetTitle(); ?></a></li>
    			<?php 
    			}
    			?>
    			</ul>
    			</li>
    		<?
    		} else {
    			?>
    			<li class="dropdown-item"><a class="dropdown-item" href="<?= $oSubSection->GetLink(); ?>" title="<?= $oSubSection->GetTitle(); ?>"> <?= $oSubSection->GetTitle(); ?></a></li>
    			<?
    		}
    	}
    	?>
    	</ul>

    </li>
		
<?
} 
?>				
</ul>
</div>
</nav>
<!-- END NAV -->