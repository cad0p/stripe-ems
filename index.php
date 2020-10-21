<?php
// Use HTTP Strict Transport Security to force client to use secure connections only
$use_hsts = true;

// iis sets HTTPS to 'off' for non-SSL requests
if ($use_hsts && isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
} elseif ($use_hsts) {
    header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], true, 301);
    // we are in cleartext at the moment, prevent further execution and output
    die();
}
?>

<!DOCTYPE html><html lang="en-US" itemscope itemtype="http://schema.org/WebPage">

<head>
	<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/><meta http-equiv="X-UA-Compatible" content="IE=edge"/><meta name="referrer" content="strict-origin-when-cross-origin"><link rel="icon" href="https://lh3.googleusercontent.com/NeifT3yfzkgb4ROmUbFpwUAMbhUNpyR5Yt2LDkotmAQwy5J1MWlsRFt-Vo9WdQceyUqWZpMYNnO3iwpXcDkwImancQQxAndP"/><meta property="og:type" content="website"/><meta property="og:title" content="Dona o Diventa Socio - EMS - Energia e Mobilita Sostenibile"/><meta itemprop="url" property="og:url" content="https://pay.energiaemobilitasostenibile.org/"/><meta itemprop="thumbnailUrl" content="https://lh3.googleusercontent.com/NeifT3yfzkgb4ROmUbFpwUAMbhUNpyR5Yt2LDkotmAQwy5J1MWlsRFt-Vo9WdQceyUqWZpMYNnO3iwpXcDkwImancQQxAndP"/><meta itemprop="image" content="https://lh3.googleusercontent.com/NeifT3yfzkgb4ROmUbFpwUAMbhUNpyR5Yt2LDkotmAQwy5J1MWlsRFt-Vo9WdQceyUqWZpMYNnO3iwpXcDkwImancQQxAndP"/><meta name="KEYWORDS" content="socio, member, association, associazione studentesca, ems, mobilita sostenibile, energia rinnovabile, auto elettriche, pompe di calore, pannelli solari, pannelli fotovoltaici, dona ems, diventa socio ems"><meta name="DESCRIPTION" content="Supporta studenti appassionati di auto elettriche ed energie rinnovabili, o diventa socio e partecipa al cambiamento!">
	<title itemprop="name">Dona o Diventa Socio - EMS - Energia e Mobilita Sostenibile</title>

</head>

<body>
	<?php

	$MEMBER_PLAN_ID = array 
	(   'nazionale'	=> 	'plan_F3BniqoWKkEScc',
		'parma'  	=> 	'plan_HFXblXc8D2SGeH',
		'milano'	=> 	'plan_HFXdpvlSdpkcJo',
		'roma'		=> 	'plan_HFXeRH6CSX83qn',
		'trieste'	=> 	'plan_HFXe8hK8ugoeZU',
		'palermo'	=> 	'plan_HFXfxmdHmS08Z4',
	);

	if (array_key_exists('email', $_REQUEST)) {
		$email = $_REQUEST['email'];
	}

	if (array_key_exists('loc', $_REQUEST) && array_key_exists($_REQUEST['loc'], $MEMBER_PLAN_ID)) {
		$loc = $_REQUEST['loc'];
	}
	else {
		$loc = 'nazionale';
	}

	$memberPlan = $MEMBER_PLAN_ID[$loc];


	$PLAN_ID = array 
	(   'single'   => 'sku_F3bNWSHnyfX71d'   ,
		'monthly'  => 'plan_F3qcyeIhHvgEMb'   ,
		'annually' => 'plan_F3qcK1A3GmLldP'
	);


	// these refer to the donation section
	if(!(array_key_exists('quantity',$_REQUEST) && array_key_exists('frequency',$_REQUEST)) 
		|| !array_key_exists($_REQUEST['frequency'], $PLAN_ID))
	{
		$quantity = 10;
		$frequency = "single";
	}
	else
	{
		$quantity = $_REQUEST['quantity'];
		$frequency = $_REQUEST['frequency'];
	}


	$plan = $PLAN_ID[$frequency];

	?>
	<!-- Load Stripe.js on your website. -->
	<script src="https://js.stripe.com/v3"></script>

	<!-- Quota Associativa -->
	<button
	style="background-color:#6772E5;color:#FFF;padding:8px 12px;border:0;border-radius:4px;font-size:1em"
	id="checkout-button-<?php echo $memberPlan ?>"
	role="link"
	>
	Paga Quota Associativa Annuale EMS <?php echo ucwords($loc) ?> (15€)
</button>


<form method="get">

<?php
	if ($email != null) {
?>
	<input type="hidden" name="email" value="<?php echo $email ?>">
<?php
	}
?>

<?php
	if ($quantity != null) {
?>
	<input type="hidden" name="quantity" value="<?php echo $quantity ?>">
<?php
	}
?>

<?php
	if ($frequency != null) {
?>
	<input type="hidden" name="frequency" value="<?php echo $frequency ?>">
<?php
	}
?>
	<br>
	<b>Seleziona la tua sede EMS: </b>
	<select name="loc">
<?php
		foreach ($MEMBER_PLAN_ID as $thisLoc => $thisPlan) {
			echo '<option value="'.$thisLoc.'"';
			if ($loc == $thisLoc) echo ' selected';
			echo ' >'.ucwords($thisLoc).'</option>\n';
		}
?>
	</select><br>
	<button type='submit' >Aggiorna il pulsante iscrizione</button>
</form> 


<div id="error-message"></div>

<script>
	var stripe = Stripe('pk_live_JGqQsMsj90FCfe9rmpGoJwmk002dwohNL9');

	var checkoutButton = document.getElementById('checkout-button-<?php echo $memberPlan ?>');
	checkoutButton.addEventListener('click', function () {
    // When the customer clicks on the button, redirect
    // them to Checkout.
    stripe.redirectToCheckout({
    	items: [{plan: '<?php echo $memberPlan ?>', quantity: 1}],

    	<?php if($email != null) echo "customerEmail: '$email',"; ?>

      // Note that it is not guaranteed your customers will be redirected to this
      // URL *100%* of the time, it's possible that they could e.g. close the
      // tab between form submission and the redirect.
		successUrl: window.location.protocol + '//www.energiaemobilitasostenibile.org/success',
		cancelUrl: window.location.protocol + '//pay.energiaemobilitasostenibile.org/',
    })
    .then(function (result) {
    	if (result.error) {
        // If `redirectToCheckout` fails due to a browser or network
        // error, display the localized error message to your customer.
        var displayError = document.getElementById('error-message');
        displayError.textContent = result.error.message;
      }
    });
  });
</script>

<br><br><br><br>


<!-- Create a button that your customers click to complete their purchase. Customize the styling to suit your branding. -->
	<button
	style="background-color:#6772E5;color:#FFF;padding:8px 12px;border:0;border-radius:4px;font-size:1em"
	id="checkout-button-<?php echo $plan ?>"
	role="link"
	>
	Dona <?php echo $quantity ?>€ a EMS
	<?php
	if ($frequency == 'single') echo '';
	else
		if ($frequency == 'monthly') echo 'Mensilmente';
	else echo 'Annualmente';
	?>
</button>


<form method="get">
	<b>Seleziona quanto vuoi donare: </b>
<?php
	if ($email != null) {
?>
	<input type="hidden" name="email" value="<?php echo $email ?>">
<?php
	}
?>

<?php
	if ($loc != null) {
?>
	<input type="hidden" name="loc" value="<?php echo $loc ?>">
<?php
	}
?>
	<input style='width: 5em;' type='number' name='quantity' min='10' value='<?php echo $quantity ?>'>
	Euro<br>
	<b>Frequenza con cui vuoi donare: </b>
	<select name="frequency">
		<option value="single" <?php if($frequency == "single") echo ' selected' ?> >Singola</option>
		<option value="monthly" <?php if($frequency == "monthly") echo ' selected' ?> >Mensile</option>
		<option value="annually" <?php if($frequency == "annually") echo ' selected' ?> >Annuale</option>
	</select><br>
	<button type='submit' >Aggiorna il pulsante donazione</button>
</form> 


<div id="error-message"></div>

<script>
	var quantity = <?php echo $quantity ?>;
	var plan = "<?php echo $plan ?>";


	var checkoutButton = document.getElementById('checkout-button-' + plan);
	checkoutButton.addEventListener('click', function () {
    // When the customer clicks on the button, redirect
    // them to Checkout.
    stripe.redirectToCheckout({
    	items: [{<?php if($frequency == 'single') echo 'sku'; else echo 'plan'; ?>: plan, quantity: quantity}],

    	
    	<?php if($email != null) echo "customerEmail: '$email',"; ?>

      // Note that it is not guaranteed your customers will be redirected to this
      // URL *100%* of the time, it's possible that they could e.g. close the
      // tab between form submission and the redirect.
      successUrl: window.location.protocol + '//www.energiaemobilitasostenibile.org/success',
      cancelUrl: window.location.protocol + '//www.energiaemobilitasostenibile.org/canceled',
    })
    .then(function (result) {
    	if (result.error) {
        // If `redirectToCheckout` fails due to a browser or network
        // error, display the localized error message to your customer.
        var displayError = document.getElementById('error-message');
        displayError.textContent = result.error.message;
      }
    });
  });
</script>
</body>