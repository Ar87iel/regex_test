/**
 * Namespace
 *
 * Add a namespace generating function to wpt.namespace.
 *
 * @category WebPT
 * @package EMRCommonJS
 * @copyright Copyright (c) 2012 WebPT, INC
 */

/**
 * @namespace wpt
 */
var wpt = window.wpt || {};

/**
 * Automatically create an object chain if parts do not exist.
 * Example:
 *     "wpt.Patient.Prescriptions" auto handles:
 *         wpt = window.wpt || {}
 *         wpt.Patient = wpt.Patient || {}
 *         wpt.Patient.Prescriptions = wpt.Patient.Prescriptions || {}
 *
 * @param  {string} namespace The expanded namespace string. (e.g. wpt.some.namespace)
 */
wpt.namespace = function(namespace) {
    var namespaces,
    // Map of typeof's that are allowed to be set, stomped, or extended
        overwritesAllowed = {
            'undefined': 1,
            'object': 1
        },
    // Begin globally
        current = (function(){ return this; })(),
    // Iterator
        thisNs;

    // Sanity check
    if ( !namespace || namespaces === null )
        throw new Error('A valid namespace set must be given');

    // Parse namespaces
    namespaces = namespace.split('.'); // Using split over regexp (jsperf.com/split-vs-regexp-for-splitting-on)

    for (var i = 0, len = namespaces.length; i < len; i++ ) {
        thisNs = namespaces[i];

        // Ensure we're not stomping anything
        if ( !overwritesAllowed[ typeof current[ thisNs ] ] )
            throw new Error("Refusing to stomp over " + thisNs + ".");

        // Create namespace if necessary
        current[ thisNs ] = current[ thisNs ] || {};
        // Dive into it for the next child
        current = current[ thisNs ];
    }
};
