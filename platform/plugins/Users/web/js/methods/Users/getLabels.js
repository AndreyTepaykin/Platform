Q.exports(function (Users, priv) {

    /**
     * Users plugin's front end code
     *
     * @module Users
     * @class Users
     */
	/**
	 * Get a user's contact labels
	 * @method getLabels
	 * @static
	 * @param {String} userId
	 * @param {String|Array} [filter] Pass a string prefix here, to filter labels by this prefix.
	 *  Or pass an array of label names, to filter by.
	 * @param {Function} callback
	 */
	Users.getLabels = function (userId, filter, callback) {
		if (typeof filter === 'function') {
			callback = filter;
			filter = undefined;
		}
		Q.req('Users/label', 'labels', function (err, data) {
			var msg = Q.firstErrorMessage(err, data);
			if (msg) {
				Users.onError.handle.call(this, msg, err, data.labels);
				Users.get.onError.handle.call(this, msg, err, data.labels);
				return callback && callback.call(this, msg);
			}
			Q.each(data.slots.labels, function (i) {
				data.slots.labels[i] = new Users.Label(data.slots.labels[i]);
			});
			Q.handle(callback, data, [err, data.slots.labels]);
		}, {
			fields: {
				userId: userId,
				filter: filter
			},
			method: 'post'
		});
	};

});