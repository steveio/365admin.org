Hi <?= $this->Get('TITLE'); ?>,

Summer Camp 365 (www.summercamp365.com) is one of the largest online summer camp
directories in the world featuring over 2000+ camps throughout North America. Our
website is free to use and is a great resource for parents researching camp programs 
for their child and potential staff searching for jobs in 2014.

We offer all camps a full free profile to promote your programs.

You can view the current profile for <?= $this->Get('TITLE'); ?> here:

<?= $this->Get('PROFILE_URL'); ?>


To edit / update your listing and complete a full camp profile please visit this link:

<?= $this->Get('ADMIN_UPDATE_URL'); ?>


<?php if ($this->Get('ACCOUNT_EXISTS')) {  // No existing account ?>

Your account details are: 

Username: <?= $this->Get('ACCOUNT_USERNAME'); ?>
Password: <?= $this->Get('ACCOUNT_PASSWORD'); ?>

<?php } // end account exists ?>

We also offer an affordable paid listing upgrade to become a featured
camp, please get in touch for more details.

You might also like to join our page on Facebook or follow us on Twitter.

We look forward to helping promote your camp programs on summercamp365.com

Thanks,

Paul Edwards
