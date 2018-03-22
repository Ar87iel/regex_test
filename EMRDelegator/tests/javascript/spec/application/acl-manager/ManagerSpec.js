/**
 * Function for testing purposes
 * @param data
 * @returns string|object
 */
var addCSRFToAjaxData = function(data) {
    return data;
};

/**
 * PhoneInputGenerator Tests
 *
 * @tests js/application/acl-manager/Manager.js
 *
 */
describe('nsAdmin', function() {
    var Manager = wpt.page.Manager,
        EditForm = wpt.page.EditForm,
        Selector = wpt.page.Selector;

    var editForm,
        selector,
        manager,
        $container,
        $selector,
        $editPanel,
        $newButton;

    afterEach(function() {
        editForm = selector = manager = $container = $selector = $editPanel = $newButton = null;

        // For safety
        $.mockjax.clear();
    });


    describe('Manager', function() {

        function loadBasicManagerInit()
        {
            // Load fixture
            jasmine.getFixtures().fixturesPath = 'spec/application/acl-manager/fixtures/';
            loadFixtures('basic.html');

            // Init objects
            editForm = new EditForm();
            selector = new Selector();
            manager = new Manager(selector, editForm);
            $container = $('.container');
            $newButton = $container.find('.new-button');
        }

        function basicManagerInitSpies()
        {
            // Neutralize "Must be overridden" errors
            spyOn(selector, 'setupEvents');
            spyOn(selector, 'selectById');
            spyOn(selector, 'clearSelectionList');
            spyOn(editForm, 'clearForm');
        }

        describe('supports across multiple instances', function() {

            it('basic property modifications', function() {
                var mgr1 = new Manager(new Selector(), new EditForm() );
                var mgr2 = new Manager(new Selector(), new EditForm() );

                mgr1.ajaxDefs.listObjects.endpoint = 'jim';
                mgr2.ajaxDefs.listObjects.endpoint = 'john';

                expect( mgr1.ajaxDefs.listObjects.endpoint ).toBe('jim');
                expect( mgr2.ajaxDefs.listObjects.endpoint ).toBe('john');
            });

            it('ajaxDefs (object of objects) modifications', function() {
                var mgr1 = new Manager(new Selector(), new EditForm() );
                var mgr2 = new Manager(new Selector(), new EditForm() );

                mgr1.objectName = 'jim';
                mgr2.objectName = 'john';

                expect( mgr1.objectName ).toBe('jim');
                expect( mgr2.objectName ).toBe('john');
            });
        });

        describe('constructor', function() {

            it('should result in an object that is an instanceof Manager', function() {
                var manager = new Manager(new Selector(), new EditForm());
                expect(manager instanceof Manager).toBeTruthy();
            });

            describe('throws errors', function() {

                it('if given no parameters', function() {
                    var noName = function() { Manager(); };
                    expect(noName).toThrowError();
                });

                it('if not given a selector', function() {
                    var noName = function() { Manager(undefined, new EditForm()); };
                    expect(noName).toThrowError("Invalid Selector instance passed");
                });

                it('if given a non Selector as selector', function() {
                    var noName = function() { Manager({}, new EditForm()); };
                    expect(noName).toThrowError('Invalid Selector instance passed');
                });

                it('if not given an EditForm', function() {
                    var noName = function() { Manager(new Selector(), undefined); };
                    expect(noName).toThrowError("Invalid EditForm instance passed");
                });

                it('if given a non EditForm as editForm', function() {
                    var noName = function() { Manager(new Selector(), {}); };
                    expect(noName).toThrowError("Invalid EditForm instance passed");
                });

            });

            it('instantiates a Dialog instance for its internal use', function() {
                spyOn( wpt.nsDialog, 'Dialog' ).and.callThrough();
                wpt.nsDialog.Dialog.prototype.setCentered = jasmine.createSpy('setCentered');
                var manager = new Manager(new Selector(), new EditForm());
                expect( wpt.nsDialog.Dialog ).toHaveBeenCalled();
            });

            it('stores the given selector instance', function() {
                var selector = new Selector();
                var manager = new Manager(selector, new EditForm());
                expect(manager.selector ).toBe(selector);
            });

            it('stores the given EditForm instance', function() {
                var editForm = new EditForm();
                var manager = new Manager(new Selector(), editForm);
                expect(manager.editForm ).toBe(editForm);
            });

            it('nulls out instance\'s $container', function() {
                var manager = new Manager(new Selector(), new EditForm());
                expect(manager.$container ).toBeNull();
            });

        });

        describe('initialize', function() {

            beforeEach(function() {
                editForm = new EditForm();
                selector = new Selector();
                manager = new Manager(selector, editForm);
                $container = $('' +
                    '<div class="the-container">' +
                        '<div class="selector"></div>' +
                        '<div class="edit-panel"></div>' +
                    '</div>');
                $selector = $container.find('.selector');
                $editPanel = $container.find('.edit-panel');


                // So we don't get Must be overridden errors
                spyOn( selector, 'initialize' );

                spyOn( editForm, 'initialize' );
                spyOn( editForm, 'closeEditor' );
                spyOn( manager, 'setupEvents' );
            });

            it('requires a $container', function() {
                var testIt = function() {
                    var returnValue = manager.initialize();
                };
                expect( testIt ).toThrowError();
            });

            it('calls linkToElements and passes it the given $container', function() {
                spyOn( manager, 'linkToElements' );
                var returnValue = manager.initialize($container);
                expect( manager.linkToElements ).toHaveBeenCalledWith($container);
            });

            it('calls initialize on its selector, giving it the selector container', function() {
                var returnValue = manager.initialize($container);
                var $selector = $container.find( manager.selectorContainerClassname );
                expect( manager.selector.initialize ).toHaveBeenCalledWith(manager, $selector);
            });

            it('calls initialize on its editForm, giving it to the editform container', function() {
                var returnValue = manager.initialize($container);
                var $editPanel = $container.find( manager.editFormContainerClassname );
                expect( manager.editForm.initialize ).toHaveBeenCalledWith(manager, $editPanel);
            });

            it('calls setupEvents', function() {
                var returnValue = manager.initialize($container);
                expect( manager.setupEvents ).toHaveBeenCalled();
            });

            it('resets loadedObjects cache', function() {
                manager.loadedObjects = [1,2];
                expect( manager.loadedObjects ).toContain(1);
                expect( manager.loadedObjects ).toContain(2);
                expect( manager.loadedObjects.length ).toBe(2);

                var returnValue = manager.initialize($container);
                expect( $.isArray(manager.loadedObjects) ).toBeTruthy();
                expect( manager.loadedObjects.length ).toBe(0);
            });

            it('calls editForm\'s closeEditor', function() {
                var returnValue = manager.initialize($container);
                expect( manager.editForm.closeEditor ).toHaveBeenCalled();
            });

            it('returns this for daisy chaining', function() {
                var returnValue = manager.initialize($container);
                expect( returnValue ).toBe(manager);
            });
        });

        describe('remove', function() {

            beforeEach(function() {
                editForm = new EditForm();
                selector = new Selector();
                manager = new Manager(selector, editForm);
                $container = $('' +
                    '<div class="the-container">' +
                    '<div class="selector"></div>' +
                    '<div class="edit-panel"></div>' +
                    '</div>');
                $selector = $container.find('.selector');
                $editPanel = $container.find('.edit-panel');

                spyOn( selector, 'remove' );
                spyOn( editForm, 'remove' );
                spyOn( manager, 'unlinkElements' );
                spyOn( manager, 'removeEvents' );
            });

            it('calls removeEvents', function() {
                manager.remove();
                expect( manager.removeEvents ).toHaveBeenCalledWith();
            });

            it('calls remove on selector', function() {
                manager.remove();
                expect( manager.selector.remove ).toHaveBeenCalledWith();
            });

            it('calls remove on editForm', function() {
                manager.remove();
                expect( manager.editForm.remove ).toHaveBeenCalledWith();
            });

            it('calls unlinkElements', function() {
                manager.remove();
                expect( manager.unlinkElements ).toHaveBeenCalledWith();
            });

            it('resets loadedObjects cache', function() {
                manager.loadedObjects = [1,2];
                expect( manager.loadedObjects ).toContain(1);
                expect( manager.loadedObjects ).toContain(2);
                expect( manager.loadedObjects.length ).toBe(2);

                var returnValue = manager.remove();
                expect( $.isArray(manager.loadedObjects) ).toBeTruthy();
                expect( manager.loadedObjects.length ).toBe(0);
            });
        });

        describe('allows linking and unlinking elements', function() {

            beforeEach(function() {
                editForm = new EditForm();
                selector = new Selector();
                manager = new Manager(selector, editForm);
                $container = $('' +
                    '<div class="the-container">' +
                    '<div class="a-selector"></div>' +
                    '<div class="a-edit-panel"></div>' +
                    '<div class="a-new-button"></div>' +
                    '</div>');
                $selector = $container.find('.selector');
                $editPanel = $container.find('.edit-panel');
            });

            describe('linkToElements', function() {

                it('stores the given $container', function() {
                    expect( manager.$container ).toBeNull();
                    manager.linkToElements( $container );
                    expect( manager.$container ).toBe( $container );
                });

                it('looks in $container by selectorContainerClassname to set $selectorContainer', function() {
                    spyOn( $.fn, 'find' ).and.callThrough();
                    var defaultClassname = manager.selectorContainerClassname;
                    manager.selectorContainerClassname = '.a-selector';
                    manager.linkToElements( $container );
                    expect( $.fn.find ).toHaveBeenCalledWith('.a-selector');
                    expect( $.fn.find ).not.toHaveBeenCalledWith(defaultClassname);
                    expect( manager.$selectorContainer ).toEqual( 'div.a-selector' );
                });

                it('looks in $container by editFormContainerClassname to set $editFormContainer', function() {
                    spyOn( $.fn, 'find' ).and.callThrough();
                    var defaultClassname = manager.editFormContainerClassname;
                    manager.editFormContainerClassname = '.a-edit-panel';
                    manager.linkToElements( $container );
                    expect( $.fn.find ).toHaveBeenCalledWith('.a-edit-panel');
                    expect( $.fn.find ).not.toHaveBeenCalledWith(defaultClassname);
                    expect( manager.$editFormContainer ).toEqual( 'div.a-edit-panel' );
                });

                it('looks in $container by newButtonClassname to set $newButton', function() {
                    spyOn( $.fn, 'find' ).and.callThrough();
                    var defaultClassname = manager.newButtonClassname;
                    manager.newButtonClassname = '.a-new-button';
                    manager.linkToElements( $container );
                    expect( $.fn.find ).toHaveBeenCalledWith('.a-new-button');
                    expect( $.fn.find ).not.toHaveBeenCalledWith(defaultClassname);
                    expect( manager.$newButton ).toEqual( 'div.a-new-button' );
                });
            });

            describe('unlinkElements function', function() {

                it('nulls out $container', function() {
                    manager.linkToElements( $container );
                    expect( manager.$container ).not.toBeNull();
                    manager.unlinkElements();
                    expect( manager.$container ).toBeNull();
                });

                it('nulls out $selectorContainer and $editFormContainer', function() {
                    manager.linkToElements( $container );
                    expect( manager.$selectorContainer ).not.toBeNull();
                    expect( manager.$editFormContainer ).not.toBeNull();
                    manager.unlinkElements();
                    expect( manager.$selectorContainer ).toBeNull();
                    expect( manager.$editFormContainer ).toBeNull();
                });

                it('nulls out $newButton reference', function() {
                    manager.linkToElements( $container );
                    expect( manager.$newButton ).not.toBeNull();
                    manager.unlinkElements();
                    expect( manager.$newButton ).toBeNull();
                });
            });
        });


        describe('setupEvents function', function() {

            it('sets up $newButton click event to call newButtonClicked', function() {
                // Init objects
                loadBasicManagerInit();
                basicManagerInitSpies();

                expect( $newButton ).not.toHandle('click.manager');

                spyOn( manager, "newButtonClicked" );

                // Already tested to call setupEvents
                manager.initialize( $container );

                expect( $newButton ).toHandle('click.manager');

                $newButton.trigger('click');

                expect( manager.newButtonClicked ).toHaveBeenCalled();
            });
        });

        describe('removeEvents function', function() {

            it('clears $newButton click event', function() {
                // Init objects
                loadBasicManagerInit();
                basicManagerInitSpies();

                expect( $newButton ).not.toHandle('click.manager');

                // Already tested to call setupEvents
                manager.initialize( $container );

                expect( $newButton ).toHandle('click.manager');

                manager.removeEvents();

                expect( $newButton ).not.toHandle('click.manager');
            });
        });

        describe('allows locking and unlocking controls', function() {

            beforeEach(function() {
                // Init objects
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );
            });

            describe('lockControls', function() {

                it('disables the new button', function() {
                    expect( $newButton ).toHaveProp('disabled', false);

                    manager.lockControls();

                    expect( $newButton ).toHaveProp('disabled', true);
                });

                it('sets the locking flag', function() {
                    expect( manager.latchedForm.isLocked() ).toBeFalsy();

                    manager.lockControls();

                    expect( manager.latchedForm.isLocked() ).toBeTruthy();

                });

                it('returns this for daisy chaining', function() {
                    var returnValue = manager.lockControls();
                    expect( returnValue ).toBe( manager );
                });

            });

            describe('unLockControls', function() {

                it('enables the new button', function() {
                    manager.lockControls();

                    expect( $newButton ).toHaveProp('disabled', true);

                    manager.unLockControls();

                    expect( $newButton ).toHaveProp('disabled', false);
                });

                it('releases the locking flag', function() {
                    manager.lockControls();

                    expect( manager.latchedForm.isLocked() ).toBeTruthy();

                    manager.unLockControls();

                    expect( manager.latchedForm.isLocked() ).toBeFalsy();
                });

                it('returns this for daisy chaining', function() {
                    var returnValue = manager.unLockControls();
                    expect( returnValue ).toBe( manager );
                });

            });

        });

        describe('newButtonClicked function', function() {

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                // Spies for these tests
                spyOn(manager, 'clearSelection');
                spyOn(editForm, 'openEditorForCreation');
            });

            it('should call clearSelection function', function() {
                var clickEvent = $.Event( "click" );
                manager.newButtonClicked(clickEvent);
                expect( manager.clearSelection ).toHaveBeenCalled();
            });

            it('calls editForm\'s openEditorForCreation function', function() {
                var clickEvent = $.Event( "click" );
                manager.newButtonClicked(clickEvent);
                expect( editForm.openEditorForCreation ).toHaveBeenCalled();
            });
        });

        describe('clearSelection function', function() {
            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );
            });

            it('calls selector\'s clearSelection function', function() {
                spyOn(selector, 'clearSelection');
                manager.clearSelection();
                expect( selector.clearSelection ).toHaveBeenCalled();
            });
        });

        describe('_capFirst function', function() {
            beforeEach(function() {
                // Init objects
                editForm = new EditForm();
                selector = new Selector();
                manager = new Manager(selector, editForm);
            });

            it('capitalizes the first letter of a given string', function() {
                var testString = "bob lob law's law blog";
                var expectedString = "Bob lob law's law blog";
                var resultString = manager._capFirst(testString);
                expect( resultString ).toBe( expectedString );
            });

            it('does not break on numbers in a string', function() {
                var testString = "123 bob lob law's law blog";
                var expectedString = "123 bob lob law's law blog";
                var resultString = manager._capFirst(testString);
                expect( resultString ).toBe( expectedString );
            });

            it('does not break when given numbers, returns given value', function() {
                var testString = 46;
                var expectedString = 46;
                var resultString = manager._capFirst(testString);
                expect( resultString ).toBe( expectedString );
            });

            it('handles no parameters', function() {
                var resultString = manager._capFirst();
                expect( resultString ).toBeUndefined();
            });
        });

        describe('_loadObject function', function() {

            var mockRequest,
                mockResponse,
                mockJaxId,

                responseCbSpy,

                done;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                //---- mockjax stuff
                responseCbSpy = jasmine.createSpy('responseCbSpy');
                done = false;
            });

            afterEach(function() {
                $.mockjax.clear();
            });

            it('calls ajax', function() {
                spyOn( $, 'ajax' );

                manager._loadObject(3);

                expect( $.ajax ).toHaveBeenCalled();
            });

            it('uses ajax url from ajaxDefs.getObject.endpoint property', function() {
                spyOn( $, 'ajax' );

                manager._loadObject(3);

                expect( $.ajax.calls.mostRecent().args[0].url ).toEqual( manager.ajaxDefs.getObject.endpoint );
            });

            it('uses ajax data from ajaxDefs.getObject.dataKey property', function() {
                spyOn( $, 'ajax' );

                var testingId = 34;
                manager._loadObject(testingId);

                expect( $.ajax.calls.mostRecent().args[0].data[ manager.ajaxDefs.getObject.dataKey ] ).toBeDefined();
                expect( $.ajax.calls.mostRecent().args[0].data[ manager.ajaxDefs.getObject.dataKey ] ).toBe( testingId );
            });

            describe('upon successful AJAX response', function() {

                var mockedSourceResponse = {};

                beforeEach(function(doneCb) {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            content: {
                                response: {}
                            }
                        }
                    };

                    // Place in key using property
                    mockedSourceResponse.response.content.response[ manager.ajaxDefs.getObject.responseKey ] = [
                        {
                            id: 1337,
                            name: 'WebPT'
                        }
                    ];

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                            doneCb();
                        }
                    });

                    var testingId = 34;
                    manager._loadObject(testingId, responseCbSpy);

                });


                it('stores results in cache by calling _updateOrAddObjectToLoadedCache', function(doneCb) {

                    spyOn( manager, '_updateOrAddObjectToLoadedCache' );

                    waitsForAndRuns(
                        function() {
                            return done;
                        },
                        function() {
                            expect( manager._updateOrAddObjectToLoadedCache ).toHaveBeenCalledWith(
                                mockedSourceResponse.response.content.response[ manager.ajaxDefs.getObject.responseKey ]
                            );
                            doneCb();
                        },
                        5
                    );

                });

                it('calls callback with true and response object upon completion', function(doneCb) {

                    waitsForAndRuns(
                        function() {
                            return done;
                        },
                        function() {
                            expect( responseCbSpy ).toHaveBeenCalledWith(
                                true,
                                mockedSourceResponse.response.content.response[ manager.ajaxDefs.getObject.responseKey ]
                            );
                            doneCb();
                        },
                        5
                    );

                });

                it('returns error if response does not contain ajaxDefs.getObject.responseKey property', function(doneCb) {

                    // Overwrite to break the response's key
                    mockedSourceResponse.response.content.response = {
                        facilities: 1,
                        someData: 'you-dont-know-how-to-read'
                    };

                    // Overwrite beforeEach's work to mock with "broken" response key
                    $.mockjax.clear();
                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });


                    var testingId = 34;
                    manager._loadObject(testingId, responseCbSpy);

                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy.calls.mostRecent().args[0] ).toBe( false );

                        expect( responseCbSpy ).toHaveBeenCalledWith(
                            false,
                            'Server did not return expected success response'
                        );
                        doneCb();
                    }, 5);

                });
            });

            describe('upon error AJAX response', function() {

                var mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            error: {
                                message: 'rawr'
                            }
                        }
                    };

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    var testingId = 34;
                    manager._loadObject(testingId, responseCbSpy);
                });

                it('calls callback with false and the error when encounters a successful response with an error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy ).toHaveBeenCalledWith(
                            false,
                            mockedSourceResponse.response.error.message
                        );
                        doneCb();
                    }, 5);
                });
            });

            describe('upon failed AJAX response', function() {

                var mockedSourceResponse = {
                    status: 500,
                    statusText: 'Error',
                    response: 'Server was unavailable'
                };

                beforeEach(function() {
                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    var testingId = 34;
                    manager._loadObject(testingId, responseCbSpy);
                });

                it('calls callback with false and the error upon XHR error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy ).toHaveBeenCalledWith(
                            false,
                            'Error'
                        );
                        doneCb();
                    }, 5);
                });
            });

        });

        describe('_updateOrAddObjectToLoadedCache function', function() {

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );
            });

            it('uses cacheId property for identifying objects in cache', function() {
                var firstTestObject = { x: 'ab', y: 16 },
                    secondTestObject = { x: 34, y: 2 };

                manager._cacheIdKey = 'x';

                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                manager._updateOrAddObjectToLoadedCache( secondTestObject );
                manager._updateOrAddObjectToLoadedCache( secondTestObject );

                expect( manager.loadedObjects[0].y ).toEqual( 16 );
                expect( manager.loadedObjects[1].y ).toEqual( 2 );

                expect( manager.loadedObjects.length ).toBe( 2 );
            });

            it('adds an object if it is not in cache', function() {
                var firstTestObject = { id: 14, some: 'data' },
                    secondTestObject = { id: 34, some: 'moreData' };

                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                expect( manager.loadedObjects[0].some ).toEqual( 'data' );

                manager._updateOrAddObjectToLoadedCache( secondTestObject );
                expect( manager.loadedObjects[1].some ).toEqual( 'moreData' );

                expect( manager.loadedObjects.length ).toBe( 2 );
            });

            it('replaces an object if it already in cache', function() {
                var firstTestObject = { id: 14, some: 'data' },
                    secondTestObject = { id: 34, some: 'moreData' };

                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                expect( manager.loadedObjects[0].some ).toEqual( 'data' );

                firstTestObject.some = 'updatedData';
                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                expect( manager.loadedObjects[0].some ).toEqual( 'updatedData' );

                expect( manager.loadedObjects.length ).toBe( 1 );
            });

            it('always adds an object if it does not have a key used for cache identification', function() {
                var testObject = { some: 'moreData' };

                manager._updateOrAddObjectToLoadedCache( testObject );
                expect( manager.loadedObjects[0].some ).toEqual( 'moreData' );

                manager._updateOrAddObjectToLoadedCache( testObject );
                expect( manager.loadedObjects[1].some ).toEqual( 'moreData' );

                expect( manager.loadedObjects.length ).toBe( 2 );
            });

        });

        describe('clearCache function', function() {

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                manager.loadedObjects = ['some', 'stuff'];
            });

            it('clears the cache', function() {
                expect( manager.loadedObjects.length ).not.toEqual( 0 );
                manager.clearCache();
                expect( manager.loadedObjects.length ).toEqual( 0 );
            });

        });

        describe('_getLoadedObject function', function() {

            var firstTestObject,
                secondTestObject,
                returnedItem;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                firstTestObject = { id: 14, some: 'data' };
                secondTestObject = { id: 34, some: 'moreData' };

                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                manager._updateOrAddObjectToLoadedCache( secondTestObject );
            });

            it('retrieves an object if in cache', function() {
                returnedItem = manager._getLoadedObject( 14 );
                expect( returnedItem ).toBe( firstTestObject );
                returnedItem = manager._getLoadedObject( 34 );
                expect( returnedItem ).toBe( secondTestObject );
            });

            it('returns null if object is not in cache', function() {
                returnedItem = manager._getLoadedObject( 123 );
                expect( returnedItem ).toBe( null );
            });

            it('uses cacheId property for identifying objects in cache', function() {
                var firstTestObject = { x: 'ab', y: 16 },
                    secondTestObject = { x: 34, y: 2 };

                manager.clearCache();

                manager._cacheIdKey = 'x';

                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                manager._updateOrAddObjectToLoadedCache( firstTestObject );
                manager._updateOrAddObjectToLoadedCache( secondTestObject );
                manager._updateOrAddObjectToLoadedCache( secondTestObject );

                returnedItem = manager._getLoadedObject( 'ab' );
                expect( returnedItem ).toBe( firstTestObject );
                returnedItem = manager._getLoadedObject( 34 );
                expect( returnedItem ).toBe( secondTestObject );
                returnedItem = manager._getLoadedObject( '34' );
                expect( returnedItem ).toBe( secondTestObject ); // Loose string -> number checks TODO temporary?
            });

        });

        describe('placeFocusOnNewButton function', function() {

            it('places focus on $newButton', function() {
                loadBasicManagerInit();
                basicManagerInitSpies();
                manager.initialize( $container );

                var spyEvent = spyOnEvent( $newButton, 'focus' );

                expect( spyEvent ).not.toHaveBeenTriggered();
                expect( 'focus' ).not.toHaveBeenTriggeredOn( $newButton );

                manager.placeFocusOnNewButton();

                expect( spyEvent ).toHaveBeenTriggered();
                expect( 'focus' ).toHaveBeenTriggeredOn( $newButton );
            });

            it('works without a $newButton set', function() {
                loadBasicManagerInit();
                basicManagerInitSpies();
                manager.initialize( $container );
                manager.$newButton = $();
                manager.placeFocusOnNewButton();
            });

        });

        describe('placeFocusOnEditActionOfGivenId function', function() {
            it('calls Selector\'s placeFocusOnEditActionOfGivenId function', function() {
                loadBasicManagerInit();
                basicManagerInitSpies();
                manager.initialize( $container );

                spyOn( selector, 'placeFocusOnEditActionOfGivenId' );

                var idToTest = 34;
                manager.placeFocusOnEditActionOfGivenId( idToTest );

                expect( selector.placeFocusOnEditActionOfGivenId ).toHaveBeenCalledWith( idToTest );
            });
        });

        describe('selectObject function', function() {

            var returnVal;

            beforeEach(function() {
                loadBasicManagerInit();

                // Neutralize "Must be overridden" errors
                spyOn(selector, 'setupEvents');
                spyOn(selector, 'clearSelectionList');
                spyOn(editForm, 'clearForm');


                spyOn( manager, '_getLoadedObject' );
                spyOn( manager, '_loadObject' );
                spyOn( selector, 'selectById' );
                spyOn( editForm, 'closeEditor' );
                spyOn( editForm, 'openEditorForEditing' );
                spyOn( selector, 'lockControls' );
                spyOn( selector, 'unLockControls' );

                manager.initialize( $container );

                spyOn( manager.dialog, 'showDialog' );
            });

            it('does nothing if the locked flag is set', function() {
                manager.latchedForm.lock();

                returnVal = manager.selectObject(3);
                expect( returnVal ).toBeUndefined();

                expect( manager._getLoadedObject ).not.toHaveBeenCalled();
            });

            it('calls _getLoadedObject with the given object id', function() {
                returnVal = manager.selectObject(34);
                expect( manager._getLoadedObject ).toHaveBeenCalledWith(34);
            });

            describe('if given loaded object', function() {

                var returnVal,
                    testCallback,
                    testId,
                    mockedReturnedData;

                beforeEach(function() {
                    loadBasicManagerInit();

                    // Neutralize "Must be overridden" errors
                    spyOn(selector, 'setupEvents');
                    spyOn(selector, 'clearSelectionList');
                    spyOn(editForm, 'clearForm');

                    testId = 5;
                    mockedReturnedData = {objectData: 'data'};

                    spyOn( manager, '_getLoadedObject' ).and.returnValue( mockedReturnedData );
                    spyOn( manager, '_loadObject' );
                    spyOn( selector, 'selectById' );
                    spyOn( editForm, 'closeEditor' );
                    spyOn( editForm, 'openEditorForEditing' );
                    spyOn( selector, 'lockControls' );
                    spyOn( selector, 'unLockControls' );

                    manager.initialize( $container );

                    spyOn( manager.dialog, 'showDialog' );

                    testCallback = jasmine.createSpy('testCallback');

                    returnVal = manager.selectObject(testId, testCallback);
                });

                it('calls Selector\'s selectById function with given object id', function() {
                    expect( selector.selectById ).toHaveBeenCalledWith(testId);
                });

                it('calls EditForm\'s openEditorForEditing function with object data', function() {
                    expect( editForm.openEditorForEditing ).toHaveBeenCalledWith( mockedReturnedData );
                });

                it('calls callback with the object data', function() {
                    expect( testCallback ).toHaveBeenCalledWith( mockedReturnedData );
                });
            });

            describe('if given non-loaded object that was not already selected', function() {

                var returnVal;

                beforeEach(function() {
                    loadBasicManagerInit();

                    // Neutralize "Must be overridden" errors
                    spyOn(selector, 'setupEvents');
                    spyOn(selector, 'clearSelectionList');
                    spyOn(editForm, 'clearForm');

                    spyOn( manager, '_getLoadedObject' ).and.returnValue(false);
                    spyOn( manager, '_loadObject' );
                    spyOn( selector, 'selectById' );
                    spyOn( editForm, 'closeEditor' );
                    spyOn( editForm, 'openEditorForEditing' );
                    spyOn( selector, 'lockControls' );
                    spyOn( selector, 'unLockControls' );

                    manager.initialize( $container );

                    spyOn( manager.dialog, 'showDialog' );

                    returnVal = manager.selectObject(5);
                });


                it('calls Selector\'s lockControls function', function() {
                    expect( selector.lockControls ).toHaveBeenCalled();
                });

                it('calls _loadObject with the given object id', function() {
                    expect( manager._loadObject ).toHaveBeenCalled();
                    expect( manager._loadObject.calls.mostRecent().args[0] ).toBe(5);
                });

                describe('in _loadObject\'s response callback', function() {

                    var testCallback;

                    beforeEach(function() {
                        loadBasicManagerInit();

                        // Neutralize "Must be overridden" errors
                        spyOn(selector, 'setupEvents');
                        spyOn(selector, 'clearSelectionList');
                        spyOn(editForm, 'clearForm');

                        spyOn( selector, 'selectById' );
                        spyOn( editForm, 'closeEditor' );
                        spyOn( editForm, 'openEditorForEditing' );
                        spyOn( selector, 'lockControls' );
                        spyOn( selector, 'unLockControls' );

                        testCallback = jasmine.createSpy('testCallback');
                    });

                    it('calls Selector\'s unLockControls function', function() {
                        spyOn( manager, '_getLoadedObject' ).and.returnValue(false);
                        spyOn( manager, '_loadObject' ).and.callFake(function(id, callback) {
                            callback();
                        });
                        manager.initialize( $container );
                        spyOn( manager.dialog, 'showDialog' );
                        returnVal = manager.selectObject(5);

                        expect( selector.unLockControls ).toHaveBeenCalled();
                    });

                    describe('if successful', function() {

                        beforeEach(function() {
                            spyOn( manager, '_getLoadedObject' ).and.returnValue(false);
                            spyOn( manager, '_loadObject' ).and.callFake(function(id, callback) {
                                callback( true, { objectData: 'data' } );
                            });
                            manager.initialize( $container );
                            returnVal = manager.selectObject(5, testCallback);
                        });

                        it('calls Selector\'s selectById with given object id', function() {
                            expect( selector.selectById ).toHaveBeenCalledWith(5);
                        });

                        it('calls Editform\'s openEditorForEditing function with object data', function() {
                            expect( editForm.openEditorForEditing ).toHaveBeenCalledWith({ objectData: 'data' });
                        });

                        it('calls callback with the object data', function() {
                            expect( testCallback ).toHaveBeenCalledWith(  { objectData: 'data' } );
                        });

                    });

                    describe('if unsuccessful', function() {

                        beforeEach(function() {
                            spyOn( manager, '_getLoadedObject' ).and.returnValue(false);
                            spyOn( manager, '_loadObject' ).and.callFake(function(id, callback) {
                                callback( false, 'Something went wrong' );
                            });
                            manager.initialize( $container );
                            spyOn( manager.dialog, 'showDialog' );
                            returnVal = manager.selectObject(5, testCallback);
                        });

                        it('calls dialog\'s showdialog with error', function() {
                            expect( manager.dialog.showDialog ).toHaveBeenCalled();
                            expect( manager.dialog.showDialog.calls.mostRecent().args[0] ).toBe('Something went wrong');
                        });

                        it('calls callback with false', function() {
                            expect( testCallback ).toHaveBeenCalledWith( false );
                        });
                    });
                });
            });

        });

        describe('populateSelectionsByArray function', function() {

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();
                spyOn(selector, 'populateSelectionList');
                manager.initialize($container);
            });

            it('throws if not given an array or object', function() {
                expect( function() {
                    manager.populateSelectionsByArray();
                } ).toThrowError();
                expect( function() {
                    manager.populateSelectionsByArray(12);
                } ).toThrowError();
                expect( function() {
                    manager.populateSelectionsByArray('Ground control to major tom');
                } ).toThrowError();
            });

            it('sets given object or array into loadedObjects cache', function() {
                var objectOne = {some: 'object'},
                    objectTwo = {another:'object'};

                expect( manager.loadedObjects.length ).toBe(0);
                manager.populateSelectionsByArray(objectOne);
                expect( manager.loadedObjects[0] ).toBe(objectOne);

                manager.populateSelectionsByArray([objectOne, objectTwo]);
                expect( manager.loadedObjects.length ).toBe(2);
                expect( manager.loadedObjects[0] ).toBe(objectOne);
                expect( manager.loadedObjects[1] ).toBe(objectTwo);
            });

            it('calls Selector\'s populateSelectionList function with given object or array', function() {
                var objectOne = {some: 'object'},
                    objectTwo = {another:'object'};

                manager.populateSelectionsByArray(objectOne);
                expect( selector.populateSelectionList ).toHaveBeenCalledWith([objectOne]);

                manager.populateSelectionsByArray([objectOne, objectTwo]);
                expect( selector.populateSelectionList ).toHaveBeenCalledWith([objectOne, objectTwo]);
            });

        });

        describe('getSelectionsFromServer function', function() {

            var mockRequest,
                mockResponse,
                mockJaxId,

                responseCbSpy,

                done;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                spyOn(selector, 'lockControls');
                spyOn(selector, 'unLockControls');
                spyOn(selector, 'populateSelectionList');

                // Already tested to call setupEvents
                manager.initialize( $container );

                //---- mockjax stuff
                responseCbSpy = jasmine.createSpy('responseCbSpy');
                done = false;
            });

            afterEach(function() {
                $.mockjax.clear();
            });

            it('calls Selector\'s lockControls function', function() {
                spyOn( $, 'ajax' );
                manager.getSelectionsFromServer();
                expect( selector.lockControls ).toHaveBeenCalled();
            });

            it('endpoint from ajaxDefs.listObjects.endpoint', function() {
                spyOn( $, 'ajax' );
                manager.ajaxDefs.listObjects.endpoint = 'rawr';
                manager.getSelectionsFromServer();
                expect( $.ajax.calls.mostRecent().args[0].url ).toEqual( manager.ajaxDefs.listObjects.endpoint );
            });

            describe('upon successful AJAX response', function() {

                var mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            content: {
                                response: {}
                            }
                        }
                    };

                    // Place in key using property
                    mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ] = [
                        {
                            id: 1337,
                            name: 'WebPT'
                        }
                    ];

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    manager.getSelectionsFromServer(responseCbSpy);

                });


                it('calls unLockControls', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( selector.unLockControls ).toHaveBeenCalled();
                        doneCb();
                    }, 5);
                });


                it('sets loadedObjects cache if ajaxDefs.listObjects.responseContainsFullObjectData is true', function(doneCb) {
                    manager.ajaxDefs.listObjects.responseContainsFullObjectData = true;

                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager.loadedObjects.length ).toBe(1);
                        expect( manager.loadedObjects[0].id ).toBe(
                            mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ][0].id
                        );
                        expect( manager.loadedObjects[0].name ).toBe(
                            mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ][0].name
                        );
                        doneCb();

                    }, 5);
                });

                it('does not set loadedObjects cache if ajaxDefs.listObjects.responseContainsFullObjectData is false', function(doneCb) {
                    manager.ajaxDefs.listObjects.responseContainsFullObjectData = false;

                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager.loadedObjects.length ).toBe(0);
                        doneCb();
                    }, 5);
                });


                it('calls Selector\'s populateSelectionList function', function(doneCb) {


                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( selector.populateSelectionList ).toHaveBeenCalledWith(
                            mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ]
                        );
                        doneCb();
                    }, 5);
                });

                // Todo perhaps function should give callback the data instead of just true
//                it('calls callback with true and response object upon completion', function(doneCb) {
//
//                    waitsForAndRuns(function() {
//                        return done;
//                    }, function() {
//                        expect( responseCbSpy ).toHaveBeenCalledWith(
//                            true,
//                            mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ]
//                        );
//                        doneCb();
//                    }, 5);
//                });
                it('calls callback with true', function(doneCb) {

                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy ).toHaveBeenCalledWith(
                            true
                        );
                        doneCb();
                    }, 5);
                });

                it('returns error if response does not contain ajaxDefs.listObjects.responseKey property', function(doneCb) {

                    // Overwrite to break the response's key
                    mockedSourceResponse.response.content.response = {
                        facilities: 1,
                        someData: 'you-dont-know-how-to-read'
                    };

                    // Overwrite beforeEach's work to mock with "broken" response key
                    $.mockjax.clear();
                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    manager.getSelectionsFromServer(responseCbSpy);

                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy.calls.mostRecent().args[0] ).toBe( false );

                        expect( responseCbSpy ).toHaveBeenCalledWith(
                            false,
                            'Server did not return expected success response'
                        );
                        doneCb();
                    }, 5);

                });
            });

            describe('upon error AJAX response', function() {

                var mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            error: {
                                message: 'rawr'
                            }
                        }
                    };

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    manager.getSelectionsFromServer(responseCbSpy);

                    spyOn( manager.dialog, 'showDialog' );
                });


                it('calls unLockControls', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( selector.unLockControls ).toHaveBeenCalled();
                        doneCb();
                    }, 5);
                });

                it('calls callback with false and the error when encounters a successful response with an error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy ).toHaveBeenCalled();
                        expect( responseCbSpy.calls.mostRecent().args[0] ).toBeFalsy();
                        doneCb();
                    }, 5);
                });

                // Todo perhaps function should be decoupled from showing dialog?
                it('calls showDialog when encounters a successful response with an error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager.dialog.showDialog ).toHaveBeenCalled();
                        doneCb();
                    }, 5);
                });
            });

            describe('upon failed AJAX response', function() {

                var mockedSourceResponse = {
                    status: 500,
                    statusText: 'Error',
                    response: 'Server was unavailable'
                };

                beforeEach(function() {

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    manager.getSelectionsFromServer(responseCbSpy);
                });


                it('calls unLockControls', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( selector.unLockControls ).toHaveBeenCalled();
                        doneCb();
                    }, 5);
                });

                it('calls callback with false and the error upon XHR error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( responseCbSpy ).toHaveBeenCalledWith(
                            false,
                            'Error'
                        );
                        doneCb();
                    }, 5);
                });
            });



        });

        describe('saveObject function', function() {

            var mockRequest,
                mockResponse,
                mockJaxId,

                responseCbSpy,
                doneCbSpy,

                done;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                spyOn(selector, 'lockControls');
                spyOn(selector, 'unLockControls');
                spyOn(selector, 'populateSelectionList');

                // Already tested to call setupEvents
                manager.initialize( $container );

                //---- mockjax stuff
                responseCbSpy = jasmine.createSpy('responseCbSpy');
                doneCbSpy = jasmine.createSpy('doneCbSpy');
                done = false;
            });

            afterEach(function() {
                $.mockjax.clear();
            });

            it('calls Selector\'s lockControls function', function() {
                spyOn( $, 'ajax' );
                manager.saveObject({id:34, name:'Jim'}, doneCbSpy);
                expect( selector.lockControls ).toHaveBeenCalled();
            });

            it('endpoint from ajaxDefs.saveObject.endpoint', function() {
                spyOn( $, 'ajax' );
                manager.ajaxDefs.saveObject.endpoint = 'rawr';
                manager.saveObject({id:34, name:'Jim'}, doneCbSpy);
                expect( $.ajax.calls.mostRecent().args[0].url ).toEqual( manager.ajaxDefs.saveObject.endpoint );
            });

            describe('upon successful AJAX response', function() {

                var mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            content: {
                                response: {}
                            }
                        }
                    };

                    // Place in key using property
                    mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ] = [
                        {
                            id: 1337,
                            name: 'WebPT'
                        }
                    ];

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    spyOn(manager, '_saveObjectAjaxSuccess' );

                    manager.saveObject({id:34, name:'Jim'}, doneCbSpy);

                });


                it('calls _saveObjectAjaxSuccess function with data', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager._saveObjectAjaxSuccess ).toHaveBeenCalled();
                        expect( manager._saveObjectAjaxSuccess.calls.mostRecent().args[0] ).toBe(doneCbSpy);
                        expect( manager._saveObjectAjaxSuccess.calls.mostRecent().args[1] ).not.toBe( mockedSourceResponse.response );
                        doneCb();
                    }, 5);
                });

            });

            describe('upon error AJAX response', function() {

                var mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            error: {
                                message: 'rawr'
                            }
                        }
                    };

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    spyOn(manager, '_saveObjectAjaxSuccess' );

                    manager.saveObject({id:34, name:'Jim'}, doneCbSpy);

                    spyOn( manager.dialog, 'showDialog' );
                });



                it('calls _saveObjectAjaxSuccess with done callback and the error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager._saveObjectAjaxSuccess ).toHaveBeenCalled();
                        expect( manager._saveObjectAjaxSuccess.calls.mostRecent().args[0] ).toBe(doneCbSpy);
                        expect( manager._saveObjectAjaxSuccess.calls.mostRecent().args[1] ).not.toBe( mockedSourceResponse.response );
                        doneCb();
                    }, 5);
                });

            });

            describe('upon failed AJAX response', function() {

                var mockedSourceResponse = {
                    status: 500,
                    statusText: 'Error',
                    response: 'Server was unavailable'
                };

                beforeEach(function() {

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    spyOn(manager, '_saveObjectAjaxError' );
                    manager.saveObject({id:34, name:'Jim'}, doneCbSpy);

                });

                it('calls _saveObjectAjaxError with done callback false and the error upon XHR error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager._saveObjectAjaxError ).toHaveBeenCalled();
                        expect( manager._saveObjectAjaxError.calls.mostRecent().args[0] ).toBe(doneCbSpy);
                        expect( manager._saveObjectAjaxError.calls.mostRecent().args[2] ).not.toBe( mockedSourceResponse.statusText );
                        doneCb();
                    }, 5);
                });
            });


        });

        describe('_saveObjectAjaxSuccess', function() {

            var doneCbSpy;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                spyOn(manager, 'lockControls');
                spyOn(manager, 'unLockControls');
                spyOn(selector, 'lockControls');
                spyOn(selector, 'unLockControls');
                spyOn(editForm, 'lockControls');
                spyOn(editForm, 'unLockControls');
                spyOn(manager, '_updateOrAddObjectToLoadedCache');
                spyOn(manager, 'selectObject');
                spyOn(editForm, 'closeEditor');

                //---- mockjax stuff
                doneCbSpy = jasmine.createSpy('doneCbSpy');
            });

            describe('when server responds with an error it', function() {

                var testData;

                beforeEach(function() {
                    testData = { error: { message: 'Server was attacked by bandits' } };
                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                });

                it('calls unLockControls function', function() {
                    expect( manager.unLockControls ).toHaveBeenCalled();
                });

                it('calls Selector\'s unLockControls function', function() {
                    expect( selector.unLockControls ).toHaveBeenCalled();
                });

                it('calls EditForm\'s unLockControls function', function() {
                    expect( editForm.unLockControls ).toHaveBeenCalled();
                });

                it('calls callback with false and the error message from server', function() {
                    expect( doneCbSpy ).toHaveBeenCalledWith(false, "Server was attacked by bandits");
                });

                it('returns early', function() {
                    expect( manager._updateOrAddObjectToLoadedCache ).not.toHaveBeenCalled();
                });
            });

            describe('when server response does not contain ajaxDefs.saveObject.responseKey property it', function() {
                it('calls callback with error', function() {
                    var testData = { content: { response: { bag: { id: 34, name: 'Bobby' } } } };

                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);

                    expect( doneCbSpy ).toHaveBeenCalledWith(false, "Server did not return expected success response");
                });

                it('returns early', function() {
                    var testData = { content: { response: { bag: { id: 34, name: 'Bobby' } } } };

                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);

                    expect( manager._updateOrAddObjectToLoadedCache ).not.toHaveBeenCalled();
                });
            });

            describe('when server responds with valid response it', function() {

                var testData;

                beforeEach(function() {
                    testData = { content: { response: { bag: { id: 34, name: 'Bobby' } } } };
                    manager.ajaxDefs.saveObject.responseKey = 'bag';
                });

                it('calls callback with true and the data', function() {
                    spyOn(manager, 'getSelectionsFromServer' );
                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                    expect( doneCbSpy ).toHaveBeenCalledWith(true, testData.content.response.bag );
                });

                it('calls _updateOrAddObjectToLoadedCache with the object data', function() {
                    spyOn(manager, 'getSelectionsFromServer' );
                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                    expect( manager._updateOrAddObjectToLoadedCache ).toHaveBeenCalledWith(testData.content.response.bag);
                });

//                it('calls EditForm\'s closeEditor function', function() {
//                    spyOn(manager, 'getSelectionsFromServer' );
//                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);
//                    expect( editForm.closeEditor ).toHaveBeenCalled();
//                });

                it('calls getSelectionsFromServer', function() {
                    spyOn(manager, 'getSelectionsFromServer' );
                    manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                    expect( manager.getSelectionsFromServer ).toHaveBeenCalled();
                });

                describe('calls getSelectionsFromServer where after it gets called back', function() {

                    describe('successfully it', function() {

                        beforeEach(function() {
                            // Mock getSelectionsFromServer to always call callback with true
                            spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function (cb) { cb(true); });
                            manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                        });

                        it('calls unLockControls', function() {
                            expect( manager.unLockControls ).toHaveBeenCalled();
                        });

                        it('calls Selector\'s unLockControls function', function() {
                            expect( selector.unLockControls ).toHaveBeenCalled();
                        });

                        it('calls EditForm\'s unLockControls function', function() {
                            expect( editForm.unLockControls ).toHaveBeenCalled();
                        });

                        it('calls selectObject function with response data key via _cacheIdKey', function() {
                            manager._cacheIdKey = 'id';
                            manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                            expect( manager.selectObject ).toHaveBeenCalledWith( testData.content.response.bag.id );

                            // Verify it is using _cacheIdKey
                            manager._cacheIdKey = 'name';
                            manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                            expect( manager.selectObject ).toHaveBeenCalledWith( testData.content.response.bag.name );
                        });
                    });

                    describe('successfully it', function() {

                        beforeEach(function() {
                            // Mock getSelectionsFromServer to always call callback with false
                            spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function (cb) { cb(false); });
                            manager._saveObjectAjaxSuccess(doneCbSpy, testData);
                        });

                        it('calls unLockControls', function() {
                            expect( manager.unLockControls ).toHaveBeenCalled();
                        });

                        it('calls Selector\'s unLockControls function', function() {
                            expect( selector.unLockControls ).toHaveBeenCalled();
                        });

                        it('calls EditForm\'s unLockControls function', function() {
                            expect( editForm.unLockControls ).toHaveBeenCalled();
                        });

                        it('does not call selectObject function', function() {
                            expect( manager.selectObject ).not.toHaveBeenCalled();

                        });
                    });

                });
            });

        });

        describe('_saveObjectAjaxError', function() {

            var doneCbSpy;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                spyOn(manager, 'lockControls');
                spyOn(manager, 'unLockControls');
                spyOn(selector, 'lockControls');
                spyOn(selector, 'unLockControls');
                spyOn(editForm, 'lockControls');
                spyOn(editForm, 'unLockControls');

                doneCbSpy = jasmine.createSpy('doneCbSpy');

                // Mock getSelectionsFromServer to always call callback with false
                spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function (cb) { cb(false); });
                manager._saveObjectAjaxError(doneCbSpy, {status:500, statusText:'Error'}, 'Error', 'error');
            });

            it('calls unLockControls', function() {
                expect( manager.unLockControls ).toHaveBeenCalled();
            });

            it('calls Selector\'s unLockControls function', function() {
                expect( selector.unLockControls ).toHaveBeenCalled();
            });

            it('calls EditForm\'s unLockControls function', function() {
                expect( editForm.unLockControls ).toHaveBeenCalled();
            });

            it('calls callback with false and the status', function() {
                expect( doneCbSpy ).toHaveBeenCalledWith(false, 'error');
            });

        });

        describe('deleteObject function', function() {

            var mockRequest,
                mockResponse,
                mockJaxId,

                responseCbSpy,
                doneCbSpy,

                done;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();


                spyOn(manager, 'lockControls');
                spyOn(manager, 'unLockControls');
                spyOn(selector, 'lockControls');
                spyOn(selector, 'unLockControls');
                spyOn(editForm, 'lockControls');
                spyOn(editForm, 'unLockControls');
                spyOn(selector, 'populateSelectionList');

                spyOn(manager, '_deleteObjectAjaxSuccess');
                spyOn(manager, '_deleteObjectAjaxError');

                // Already tested to call setupEvents
                manager.initialize( $container );

                spyOn( manager.dialog, 'showDialog' );

                //---- mockjax stuff
                responseCbSpy = jasmine.createSpy('responseCbSpy');
                doneCbSpy = jasmine.createSpy('doneCbSpy');
                done = false;
            });

            afterEach(function() {
                $.mockjax.clear();
            });

            describe('when Manager is locked it', function() {

                beforeEach(function() {
                    spyOn($, 'ajax' ).and.callThrough();

                    manager.latchedForm.lock();
                    manager.deleteObject({id:34, name:'Jim'}, doneCbSpy);
                });

                it('does not call Selector\'s lockControls function', function() {
                    expect( manager.lockControls ).not.toHaveBeenCalled();
                });

                it('does not call Selector\'s unLockControls function', function() {
                    expect( selector.lockControls ).not.toHaveBeenCalled();
                });

                it('does not call EditForm\'s unLockControls function', function() {
                    expect( editForm.lockControls ).not.toHaveBeenCalled();
                });

                it('does not trigger ajax', function() {
                    expect( $.ajax ).not.toHaveBeenCalled();
                });
            });

            it('calls Selector\'s lockControls function', function() {
                spyOn($, 'ajax' );
                manager.deleteObject({id:34, name:'Jim'}, doneCbSpy);
                expect( manager.lockControls ).toHaveBeenCalled();
            });

            it('calls Selector\'s unLockControls function', function() {
                spyOn($, 'ajax' );
                manager.deleteObject({id:34, name:'Jim'}, doneCbSpy);
                expect( selector.lockControls ).toHaveBeenCalled();
            });

            it('calls EditForm\'s unLockControls function', function() {
                spyOn($, 'ajax' );
                manager.deleteObject({id:34, name:'Jim'}, doneCbSpy);
                expect( editForm.lockControls ).toHaveBeenCalled();
            });

            it('endpoint from ajaxDefs.deleteObject.endpoint', function() {
                spyOn($, 'ajax' );
                manager.ajaxDefs.deleteObject.endpoint = 'rawr';
                manager.deleteObject({id:34, name:'Jim'}, doneCbSpy);
                expect( $.ajax.calls.mostRecent().args[0].url ).toEqual( manager.ajaxDefs.deleteObject.endpoint );
            });

            describe('upon successful AJAX response', function() {

                var testData,
                    mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            content: {
                                response: {}
                            }
                        }
                    };

                    // Place in key using property
                    mockedSourceResponse.response.content.response[ manager.ajaxDefs.listObjects.responseKey ] = [
                        {
                            id: 1337,
                            name: 'WebPT'
                        }
                    ];

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    testData = {id:34, name:'Jim'};
                    manager.deleteObject(testData, doneCbSpy);

                });


                it('calls _saveObjectAjaxSuccess function with data', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager._deleteObjectAjaxSuccess ).toHaveBeenCalled();
                        expect( manager._deleteObjectAjaxSuccess.calls.mostRecent().args[0] ).toBe(testData);
                        expect( manager._deleteObjectAjaxSuccess.calls.mostRecent().args[1] ).toBe(doneCbSpy);
                        expect( manager._deleteObjectAjaxSuccess.calls.mostRecent().args[2] ).not.toBe( mockedSourceResponse.response );
                        doneCb();
                    }, 5);
                });

            });

            describe('upon error AJAX response', function() {

                var testData,
                    mockedSourceResponse = {};

                beforeEach(function() {
                    mockedSourceResponse = {
                        status: 200,
                        response: {
                            error: {
                                message: 'rawr'
                            }
                        }
                    };

                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    testData = {id:34, name:'Jim'};
                    manager.deleteObject(testData, doneCbSpy);

                });


                it('calls _saveObjectAjaxSuccess with done callback and the error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager._deleteObjectAjaxSuccess ).toHaveBeenCalled();
                        expect( manager._deleteObjectAjaxSuccess.calls.mostRecent().args[0] ).toBe(testData);
                        expect( manager._deleteObjectAjaxSuccess.calls.mostRecent().args[1] ).toBe(doneCbSpy);
                        expect( manager._deleteObjectAjaxSuccess.calls.mostRecent().args[2] ).not.toBe( mockedSourceResponse.response );
                        doneCb();
                    }, 5);
                });

            });

            describe('upon failed AJAX response', function() {

                var testData,
                    mockedSourceResponse = {
                    status: 500,
                    statusText: 'Error',
                    response: 'Server was unavailable'
                };

                beforeEach(function() {
                    $.mockjax({
                        url: '*',
                        status: mockedSourceResponse.status || 200,
                        statusText: mockedSourceResponse.statusText || 'OK',
                        responseTime: 1,
                        responseText: mockedSourceResponse.response,
                        response: function(settings) {
                            mockRequest = settings;
                            mockResponse = this;
                            done = true;
                        }
                    });

                    testData = {id:34, name:'Jim'};
                    manager.deleteObject(testData , doneCbSpy);
                });

                it('calls _saveObjectAjaxError with done callback false and the error upon XHR error', function(doneCb) {
                    waitsForAndRuns(function() {
                        return done;
                    }, function() {
                        expect( manager._deleteObjectAjaxError ).toHaveBeenCalled();
                        expect( manager._deleteObjectAjaxError.calls.mostRecent().args[0] ).toBe(testData);
                        expect( manager._deleteObjectAjaxError.calls.mostRecent().args[1] ).toBe(doneCbSpy);
                        expect( manager._deleteObjectAjaxError.calls.mostRecent().args[3] ).not.toBe( mockedSourceResponse.statusText );
                        doneCb();
                    }, 5);
                });
            });
        });

        describe('_deleteObjectAjaxSuccess', function() {

            var testData,
                testResponse,
                doneCbSpy;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                spyOn(manager, 'unLockControls');
                spyOn(selector, 'unLockControls');
                spyOn(editForm, 'unLockControls');

                spyOn(manager.dialog, 'showDialog');

                doneCbSpy = jasmine.createSpy('doneCbSpy');
            });


            describe('when server responds with an error it', function() {

                var testData, testResponse;

                beforeEach(function() {
                    testData = {id: 55, name: 'Jim'};
                    testResponse = { error: { message: 'Server was attacked by bandits' } };

                    spyOn( manager, 'getSelectionsFromServer');
                    manager._deleteObjectAjaxSuccess(testData, doneCbSpy, testResponse);
                });

                it('calls unLockControls function', function() {
                    expect( manager.unLockControls ).toHaveBeenCalled();
                });

                it('calls Selector\'s unLockControls function', function() {
                    expect( selector.unLockControls ).toHaveBeenCalled();
                });

                it('calls EditForm\'s unLockControls function', function() {
                    expect( editForm.unLockControls ).toHaveBeenCalled();
                });

                it('calls callback with false and the error message from server', function() {
                    expect( doneCbSpy ).toHaveBeenCalledWith(false, "Server was attacked by bandits");
                });

                it('shows a dialog with text from server', function() {
                    expect( manager.dialog.showDialog ).toHaveBeenCalled();
                    expect(
                        manager.dialog.showDialog.calls.mostRecent().args[0].indexOf("Server was attacked by bandits" )
                    ).toBeGreaterThan(-1);
                });

                it('returns early', function() {

                    expect( manager.getSelectionsFromServer ).not.toHaveBeenCalled();
                });
            });

            describe('when server response does not contain ajaxDefs.deleteObject.responseKey property it', function() {

                var testData, testResponse;

                beforeEach(function() {
                    var testData = { id: 34, name: 'Bobby' };
                    var testResponse = { content: { response: { bag: testData } } };
                    spyOn( manager, 'getSelectionsFromServer');
                    manager._deleteObjectAjaxSuccess(testData, doneCbSpy, testResponse);
                });

                it('calls callback with error', function() {
                    expect( doneCbSpy ).toHaveBeenCalledWith(false, "Failed to delete object for an unknown reason.");
                });

                it('returns early and does not call getSelectionsFromServer', function() {
                    expect( manager.getSelectionsFromServer ).not.toHaveBeenCalled();
                });
            });

            describe('when server responds with valid response it', function() {

                var testData, testResponse;

                beforeEach(function() {
                    testData = { id: 34, name: 'Bobby' };
                    testResponse = { content: { response: { success: true } } };
                    spyOn(manager, 'getSelectionsFromServer' );
                    spyOn(manager, '_updateOrAddObjectToLoadedCache' );
                    spyOn(editForm, 'closeEditor' );
                    manager._deleteObjectAjaxSuccess(testData, doneCbSpy, testResponse);
                });

                it('calls callback with true', function() {
                    expect( doneCbSpy ).toHaveBeenCalledWith(true );
                });

                it('calls EditForm\'s closeEditor function', function() {
                    expect( editForm.closeEditor ).toHaveBeenCalled();
                });

                it('calls getSelectionsFromServer', function() {
                    expect( manager.getSelectionsFromServer ).toHaveBeenCalled();
                });

            });

            describe('after it gets called back from getSelectionsFromServer', function() {

                var testData, testResponse;

                beforeEach(function() {
                    testData = { id: 34, name: 'Bobby' };
                    testResponse = { content: { response: { success: true } } };
                    spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function(cb) { cb(); });
                    spyOn(manager, '_updateOrAddObjectToLoadedCache' );
                    spyOn(editForm, 'closeEditor' );
                    manager._deleteObjectAjaxSuccess(testData, doneCbSpy, testResponse);
                });

                describe('it', function() {

                    it('calls unLockControls', function() {
                        expect( manager.unLockControls ).toHaveBeenCalled();
                    });

                    it('calls Selector\'s unLockControls function', function() {
                        expect( selector.unLockControls ).toHaveBeenCalled();
                    });

                    it('calls EditForm\'s unLockControls function', function() {
                        expect( editForm.unLockControls ).toHaveBeenCalled();
                    });

                });

//                describe('with true', function() {
//                    spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function(cb) { cb(true); });
//                    manager._deleteObjectAjaxSuccess(testData, doneCbSpy, testResponse);
//                });
//
//                describe('with false', function() {
//
//                    spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function(cb) { cb(false); });
//                    manager._deleteObjectAjaxSuccess(testData, doneCbSpy, testResponse);
//                });
            });

        });

        describe('_deleteObjectAjaxError', function() {

            var testData, testResponse, doneCbSpy;

            beforeEach(function() {
                loadBasicManagerInit();
                basicManagerInitSpies();

                // Already tested to call setupEvents
                manager.initialize( $container );

                spyOn(manager, 'unLockControls');
                spyOn(selector, 'unLockControls');
                spyOn(editForm, 'unLockControls');

                spyOn(manager.dialog, 'showDialog');

                doneCbSpy = jasmine.createSpy('doneCbSpy');

                testData = { id: 34, name: 'Bobby' };

                // Mock getSelectionsFromServer to always call callback with false
                spyOn(manager, 'getSelectionsFromServer' ).and.callFake(function (cb) { cb(false); });
                manager._deleteObjectAjaxError(testData, doneCbSpy, {status:500, statusText:'Error'}, 'Error', 'error');
            });

            it('calls unLockControls', function() {
                expect( manager.unLockControls ).toHaveBeenCalled();
            });

            it('calls Selector\'s unLockControls function', function() {
                expect( selector.unLockControls ).toHaveBeenCalled();
            });

            it('calls EditForm\'s unLockControls function', function() {
                expect( editForm.unLockControls ).toHaveBeenCalled();
            });

            it('calls callback with false and the status', function() {
                expect( doneCbSpy ).toHaveBeenCalledWith(false, 'error');
            });

        });
    });

});
