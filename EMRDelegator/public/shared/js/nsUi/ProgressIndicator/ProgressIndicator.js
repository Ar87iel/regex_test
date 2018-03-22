/**
 * Basic Progress Indicator Object
 *
 * Used for AJAX indicators, processing indicators, etc...
 *
 * @category WebPT
 * @package EMRAuth
 * @copyright Copyright (c) 2012 WebPT, INC
 */

var wpt = window.wpt || {};
wpt.nsUi = wpt.nsUi || {};

/**
 * Initialize a ProgressIndicator instance with a given element.
 *
 * Note: This immediately hides the given element.
 *
 * @param {jQuery} $el Selector to DOM element
 *                   containing/representing a loading indication.
 * @constructor
 */
wpt.nsUi.ProgressIndicator = function($el) {
    this.setProgressIndicatorElement( $el );
};

/**
 * Change the element this ProgressIndicator instance represents.
 *
 * Note: This immediately hides the given element.
 *
 * @param {jQuery} $el Selector to DOM element
 *                   containing/representing a loading indication.
 */
wpt.nsUi.ProgressIndicator.prototype.setProgressIndicatorElement = function($el) {
    this.$el = $el;
    this.hide();
};

/**
 * Hide this instance's DOM element.
 */
wpt.nsUi.ProgressIndicator.prototype.show = function() {
    this.$el.removeClass('hidden');
};

/**
 * Reveal this instance's DOM element.
 */
wpt.nsUi.ProgressIndicator.prototype.hide = function() {
    this.$el.addClass('hidden');
};