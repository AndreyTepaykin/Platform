(function (Q, $, window, undefined) {
/**
 * Assets/plan tool.
 * @class Assets/plan
 * @constructor
 * @param {Object} [options] options to pass
 *  @param {String} options.payments Payment gateway, can be "authnet" or "stripe"
 */
Q.Tool.define("Assets/plan", function(options) {
	var tool = this;
	var state = this.state;

	var pipe = new Q.pipe(["planStream", "subscriptionStream"], tool.refresh.bind(tool));
	Q.Streams.get(state.publisherId, state.streamName, function (err) {
		if (err) {
			return;
		}

		tool.planStream = this;
		pipe.fill("planStream")();

		Q.req('Assets/subscription', 'data', function (err, response) {
			if (err) {
				return;
			}

			var publisherId = Q.getObject([tool.planStream.fields.publisherId, tool.planStream.fields.name, "publisherId"], response.slots.data.subscribed);
			var streamName = Q.getObject([tool.planStream.fields.publisherId, tool.planStream.fields.name, "streamName"], response.slots.data.subscribed);

			if (publisherId && streamName) {
				Q.Streams.get(publisherId, streamName, function (err) {
					if (err) {
						return;
					}

					tool.subscriptionStream = this;
					pipe.fill("subscriptionStream")();
				});
			} else {
				tool.subscriptionStream = null;
				pipe.fill("subscriptionStream")();
			}
		}, {

		});
	});
},

{
	publisherId: null,
	streamName: null,
	payments: "stripe",
	immediatePayment: true,
	icon: {
		defaultSize: 200
	},
	onSubscribe: new Q.Event()
},

{
	refresh: function () {
		var tool = this;
		var state = this.state;
		var $toolElement = $(tool.element);

		var period = tool.planStream.getAttribute("period");
		var price = tool.planStream.getAttribute('amount');
		var currency = tool.planStream.getAttribute('currency');
		var lastChargeTime = null;
		var started = null;
		var subscribed = false;
		var endsIn = null;
		var stopped = false;
		if (tool.subscriptionStream) {
			stopped = tool.subscriptionStream.getAttribute("stopped");
			subscribed = !stopped;
			period = tool.subscriptionStream.getAttribute("period");
			lastChargeTime = parseInt(tool.subscriptionStream.getAttribute("lastChargeTime"));
			started = new Date(lastChargeTime * 1000).toDateString().split(' ').slice(1).join(' ');
			price = tool.subscriptionStream.getAttribute('amount');
			endsIn = new Date(lastChargeTime * 1000);
			switch (period) {
				case "annually":
					endsIn.addYears(1);
					break;
				case "monthly":
					endsIn.addMonths(1);
					break;
				case "weekly":
					endsIn.addWeeks(1);
					break;
				case "daily":
					endsIn.addDays(1);
					break;
			}
			endsIn = endsIn.toDateString().split(' ').slice(1).join(' ');
		}
		var _refreshSubscriptionAndTool = function () {
			Q.Streams.get.force(tool.subscriptionStream.fields.publisherId, tool.subscriptionStream.fields.name, function (err) {
				if (err) {
					return;
				}

				tool.subscriptionStream = this;
				tool.refresh();
			});
		};

		$toolElement.attr("data-subscribed", subscribed);
		$toolElement.attr("data-stopped", stopped);
		Q.Template.render('Assets/plan', {
			text: tool.text,
			status: subscribed ? tool.text.subscriptions.Subscribed : tool.text.subscriptions.Unsubscribed,
			started: started,
			endsIn: {
				text: subscribed ? tool.text.subscriptions.NextPay : tool.text.subscriptions.EndsIn,
				date: endsIn
			},
			period: period,
			price: (currency === "USD" ? '$' : currency) + parseFloat(price).toFixed(2),
			iconUrl: tool.planStream.iconUrl(state.icon.defaultSize)
		}, function (err, html) {
			if (err) {
				return;
			}

			Q.replace(tool.element, html);
			$toolElement.activate();

			$("button[name=subscribe]", tool.element).on(Q.Pointer.fastclick, function () {
				var $this = $(this);
				$this.addClass("Q_working");
				if (tool.subscriptionStream) {
					return Q.req("Assets/subscription", ["subscribe"], function (err, response) {
						$this.removeClass("Q_working");
						var msg = Q.firstErrorMessage(err);
						if (msg) {
							return Q.alert(msg);
						}

						if (response.slots.subscribe) {
							return _refreshSubscriptionAndTool();
						}


					}, {
						method: "put",
						fields: {
							publisherId: tool.planStream.fields.publisherId,
							streamName: tool.planStream.fields.name
						}
					});
				}

				Q.Assets.Subscriptions.subscribe(state.payments, {
					planPublisherId: tool.planStream.fields.publisherId,
					planStreamName: tool.planStream.fields.name,
					immediatePayment: state.immediatePayment
				}, function (err, status, subscriptionStream) {
					$this.removeClass("Q_working");
					if (err) {
						return;
					}

					if (status) {
						Q.Streams.get(subscriptionStream.publisherId, subscriptionStream.streamName, function (err) {
							if (err) {
								return;
							}

							tool.subscriptionStream = this;
							_refreshSubscriptionAndTool();
							Q.handle(state.onSubscribe, tool);
						});
					}
				});
			});
			$("button[name=unsubscribe]", tool.element).on(Q.Pointer.fastclick, function () {
				Q.confirm(tool.text.subscriptions.AreYouSureUnsubscribe, function (result) {
					if (!result) {
						return;
					}

					Q.req("Assets/subscription", ["unsubscribe"], function (err, response) {
						var msg = Q.firstErrorMessage(err);
						if (msg) {
							return Q.alert(msg);
						}

						Q.alert(tool.text.subscriptions.YouUnsubscribedFromPlan.interpolate({
							planTitle: tool.planStream.fields.title,
							endsIn: Q.Tool.setUpElementHTML('div', 'Q/timestamp', {
								capitalized: true,
								time: endsIn
							}, 'Q_timestamp', tool.prefix)
						}));

						Q.Streams.get.force(tool.subscriptionStream.fields.publisherId, tool.subscriptionStream.fields.name, function (err) {
							if (err) {
								return;
							}

							tool.subscriptionStream = this;
							tool.refresh();
						});
					}, {
						method: "put",
						fields: {
							publisherId: tool.planStream.fields.publisherId,
							streamName: tool.planStream.fields.name
						}
					});
				});
			});
		});
	}
});

Q.Template.set('Assets/plan',
`<h2 class="Assets_plan_status">{{status}}</h2>
	<div class="Assets_plan_period">{{text.subscriptions.Period}}: {{period}}</div>
	<div class="Assets_plan_price">{{text.subscriptions.Price}}: {{price}}</div>
	<div class="Assets_plan_started">{{text.subscriptions.Started}}: {{started}}</div>
	<div class="Assets_plan_endsIn">{{endsIn.text}}: {{&tool "Q/timestamp" "endsIn" capitalized=true time=endsIn.date}}</div>
	<button class="Q_button" name="unsubscribe">{{text.subscriptions.Unsubscribe}}</button>
	<button class="Q_button" name="subscribe">{{text.subscriptions.Subscribe}}</button>`
);

})(Q, Q.$, window);