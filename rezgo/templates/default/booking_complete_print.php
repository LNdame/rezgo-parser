<?
	// grab and decode the trans_num if it was set
	$trans_num = $site->decode($_REQUEST['trans_num']);

	// send the user home if they shoulden't be here
	if(!$trans_num) $site->sendTo($site->base."/booking-not-found");

	$company = $site->getCompanyDetails();

	$rzg_payment_method = 'None';
?>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Booking - <?=$trans_num?></title>

	<!-- Bootstrap CSS -->
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css" rel="stylesheet">

	<!-- Font awesome --> 
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
	<!--[if IE 7]><link href="<?=$this->path?>/css/font-awesome-ie7.css" rel="stylesheet"><![endif]-->

	<!-- Rezgo stylesheet -->
	<link href="<?=$site->path?>/css/rezgo.css" rel="stylesheet">

	<? if($site->exists($site->getStyles())) { ?><style><?=$site->getStyles();?></style><? } ?>

	<!-- jQuery & Bootstrap JS -->
	<script type="text/javascript" src="<?=$site->base?>/js/iframeResizer.contentWindow.min.js"></script>
	<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="<?=$site->path?>/js/bootstrap.min.js"></script>

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDkCWu6MoROFlsRGoqFj-AXPEApsVjyTiA&sensor=false&libraries=places"></script>
</head>
<body>
	<div class="container-fluid rezgo-container">
		<? if(!$site->getBookings('q='.$trans_num)) {
			$site->sendTo("/booking-not-found:".$_REQUEST['trans_num']); 
		} ?>

		<? foreach( $site->getBookings('q='.$trans_num) as $booking ): ?>
			<? $item = $site->getTours('t=uid&q='.$booking->item_id, 0); ?>

			<? $site->readItem($booking) ?>

			<div class="rezgo-content-row">
				<h2 id="rezgo-receipt-head-your-booking">Your Booking (booked on <?=date((string) $company->date_format, (int) $booking->date_purchased_local)?> / local time)</h2>

				<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
					<tr id="rezgo-receipt-transnum">
						<td class="rezgo-td-label"><span>Transaction #</span></td>
						<td class="rezgo-td-data"><?=$booking->trans_num?></td>
					</tr>

					<tr id="rezgo-receipt-have-booked">
						<td class="rezgo-td-label"><span>You have booked</span></td>
						<td class="rezgo-td-data"><?=$booking->tour_name?> &mdash; <?=$booking->option_name?></td>
					</tr>

					<? if ((string) $booking->date != 'open') { ?>
						<tr id="rezgo-receipt-booked-for">
							<td class="rezgo-td-label"><span>Booked For</span></td>
							<td class="rezgo-td-data"><?=date((string) $company->date_format, (int) $booking->date)?>
							<? if ($booking->time != '') { ?> at <?=$booking->time?><? } ?>
							</td>
						</tr>
						<? } else { ?>
						<? if ($booking->time) { ?>
							<tr id="rezgo-receipt-booked-for">
								<td class="rezgo-td-label"><span>Time:</span></td>
								<td class="rezgo-td-data"><span><?=$booking->time?></span></td>
							</tr>
						<? } ?>
					<? } ?>

					<? if (isset($booking->expiry)) { ?>
						<tr>
							<td class="rezgo-td-label">Expires</td>
							<? if ((int) $booking->expiry !== 0) { ?>
								<td class="rezgo-td-data"><?=date((string) $company->date_format, (int) $booking->expiry)?>
							<? } else { ?>
								<td class="rezgo-td-data">Never
							<? } ?>
							</td>
						</tr>
					<? } ?>
					
					<? if ((string) $item->duration != '') { ?>
						<tr id="rezgo-receipt-duration">
							<td class="rezgo-td-label"><span>Duration</span></td>
							<td class="rezgo-td-data"><?=$item->duration?></td>
						</tr>
					<? } ?>
					
					<? if ($item->location_name != '') {
						$location = $item->location_name.', '.$item->location_address;
					} else {
						unset($loc);
						if($site->exists($item->city)) $loc[] = $item->city;
						if($site->exists($item->state)) $loc[] = $item->state;
						if($site->exists($item->country)) $loc[] = $site->countryName($item->country);
						if($loc) $location = implode(', ', $loc);
					}
					if (isset($location) && $location != '') { ?>
						<tr id="rezgo-receipt-location">
							<td class="rezgo-td-label"><span>Location</span></td>
							<td class="rezgo-td-data"><?=$location?></td>
						</tr>
					<? } ?>

					<? if ((string) $item->details->pick_up != '') { ?>
						<tr id="rezgo-receipt-pickup">
							<td class="rezgo-td-label"><span>Pickup/Departure Information</span></td>
							<td class="rezgo-td-data"><?=$item->details->pick_up?></td>
						</tr>
					<? } ?>

					<? if ((string) $item->details->drop_off != '') { ?>
						<tr id="rezgo-receipt-dropoff">
							<td class="rezgo-td-label"><span>Dropoff/Return Information</span></td>
							<td class="rezgo-td-data"><?=$item->details->drop_off?></td>
						</tr>
					<? } ?>

					<? if ((string) $item->details->bring != '') { ?>
						<tr id="rezgo-receipt-thingstobring">
							<td class="rezgo-td-label"><span>Things to bring</span></td>
							<td class="rezgo-td-data"><?=$item->details->bring?></td>
						</tr>
					<? } ?>

					<? if ((string) $item->details->itinerary != '') { ?>
						<tr class="rezgo-receipt-itinerary">
							<td colspan="2"><strong>Itinerary</strong></td>
						</tr>
						<tr class="rezgo-receipt-itinerary">
							<td colspan="2" class="rezgo-td-data"><?=$item->details->itinerary?></td>
						</tr>
					<? } ?>
				</table>
			</div>

			<? if ($item->lat != '' && $item->lon != '') { ?>
				<? if ($item->map_type == 'ROADMAP') {
				$embed_type = 'roadmap';
				} else {
				$embed_type = 'satellite';
				} ?>

				<!-- start receipt map -->	
				<div style="page-break-after:always;"></div>

				<div class="row" id="rezgo-receipt-map-container">
					<div class="col-xs-12">
						<h3 id="rezgo-receipt-head-map"><span>Map</span></h3>
						<? if ($item->location_name) { ?>
							<div id="rezgo-receipt-map-location">
								<strong><?=$item->location_name?></strong><br />
								<?=$item->location_address?>
							</div>
						<? } ?>
						<div id="rezgo-receipt-map">
							<iframe width="100%" height="390" frameborder="0" style="border:0;margin-bottom:0;pointer-events:none;" src="https://www.google.com/maps/embed/v1/view?key=AIzaSyCqFNdI5b319sgzE3WH3Bw97fBl4kRVzWw&maptype=<?=$embed_type?>&center=<?=$item->lat?>,<?=$item->lon?>&zoom=<?=(($item->zoom != '' && $item->zoom > 0) ? $item->zoom : 6)?>"></iframe>
						</div>
					</div>
				</div>	
				<!-- end receipt map -->
			<? } ?>

			<div style="page-break-after:always;"></div>

			<? if($booking->payment_method != 'None') {
				$rzg_payment_method = $booking->payment_method;
			} ?>

			<div class="rezgo-content-row" id="rezgo-receipt-payment-info">
				<h2 id="rezgo-receipt-head-payment-info"><span>Payment Information</span></h2>

				<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
					<tr id="rezgo-receipt-name">
						<td class="rezgo-td-label">Name</td>
						<td class="rezgo-td-data"><?=$booking->first_name?> <?=$booking->last_name?></td>
					</tr>

					<tr id="rezgo-receipt-address">
						<td class="rezgo-td-label">Address</td>
						<td class="rezgo-td-data"><?=$booking->address_1?><? if($site->exists($booking->address_2)) { ?>, <?=$booking->address_2?><? } ?><? if($site->exists($booking->city)) { ?>, <?=$booking->city?><? } ?><? if($site->exists($booking->stateprov)) { ?>, <?=$booking->stateprov?><? } ?><? if($site->exists($booking->postal_code)) { ?>, <?=$booking->postal_code?><? } ?>, <?=$site->countryName($booking->country)?></td>
					</tr>

					<tr id="rezgo-receipt-phone">
						<td class="rezgo-td-label">Phone Number</td>
						<td class="rezgo-td-data"><?=$booking->phone_number?></td>
					</tr>

					<tr id="rezgo-receipt-email">
						<td class="rezgo-td-label">Email Address</td>
						<td class="rezgo-td-data"><?=$booking->email_address?></td>
					</tr>

					<? if($booking->overall_total > 0) { ?>
						<tr id="rezgo-receipt-payment-method">
							<td class="rezgo-td-label">Payment Method</td>
							<td class="rezgo-td-data"><?=$rzg_payment_method?></td>
						</tr>
						<? if($booking->payment_method == 'Credit Cards') { ?>
							<tr id="rezgo-receipt-cardnum">
								<td class="rezgo-td-label">Card Number</td><td class="rezgo-td-data"><?=$booking->card_number?></td>
							</tr>
						<? } ?>

						<? if($site->exists($booking->payment_method_add->label)) { ?>
							<tr>
								<td class="rezgo-td-label"><?=$booking->payment_method_add->label?></td><td class="rezgo-td-data"><?=$booking->payment_method_add->value?></td>
							</tr>
						<? } ?>
					<? } ?>

					<tr id="rezgo-receipt-payment-status">
						<td class="rezgo-td-label">Payment Status</td>
						<td class="rezgo-td-data"><?=(($booking->status == 1) ? 'CONFIRMED' : '')?><?=(($booking->status == 2) ? 'PENDING' : '')?><?=(($booking->status == 3) ? 'CANCELLED' : '')?></td>
					</tr>

					<? if($site->exists($booking->trigger_code)) { ?>
						<tr id="rezgo-receipt-trigger">
							<td class="rezgo-td-label" class="rezgo-promo-label"><span>Promotional Code</span></td>
							<td class="rezgo-td-data"><?=$booking->trigger_code?></td>
						</tr>
					<? } ?>

					<tr id="rezgo-receipt-charges">
						<td class="rezgo-td-label">Charges</td>
						<td class="rezgo-td-data">
							<table class="table-responsive">
								<table class="table table-bordered table-striped rezgo-billing-cart">
									<tr>
										<td class="text-right"><label>Type</label></td>
										<td class="text-right"><label class="hidden-xs">Qty.</label></td>
										<td class="text-right"><label>Cost</label></td>
										<td class="text-right"><label>Total</label></td>
									</tr>

									<? foreach( $site->getBookingPrices() as $price ): ?>
										<tr>
											<td class="text-right"><?=$price->label?></td>
											<td class="text-right"><?=$price->number?></td>
											<td class="text-right">
											<? if($site->exists($price->base)) { ?>
												<span class="discount"><?=$site->formatCurrency($price->base)?></span>
											<? } ?>
											&nbsp;<?=$site->formatCurrency($price->price)?></td>
											<td class="text-right"><?=$site->formatCurrency($price->total)?></td>
										</tr>
									<? endforeach; ?>

									<tr>
										<td colspan="3" class="text-right"><strong>Sub-total</strong></td>
										<td class="text-right"><?=$site->formatCurrency($booking->sub_total)?></td>
									</tr>

									<? foreach( $site->getBookingLineItems() as $line ) { ?>
										<?
											unset($label_add);
											if($site->exists($line->percent) || $site->exists($line->multi)) {
												$label_add = ' (';
										
													if($site->exists($line->percent)) $label_add .= $line->percent.'%';
													if($site->exists($line->multi)) {
														if(!$site->exists($line->percent)) $label_add .= $site->formatCurrency($line->multi);
														$label_add .= ' x '.$booking->pax;
													}
										
												$label_add .= ')';	
											}
										?>

										<tr>
											<td colspan="3" class="text-right"><strong><?=$line->label?><?=$label_add?></strong></td>
											<td class="text-right"><?=$site->formatCurrency($line->amount)?></td>
										</tr>
									<? } ?>

									<? foreach( $site->getBookingFees() as $fee ): ?>
										<? if( $site->exists($fee->total_amount) ): ?>
											<tr>
												<td colspan="3" class="text-right"><strong><?=$fee->label?></strong></td>
												<td class="text-right"><?=$site->formatCurrency($fee->total_amount)?></td>
											</tr>
										<? endif; ?>
									<? endforeach; ?>

									<tr>
										<td colspan="3" class="text-right"><strong>Total</strong></td>
										<td class="text-right"><strong><?=$site->formatCurrency($booking->overall_total)?></strong></td>
									</tr>

									<? if($site->exists($booking->deposit)) { ?>
										<tr>
											<td colspan="3" class="text-right"><strong>Deposit</strong></td>
											<td class="text-right"><strong><?=$site->formatCurrency($booking->deposit)?></strong></td>
										</tr>
									<? } ?>

									<? if($site->exists($booking->overall_paid)) { ?>
										<tr>
											<td colspan="3" class="text-right"><strong>Total Paid</strong></td>
											<td class="text-right"><strong><?=$site->formatCurrency($booking->overall_paid)?></strong></td>
										</tr>

										<tr>
											<td colspan="3" class="text-right"><strong>Total&nbsp;Owing</strong></td>
											<td class="text-right"><strong><?=$site->formatCurrency(((float)$booking->overall_total - (float)$booking->overall_paid))?></strong></td>
										</tr>
									<? } ?>
								</table>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<? if(count($site->getBookingForms()) > 0 OR count($site->getBookingPassengers()) > 0) { ?>
				<div class="rezgo-content-row" id="rezgo-receipt-guest-info">
					<h2 id="rezgo-receipt-head-guest-info"><span>Guest Information</span></h2>

					<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
						<? foreach($site->getBookingForms() as $form) { ?>
							<? if(in_array($form->type, array('checkbox','checkbox_price'))) { ?>
								<? if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
							<? } ?>
							<tr class="rezgo-receipt-primary-forms">
								<td class="rezgo-td-label"><?=$form->question?>:</td>
								<td class="rezgo-td-data"><?=$form->answer?></td>
							</tr>
						<? } ?>
						<? foreach($site->getBookingPassengers() as $passenger) { ?>
							<tr class="rezgo-receipt-pax">
								<td class="rezgo-td-label"><?=$passenger->label?> <?=$passenger->num?>:</td>
								<td class="rezgo-td-data"><?=$passenger->first_name?> <?=$passenger->last_name?></td>
							</tr>
							<? if ((string) $passenger->phone_number != '') { ?>
								<tr class="rezgo-receipt-pax-phone">
									<td class="rezgo-td-label">Phone Number:</td>
									<td class="rezgo-td-data"><?=$passenger->phone_number?></td>
								</tr>
							<? } ?>
							<? if ((string) $passenger->email_address != '') { ?>
								<tr class="rezgo-receipt-pax-email">
									<td class="rezgo-td-label">Email:</td>
									<td class="rezgo-td-data"><?=$passenger->email_address?></td>
								</tr>
							<? } ?>
							<? foreach( $passenger->forms->form as $form ) { ?>
								<? if (in_array($form->type, array('checkbox','checkbox_price'))) { ?>
									<? if($site->exists($form->answer)) { $form->answer = 'yes'; } else { $form->answer = 'no'; } ?>
								<? } ?>
								<tr class="rezgo-receipt-guest-forms">
									<td class="rezgo-td-label"><?=$form->question?>:</td>
									<td class="rezgo-td-data"><?=$form->answer?></td>
								</tr>
							<? } ?>
							<tr>
								<td class="rezgo-td-label">&nbsp;</td>
								<td class="rezgo-td-data">&nbsp;</td>
							</tr>
						<? } ?>
					</table>
				</div>
			<? } ?>

			<div class="rezgo-content-row" id="rezgo-receipt-customer-service-section">
				<h2 id="rezgo-receipt-head-customer-service"><span>Customer Service</span></h2>

				<table border="0" cellspacing="0" cellpadding="2" class="rezgo-table-list">
					<tr id="rezgo-receipt-cancel">
						<td class="rezgo-td-label"><span>Cancellation Policy</span></td>
						<td class="rezgo-td-data">
						<? if($site->exists($booking->rezgo_gateway)) { ?>
					
							Canceling a booking with Rezgo can result in cancellation fees being
							applied by Rezgo, as outlined below. Additional fees may be levied by
							the individual supplier/operator (see your Rezgo 
							<? echo ((string) $booking->ticket_type == 'ticket') ? 'Ticket' : 'Voucher' ?> for specific
							details). When canceling any booking you will be notified via email,
							facsimile or telephone of the total cancellation fees.<br />
							<br />
							1. Event, Attraction, Theater, Show or Coupon Ticket<br />
							These are non-refundable in all circumstances.<br />
							<br />
							2. Gift Certificate<br />
							These are non-refundable in all circumstances.<br />
							<br />
							3. Tour or Package Commencing During a Special Event Period<br />
							These are non-refundable in all circumstances. This includes,
							but is not limited to, Trade Fairs, Public or National Holidays,
							School Holidays, New Year's, Thanksgiving, Christmas, Easter, Ramadan.<br />
							<br />
							4. Other Tour Products & Services<br />
							If you cancel at least 7 calendar days in advance of the
							scheduled departure or commencement time, there is no cancellation
							fee.<br />
							If you cancel between 3 and 6 calendar days in advance of the
							scheduled departure or commencement time, you will be charged a 50%
							cancellation fee.<br />
							If you cancel within 2 calendar days of the scheduled departure
							or commencement time, you will be charged a 100% cancellation fee.
							<br />
						<? } else { ?>
							<? if($site->exists($item->details->cancellation)) { ?>
								<?=$item->details->cancellation?>
								<br />
							<? } ?>
						<? } ?>
				
						View terms and conditions: <strong>http://<?=$site->getDomain()?>.rezgo.com/terms</strong>
						</td>
					</tr>
			
					<? if($site->exists($booking->rid)) { ?>
						<tr id="rezgo-receipt-customer-service">
							<td class="rezgo-td-label"><span>Customer Service</span></td>
							<td class="rezgo-td-data">
							<? if($site->exists($booking->rezgo_gateway)) { ?>
								Rezgo.com<br />
								Attn: Partner Bookings<br />
								333 Brooksbank Avenue<br />
								Suite 718<br />
								North Vancouver, BC<br />
								Canada V7J 3V8<br />
								(604) 983-0083<br />
								bookings@rezgo.com
							<? } else { ?>
								<? $company = $site->getCompanyDetails('p'.$booking->rid); ?>
								<?=$company->company_name?><br />
								<?=$company->address_1?> <?=$company->address_2?><br />
								<?=$company->city?>, <? if($site->exists($company->state_prov)) { ?><?=$company->state_prov?>, <? } ?><?=$site->countryName($company->country)?><br />
								<?=$company->postal_code?><br />
								<?=$company->phone?><br />
								<?=$company->email?>
								<? if($site->exists($company->tax_id)) { ?>
									<br />
									<br />
									<?=$company->tax_id?>
								<? } ?>
							<? } ?>
							</td>
						</tr>
					<? } ?>

					<tr id="rezgo-receipt-provided-by">
						<td class="rezgo-td-label"><span>Service Provided By</span></td>
						<td class="rezgo-td-data">
							<? $company = $site->getCompanyDetails($booking->cid); ?>
							<?=$company->company_name?><br />
							<?=$company->address_1?> <?=$company->address_2?><br />
							<?=$company->city?>, <? if($site->exists($company->state_prov)) { ?><?=$company->state_prov?>, <? } ?><?=$site->countryName($company->country)?><br />
							<?=$company->postal_code?><br />
							<?=$company->phone?><br />
							<?=$company->email?>
							<? if($site->exists($company->tax_id)) { ?>
							<br />
							Tax ID: <?=$company->tax_id?>
							<? } ?>
						</td>
					</tr>
				</table>
			</div>
		<? endforeach; ?>
	</div>
</body>
</html>