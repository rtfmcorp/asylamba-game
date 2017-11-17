<div class="component player rank">
	<div class="head skin-2"></div>
	<div class="fix-body">
		<div class="body">
            <script src="https://js.stripe.com/v3/"></script>

            <form id="stripe-donation-form">
                <h4>Formulaire de donation</h4>
                <label>
                    <input name="amount" class="field is-empty" placeholder="1000 (10€)" />
                    <span><span>Montant</span></span>
                </label>
                <label>
                    <div id="card-element" class="field is-empty"></div>
                    <span><span>Carte bancaire</span></span>
                </label>
                    <button type="submit"><span>Valider</span></button>
                <div class="outcome">
                    <div class="error" role="alert"></div>
                    <div class="success"></div>
                </div>
            </form>
            <script type="text/javascript">
                var stripe = Stripe("<?= $this->getContainer()->getParameter('stripe_public_key'); ?>");
            </script>
            <script type="text/javascript" src="<?= JS ?>budget/donate.js"></script>
		</div>
	</div>
</div>