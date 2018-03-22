/**
 * Page Loading Panel
 *
 * For enhanced user experience, EMR App pages have a special loading overlay that hides the interface
 * until it is in a ready state.
 *
 * This file defines an object which ensures the loader DOM is present and handles the displaying and hiding
 * of it, defines the signals used for communicating with it, subscribes the panel to
 * wpt.page.on.pageIsLoaded and finally initializes the loader to be displayed.
 *
 * Please reference https://www.lucidchart.com/documents/view/486d-bdac-50ff070b-9e78-172d0a005798 for
 * more information.
 */

// Extend on block with signals
(function(wptPage) {

    var wptPageOn = wptPage.on = wptPage.on || {};

    /**
     * Triggered when the page is done loading the minimum work necessary for display.
     *
     * Dispatched with no parameters.
     *
     * Primarily used to signal the master loading panel we are ready for it to reveal
     * the interface.
     *
     * @type {signals.Signal}
     */
    wptPageOn.pageIsLoaded = new signals.Signal();

    /**
     * Triggered when the master loading panel has revealed the actual interface.
     *
     * Dispatched with no parameters.
     *
     * Primarily used to listen for when final activity should run.
     *
     * @type {signals.Signal}
     */
    wptPageOn.pageIsDisplayed = new signals.Signal();

})(wpt.page);

wpt.nsCommon.pageLoadingPanel = {
    _html: '<div class="master-page-loading-panel"></div>',

    /**
     * Select or create a loading panel DOM element if it does not exist already
     */
    _createDom: function() {
        var $existingEl = jQuery('.master-page-loading-panel');
        if ( !$existingEl.length ) {
            $existingEl = jQuery( this._html );
            jQuery('body').prepend( $existingEl );
        }
        return $existingEl;
    },

    /**
     * Show the loading panel incase it isn't already visible
     */
    show: function() {
        var $panel = this._createDom();
        jQuery('.master-page-loading-panel').show();
    },

    /**
     * Hide the loading panel and dispatch wpt.page.on.pageIsDisplayed.
     *
     * Dispatches:
     *  wpt.page.on.pageIsDisplayed
     */
    hide: function() {
        var $panel = this._createDom();
        $panel.hide();
        wpt.page.on.pageIsDisplayed.dispatch();
    }
};

// Listen on wpt.page.on.pageIsLoaded for hiding the panel in its context
wpt.page.on.pageIsLoaded.add(wpt.nsCommon.pageLoadingPanel.hide, wpt.nsCommon.pageLoadingPanel);

// Actual behavior!! This is small so should not incur too much panelty
// @TODO Test this for timing stats
wpt.nsCommon.pageLoadingPanel.show();

