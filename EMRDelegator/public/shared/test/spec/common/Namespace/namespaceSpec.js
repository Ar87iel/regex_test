

describe('Common Namespace Function', function() {

    describe('wpt.namespace()', function() {

        var namespace = wpt.namespace,
            global = (function(){ return this; })();

        beforeEach(function() {

        });

        afterEach(function() {
            delete global.kablammo;
            delete global.banana;
        });


        it('throws error if nothing is passed', function() {
            expect( function() {
                namespace();
            } ).toThrow();
        });

        it('throws error if invalid namespace is passed', function() {
            expect( function() {
                namespace('');
            } ).toThrow();
        });

        it('creates a single namespace', function() {
            expect( global.kablammo ).not.toBeDefined();
            namespace('kablammo');
            expect( global.kablammo ).toBeDefined();
        });

        it('creates a namespace tree', function() {
            expect( global.banana ).not.toBeDefined();
            namespace('banana.nana.fo.fana');
            expect( global.banana ).toBeDefined();
            expect( global.banana.nana ).toBeDefined();
            expect( global.banana.nana.fo ).toBeDefined();
            expect( global.banana.nana.fo.fana ).toBeDefined();
        });

        it('does not stomp existing namespaces', function() {
            global.stuff = { things: 1, nicknacs: 1 };

            expect( global.stuff ).toBeDefined();

            namespace('stuff');

            expect( global.stuff ).toBeDefined();
            expect( global.stuff.things ).toEqual(1);
        });

        it('does not stomp existing namespace trees', function() {
            global.stuff = {};
            global.stuff.awesome = {};
            global.stuff.awesome.stuff = {a:42};
            global.stuff.mediocre = {};

            namespace('stuff.awesome.stuff.shamwow');

            expect( global.stuff.awesome ).toBeDefined();
            expect( global.stuff.awesome.stuff ).toBeDefined();
            expect( global.stuff.awesome.stuff.a ).toEqual(42);
            expect( global.stuff.awesome.stuff.shamwow ).toBeDefined();
            expect( global.stuff.mediocre ).toBeDefined();
        });

        it('does not stomp existing properties', function() {
            global.stuff = {};
            global.stuff.awesome = {};
            global.stuff.awesome.stuff = 42;

            expect( function() {
                namespace('stuff.awesome.stuff.sandwich');
            } ).toThrow();

            expect( global.stuff.awesome.stuff ).toEqual( 42 );
            expect( global.stuff.awesome.stuff.sandwich ).not.toBeDefined();

        });

    });

});