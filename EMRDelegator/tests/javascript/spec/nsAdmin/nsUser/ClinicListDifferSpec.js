// @tests js/nsAdmin/nsUser/ClinicListDiffer.js

describe('ClinicListDiffer', function(){

    var ClinicListDiffer = wpt.nsAdmin.nsUser.ClinicListDiffer;
    var instance;

    beforeEach(function() {
        instance = new ClinicListDiffer();
    });

    var fakeClinic1 = { id: 1 };
    var fakeClinic2 = { id: 2 };
    var fakeClinic3 = { id: 3 };
    var fakeClinic4 = { id: 4 };
    var fakeClinic5 = { id: 5 };
    var fakeClinic6 = { id: 6 };

    using(
        {
            ///////
            '0-0': {
                listA: [],
                listB: [],
                expectedArray: []
            },
            ///////
            '0-1': {
                listA: [],
                listB: [fakeClinic1],
                expectedArray: [fakeClinic1]
            },
            '1-0': {
                listA: [fakeClinic1],
                listB: [],
                expectedArray: []
            },
            ///////
            'n-0': {
                listA: [fakeClinic1, fakeClinic2, fakeClinic3],
                listB: [],
                expectedArray: []
            },
            '0-n': {
                listA: [],
                listB: [fakeClinic1, fakeClinic2, fakeClinic3],
                expectedArray: [fakeClinic1, fakeClinic2, fakeClinic3]
            },
            ///////
            '1-1 (same)': {
                listA: [fakeClinic1],
                listB: [fakeClinic1],
                expectedArray: []
            },
            '1-1 (diff)': {
                listA: [fakeClinic1],
                listB: [fakeClinic2],
                expectedArray: [fakeClinic2]
            },
            ///////
            '1-n (same)': {
                listA: [fakeClinic1],
                listB: [fakeClinic1, fakeClinic2, fakeClinic3],
                expectedArray: [fakeClinic2, fakeClinic3]
            },
            '1-n (diff)': {
                listA: [fakeClinic1],
                listB: [fakeClinic2, fakeClinic3, fakeClinic4],
                expectedArray: [fakeClinic2, fakeClinic3, fakeClinic4]
            },
            ///////
            'n-1 (same)': {
                listA: [fakeClinic1, fakeClinic2, fakeClinic3],
                listB: [fakeClinic1],
                expectedArray: []
            },
            'n-1 (diff)': {
                listA: [fakeClinic1, fakeClinic2, fakeClinic3],
                listB: [fakeClinic4],
                expectedArray: [fakeClinic4]
            },
            ///////
            'n-n (diffed)': {
                listA: [fakeClinic1, fakeClinic2, fakeClinic3],
                listB: [fakeClinic4, fakeClinic5, fakeClinic6],
                expectedArray: [fakeClinic4, fakeClinic5, fakeClinic6]
            },
            'n-n (same)': {
                listA: [fakeClinic1, fakeClinic2, fakeClinic3],
                listB: [fakeClinic1, fakeClinic2, fakeClinic3],
                expectedArray: []
            },
            'n-n (some same / some diff)': {
                listA: [fakeClinic1, fakeClinic2, fakeClinic3],
                listB: [fakeClinic3, fakeClinic4, fakeClinic5],
                expectedArray: [fakeClinic4, fakeClinic5]
            }
        },
        function(scenario, desc) {

            it('handles clinic lists diff: ' + desc, function() {

                var diffList = instance.diffClinicList(scenario.listA, scenario.listB);

                expect( diffList ).toEqual( scenario.expectedArray );

            });

        }
    );

    it('throws error if given a clinic without an id in the first clinic list', function() {
        function testFunc() {
            var diffList = instance.diffClinicList( [ { foo: 'bar' } ], [ fakeClinic1 ] );
        }

        expect( testFunc ).toThrowError('Clinics in the old list must have an id. Given: {"foo":"bar"}');
    });

    it('throws error if given a clinic without an id in the second clinic list', function() {
        function testFunc() {
            var diffList = instance.diffClinicList( [ fakeClinic1 ], [ { foo: 'bar' } ] );
        }

        expect( testFunc ).toThrowError('Clinics in the new list must have an id. Given: {"foo":"bar"}');
    });
});