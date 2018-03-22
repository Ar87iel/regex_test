
/**
 * Prepend the current basePath to a url.
 *
 * Note: This utilizes wpt.onload's basePath parameter.
 *
 * @param {string} url Relative URL
 */
wpt.nsCommon.basePath = function( url ) {
    var basePath = wpt.onload.basePath ? ( wpt.onload.basePath + '/' ) : '';
    return basePath + url;
};
