(function (Q, $, window, document, undefined) {

    /**
     * Brings Element content text to the maximum size inside element, by changing font size until it will be closer
     * to element Width or Height
     * @method textfill
     * @param {Object} [options] options object that contains function parameters
     *   @param {Number} [options.maxFontPixels] Maximum size of text font,
	 *   set this if your text container is large and you don't want to have extra large text on page
	 *   @param {Number} [options.maxLines] Maximum number of lines,
	 *   set this if you'd like to have a maximum number of lines.
     *   @default null
     */
    Q.Tool.jQuery('Q/textfill',

        function (options) {

            return $(this).plugin("Q/textfill", 'refresh', options);

        },

        {},

        {
            refresh: function (options) {
				var o = Q.extend({}, this.state('Q/textfill'), options);
                var ourElement, ourText = "";
                $('*:visible', this).each(function () {
                    var $t = $(this);
                    if (!$t.children().length && $t.text().length > ourText.length) {
                        ourElement = $t;
                        ourText = $t.text();
                    }
                });
                var fontSize = o.maxFontPixels || (ourElement.height() + 10);
                var maxHeight = $(this).innerHeight();
                var maxWidth = $(this).innerWidth();
                var textHeight;
                var textWidth;
				var lines;
                do {
                    ourElement.css('font-size', fontSize);
                    textHeight = ourElement.outerHeight(true);
                    textWidth = ourElement.outerWidth(true);
					if (o.maxLines) {
						lines = textHeight / Math.floor(parseInt(fontSize.toString().replace('px','')) * 1.5);
					}
                } while (--fontSize > 3
					&& (
						textHeight > maxHeight || textWidth > maxWidth
						|| (o.maxLines && lines > o.maxLines)
					)
				);
                return this;
            }
        }

    );

})(Q, jQuery, window, document);