<?php 
	// This is the gift card page
	require('rezgo/include/page_header.php');

	// start a new instance of RezgoSite
	$site = new RezgoSite();

	// only show once after purchase
	if (!$_SESSION['GIFT_CARD_KEY']) {
		$site->sendTo($site->base."/gift-card");
	}
?>

<?=$site->getTemplate('frame_header')?>

<?=$site->getTemplate('gift_card_receipt')?>

<?=$site->getTemplate('frame_footer')?>