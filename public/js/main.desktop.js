jQuery(document).ready(function($) {
	// RENDERING MODULE
	// ################
	render = {
		calling: 0,

		viewport: {
			w: 0,
			h: 0
		},

		column: {
			number: 0,
			defaultSize: 300
		},

		make: function() {
			render.calling++;
			render.column.number = 0;

			render.viewport.h = parseInt($('body').css('height'));
			render.viewport.w = parseInt($('body').css('width'));

			// hauteur du #contenair
			var hBar = parseInt($('#nav').css('height'));
			var hContent = render.viewport.h - (2 * (hBar + 3));

			$('#container').css('height', hContent);

			// traitement des colonnes
			$('.component').each(function(i) {
				var currentColumn = $(this);
				render.column.number += parseInt(parseInt(currentColumn.css('width')) / render.column.defaultSize);

				currentColumn.css('height', hContent);
				currentColumn.find('.fix-body').css('height', hContent - parseInt(currentColumn.find('.head').css('height')));

				if ($('body').hasClass('no-scrolling') && !currentColumn.hasClass('hasMover')) {
					currentColumn.find('.fix-body').append('<a href="#" class="toTop"></a>');
					currentColumn.find('.fix-body').append('<a href="#" class="toBottom"></a>');
					currentColumn.addClass('hasMover');

					columnController.move(currentColumn.find('.fix-body'), 'top');
				}
			});

			$('#content').css('width', (render.column.number * render.column.defaultSize + 10000));

			if (render.calling == 1) {
				var initSftr = parseInt($('body').data('init-sftr'));
				if (initSftr > 0) {
					panelController.position += initSftr;
				}
				panelController.move(0, 'left', 0);
			}
			
			if ($('#map').length == 1) {
				mapController.init();
			}
		},

		addComponent: function(position, content, time, callback) {
			var newColumn;
			var time = (time == undefined) ? 500 : time;

			if (position > 0) {
				$('.component:nth-child(' + position + ')').after(content);
				newColumn = $('.component:nth-child(' + (position + 1) + ')');
			} else {
				$('.component:last').after(content);
				newColumn = $('.component:last');
			}

			var head = newColumn.find('.head');
			var body = newColumn.find('.body');
			var size = 0;
			if (newColumn.hasClass('size2')) {
				size = 2;
			} else if (newColumn.hasClass('size3')) {
				size = 3;
			} else {
				size = 1;
			}

			body.css('display', 'none');
			newColumn.css('width', 0);

			newColumn.animate({
				'width': (render.column.defaultSize * size)
				}, time, function() {
					body.css({
						'display': 'block',
						'left': '20px',
						'opacity': '0'
					});

					render.make();

					body.animate({
						left: 0,
						opacity: 1
					}, 300);

					if (callback != undefined) {
						callback();
					}
			});
		},

		removeComponent: function(position, time, callback) {
			var time = (time == undefined) ? 500 : time;

			if (position > 0) {
				var	component = $('.component:nth-child(' + (position + 1) + ')');
			} else {
				var	component = $('.component:last');
			}
			
			component.find('.head').html('');
			component.find('.body').html('');

			component.animate({
				'width': 0
			}, time, function() {
				component.remove();
				if (callback != undefined) {
					callback();
				}
			});
		}
	};

	// lancer le render lors du chargement et du resize
	$(window).on('load resize', function() {
		render.make();
	});

	// ADD INFOPANEL COMPONENT
	// ##########################
	var infoPanel = {
		addedColumn: undefined
	};

	$('.addInfoPanel').on('click', function() {
		var that  = $(this);
		var index = parseInt(that.parents('.component').index()) + 1;
		var query = game.path + 'ajax/a-';

		switch (that.data('info-type')) {
			case 'building': query += 'buildingpanel/building-' + that.data('building-id') + '/lvl-' + that.data('building-current-level'); break;
			case 'ship': query += 'shippanel/ship-' + that.data('ship-id'); break;
			case 'techno': query += 'technopanel/techno-' + that.data('techno-id'); break;
		}

		$.get(query)
		 .done(function(data) {
		 	if (infoPanel.addedColumn != undefined) {
		 		render.removeComponent(infoPanel.addedColumn, 500, function() {
					render.addComponent(index, data);
		 		});
			 	infoPanel.addedColumn = undefined;
			 	infoPanel.addedColumn = index;
		 	} else {
			 	infoPanel.addedColumn = index;
				render.addComponent(index, data);
		 	}
		}).fail(function() {
			alertController.add(101, 'chargement des données interrompu');
		});
	});

	$('.removeInfoPanel').live('click', function() {
		if (infoPanel.addedColumn != undefined) {
			render.removeComponent(parseInt($(this).parents('.component').index()));
		 	infoPanel.addedColumn = undefined;
	 	}
	});

	$('.build-item').live('mouseover', function() {
		$(this).find('.info').css('display', 'block');
	});
	$('.build-item').live('mouseleave', function() {
		$(this).find('.info').css('display', 'none');
	});

// MOVING COMPONANT MODULE
// #######################
	columnController = {
		smallStep: 50,

		largeStep: 300,
		time: 500,

		move: function(fixBody, direction, hasToAnimate) {
			// mode de mouvement
			var hasToAnimate = (hasToAnimate == undefined) ? true : false;

			// objet DOM
			var content = fixBody.find('.body');
			var toTop = fixBody.find('a.toTop');
			var toBottom = fixBody.find('a.toBottom');
			var hFixBody = parseInt(fixBody.css('height'));
			var hBody	 = parseInt(content.css('height'));

			
			var position = 0;
			var nextPosition  = (content.css('top') == 'auto') ? 0 : parseInt(content.css('top'));
			if (hasToAnimate) {
				nextPosition += (direction == 'top') ? columnController.largeStep : -columnController.largeStep;
			} else {
				nextPosition += (direction == 'top') ? columnController.smallStep : -columnController.smallStep;
			}

			if (hFixBody >= hBody) {
				position = 0;
				toTop.css('display', 'none');
				toBottom.css('display', 'none');
			} else if (nextPosition >= 0) {
				position = 0;
				toTop.css('display', 'none');
				toBottom.css('display', 'block');
			} else if (-(nextPosition) + hFixBody >= hBody) {
				position = -(hBody - hFixBody + 30);
				toTop.css('display', 'block');
				toBottom.css('display', 'none');
			} else {
				position = nextPosition;
				toTop.css('display', 'block');
				toBottom.css('display', 'block');
			}

			if (hasToAnimate) {
				content.stop().animate({
					'top': position
				}, columnController.time);
			} else {
				content.css({
					'top': position
				});
			}
		}
	};

	// affiche/cache les flèches directionelles
	$('.no-scrolling .component .fix-body').live('mouseover', function(e) {
		if (!$(this).hasClass('hover')) { 
			$('.component .fix-body').removeClass('hover');
			$(this).addClass('hover');
		}
	});

	// movers button event
	$('.no-scrolling .component a.toTop').live('click', function(e) {
		columnController.move($(this).parent(), 'top');
	});
	$('.no-scrolling .component a.toBottom').live('click', function(e) {
		columnController.move($(this).parent(), 'bottom');
	});

	// movers keyboard eventdocument
	$(document).keydown(function(e) {
		switch(e.keyCode) {
			case 38: columnController.move($('.no-scrolling .component .fix-body.hover'), 'top');	 break;
			case 40: columnController.move($('.no-scrolling .component .fix-body.hover'), 'bottom'); break;
			default: break;
		}
	});

	// movers scroll event FIREFOX
	$(window).bind('DOMMouseScroll', function(e) {
		e.preventDefault();
		if (e.originalEvent.detail > 0) {
			columnController.move($('.no-scrolling .component .fix-body.hover'), 'bottom', false);
		} else {
			columnController.move($('.no-scrolling .component .fix-body.hover'), 'top', false);
		}
	});
	// movers scroll event OTHERS
	$(window).bind('mousewheel', function(e) {
		e.preventDefault();
		if (e.originalEvent.wheelDelta < 0) {
			columnController.move($('.no-scrolling .component .fix-body.hover'), 'bottom', false);
		} else {
			columnController.move($('.no-scrolling .component .fix-body.hover'), 'top', false);
		}
	});

// #############################
// #### MOVING PANEL MODULE ####
// #############################
	panelController = {
		position: -2,
		maxPosition: 0,

		// déplace si possible le panneau
		move: function(nbr, direction, time) {
			var hasToAnimate = false;
			var time = (time == undefined) ? (300 * nbr) : time;
				panelController.maxPosition = render.column.number + 1 - parseInt(render.viewport.w / render.column.defaultSize);

			if (direction == 'left') {
				if ((panelController.position - nbr) >= -2) {
					panelController.position = panelController.position - nbr;
					hasToAnimate = true;
				}
			} else {
				if ((panelController.position + nbr) <= panelController.maxPosition - 1) {
					panelController.position = panelController.position + nbr;
					hasToAnimate = true;
				}
			}

			if (hasToAnimate) {
				$('#content').stop().animate({
					'left': -(panelController.position * 300) + 60
				}, time);
				$('#background-paralax').stop().animate({
					'left': -((panelController.position + 2) * 25)
				}, time);
			}

			panelController.position < 0
				? $('#movers .toLeft').hide()
				: $('#movers .toLeft').show();

			panelController.position >= panelController.maxPosition - 1
				? $('#movers .toRight').hide()
				: $('#movers .toRight').show();

			$('#content a').each(function() {
				$(this).attr('href', panelController.rewriteLink($(this).attr('href')));
			});

			$('#content form').each(function() {
				$(this).attr('action', panelController.rewriteLink($(this).attr('action')));
			});
		},

		rewriteLink: function(link) {
			if (link.indexOf('action') > -1 && !(link.indexOf('faction') > -1)) {
				if (link.indexOf('sftr') > -1) {
					link = link.replace(/sftr\-[0-9]+/, ('sftr-' + (panelController.position + 2)));
				} else {
					link += '/sftr-' + (panelController.position + 2);
				}
			}
			return link;
		}
	};

	// movers button event
	$('.toLeft').on('click', function(e) {
		e.preventDefault();
		panelController.move(1, 'left');
	});
	$('.toRight').on('click', function(e) {
		e.preventDefault();
		panelController.move(1, 'right');
	});

	// movers keyboard event
	$('body').keydown(function(e) {
		switch(e.keyCode) {
			case 37: panelController.move(1, 'left');  break;
			case 39: panelController.move(1, 'right'); break;
			default: break;
		}
	});
	
// ################################# //
// ####### MAP MOVER MODULE ######## //
// ################################# //
	var mapController = {
		map: {
			obj: $('#map'),
			ratio: $('#map').data('map-ratio'),
			size: $('#map').data('map-size'),
			overflow: 400
		},

		minimap: {
			obj: $('.mini-map'),
			size: parseInt($('.mini-map').css('width'))
		},

		params: {
			cMovingSpeed: 250,
			kMovingSpeed: 20,
			animationSpeed: 250,
			first: true,
			locked: false,
		},

		mouseMoving: {
			lastX: undefined,
			lastY: undefined
		},

		// initialise la carte
		init: function() {
			if (document.getElementById('map') != undefined) {
				mapController.resizeViewport();
				mapController.replaceViewport();
				mapController.showCoord();

				if (mapController.params.first == true) {
					mapController.moveTo(
						mapController.map.obj.data('begin-x-position'),
						mapController.map.obj.data('begin-y-position'),
						0
					);
					mapController.params.first = false;
				}
			}
		},

		// déplace la map si possbile
		move: function(left, top, animation) {
			var leftPosition = (mapController.map.obj.css('left') == 'auto') ? 0 : parseInt(mapController.map.obj.css('left'));
			var topPosition  = (mapController.map.obj.css('top')  == 'auto') ? 0 : parseInt(mapController.map.obj.css('top'));
			var animation 	 = (animation == undefined) ? 0 : animation;

			var nextLeftPosition = leftPosition + left;
			var nextTopPosition = topPosition + top;

			if (nextLeftPosition > mapController.map.overflow) {
				nextLeftPosition = mapController.map.overflow;
			} else if (nextLeftPosition < -(mapController.map.size + mapController.map.overflow - render.viewport.w)) {
				nextLeftPosition = -(mapController.map.size + mapController.map.overflow - render.viewport.w);
			}

			if (nextTopPosition > mapController.map.overflow) {
				nextTopPosition = mapController.map.overflow;
			} else if (nextTopPosition < -(mapController.map.size + mapController.map.overflow - render.viewport.h)) {
				nextTopPosition = -(mapController.map.size + mapController.map.overflow - render.viewport.h);
			}

			if (animation > 0) {
				mapController.map.obj.animate({
					'left': nextLeftPosition,
					'top': nextTopPosition
				}, animation, function() {
						mapController.replaceViewport();
						mapController.showCoord();
					}
				);
			} else {
				mapController.map.obj.css({
					'left': nextLeftPosition,
					'top': nextTopPosition
				});
				mapController.replaceViewport();
				mapController.showCoord();
			}
		},

		// affiche les coordonnées actuelle
		showCoord: function() {
			$('#coord-box').html(
				Math.ceil(-(parseInt(mapController.map.obj.css('left')) - Math.ceil(render.viewport.w / 2)) / mapController.map.ratio)
				+ ':' +
				(Math.ceil(-(parseInt(mapController.map.obj.css('top'))  - Math.ceil(render.viewport.h / 2)) / mapController.map.ratio) - 2)
			);
		},

		// résoud un couple de coordonnée et bouge vers celle-ci
		moveTo: function(x, y, animationSpeed) {
			var anms = animationSpeed;
			if (animationSpeed == undefined) { anms = 500 }

			var toLeft = (-parseInt(mapController.map.obj.css('left')) + Math.ceil(render.viewport.w / 2)) - (x * mapController.map.ratio);
			var toTop  = (-parseInt(mapController.map.obj.css('top'))  + Math.ceil(render.viewport.h / 2)) - (y * mapController.map.ratio) - 30;

			mapController.move(toLeft, toTop, anms);
		},

		// redimensionne le viewport de la minimap
		resizeViewport: function() {
			mapController.minimap.obj.find('.viewport')
				.css('width', render.viewport.w * mapController.minimap.size / mapController.map.size)
				.css('height', render.viewport.h * mapController.minimap.size / mapController.map.size);
		},

		// replace le viewport de la minimap sur les coordonnées courantes
		replaceViewport: function() {
			mapController.minimap.obj.find('.viewport')
				.css('top', -(parseInt(mapController.map.obj.css('top'))) * (mapController.minimap.size / mapController.map.size))
				.css('left', -(parseInt(mapController.map.obj.css('left'))) * (mapController.minimap.size / mapController.map.size));
		}
	};

	// évènement sur les movers
	$('#mapToLeft').live('click', function(e) 	{ mapController.move(mapController.params.cMovingSpeed, 0, mapController.params.animationSpeed); });
	$('#mapToRight').live('click', function(e) 	{ mapController.move(-mapController.params.cMovingSpeed, 0, mapController.params.animationSpeed); });
	$('#mapToTop').live('click', function(e) 	{ mapController.move(0, mapController.params.cMovingSpeed, mapController.params.animationSpeed); });
	$('#mapToBottom').live('click', function(e) { mapController.move(0, -mapController.params.cMovingSpeed, mapController.params.animationSpeed); });

	// évènement du clavier sur les movers
	$(document).keydown(function(e) {
		switch(e.keyCode) {
			case 37: mapController.move(mapController.params.kMovingSpeed, 0, 0);  break;
			case 38: mapController.move(0, mapController.params.kMovingSpeed, 0);  break;
			case 39: mapController.move(-mapController.params.kMovingSpeed, 0, 0); break;
			case 40: mapController.move(0, -mapController.params.kMovingSpeed, 0); break;
			default: break;
		}
	});

	// évènement sur les secteurs, centre la map sur le barycentre du secteur
	$('.mini-map .moveTo').on('click', function(e) {
		mapController.moveTo(
			$(this).data('x-position'),
			$(this).data('y-position')
		);
	});

	$('#map').on('mousedown', function(e) {
		mapController.params.locked = true;
		mapController.mouseMoving.lastX = e.pageX;
		mapController.mouseMoving.lastY = e.pageY;

		$(this).css('cursor', 'move');
		return false;
	});
	$(document).on('mouseup', function() {
		mapController.params.locked = false;

		$('#map').css('cursor', 'default');
		return true;
	});
	$('#map').on('mousemove', function(e) {
		if (mapController.params.locked == true) {
			e.preventDefault();

			var newX = e.pageX - mapController.mouseMoving.lastX;
			var newY = e.pageY - mapController.mouseMoving.lastY;

			mapController.move(newX, newY, 0);

			mapController.mouseMoving.lastX = e.pageX;
			mapController.mouseMoving.lastY = e.pageY;
		}
	});

// ################################# //
// ####### ACTION BOX MODULE ####### //
// ################################# //
	actionbox = {
		obj: $('#action-box'),
		relatedPlace: $('#map').data('related-place'),

		// affiche la box
		open: function() {
			actionbox.obj.animate({
				bottom: 0
			}, 300);
		},

		// masque la box
		close: function() {
			actionbox.obj.animate({
				bottom: -300
			}, 300);

			$('.loadSystem.active').removeClass('active');
		},

		// masque la box, charge le contenu et affiche la box
		load: function(systemid) {
			actionbox.close();
			$.get(game.path + 'ajax/a-loadsystem/systemid-' + systemid + '/relatedplace-' + actionbox.relatedPlace)
			 .done(function(data) {
				actionbox.obj.html(data);
				actionbox.open();

				$('.loadSystem[data-system-id="' + systemid + '"]').addClass('active');
			}).fail(function() {
				alertController.add(101, 'chargement des données interrompu');
			});
		},

		// ouvre une place
		openPlace: function(placeid) {
			$('#place-' + placeid).animate({
				width: parseInt($('#place-' + placeid).find('.content').css('width')) + 20
			}, 200);
		},

		// ferme une place
		closePlace: function(placeid) {
			$('#place-' + placeid).animate({
				width: 0
			}, 200);
		},

		// bouge à gauche si possible
		moveToLeft: function() {
			var position = parseInt(actionbox.obj.find('.system, .rank').css('left'));

			if (position + 300 > 0) {
				actionbox.obj.find('.system, .rank').animate({
					left: 0
				}, 200);
			} else {
				actionbox.obj.find('.system, .rank').animate({
					left: position + 300
				}, 200);
			}
		},

		// bouge à droite si possible
		moveToRight: function() {
			var position = parseInt(actionbox.obj.find('.system, .rank').css('left'));

			if (parseInt(actionbox.obj.find('.system ul, .rank ul').css('width')) + position >= render.viewport.w) {
				actionbox.obj.find('.system, .rank').animate({
					left: position - 300
				}, 200);
			}
		},

		// affiche les box d'actions
		swtichAction: function(elem) {
			var id = elem.data('target');

			//elem.parent().parent().find('p .subcontext').text(' / ' + elem.find('img').attr('alt'));
			elem.parent().parent().find('.right > div').each(function() {
				if ($(this).data('id') == id) {
					$(this).css('display', 'block');
				} else {
					$(this).css('display', 'none');
				}
			});
		}
	};

	$('.loadSystem').live('click', function() {
		actionbox.load($(this).data('system-id'));
	});

	$('#map, .closeactionbox').live('click', function() {
		actionbox.close();
	});

	$('#action-box .place').live('click', function() {
		var place = $(this);
		var target = place.find('a').data('target');
		$('#action-box .place').removeClass('active');

		$('#action-box .action').each(function(i) {
			if (i == target) {
				place.addClass('active');
				actionbox.openPlace(i);
			} else {
				actionbox.closePlace(i);
			}
		});
	});

	$('#action-box .place.active').live('click', function() {
		$('#action-box .action').each(function(i) {
				actionbox.closePlace(i);
		});
		$('#action-box .place').removeClass('active');
	});

	$('#action-box #actboxToLeft').live('click', function() {
		actionbox.moveToLeft();
	});

	$('#action-box #actboxToRight').live('click', function() {
		actionbox.moveToRight();
	});

	$('.actionbox-sh').live('click', function() {
		actionbox.swtichAction($(this));
	});

	$('.moveTo').live('click', function(e) {
		mapController.moveTo(
			$(this).data('x-position'),
			$(this).data('y-position')
		);
	});

	/* DIVERS */
	$('.new-transaction.resources #resources-quantity').live('keyup', function() {
		var quantity  = $(this).val();
		var rate 	  = $(this).data('rate');
		var price 	  = quantity * rate;
		var variation = price * $(this).data('variation') / 100;

		$('.new-transaction.resources #resources-price').val(Math.ceil(price));
		$('.new-transaction.resources .min-price').text(utils.numberFormat(Math.ceil(price - variation)));
		$('.new-transaction.resources .max-price').text(utils.numberFormat(Math.floor(price + variation)));
	});

	$('.base-type .list-choice button').live('click', function() {
		var index = $('.base-type .list-choice button').index(this);

		$('.base-type .desc-choice').hide();
		$('.base-type .list-desc .desc-choice:nth-child(' + (index + 1) + ')').show();
	});

// ###########################
// #### MARKET MECHANISME ####
// ###########################
	$('.sell-form input[name="quantity"]').live('keyup', function() {
		var box  = $(this).parents('.sell-form'),
			maxQ = box.data('max-quantity'),
			minP = box.data('min-price'),
			rate = box.data('rate');
		var quantity = $(this).val(),
			rMinP = Math.ceil(quantity * minP),
			price = Math.ceil(quantity * rate);

		if (quantity > maxQ || price < rMinP) {
			box.find('input[type="submit"]').attr('disabled', 'disabled');
		} else {
			box.find('input[type="submit"]').removeAttr('disabled');
		}
		box.find('.sf-min-price .value').html(utils.numberFormat(Math.ceil(rMinP)));
		box.find('.sf-price input').val(price);	
	});

	/* OTHER ANNIMATIONS */
	(function() {
		var t = $('#nav .box a.current-base img');
		var i = 0;

		setInterval(function() {
			if (i > 20 && i < 40) {
				t.css('box-shadow', '0 0 0 ' + (i - 20) + 'px rgba(255, 255, 255, ' + (0.5 - ((i - 20) * 5 / 100)) + ')');
			} else if (i > 40) {
				i = 0;
			}
			
			i++;
		}, 100);
	})();

	/* SORTING STUFF */
	$('.sort-button a').live('click', function(e) {
		e.preventDefault();

		var parent = $(this).closest('.body');
		var type = $(this).data('sort-type');
		var direction = $(this).data('sort-direction');

		parent.find('.sort-button a')
			.removeClass('active')
			.removeClass('up')
			.removeClass('down');

		$(this).addClass('active');
		$(this).addClass(direction);

		if (direction == 'up') {
			$(this).data('sort-direction', 'down');
		} else {
			$(this).data('sort-direction', 'up');
		}

		parent.find('.transaction').sort(function(a, b) {
			if (direction == 'up') {
				return $(a).data('sort-' + type) < $(b).data('sort-' + type) ? 1 : -1;
			} else {
				return $(a).data('sort-' + type) > $(b).data('sort-' + type) ? 1 : -1;
			}
		}).remove().appendTo(parent);
	});

});