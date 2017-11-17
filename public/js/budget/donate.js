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

var addDonationToList = donation => {
    $("#donations-list .list").prepend(
        '<div class="player color' + donation.player.rColor + '" style="display:none;">' +
            '<a href="/embassy/player-' + donation.player.id + '">' +
                '<img src="/public/media/avatar/small/' + donation.player.avatar + '.png" alt="' + donation.player.name + '" class="picto" />' +
            '</a>' +
            '<span class="title">' + new Date(Date.parse(donation.created_at.date)).toLocaleString() + '</span>' +
            '<strong class="name">' + donation.player.name + '</strong>' +
            '<span class="experience">' + (donation.amount / 100).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' €</span>' +
        '</div>'
    ).find('.player:first-child').slideDown('fast');
};

var increaseDonationAmount = amount => {
    $("#donations-amount .value").fadeTo('fast', 0, function() {
        $(this).text(
            (((parseInt($(this).text().replace(/\D/g,'')) + amount) / 100)
            .toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})) + ' €'
        ).fadeTo('fast', 1);
    });
};

function setOutcome(result) {
  var errorElement = document.querySelector('.error');
  errorElement.classList.remove('visible');
  var successElement = document.querySelector('.success');
  successElement.classList.remove('visible');

  if (result.token) {
    var amount = document.querySelector('input[name=amount]').value;
    donationButton.setWording('Validation du paiement');
    
    if (amount.length === 0) {
        donationButton.unlock();
        errorElement.textContent = 'Aucun montant n\'a été spécifié';
        errorElement.classList.add('visible');
        return;
    }

    $.ajax({
        url: '/ajax/a-donate',
        type: 'POST',
        data: JSON.stringify({amount: amount, data: result.token}),
        contentType: 'application/json',
        dataType: 'json',
        success: function(response) {
            successElement.textContent = response.message;
            successElement.classList.add('visible');
            donationButton.unlock();
            addDonationToList(response.donation);
            increaseDonationAmount(response.donation.amount);
            $("#stripe-donation-form").trigger('reset');
        },
        error: function(error) {
            donationButton.unlock();
            errorElement.textContent = result.error.message;
            errorElement.classList.add('visible');
        }
    });

  } else if (result.error) {
    donationButton.unlock();
    errorElement.textContent = result.error.message;
    errorElement.classList.add('visible');
  }
}

var donationButton = {
    state: false,
    wording: 'Valider',
    isLocked: () => this.state,
    lock: () => {
        donationButton.state = true;
        $("#stripe-donation-form button")
            .addClass('locked')
            .prepend('<img src="/public/media/budget/padlock.svg" alt="lock">')
        ;
    },
    unlock: () => {
        donationButton.state = false;
        donationButton.setWording('Valider');
        $("#stripe-donation-form button")
            .removeClass('locked')
            .find('img')
            .remove()
        ;
    },
    setWording: (wording) => {
        donationButton.wording =  wording;
        $("#stripe-donation-form button span").text(wording);
    }
};

card.on('change', function(event) {
  setOutcome(event);
});

var donationLock = false;
document.querySelector('#stripe-donation-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (donationButton.isLocked()) return false;
    donationButton.lock();
    donationButton.setWording('Envoi à la passerelle de paiement');
    var form = document.querySelector('#stripe-donation-form');
    $(form).find('button').addClass('locked');
    stripe.createToken(card).then(setOutcome);
});