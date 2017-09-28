<?php

$playerManager = $this->getContainer()->get('zeus.player_manager');
$session = $this->getContainer()->get('session_wrapper');

$godSons = $playerManager->getGodSons($session->get('playerId'));

# display
echo '<div class="component player rank">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';?>
        <script src="https://js.stripe.com/v3/"></script>

        <form id="stripe-donation-form">
          <label>
            <input name="cardholder-email" class="field is-empty" placeholder="john.doe@gmail.com" />
            <span><span>Email</span></span>
          </label>
          <label>
            <input name="amount" class="field is-empty" placeholder="1000 (10€)" />
            <span><span>Montant</span></span>
          </label>
          <label>
            <div id="card-element" class="field is-empty"></div>
            <span><span>Carte bancaire</span></span>
          </label>
          <button type="submit">Valider</button>
          <div class="outcome">
            <div class="error" role="alert"></div>
            <div class="success">
              Success! Your Stripe token is <span class="token"></span>
            </div>
          </div>
        </form>
        <script type="text/javascript">
            // Create a Stripe client
            var stripe = Stripe("<?= $this->getContainer()->getParameter('stripe_public_key'); ?>");
            var elements = stripe.elements();

            var card = elements.create('card', {
              iconStyle: 'solid',
              style: {
                base: {
                  iconColor: '#8898AA',
                  color: 'white',
                  lineHeight: '36px',
                  fontWeight: 300,
                  fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                  fontSize: '19px',

                  '::placeholder': {
                    color: '#8898AA',
                  },
                },
                invalid: {
                  iconColor: '#e85746',
                  color: '#e85746',
                }
              },
              classes: {
                focus: 'is-focused',
                empty: 'is-empty',
              },
            });
            card.mount('#card-element');

            var inputs = document.querySelectorAll('input.field');
            Array.prototype.forEach.call(inputs, function(input) {
              input.addEventListener('focus', function() {
                input.classList.add('is-focused');
              });
              input.addEventListener('blur', function() {
                input.classList.remove('is-focused');
              });
              input.addEventListener('keyup', function() {
                if (input.value.length === 0) {
                  input.classList.add('is-empty');
                } else {
                  input.classList.remove('is-empty');
                }
              });
            });

            function setOutcome(result) {
              var successElement = document.querySelector('.success');
              var errorElement = document.querySelector('.error');
              successElement.classList.remove('visible');
              errorElement.classList.remove('visible');

              if (result.token) {
                // Use the token to create a charge or a customer
                // https://stripe.com/docs/charges
                successElement.querySelector('.token').textContent = result.token.id;
                successElement.classList.add('visible');
                
                var amount = document.querySelector('input[name=amount]').value;
                
                $.ajax({
                    url: '/ajax/a-donate',
                    type: 'POST',
                    data: JSON.stringify({amount: amount, data: result.token}),
                    contentType: 'application/json',
                    success: function(response) {
                        console.log(response);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                })
                
              } else if (result.error) {
                errorElement.textContent = result.error.message;
                errorElement.classList.add('visible');
              }
            }

            card.on('change', function(event) {
              setOutcome(event);
            });

            document.querySelector('#stripe-donation-form').addEventListener('submit', function(e) {
                e.preventDefault();
                var form = document.querySelector('#stripe-donation-form');
                var extraDetails = {
                  name: form.querySelector('input[name=cardholder-email]').value,
                };
                stripe.createToken(card, extraDetails).then(setOutcome);
            });
        </script>
    <?php
		echo '</div>';
	echo '</div>';
echo '</div>';