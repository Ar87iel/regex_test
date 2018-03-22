/**
 * Test remove array items from first parameter in second parameter and return it
 *
 * @tests js/application/facility/edit/removeFromArray.js
 */

wpt.nsPreferences = wpt.nsPreferences || {};

describe('Remove array items from array', function() {
    var arrayToRemove = [{id:101, value:2}];
    var arrayValid = [{id:101, value:2}, {id:102, value:1}, {id:104, value:0}, {id:105, value:0}];
    var arrayExpected = [{id:102, value:1}, {id:104, value:0}, {id:105, value:0}];
    var arrayEmpty = [];
    var dataTest = [
        ['Test with both parameters are valid', arrayToRemove, arrayValid, arrayExpected],
        ['Test with first parameters is empty', arrayEmpty, arrayValid, arrayExpected],
        ['Test with second parameters is empty', arrayToRemove, arrayEmpty, arrayEmpty],
        ['Test with both parameters empty', arrayEmpty, arrayEmpty, arrayEmpty]
    ];

    var testWithData = function (dataItem) {
        return function () {
            var result = wpt.nsPreferences.removeFromArray(dataItem[1], dataItem[2]);

            expect(result.length).toBe(dataItem[3].length);
        };
    };

    dataTest.forEach(function (dataItem) {
        it (dataItem[0], testWithData(dataItem));
    });

    it ('Test with second undefined parameter'), function(){
        var result = wpt.nsPreferences.removeFromArray([{id:101, value:2}]);

        expect(result).toBeUndefined();
    }
});
