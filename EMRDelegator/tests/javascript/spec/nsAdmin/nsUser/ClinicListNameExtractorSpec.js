// @tests js/nsAdmin/nsUser/ClinicListNameExtractor.js

describe('ClinicListNameExtractor', function(){

    var ClinicListNameExtractor = wpt.nsAdmin.nsUser.ClinicListNameExtractor;
    var instance;

    beforeEach(function() {
        instance = new ClinicListNameExtractor();
    });

    it('handles no objects', function() {
        var extractedNames = instance.extractNames([]);
        expect( extractedNames ).toEqual([]);
    });

    it('throw if given objects without name property', function() {
        function testFunc() {
            var extractedNames = instance.extractNames( [
                { foo: 'bar' },
                { crazy: 'sauce' }
            ] );
        }

        expect( testFunc ).toThrowError('All provided objects must have a name property. Given: {"foo":"bar"}');
    });

    it('handles single object with name property', function() {
        var extractedNames = instance.extractNames( [
            { name: 'Foo' }
        ] );

        expect( extractedNames.length ).toBe( 1 );
        expect( extractedNames[0] ).toBe( 'Foo' );
    });

    it('handles multiple objects with name property', function() {
        var extractedNames = instance.extractNames( [
            { name: 'Foo' },
            { name: 'Bar' }
        ] );

        expect( extractedNames.length ).toBe( 2 );
        expect( extractedNames[0] ).toBe( 'Foo' );
        expect( extractedNames[1] ).toBe( 'Bar' );
    });

});