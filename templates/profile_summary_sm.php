<?php 
$oProfile = $this->Get("oProfile");
?>

<div class="profile-summary-01-sm">
   <div class="profile-summary-01-sm-item">
      <div class="profile-summary-01-sm-img">
      </div>
      <div class="profile-summary-01-sm-item">
      <p class="p_small"><?= $oProfile->GetTitle(); ?></p>
      <p class="p_small"><?= $oProfile->GetCompanyName(); ?></p>
      </div>
   </div>
</div>
