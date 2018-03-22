// @tests js/nsAdmin/nsUser/UserClinicWarningDialog.js

describe('UserClinicWarningDialog', function(){

    var UserClinicWarningDialog = wpt.nsAdmin.nsUser.UserClinicWarningDialog;
    var instance;

    beforeEach(function() {
        instance = new UserClinicWarningDialog();
    });

    it('throws err when save click handler not provided', function() {
        function errFunc() {
            instance.showDialog('userName', [], undefined);
        }
        expect(errFunc ).toThrowError('saveHandler must be a function');
    });

    it('throws err when cancel click handler not provided', function() {
        function errFunc() {
            instance.showDialog('userName', [], function() {}, undefined);
        }
        expect(errFunc ).toThrowError('cancelHandler must be a function');
    });


    describe('when provided valid click and save handlers', function() {

        var userName = 'userName';
        var clinicNames = ['jim', 'joe', 'bobbie'];
        var submitSpy, cancelSpy, dialogCtorSpy;
        var dialogInstance;

        beforeEach(function() {
            submitSpy = jasmine.createSpy('submitCallback');
            cancelSpy = jasmine.createSpy('cancelCallback');
            dialogInstance = jasmine.createSpyObj('dialogInstance', ['showDialog', 'setWidth', 'closeDialog']);
            dialogCtorSpy = spyOn(wpt.nsDialog, 'Dialog' ).and.returnValue(dialogInstance);
            instance.showDialog(userName, clinicNames, submitSpy, cancelSpy);
        });

        it('assert dialog custom class', function() {
            expect( dialogInstance.customClass ).toBe('user-clinic-warning-dialog');
        });

        it('assert user name ends up in dialog', function() {
            var str = '<b>' + userName + '</b>';
            expect( dialogInstance.showDialog.calls.mostRecent().args[0] ).toContain(str);
        });

        it('assert clinic names end up in dialog', function() {
            var str = '<b>jim</b>, <b>joe</b>, <b>bobbie</b>';
            expect( dialogInstance.showDialog.calls.mostRecent().args[0] ).toContain(str);
        });

        it('calls save click handler when Save button is pressed', function() {
            var buttonsArray = dialogInstance.showDialog.calls.mostRecent().args[3];
            var saveButton = buttonsArray[0];
            expect( submitSpy ).not.toHaveBeenCalled();
            saveButton.click.call(dialogInstance);
            expect( dialogInstance.closeDialog ).toHaveBeenCalled();
            expect( submitSpy ).toHaveBeenCalled();
            expect( cancelSpy ).not.toHaveBeenCalled();
        });

        it('calls cancel click handler when Cancel button is pressed', function() {
            var buttonsArray = dialogInstance.showDialog.calls.mostRecent().args[3];
            var cancelButton = buttonsArray[1];
            expect( cancelSpy ).not.toHaveBeenCalled();
            cancelButton.click.call(dialogInstance);
            expect( dialogInstance.closeDialog ).toHaveBeenCalled();
            expect( cancelSpy ).toHaveBeenCalled();
            expect( submitSpy ).not.toHaveBeenCalled();
        });

    });

});