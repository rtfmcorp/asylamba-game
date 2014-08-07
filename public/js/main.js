jQuery(document).ready(function($) {
// ################################# //
// #### ALERT CONTROLLER MODULE #### //
// ################################# //
	alertController = {
		// propriétés du module
		queue: [],
		counter: 0,
		launcher: 500,
		speed: 1500,

		// ressources du modules
		imgPath: game.path + 'public/media/alert/',
		resources: {
			'100': ['information', 'info.png'],
			'101': ['erreur', 'error.png'],
			'102': ['réussite', 'success.png'],
			'103': ['erreur utilisateur', 'fillform.png'],

			'200': ['information', 'bug.png'],
			'201': ['erreur', 'bug.png'],
			'202': ['réussite', 'bug.png'],

			'300': ['construction terminée', 'generator.png'],
			'301': ['raffinerie pleine', 'refinery.png'],
			'302': ['technologie achevée', 'techno.png'],
			'303': ['vaisseaux livrés', 'dock1.png'],
			'304': ['vaisseau livré', 'dock2.png'],
			'305': ['---', '###.png'],
			'306': ['---', '###.png'],
			'307': ['caisse vide', 'credit.png'],
			'308': ['avancée technologique', 'research.png'],
			'309': ['centre de notification', 'message.png'],
			'310': ['messagerie', 'message.png'],
			'311': ['contre-espionnage', 'antispy.png'],
			'312': ['combat', 'fight.png'],
			'313': ['transaction effectuée', 'market.png']
		},

		// initialisation : parse et lance le runtime
		init: function() {
			$('#alert-content li').each(function(i) {
				var alert = $(this);
				alertController.add(alert.data('type'), alert.html());
				alert.remove();
			});

			$('#alert-content').remove();
			alertController.runtime();
		},

		// ajoute une alert à la liste d'attente
		add: function(type, content) {
			var ctn   = new String();
			var title = (alertController.resources[type] != undefined) ? alertController.resources[type][0] : 'alerte inconnue';
			var img   = (alertController.resources[type] != undefined) ? alertController.resources[type][1] : '7';

			ctn += '<li data-title="' + title + '" data-content="' + content + '">';
			ctn += '<img src="' + alertController.imgPath + img + '" alt="' + title + '" />'
			ctn += '</li>';

			alertController.queue.push(ctn);
		},

		// affiche la prochaine alert
		display: function() {
			var alert = alertController.queue[0];
			alertController.queue.shift();
			alertController.counter++;

			$('#alert').append(alert);

			var alertElement = $('#alert li:last-child()');
			alertElement.css({
				'position': 'absolute',
				'opacity': 0,
				'bottom': 3,
				'right': 0
			}).animate({
					'right': ($('#alert').width() - (alertController.counter * 67)),
					'opacity': 1
				}, alertController.speed, function() {
					alertElement.css({
						'position': 'relative',
						'bottom': 0,
						'right': 0
					});
			});
		},

		// supprime une alert affichée
		hide: function(alert) {
			alertController.counter--;
			$('.alert-bull').remove();
			alert.animate({
					'bottom': -30,
					'opacity': 0
				}, 250, function() {
					alert.remove();
			});
		},

		// lance l'affichage de la prochaine alert
		runtime: function() {
			setInterval(function() {
				if (alertController.queue.length > 0) {
					alertController.display();
				}
			}, alertController.launcher);
		}
	};

	// initialisation du module
	alertController.init();

	// suppression d'une alert
	$('#alert li').live('click', function(e) {
		alertController.hide($(this));
	});

	// affichage de l'info box
	$('#alert li').live('mouseover', function() {
		var title 	= $(this).data('title');
		var content = $(this).data('content');

		$('body').append('<span class="alert-bull"><span class="title">' + title + '</span>' + content + '</span>');
		var bull = $('.alert-bull');
		bull.css('left', $(this).offset().left + 'px');
		bull.css('top', ($(this).offset().top - bull.height() - 18) + 'px');

		$('#alert li').live('mouseout', function() {
			$('.alert-bull').remove();
		});
	});

// ################################# //
// ####### UNI INVEST MODULE ####### //
// ################################# //
	uniInvest = {
		// propriétés du module
		run: false,
		total: 0,

		percent: {
			natural: 0,
			social: 0,
			informatic: 0,
			life: 0,
			rest: 0
		},

		// initialisation
		init: function() {
			uniInvest.total = $('#uni-total-invest').data('invest');

			uniInvest.percent.natural = $('#uni-percent-natural').data('percent');
			uniInvest.percent.social = $('#uni-percent-social').data('percent');
			uniInvest.percent.informatic = $('#uni-percent-informatic').data('percent');
			uniInvest.percent.life = $('#uni-percent-life').data('percent');

			uniInvest.percent.rest = 100 - uniInvest.percent.natural - uniInvest.percent.social - uniInvest.percent.informatic - uniInvest.percent.life;

			uniInvest.run = true;
		},

		// 
		update: function(type, quantity) {
			if (uniInvest.run == false) {
				uniInvest.init();
			}

			if (quantity < 0) {
				quantity = Math.abs(quantity);

				if (uniInvest.percent[type]  - quantity >= 0) {
					uniInvest.percent[type] -= quantity;
					uniInvest.percent.rest  += quantity;
					$.get(game.path + 'ajax/a-decreaseinvestuni/category-' + type);
				}
			} else {
				if (uniInvest.percent.rest > 0 && uniInvest.percent[type] < 100) {
					uniInvest.percent[type] += quantity;
					uniInvest.percent.rest  -= quantity;
					$.get(game.path + 'ajax/a-increaseinvestuni/category-' + type);
				} else {
					alertController.add(101, 'Pas assez de points libres');
				}
			}

			utils.numberFormat(Math.round(uniInvest.total * uniInvest.percent[type] / 100));

			$('#uni-percent-' + type + ' .uni-value').text(uniInvest.percent[type]);
			$('#uni-percent-' + type + ' .progress-bar .content').css('width', uniInvest.percent[type] + '%');
			$('#uni-percent-rest .uni-value').text(uniInvest.percent.rest);
			$('#uni-invest-' + type + ' .uni-value').text(utils.numberFormat(Math.round(uniInvest.total * uniInvest.percent[type] / 100)));

			if (uniInvest.percent.rest > 0) {
				$('#uni-percent-rest').css('display', 'block');
			} else {
				$('#uni-percent-rest').css('display', 'none');
			}
		}
	};

	$('.uni-invest-button.increase').live('click', function(e) {
		e.preventDefault();
		uniInvest.update($(this).data('type'), 1);
	});
	$('.uni-invest-button.decrease').live('click', function(e) {
		e.preventDefault();
		uniInvest.update($(this).data('type'), -1);
	});

// ########################################## //
// ####### MOVE SHIP BETWEEN SQUADRON ####### //
// ########################################## //
	squadronTransfer = {
		obj: {
			dock: undefined,
			squadron: undefined,
			army: undefined
		},

		ref: {
			commander: 0,
			base: 0
		},

		squadronSelected: undefined,
		token: false,

		// initialisation
		init: function() {
			if ($('.baseTransfer').length == 1) {
				// ref obj
				this.obj.dock = $('.baseTransfer .dock');
				this.obj.squadron = $('.baseTransfer .squadron');
				this.obj.army = $('.commanderTransfer');

				// ref data
				this.ref.commander = $('.baseTransfer').data('commander');
				this.ref.base = $('.baseTransfer').data('base');
			}
		},

		// change l'esquadrille selectionnée
		changeSquadron: function(id) {
			// remise a zero
			this.squadronSelected = undefined;
			squadronTransfer.obj.squadron.find('a').each(function(i) {
				$(this).addClass('empty');
				$(this).find('.quantity').text('0');
			});

			this.obj.army.find('.squadron').each(function(i) {
				if (id == $(this).data('squadron-id')) {
					$(this).addClass('active');
					squadronTransfer.squadronSelected = $(this).data('squadron-id');
					var ships = $(this).data('squadron-ships');

					squadronTransfer.obj.squadron.find('a').each(function(i) {
						if (ships[i] > 0) {
							$(this).removeClass('empty');
							$(this).find('.quantity').text(ships[i]);
						}
					});

				} else {
					$(this).removeClass('active');
				}
			});
		},

		// déplace un/des vaisseau/x vers le dock
		move: function(direction, shipId, quantity) {
			if (this.squadronSelected == undefined) {
				alertController.add(101, 'Vous devez d\'abord sélectionner une escadrille pour transférer des vaisseaux');
			} else {
				var olQuantity = parseInt(this.obj.squadron.find('a:nth-child(' + (shipId + 1) + ') .quantity').text());
				var orQuantity = parseInt(this.obj.dock.find('a:nth-child(' + (shipId + 1) + ') .quantity').text());

				if (direction == 'ctb') {
						quantity = (quantity == undefined) ? olQuantity : quantity;
					var nlQuantity = olQuantity - quantity;
					var nrQuantity = orQuantity + quantity;
				} else {
						quantity = (quantity == undefined) ? orQuantity : quantity;
					var nlQuantity = olQuantity + quantity;
					var nrQuantity = orQuantity - quantity;
				}

				if (direction == 'ctb' && nlQuantity < 0) {
					// alertController.add(101, 'Pas de vaisseau de ce type dans l\'esquadrille');	
				} else if (direction == 'btc' && nrQuantity < 0) {
					// alertController.add(101, 'Pas de vaisseau de ce type dans les hangars');	
				} else {
					if (direction == 'ctb') {
						this.updateSquadron(this.squadronSelected, 'remove', shipId, Math.abs(quantity));
					} else {
						this.updateSquadron(this.squadronSelected, 'add', shipId, Math.abs(quantity));
					}

					if (!this.token) {
						alertController.add(101, 'Esquadrille pleine');
					} else {
						var lShip = this.obj.squadron.find('a:nth-child(' + (shipId + 1) + ')');
						var rShip = this.obj.dock.find('a:nth-child(' + (shipId + 1) + ')');

						this.updateShip(lShip, nlQuantity);
						this.updateShip(rShip, nrQuantity);

						// envoyer la requête
						$.get(game.path 
							+ 'ajax/a-assignship/base-' + this.ref.base 
							+ '/commander-' + this.ref.commander 
							+ '/direction-' + direction
							+ '/squadron-' + this.squadronSelected
							+ '/ship-' + shipId
							+ '/quantity-' + quantity);
					}
				}
			}
		},

		updateShip: function(obj, quantity) {
			obj.find('.quantity').text(quantity);

			if (quantity <= 0) {
				obj.addClass('empty');
			} else {
				obj.removeClass('empty');
			}
		},

		updateSquadron: function(id, mode, ship, quantity) {
			if (this.squadronSelected == undefined) {
				return false;
			}

			this.obj.army.find('.squadron').each(function(i) {
				if (id == $(this).data('squadron-id')) {
					var pev  = $(this).data('squadron-pev');
					var nPev = 0;

					if (mode == 'add') {
						nPev = pev + (quantity * game.shipsPev[ship]);
					} else {
						nPev = pev - (quantity * game.shipsPev[ship]);
					}

					if (nPev < 0 || nPev > 100) {
						squadronTransfer.token = false;
					} else {
						var ships = $(this).data('squadron-ships');
						if (mode == 'add') {
							ships[ship] += quantity;
						} else {
							ships[ship] -= quantity;
						}

						$(this).find('em').text(nPev + '/100');
						$(this).data('squadron-pev', nPev);
						$(this).data('squadron-ships', ships);

						squadronTransfer.token = true;
					}
				}
			});
		}
	};

	squadronTransfer.init();

	$('.commanderTransfer .squadron').live('click', function(e) {
		if ($(this).hasClass('active')) {
			squadronTransfer.changeSquadron(undefined);
		} else {
			squadronTransfer.changeSquadron($(this).data('squadron-id'));
		}
	});

	$('.baseTransfer .squadron a').live('click', function(e) {
		squadronTransfer.move('ctb', $(this).data('ship-id'), 1);
	});

	$('.baseTransfer .dock a').live('click', function(e) {
		squadronTransfer.move('btc', $(this).data('ship-id'), 1);
	});

// #################################### //
// #### CLASSE UTILITAIRE GENERALE #### //
// #################################### //
	utils = {
		addPlural: function(number, plural, expression) {
			if (!plural) { plural = ''; }
			if (!expression) { expression = ''; }
			if (number > 1) {
				return (expression == '' && plural == '') ? 's' : plural;
			} else {
				if (expression == '') {
					return '';
				} else {
					return expression;
				}
			}
		},

		trim: function(str, chars) {
			var chars = chars || '\s';
			return str.replace(new RegExp('[' + chars + ']+$', 'g'), '');
		},

		numberFormat: function(number) {
			var nbr = number.toString();
				number = new String();
			for (var i = 0; i < nbr.length; i++) {
				if (i % 3 == 2) {
					number = ' ' + nbr[nbr.length - 1 - i] + number;
				} else {
					number = nbr[nbr.length - 1 - i] + number;
				}
			};
			return number;
		},

		arraySum: function(array) {
			var sum = 0;
			for (var i = 0; i < array.length; i++) {
				sum += array[i];
			};
			return sum;
		}
	};

// ################################# //
// #### TIMER CONTROLLER MODULE #### //
// ################################# //
	time = {
		timer: function(target) {
			var timerT = $(target).html().split('\'');
			var timerM = timerT[0]; var timerS = timerT[1];

			if (timerS > 0) {
				var outputS = timerS - 1;
				if (outputS < 10) { outputS = '0' + outputS; }
				$(target).html(timerM + '\'' + outputS);
			} else if (timerM > 0) {
				var outputS = 59;
				var outputM = timerM - 1;
				if (outputM < 10) { outputM = '0' + outputM; }
				$(target).html(outputM + '\'' + outputS);
			} else {
				window.location.reload();
			}
		},

		secondToFormat: function(seconds, format) {
			if (!format) { format = 'large'; }

			var toReturn = '';
			var rel = Math.floor(seconds / 3600);
			var min = Math.floor((seconds - (rel * 3600)) / 60);
			var sec = seconds - (rel * 3600) - (min * 60);

			if (format == 'large') {
				toReturn += (rel > 0) ? rel + ' ' + 'relève' + utils.addPlural(rel) + ', ' : '';
				toReturn += (min > 0) ? min + ' ' + 'minute' + utils.addPlural(min) + ', ' : '';
				toReturn += (sec > 0) ? sec + ' ' + 'seconde' + utils.addPlural(sec) : '';
			} else if (format == 'short') {
				toReturn += rel + ' ' + 'relève' + utils.addPlural(rel) + ', ' + min + ' ' + '\'' + utils.addPlural(min) + ', ' + sec + ' ' + '\'\'' + utils.addPlural(sec);
			} else if (format == 'lite') {
				min = (min > 9) ? min : '0' + min;
				sec = (sec > 9) ? sec : '0' + sec;
				toReturn += rel + ':' + min + ':' + sec;
			}
			return utils.trim(toReturn, ', ');
		}
	};

	$(function() {
		$('.progress').each(function(i) {
			var queue = $(this);
			var bar = queue.find('.progress-bar');
			var text = queue.find('.progress-text');

			var totalTime = parseInt(queue.data('progress-total-time'));
			var currentTime = parseInt(queue.data('progress-current-time'));
			var format = queue.data('progress-output');
			var reload = !Boolean(queue.data('progress-no-reload'));

			var position;

			setInterval(function() {
				currentTime--;

				if (currentTime <= 0 && reload) {
					window.location.reload();
				} else if (currentTime <= 0 && !reload) {
					// no
				} else {
					position = (((totalTime - currentTime) / totalTime) * 100);

					text.html(time.secondToFormat(currentTime, format));
					bar.css('width', position + '%');
				}
			}, 1000);
		});

		$('.progress-ship').each(function(i) {
			var container = $(this);
			var bar = container.find('.bar');
			var timer = container.find('.time');

			var total = parseInt(container.data('progress-total-time'));
			var reversed = Boolean(container.data('progress-reverse'));

			var current = reversed
				? parseInt(container.data('progress-current-time'))
				: total - parseInt(container.data('progress-current-time'));

			var position;

			setInterval(function() {
				current--;

				if (current > 0) {
					timer.html(time.secondToFormat(current, 'lite'));

					if (reversed) {
						position = (((total - current) / total) * 100);
					} else {
						position = (((total - current) / total) * 100);
					}

					if (reversed) {
						bar.css('width', (100 - position) + '%');
					} else {
						bar.css('width', position + '%');
					}
				}
			}, 1000);
		});
	});

	// GENERAL SHOW/HIDE FUNCTION
	// --------------------------
	$('.sh').live('click', function(e) {
		e.preventDefault();
		var target = $('#' + $(this).data('target'));

		if (target.css('display') == 'none') {
			$('.overbox').css('display', 'none');
			target.css('display', 'block');
		} else {
			target.css('display', 'none');
		}
	});

	$('#container').live('click' ,function(e) {
		$('.overbox').css('display', 'none');
	});

	$('.switch-class').live('click', function(e) {
		$(this).toggleClass($(this).data('class'));
	});

	$('.switch-class-parent').live('click', function(e) {
		$(this).parent().toggleClass($(this).data('class'));
	});

	$('.notif.unreaded .read-notif').live('click', function(e) {
		var notif  = $(this).parent();
		var notifs = $('#general-notif-container');
		var count  = parseInt(notifs.find('span.number').text()) - 1;

		notif.removeClass('unreaded');
		notifs.find('span.number').text(count);
		if (count == 0) {
			notifs.removeClass('active');
		}
		$.get(game.path + 'ajax/a-readnotif/notif-' + notif.data('notif-id'));
	});

	// STD AJAX ACTION LINK
	$('.notif a.ajax-action').live('click', function(e) {
		e.preventDefault();

		var notif = $(this).parent().parent();
		var ajaxTarget = $(this).data('ajax-target');

		if (ajaxTarget !== undefined) {
			$.get(ajaxTarget)
			 .done(function(data) {
			 	notif.css('display', 'none');
			}).fail(function() {
				alertController.add(101, 'Une erreur réseaux est survenue');
			});
		} else {
			alertController.add(101, 'Une erreur réseaux est survenue');
		}
	});

	// GENERAL INFOBULL CORE
	// ---------------------
	drawBull = function(target, xpos, ypos) {
		if (target.attr('title') == undefined) {
			return false;
		} else {
			var content = target.attr('title');
				target.attr('title', '');

			$('body').append('<span class="bull ' + ypos + ' ' + xpos + '">' + content + '</span>');
			var bull = $('.bull');

			if (ypos == 'top') {
				var sizeTop  = target.offset().top - bull.height() - 18;
			} else {
				var sizeTop  = target.offset().top + target.height() + 12;
			}

			if (xpos == 'left') {
				var sizeLeft  = target.offset().left + target.width() - bull.width() - 2;
			} else {
				var sizeLeft  = target.offset().left;
			}

			bull.css({
				top:  sizeTop,
				left: sizeLeft
			});
			bull.fadeToggle(50);

			$('.hb').live('mouseout', function(e) {
				target.attr('title', content);
				$('.bull').remove();
			});
		}
	}

	$('.hb').live('mouseover', function(e) {
		if ($(this).hasClass('lt')) {
			drawBull($(this), 'left', 'top');
		} else if ($(this).hasClass('lb')) {
			drawBull($(this), 'left', 'bottom');
		} else if ($(this).hasClass('rt')) {
			drawBull($(this), 'right', 'top');
		} else if ($(this).hasClass('rb')) {
			drawBull($(this), 'right', 'bottom');
		} else {
			drawBull($(this), 'left', 'bottom');
		}
	});

	$('.releve-timer').each(function() {
		var target = $(this);

		setInterval(function() {
			var timerT = target.text().split(':');
			var timerM = timerT[0]; var timerS = timerT[1];

			if (timerS > 0) {
				var outputS = timerS - 1;
				if (outputS < 10) { outputS = '0' + outputS; }
				target.html(timerM + ':' + outputS);
			} else if(timerM > 0) {
				var outputS = 59;
				var outputM = timerM - 1;
				if (outputM < 10) { outputM = '0' + outputM; }
				target.html(outputM + ':' + outputS);
			} else {
				target.html('--:--');
			}
		}, 1000);
	});

	// dynamic ships box
	$('.dynamic-ship-box .ship-pack').live('keyup', function(e) {
		var container	= $(this).parent();
		var input    	= $(this).val(); if (isNaN(input) || input < 1 || input > 99) { input = 0; }
		var maxShip  	= container.data('maxship');
		var cost     	= container.data('ship-cost');
		var duration    = container.data('ship-second');
		var target		= '';

		link = container.find('.button').attr('href').split('/');
		for (var i = 0; i < link.length - 1; i++) {
			target += link[i] + '/';
		};
		target += 'quantity-' + input;

		cost = cost * input;
		duration = time.secondToFormat((duration * input), 'lite');

		if (input > maxShip || input == 0) {
			container.find('.button').addClass('disable');
		} else {
			container.find('.button').removeClass('disable');
		}

		// format number
		cost = cost.toString();
		var arrayCost = new Array();
		var formatCost = '';
		for (var i = cost.length - 1; i >= 0; i--) {
			arrayCost.push(cost[i]);
			if ((i - cost.length) % 3 == 0) {
				arrayCost.push(' ');
		}}
		arrayCost = arrayCost.reverse();
		for (var i in arrayCost) { formatCost += arrayCost[i]; }

		// display
		container.find('.final-cost').text(formatCost);
		container.find('.final-time').text(duration);
		container.find('.final-number').text(input);
		container.find('.button').attr('href', target);
	});

	$('.dynamic-ship-box .button').live('click', function(e) {
		if ($(this).hasClass('disable')) {
			e.preventDefault();
		}
	});

	/* MORE-MESSAGE */
	$('.more-item').live('click', function(e) {
		e.preventDefault();
		var link = $(this);
		var dir  = link.data('dir');

		$.get(link.attr('href'))
		 .done(function(data) {
		 	link.after(data);
		 	link.remove();

		 	if (dir == 'top') {
		 		columnController.move($('.no-scrolling .component .fix-body.hover'), 'top');
		 	} else {
		 		columnController.move($('.no-scrolling .component .fix-body.hover'), 'bottom');
		 	}
		}).fail(function() {
			alertController.add(101, 'chargement des données interrompu');
		});
	});

	$('.more-thread').live('click', function(e) {
		e.preventDefault();
		var link = $(this);
		var data;

		$.get(link.attr('href'))
		 .done(function(data) {
		 	data = $.parseHTML(data);

		 	console.log(data.length);

	 		render.removeComponent(-1, 500, function() {
	 			for (var i = 0; i < data.length; i++) {

					if (i + 1 == data.length) {
						render.addComponent(-1, data[i], 500, function() {
							panelController.move((data.length - 1), 'right');
						});
					} else {
						render.addComponent(-1, data[i], 500);
					}
	 			};
	 		});
		}).fail(function() {
			alertController.add(101, 'chargement des données interrompu');
		});
	});

	// using module
	$('.autocomplete-player').autocomplete(game.path + 'ajax/a-autocompleteplayer/');
});