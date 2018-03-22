// @tests js/nsAdmin/nsUser/UserClinicWarningMediator.js

describe('UserClinicWarningMediator', function(){

    var mockDiffer, mockExtractor, mockDialog;
    var UserClinicWarningMediator = wpt.nsAdmin.nsUser.UserClinicWarningMediator;
    var instance;

    beforeEach(function() {
        mockDiffer = jasmine.createSpyObj('differ', ['diffClinicList'] );
        mockExtractor = jasmine.createSpyObj('extractor', ['extractNames'] );
        mockDialog = jasmine.createSpyObj('dialog', ['showDialog'] );
        instance = new UserClinicWarningMediator(mockDiffer, mockExtractor, mockDialog);
    });

    describe('triggerClinicWarningDialog', function() {

        it('throws if not given a submitCallback function', function() {
            function doStuff() {
                instance.triggerClinicWarningDialog(
                    'foo',                      // username
                    [],                         // originalClinicList
                    [],                         // newClinicList
                    undefined,                  // submitCallback
                    function () {}              // cancelCallback
                );
            }

            expect( doStuff ).toThrowError('submitCallback must be a function');
        });

        it('throws if given a non-function submitCallback', function() {

            function doStuff() {
                instance.triggerClinicWarningDialog(
                    'foo',                      // username
                    [],                         // originalClinicList
                    [],                         // newClinicList
                    'sky is blue',              // submitCallback
                    function () {}              // cancelCallback
                );
            }

            expect( doStuff ).toThrowError('submitCallback must be a function');
        });

        it('throws if given a non-function cancelCallback', function() {

            function doStuff() {
                instance.triggerClinicWarningDialog(
                    'foo',                      // username
                    [],                         // originalClinicList
                    [],                         // newClinicList
                    function() {},              // submitCallback
                    'water is sometimes blue'   // cancelCallback
                );
            }

            expect( doStuff ).toThrowError('cancelCallback must be a function if it\'s provided');
        });

        it('calls submitCallback if there are no diffed clinics', function() {
            var submitSpy = jasmine.createSpy('submitCallback');

            mockDiffer.diffClinicList.and.returnValue([]);

            instance.triggerClinicWarningDialog(
                'foo',                      // username
                [],                         // originalClinicList
                [],                         // newClinicList
                submitSpy,                  // submitCallback
                undefined                   // cancelCallback
            );

            expect( submitSpy ).toHaveBeenCalled();
        });

        describe('when the clinic lists differ', function() {

            var submitSpy, cancelSpy;

            var fakeClinic1 = { id: 1 };
            var fakeClinic2 = { id: 2 };
            var fakeClinic3 = { id: 3 };
            var fakeClinic4 = { id: 4 };
            var fakeClinic5 = { id: 5 };
            var fakeClinic6 = { id: 6 };

            var listA = [fakeClinic1, fakeClinic2 ],
                listB = [fakeClinic2, fakeClinic3 ],
                listC = [fakeClinic3];

            var nameListC = ['fakeClinic3'];


            beforeEach(function() {
                submitSpy = jasmine.createSpy('submitCallback');
                cancelSpy = jasmine.createSpy('cancelCallback');

                mockDiffer.diffClinicList.and.callFake(function(_listA, _listB) {
                    if ( _listA == listA && _listB == listB ) {
                        return listC;
                    } else {
                        return [];
                    }
                });

                mockExtractor.extractNames.and.callFake(function(clinicList) {
                    if ( clinicList == listC ) {
                        return nameListC;
                    } else {
                        return [];
                    }
                })

            });

            it('calls showDialog with cancelCallback when cancelCallback is given', function() {
                instance.triggerClinicWarningDialog(
                    'foo',                      // username
                    listA,                      // originalClinicList
                    listB,                      // newClinicList
                    submitSpy,                  // submitCallback
                    cancelSpy                   // cancelCallback
                );

                expect( submitSpy ).not.toHaveBeenCalled();
                expect( mockDialog.showDialog ).toHaveBeenCalledWith(
                    'foo',
                    nameListC,
                    submitSpy,
                    cancelSpy
                );
            });

            it('calls showDialog with no-op function when cancelCallback is missing', function() {
                instance.triggerClinicWarningDialog(
                    'foo',                      // username
                    listA, // originalClinicList
                    listB, // newClinicList
                    submitSpy,                  // submitCallback
                    undefined                   // cancelCallback
                );

                expect( submitSpy ).not.toHaveBeenCalled();
                expect( mockDialog.showDialog ).toHaveBeenCalledWith(
                    'foo',
                    nameListC,
                    submitSpy,
                    jasmine.any(Function)
                );
                expect( mockDialog.showDialog.calls.mostRecent().args[3] ).not.toBe(cancelSpy);
            });

        });

    });

});