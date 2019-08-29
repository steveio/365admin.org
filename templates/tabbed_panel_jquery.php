	var setActive = function(id) {
		$("div[id^='TC']").hide();
		$("#TC"+id).show();
		$("li.tab").removeClass('active');
		$("#TAB"+id+"_LI").addClass('active');
		$("span.tab").removeClass('active-text');
		$("#TAB"+id+"_SP").addClass('active-text');
		$("a#TAB"+id).blur();

		/* persist selected tab in cookie */
		$.cookie("<?= $this->Get("COOKIE_NAME"); ?>", "TAB"+id);
		
	};

	/* set active tab visible */
	$("[id^='TC']").hide();
	$("#TC<?= substr($this->Get("ACTIVE_TAB"),-2); ?>").show();		

	<? foreach($this->Get("TABS") as $oTab) { ?>
	$('a#<?= $oTab->GetId(); ?>').click(function() {
		$("#user_msg").html('');
		setActive("<?= substr($oTab->GetId(),-2); ?>");
		return false;
	});
	<? } ?>