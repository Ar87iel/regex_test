/**
 * Add/Edit Facility's DatetimeLocalUtc helper
 *
 * @tests js/application/facility/edit/jQueryDatetimeLocalUtc.js
 *
 */
describe('jQueryDatetimeLocalUtc', function() {

    it('Does not affect normal setting and getting of input fields', function () {
        var $input = $('<input type="text">');
        var expected = 'this thing';
        $input.val(expected);
        expect($input.val()).toBe(expected);
    });

    it('Changes datetime-local elements to return values in UTC using Z', function () {
        var $input = $('<input type="datetime-local">');
        var expected = '1983-12-03T22:10:00.000Z';
        $input.val(expected);
        expect($input.val()).toBe(expected);
    });

    it('Returns date with Zulu time if 00:00 is the timezone', function () {
        var $input = $('<input type="datetime-local">');
        $input.val('2011-03-03T06:00:00.000+00:00');
        expect($input.val()).toBe('2011-03-03T06:00:00.000Z');
    });

    it('Does not return Zulu time if 00:00 is sent to a normal text input', function () {
        var $input = $('<input type="text">'),
            date = '2011-03-03T06:00:00.000+00:00';
        $input.val(date);
        expect($input.val()).toBe(date);
    });

    it('Converts other time zones to UTC with Zulu', function () {
        var $input = $('<input type="datetime-local">');
        $input.val('2011-03-03T12:06:23.000-07:00');
        expect($input.val()).toBe('2011-03-03T19:06:23.000Z');
    });


    it('Assumes no time zone is UTC', function () {
        var $input = $('<input type="datetime-local">');
        var date = '2011-03-03T12:06:00.000';
        $input.val(date);
        expect($input.val()).toBe(date + 'Z');
    });
});
