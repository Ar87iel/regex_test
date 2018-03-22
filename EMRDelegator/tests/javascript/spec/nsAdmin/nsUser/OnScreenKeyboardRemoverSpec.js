// @tests js/nsAdmin/nsUser/OnScreenKeyboardRemover.js

describe('wpt.nsAdmin.nsUser.OnScreenKeyboardRemover', function(){
    var options;
    var remover;
    var blurSpy;
    var timeout;
    var document;
    beforeEach(function(){
        timeout = 3057;
        blurSpy = jasmine.createSpy();
        document = {
            activeElement: {
                blur: blurSpy
            }
        };

        options = {
            document: document,
            timeout: timeout
        };
        remover = new wpt.nsAdmin.nsUser.OnScreenKeyboardRemover(options);

        jasmine.clock().install();
    });
    afterEach(function(){
        jasmine.clock().uninstall();
    });

    it('immediately calls blur on the activeElement', function(){
        remover.hideKeyboardAndCall(function(){});
        expect( blurSpy ).toHaveBeenCalled();
    });

    it('calls the callback after the configured timeout', function(){
        var callbackSpy = jasmine.createSpy();
        remover.hideKeyboardAndCall(callbackSpy);

        expect( callbackSpy ).not.toHaveBeenCalled();
        jasmine.clock().tick(timeout - 1);
        expect( callbackSpy ).not.toHaveBeenCalled();
        jasmine.clock().tick(1);
        expect( callbackSpy ).toHaveBeenCalled();
    });

    it('does not call blur when a document isn\'t configured', function(){
        remover = new wpt.nsAdmin.nsUser.OnScreenKeyboardRemover({
            timeout: timeout
        });
        remover.hideKeyboardAndCall(function(){});

        expect( blurSpy ).not.toHaveBeenCalled();
        jasmine.clock().tick(timeout);
        expect( blurSpy ).not.toHaveBeenCalled();
    });

    it('calls callback immediately if zero timeout is configured', function(){
        remover = new wpt.nsAdmin.nsUser.OnScreenKeyboardRemover({
            document: document,
            timeout: 0
        });

        var callbackSpy = jasmine.createSpy();
        remover.hideKeyboardAndCall(callbackSpy);

        jasmine.clock().tick(0);
        expect( callbackSpy ).toHaveBeenCalled();
    });

    it('calls callback after DEFAULT_TIMEOUT if no timeout is configured', function(){
        remover = new wpt.nsAdmin.nsUser.OnScreenKeyboardRemover({
            document: document
        });
        remover.hideKeyboardAndCall(function(){});

        var callbackSpy = jasmine.createSpy();
        remover.hideKeyboardAndCall(callbackSpy);

        expect( callbackSpy ).not.toHaveBeenCalled();
        jasmine.clock().tick(wpt.nsAdmin.nsUser.OnScreenKeyboardRemover.DEFAULT_TIMEOUT - 1);
        expect( callbackSpy ).not.toHaveBeenCalled();
        jasmine.clock().tick(1);
        expect( callbackSpy ).toHaveBeenCalled();
    });

    describe('when nothing is configured', function(){
        var callbackSpy;
        beforeEach(function(){
            remover = new wpt.nsAdmin.nsUser.OnScreenKeyboardRemover();

            callbackSpy = jasmine.createSpy();
            remover.hideKeyboardAndCall(callbackSpy);
        });

        it('does not call blur when nothing is configured', function(){
            expect( blurSpy ).not.toHaveBeenCalled();
            jasmine.clock().tick(wpt.nsAdmin.nsUser.OnScreenKeyboardRemover.DEFAULT_TIMEOUT);
            expect( blurSpy ).not.toHaveBeenCalled();
        });

        it('calls callback after DEFAULT_TIMEOUT if nothing is configured', function(){
            expect( callbackSpy ).not.toHaveBeenCalled();
            jasmine.clock().tick(wpt.nsAdmin.nsUser.OnScreenKeyboardRemover.DEFAULT_TIMEOUT - 1);
            expect( callbackSpy ).not.toHaveBeenCalled();
            jasmine.clock().tick(1);
            expect( callbackSpy ).toHaveBeenCalled();
        });

    });

});
