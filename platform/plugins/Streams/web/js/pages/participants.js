Q.page("Streams/participating", function () {
	var userId = Q.Users.loggedInUserId();

	$(".Streams_participating_stream_checkmark").on("change", function () {
		var $this = $(this);
		var publisherId = $this.attr('data-publisherId');
		var name = $this.prop('name');

		if ($this.prop('checked')) {
			Q.Streams.Stream.subscribe(publisherId, name);
		} else {
			Q.Streams.Stream.unsubscribe(publisherId, name);
		}
	});

	var _modIdentified = function () {
		var $this = $(this);
		var $item = $this.closest('.Streams_participating_item');
		var type = $item.attr('data-type');

		Q.Users.setIdentifier({
			identifierType: type,
			onSuccess: function () {
				Q.Text.get('Streams/content', function (err, text) {
					var msg = Q.firstErrorMessage(err);
					if (msg) {
						return console.warn(msg);
					}

					var text = Q.getObject('followup.'+type+'.CheckYour' + type.toCapitalized(), text);
					Q.alert(text);
				});
			}
		});
	};

	$(".Streams_participating_item .Streams_participant_plus_icon").on(Q.Pointer.fastclick, _modIdentified);
	$(".Streams_participating_item span.Streams_participating_id").on(Q.Pointer.fastclick, _modIdentified);
	$(".Streams_participating_item .Streams_participant_subscribed_icon").on(Q.Pointer.fastclick, function () {
		var $this = $(this);
		var $item = $this.closest('.Streams_participating_item');
		var subscribed = $item.attr('data-subscribed') === 'true';
		var $identifier = $('span.Streams_participating_id', $item);
		var slot = subscribed ? 'unsubscribe' : 'subscribe';

		var _subscriptionRequest = function () {
			$item.addClass('Q_working');

			Q.req('Users/identifier', [slot], function (err, data) {
				$item.removeClass('Q_working');
				var fem = Q.firstErrorMessage(err, data && data.errors);
				if (fem) {
					return Q.alert(fem);
				}

				$item.attr('data-subscribed', subscribed ? 'false' : 'true');
			}, {
				fields: {
					identifier: $identifier.text(),
					type: $item.attr('data-type')
				}
			});
		};

		if (!subscribed) {
			return _subscriptionRequest();
		}

		Q.Text.get('Streams/content', function (err, text) {
			var msg = Q.firstErrorMessage(err);
			if (msg) {
				return console.warn(msg);
			}

			var text = Q.getObject('followup.AreYouSureUnsubscribeIdentifier', text);
			Q.confirm(text, function (res) {
				if (!res) {
					return;
				}

				_subscriptionRequest();
			});
		});
	});

	$(".Streams_participating_item .Streams_participant_delete_icon").on(Q.Pointer.fastclick, function () {
		var $this = $(this);
		var $item = $this.closest('.Streams_participating_item');
		var subscribed = $item.attr('data-subscribed') === 'true';
		var $identifier = $('span.Streams_participating_id', $item);
		var slot = subscribed ? 'unsubscribe' : 'subscribe';

		$item.addClass('Q_working');

		Q.req('Users/identifier', [], function (err, data) {
			$item.removeClass('Q_working');
			var fem = Q.firstErrorMessage(err, data && data.errors);
			if (fem) {
				return Q.alert(fem);
			}

			$item.attr('data-defined', 'false');
			$identifier.html('');
		}, {
			method: 'delete',
			fields: {
				identifier: $identifier.text()
			}
		});
	});

	// listen Streams/user/emailAddress stream to reflect changes
	Q.Streams.get(userId, 'Streams/user/emailAddress', function (err) {
		var fem = Q.firstErrorMessage(err);
		if (fem) {
			return console.warn("Streams/participating: " + fem);
		}

		this.onFieldChanged('content').set(function (fields) {
			document.location.reload();
		}, true);
	});

	// listen Streams/user/mobileNumber stream to reflect changes
	Q.Streams.get(userId, 'Streams/user/mobileNumber', function (err) {
		var fem = Q.firstErrorMessage(err);
		if (fem) {
			return console.warn("Streams/participating: " + fem);
		}

		this.onFieldChanged('content').set(function (fields) {
			document.location.reload();
		}, true);
	});

	return function () {

	};

}, 'Communities');

Q.Tool.onActivate("Q/expandable").set(function () {
	this.state.beforeExpand.set(function () {
		// create Streams/participants tools
		$(".Streams_participating_stream td[data-type=participants][data-processed=0]", this.element).each(function () {
			$("<div>").tool("Streams/participants", {
				'publisherId': this.getAttribute("data-publisherId"),
				'streamName': this.getAttribute("data-streamName"),
				'showSummary': true,
				'showBlanks': false,
				'showControls': true,
				'maxShow': 100
			}).appendTo(this).activate();

			this.setAttribute("data-processed", 1);
		});
	});
}, true);
