<fieldset id="payment">
    <legend><?php echo $text_credit_card; ?></legend>
	<?php if($description){ ?>
		<p><?php echo $description; ?></p>
	<?php } ?>
</fieldset>
<form method="post" id="eftsecure-form" action="https://services.callpay.com/eft/">
    <input type="hidden" name="token" value="<?php echo $token; ?>"/>
    <input type="hidden" name="amount" value="<?php echo $amount; ?>"/>
    <input type="hidden" name="merchant_reference" value="<?php echo $merchant_reference; ?>"/>
    <input type="hidden" name="organisation_id" value="<?php echo $organisation_id; ?>"/>
    <input type="hidden" name="success_url" value="<?php echo $success_url; ?>"/>
    <input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>"/>
</form>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$('#eftsecure-form').submit();
});
//--></script>