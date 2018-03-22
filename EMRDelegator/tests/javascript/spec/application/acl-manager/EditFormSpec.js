/**
 * * Manager's EditForm Tests
 *
 * @tests js/application/acl-manager/Manager.js
 *
 */

describe('nsAdmin', function() {
    var Manager = wpt.page.Manager,
        EditForm = wpt.page.EditForm,
        Selector = wpt.page.Selector;

    var
        // Helper functions
        loadBasicMocks,
        loadBasicMocksAndInit,

        // Helper references
        editForm,
        selector,
        manager,
        $container,
        $selector,
        $editPanel,
        $newButton;

    loadBasicMocks = function() {
        // Load fixture
        jasmine.getFixtures().fixturesPath = 'spec/application/acl-manager/fixtures/';
        loadFixtures('basic.html');

        // Init objects
        editForm = new EditForm();
        selector = new Selector();
        manager = new Manager(selector, editForm);
        $container = $('.container');
        $editPanel = $container.find('.edit-panel');

        // Neutralize "Must be overridden" errors
        spyOn(selector, 'setupEvents');
        spyOn(selector, 'selectById');
        spyOn(selector, 'clearSelectionList');
        spyOn(selector, 'removeEvents');
        spyOn(editForm, 'clearForm');
    };

    loadBasicMocksAndInit = function() {
        loadBasicMocks();
        manager.initialize( $container );
    };


    afterEach(function() {
        editForm = selector = manager = $container = $selector = $editPanel = $newButton = null;

        // For safety
        $.mockjax.clear();
    });

    describe('EditForm', function() {


        it('is an EditForm when instantiated', function() {
            editForm = new EditForm();
            expect( editForm instanceof wpt.page.EditForm ).toBeTruthy();
        });

        describe('when initialized', function() {

            beforeEach(function() {
                loadBasicMocks();
                spyOn(editForm, "linkToElements" ).and.callThrough();
                spyOn(editForm, "setupEvents" ).and.callThrough();
                editForm.selectedId = 19287; // to test it gets nulled
                manager.initialize( $container );
            });

            it('calls linkToElements', function() {
                expect( editForm.linkToElements ).toHaveBeenCalledWith( $container.find( manager.editFormContainerClassname ) );
            });

            it('calls setupEvents', function() {
                expect( editForm.setupEvents ).toHaveBeenCalled();
            });

            it('records manager reference', function() {
                expect( editForm.manager ).toBe( manager );
            });

            it('throws if manager reference not given', function() {
                function testIt() {
                    editForm.initialize();
                }

                expect( testIt ).toThrowError();
            });

        });

        describe('when removed', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
                spyOn(editForm, "unlinkElements" ).and.callThrough();
                spyOn(editForm, "removeEvents" ).and.callThrough();
            });

            it('calls unlinkElements', function() {
                expect( editForm.unlinkElements ).not.toHaveBeenCalled();
                manager.remove();
                expect( editForm.unlinkElements ).toHaveBeenCalled();
            });

            it('calls removeEvents', function() {
                expect( editForm.removeEvents ).not.toHaveBeenCalled();
                manager.remove();
                expect( editForm.removeEvents ).toHaveBeenCalled();
            });

        });


        describe('links to elements', function() {
            it('via linkToElements function', function() {
                loadBasicMocksAndInit();
                expect( $.isFunction( editForm.linkToElements ) ).toBeTruthy();
            });

            it('where it stores the given container', function() {
                loadBasicMocksAndInit();
                var $myEl = $container.find( manager.editFormContainerClassname );
                editForm = new EditForm();
                editForm.editFormLatch = jasmine.createSpyObj('editFormLatch', ['trackElement']);
                editForm.linkToElements( $myEl );
                expect( editForm.$container ).toBe( $myEl );
            });

            it('where it finds elements inside the given container based on their classname properties', function() {
                loadBasicMocksAndInit();
                var $myEl = $container.find( manager.editFormContainerClassname );
                editForm = new EditForm();
                editForm.editFormLatch = jasmine.createSpyObj('editFormLatch', ['trackElement']);
                editForm.linkToElements( $myEl );
                expect( editForm.$editingTitle ).toEqual( $myEl.find( editForm.editingTitleClassname ) );
                expect( editForm.$editForm ).toEqual( $myEl.find( editForm.editPanelClassname ) );
                expect( editForm.$closeButton ).toEqual( $myEl.find( editForm.closeButtonClassname ) );
                expect( editForm.$submitButon ).toEqual( $myEl.find( editForm.submitButonClassname ) );
                expect( editForm.$loadingIndicator ).toEqual( $myEl.find( editForm.loadingIndicatorClassname ) );
            });

            it('where it instantiates a ProgressIndicator', function() {
                loadBasicMocksAndInit();
                var $myEl = $container.find( manager.editFormContainerClassname );
                editForm = new EditForm();
                editForm.editFormLatch = jasmine.createSpyObj('editFormLatch', ['trackElement']);
                editForm.linkToElements( $myEl );
                expect( editForm.progressIndicator instanceof wpt.nsUi.ProgressIndicator ).toBeTruthy();
            });
        });

        it('clears element references when unlinked', function() {
            loadBasicMocksAndInit();

            expect( editForm.$container ).not.toBeNull();
            expect( editForm.$editingTitle ).not.toBeNull();
            expect( editForm.$editForm ).not.toBeNull();
            expect( editForm.$closeButton ).not.toBeNull();
            expect( editForm.$submitButon ).not.toBeNull();
            expect( editForm.$loadingIndicator ).not.toBeNull();
            expect( editForm.progressIndicator ).not.toBeNull();

            editForm.unlinkElements();

            expect( editForm.$container ).toBeNull();
            expect( editForm.$editingTitle ).toBeNull();
            expect( editForm.$editForm ).toBeNull();
            expect( editForm.$closeButton ).toBeNull();
            expect( editForm.$submitButon ).toBeNull();
            expect( editForm.$loadingIndicator ).toBeNull();
            expect( editForm.progressIndicator ).toBeNull();
        });


        describe('matches Manager\'s requirement', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
            });

            it('of having a setupEvents function', function() {
                expect( $.isFunction( editForm.setupEvents ) ).toBeTruthy();
            });

            it('of having a removeEvents function', function() {
                expect( $.isFunction( editForm.removeEvents ) ).toBeTruthy();
            });

            it('of having a lockControls function', function() {
                expect( $.isFunction( editForm.lockControls ) ).toBeTruthy();
            });

            it('of having an unLockControls function', function() {
                expect( $.isFunction( editForm.unLockControls ) ).toBeTruthy();
            });

            it('of having an openEditorForCreation function', function() {
                expect( $.isFunction( editForm.openEditorForCreation ) ).toBeTruthy();
            });
            it('of having an openEditorForEditing function', function() {
                expect( $.isFunction( editForm.openEditorForEditing ) ).toBeTruthy();
            });
            it('of having an closeEditor function', function() {
                expect( $.isFunction( editForm.closeEditor ) ).toBeTruthy();
            });
            it('of having an placeFocusOnStartingField function', function() {
                expect( $.isFunction( editForm.placeFocusOnStartingField ) ).toBeTruthy();
            });
            it('of having an populateForm function', function() {
                expect( $.isFunction( editForm.populateForm ) ).toBeTruthy();
            });
            it('of having an clearForm function', function() {
                expect( $.isFunction( editForm.clearForm ) ).toBeTruthy();
            });
            it('of having an getCurrentFormData function', function() {
                expect( $.isFunction( editForm.getCurrentFormData ) ).toBeTruthy();
            });
            it('of having an validateForm function', function() {
                expect( $.isFunction( editForm.validateForm ) ).toBeTruthy();
            });
            it('of having an clearInvalidFlags function', function() {
                expect( $.isFunction( editForm.clearInvalidFlags ) ).toBeTruthy();
            });
        });


        describe('when locked', function() {
            var $closeButton, $deleteButton, $submitButton;
            beforeEach(function() {
                loadBasicMocksAndInit();
                $closeButton = editForm.$closeButton;
                $deleteButton = editForm.$deleteButton;
                $submitButton = editForm.$submitButon;
            });

            it('disables $closeButton', function() {
                expect( $closeButton ).not.toBeDisabled();
                editForm.lockControls();
                expect( $closeButton ).toBeDisabled();
            });
            it('disables $submitButton', function() {
                expect( $submitButton ).not.toBeDisabled();
                editForm.lockControls();
                expect( $submitButton ).toBeDisabled();
            });

        });

        describe('when unlocked', function() {
            var $closeButton, $deleteButton, $submitButton;
            beforeEach(function() {
                loadBasicMocksAndInit();
                spyOn( editForm.progressIndicator, 'hide' ).and.callThrough();
                $closeButton = editForm.$closeButton;
                $deleteButton = editForm.$deleteButton;
                $submitButton = editForm.$submitButon;
            });

            it('enables $closeButton', function() {
                expect( $closeButton ).not.toBeDisabled();
                editForm.lockControls();
                expect( $closeButton ).toBeDisabled();
                editForm.unLockControls();
                expect( $closeButton ).not.toBeDisabled();
            });

            it('enables $submitButton', function() {
                expect( $submitButton ).not.toBeDisabled();
                editForm.lockControls();
                expect( $submitButton ).toBeDisabled();
                editForm.unLockControls();
                expect( $submitButton ).not.toBeDisabled();
            });

            it('hides progress indicator', function() {
                editForm.unLockControls();
                expect( editForm.progressIndicator.hide ).toHaveBeenCalled();
            });

        });

        describe('close button clicked event', function() {

            var $closeButton;
            beforeEach(function() {
                loadBasicMocksAndInit();
                $closeButton = editForm.$closeButton;
            });


            it('gets set in setupEvents', function() {
                expect( $closeButton ).toHandle('click.editForm');
            });

            it('gets cleared in removeEvents', function() {
                expect( $closeButton ).toHandle('click.editForm');
                manager.remove();
                expect( $closeButton ).not.toHandle('click.editForm');
            });

            it('calls closeEditor with return focus as true', function() {
                spyOn(editForm, 'closeEditor' ).and.callThrough();
                $closeButton.trigger('click');
                expect( editForm.closeEditor ).toHaveBeenCalledWith(true);
            });
        });

        describe('submit button clicked event', function() {

            var $submitButton;

            beforeEach(function() {
                loadBasicMocksAndInit();
                $submitButton = editForm.$submitButon;
            });

            it('gets set in setupEvents', function() {
                expect( $submitButton ).toHandle('click.editForm');
            });

            it('gets cleared in removeEvents', function() {
                expect( $submitButton ).toHandle('click.editForm');
                manager.remove();
                expect( $submitButton ).not.toHandle('click.editForm');
            });

            it('calls clearInvalidFlags', function() {
                spyOn(editForm, 'clearInvalidFlags' );
                spyOn(editForm, 'validateForm' );
                $submitButton.trigger('click');
                expect( editForm.clearInvalidFlags ).toHaveBeenCalled();
            });

            it('calls validateForm', function() {
                spyOn(editForm, 'clearInvalidFlags' );
                spyOn(editForm, 'validateForm' );
                $submitButton.trigger('click');
                expect( editForm.validateForm ).toHaveBeenCalled();
            });

            describe('calls validateForm and if it returns false', function() {

                beforeEach(function() {
                    spyOn(editForm, 'clearInvalidFlags' );
                    spyOn(editForm, 'validateForm' ).and.returnValue(false);
                    spyOn(manager, 'saveObject' );
                });

                it('does not call saveObject', function() {
                    $submitButton.trigger('click');
                    expect( manager.saveObject ).not.toHaveBeenCalled();
                });
            });

            describe('calls validateForm and if it returns true', function() {
                beforeEach(function() {
                    spyOn(editForm, 'clearInvalidFlags' );
                    spyOn(editForm, 'validateForm' ).and.returnValue(true);
                    spyOn(editForm, 'getCurrentFormData' ).and.returnValue("currentFormData");
                });

                it('calls getCurrentFormData', function() {
                    spyOn(manager, 'saveObject' );
                    $submitButton.trigger('click');
                    expect( editForm.getCurrentFormData ).toHaveBeenCalled();
                });

                it('calls saveObject with current form data and a callback', function() {
                    spyOn(manager, 'saveObject' );
                    $submitButton.trigger('click');
                    expect( manager.saveObject ).toHaveBeenCalled();
                    expect( manager.saveObject.calls.mostRecent().args[0] ).toBe("currentFormData");
                    expect( $.isFunction( manager.saveObject.calls.mostRecent().args[1] ) ).toBeTruthy();
                });

                describe('calls saveObject with current form data and if it fails', function() {

                    beforeEach(function() {
                        spyOn(manager, 'saveObject' ).and.callFake(function(data, cb) { cb(false, data); });
                        spyOn(manager.dialog, 'showDialog');
                    });

                    it('shows dialog', function() {
                        $submitButton.trigger('click');
                        expect( manager.dialog.showDialog ).toHaveBeenCalled();
                        expect( manager.dialog.showDialog.calls.mostRecent().args[0] ).toContain("Failed to save:");
                        expect( manager.dialog.showDialog.calls.mostRecent().args[0] ).toContain("currentFormData");
                        expect( manager.dialog.showDialog.calls.mostRecent().args[1] ).toBe('Failed to save');
                        expect( manager.dialog.showDialog.calls.mostRecent().args[2] ).toBeTruthy();
                    });
                });

            });
        });

        describe('container keydown event', function() {

            var $editFormContainer;
            beforeEach(function() {
                loadBasicMocksAndInit();
                $editFormContainer = editForm.$container;
            });

            it('gets set in setupEvents', function() {
                expect( $editFormContainer ).toHandle('keydown.editForm');
            });

            it('gets cleared in removeEvents', function() {
                expect( $editFormContainer ).toHandle('keydown.editForm');
                manager.remove();
                expect( $editFormContainer ).not.toHandle('keydown.editForm');
            });

            it('calls closeEditor with return focus true if escape key is pressed', function() {
                var escapeKeyDown = $.Event('keydown', {keyCode: 27} ),
                    nonEscapeKeyDown = $.Event('keydown', {keyCode: 21} );

                spyOn( editForm, 'closeEditor' );

                $editFormContainer.trigger(nonEscapeKeyDown);

                expect( editForm.closeEditor ).not.toHaveBeenCalled();
                expect( editForm.closeEditor ).not.toHaveBeenCalledWith(true);

                $editFormContainer.trigger(escapeKeyDown);

                expect( editForm.closeEditor ).toHaveBeenCalled();
                expect( editForm.closeEditor ).toHaveBeenCalledWith(true);
            });
        });


        describe('keeps tabbing inside container', function() {

            var $firstTabbable, $lastTabbable;
            beforeEach(function() {
                loadBasicMocksAndInit();
                var $editFormContainer = editForm.$container;
                $firstTabbable = editForm.$firstTabbable;
                $lastTabbable = editForm.$lastTabbable;
            });

            it('adds a keydown event on first tabbable element', function() {
                expect( $firstTabbable ).toHandle('keydown.tabbing');
            });

            it('adds a keydown event on last tabbable element', function() {
                expect( $lastTabbable).toHandle('keydown.tabbing');
            });

            it('removes events on removeEvents', function() {
                manager.remove();
                expect( $firstTabbable ).not.toHandle('keydown.tabbing');
                expect( $lastTabbable ).not.toHandle('keydown.tabbing');
            });

            describe('last tabbable\'s keydown event', function() {
                var triggerEvt,
                    passEvt;

                beforeEach(function() {
                    triggerEvt = $.Event('keydown', {keyCode: 9, shiftKey: false} );
                    passEvt = $.Event('keydown', {keyCode: 61, shiftKey: true} );
                    $lastTabbable.focus();
                });

                it('calls preventDefault on event if tab was pressed', function() {
                   var spyEvent = spyOnEvent( $lastTabbable, 'keydown' );

                    $lastTabbable.trigger(passEvt);
                    expect( 'keydown' ).not.toHaveBeenPreventedOn( $lastTabbable );
                    expect( spyEvent ).not.toHaveBeenPrevented();

                    $lastTabbable.trigger(triggerEvt);
                    expect( 'keydown' ).toHaveBeenPreventedOn( $lastTabbable );
                    expect( spyEvent ).toHaveBeenPrevented();
                });

                it('sets focus on first tabbable element if tab was pressed', function() {
                    var spyEvent = spyOnEvent( $firstTabbable, 'focus');
                    $lastTabbable.trigger(passEvt);
                    expect( spyEvent ).not.toHaveBeenTriggered();
                    $lastTabbable.trigger(triggerEvt);
                    expect( spyEvent ).toHaveBeenTriggered();
                });
            });

            describe('first tabbable\'s keydown event', function() {
                var triggerEvt,
                    passEvt;

                beforeEach(function() {
                    triggerEvt = $.Event('keydown', {keyCode: 9, shiftKey: true} );
                    passEvt = $.Event('keydown', {keyCode: 61, shiftKey: false} );
                    $firstTabbable.focus();
                });

                it('calls preventDefault on event if shift+tab was pressed', function() {
                    var spyEvent = spyOnEvent( $firstTabbable, 'keydown' );

                    $firstTabbable.trigger(passEvt);
                    expect( 'keydown' ).not.toHaveBeenPreventedOn( $firstTabbable );
                    expect( spyEvent ).not.toHaveBeenPrevented();

                    $firstTabbable.trigger(triggerEvt);
                    expect( 'keydown' ).toHaveBeenPreventedOn( $firstTabbable );
                    expect( spyEvent ).toHaveBeenPrevented();
                });

                it('sets focus on last tabbable element if shift+tab was pressed', function() {
                    var spyEvent = spyOnEvent( $lastTabbable, 'focus');
                    $firstTabbable.trigger(passEvt);
                    expect( spyEvent ).not.toHaveBeenTriggered();
                    $firstTabbable.trigger(triggerEvt);
                    expect( spyEvent ).toHaveBeenTriggered();
                });
            });
        });

        describe('provides functions to override', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
            });

            describe('to return editing title from object', function() {

                it('returns editing title from object', function() {
                    var dummyObject = {
                            id: 13,
                            name: 'Freddie'
                        },
                        returnValue = editForm.getEditingTitleFromObject(dummyObject);

                    expect( returnValue ).toContain('Freddie');
                });

                it('throws if given an invalid parameter or object', function() {
                    var funcyTown = function() {
                        editForm.getEditingTitleFromObject();
                    };

                    expect(funcyTown).toThrowError();
                });
            });

            describe('to place user\'s focus on starting field', function() {
                it('exists', function() {
                    expect( $.isFunction( editForm.placeFocusOnStartingField ) ).toBeTruthy();
                });

                it('throws by default to enforce being overridden', function() {
                    var funcyTown = function() {
                        editForm.placeFocusOnStartingField();
                    };

                    expect(funcyTown).toThrowError('Must be overridden!');
                });
            });

            describe('to populate the form with data', function() {
                it('exists', function() {
                    expect( $.isFunction( editForm.populateForm ) ).toBeTruthy();
                });

                it('throws by default to enforce being overridden', function() {
                    var funcyTown = function() {
                        editForm.populateForm();
                    };

                    expect(funcyTown).toThrowError('Must be overridden!');
                });

            });

            describe('to clear the form of data', function() {
                it('exists', function() {
                    expect( $.isFunction( editForm.clearForm ) ).toBeTruthy();
                });

                it('throws by default to enforce being overridden', function() {

                    // clearForm was spied upon to get past this protection
                    // so unspy it by redefining
                    editForm = new EditForm();

                    var funcyTown = function() {
                        editForm.clearForm();
                    };

                    expect(funcyTown).toThrowError('Must be overridden!');
                });

            });

            describe('to get the data of the current state of the form', function() {

                it('exists', function() {
                    expect( $.isFunction( editForm.getCurrentFormData ) ).toBeTruthy();
                });

                it('throws by default to enforce being overridden', function() {
                    var funcyTown = function() {
                        editForm.getCurrentFormData();
                    };

                    expect(funcyTown).toThrowError('Must be overridden!');
                });

            });

            describe('to validate the form', function() {

                it('exists', function() {
                    expect( $.isFunction( editForm.validateForm ) ).toBeTruthy();
                });

                it('throws by default to enforce being overridden', function() {
                    var funcyTown = function() {
                        editForm.validateForm();
                    };

                    expect(funcyTown).toThrowError('Must be overridden!');
                });

            });

            describe('to clear any invalid state from the form (from validateForm)', function() {
                it('exists', function() {
                    expect( $.isFunction( editForm.clearInvalidFlags ) ).toBeTruthy();
                });

                it('throws by default to enforce being overridden', function() {
                    var funcyTown = function() {
                        editForm.clearInvalidFlags();
                    };

                    expect(funcyTown).toThrowError('Must be overridden!');
                });

            });
        });

        describe('when opening the editor for creation', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
                spyOn(manager.dialog, 'showDialog' );

                editForm.clearForm.calls.reset(); // Reset spy created earlier
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
            });

            it('sets editing title to manager\'s object name', function() {
                editForm.openEditorForCreation();
                expect( editForm.$editingTitle ).toHaveText('> New Object');
                manager.objectName = 'Test';
                editForm.openEditorForCreation();
                expect( editForm.$editingTitle ).toHaveText('> New Test');
            });

            it('calls clearForm', function() {
                editForm.openEditorForCreation();
                expect( editForm.clearForm ).toHaveBeenCalled();
            });

            it('shows the edit form container', function() {

                manager.dialog.showDialog.and.callThrough();

                expect( editForm.$container ).toBeHidden();
                editForm.openEditorForCreation();
                expect( editForm.$container ).toBeVisible();
                editForm.closeEditor();
            });

            it('calls placeFocusOnStartingField', function() {
                editForm.openEditorForCreation();
                expect( editForm.placeFocusOnStartingField ).toHaveBeenCalled();
            });

            it('sets the internal mode flag to creation', function() {
                editForm.openEditorForCreation();
                expect( editForm._mode ).toBe( 'creating' );
            });

        });

        describe('when opening the editor for editing', function() {

            var dummyObject;

            beforeEach(function() {
                loadBasicMocksAndInit();
                editForm.clearForm.calls.reset(); // Reset spy created earlier
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                dummyObject = {
                    id: 13,
                    name: 'Johnny'
                };
            });

            it('sets editing title to given object\'s name', function() {
                editForm.openEditorForEditing(dummyObject);
                expect( editForm.$editingTitle ).toHaveText('> Editing Johnny');
                expect( editForm.$editingTitle ).toContainHtml('<b>Johnny</b>');
            });

            it('calls clearForm', function() {
                editForm.openEditorForEditing(dummyObject);
                expect( editForm.clearForm ).toHaveBeenCalled();
            });

            it('shows the edit form container', function() {
                expect( editForm.$container ).toBeHidden();
                editForm.openEditorForEditing(dummyObject);
                expect( editForm.$container ).toBeVisible();
            });

            it('calls placeFocusOnStartingField', function() {
                editForm.openEditorForEditing(dummyObject);
                expect( editForm.placeFocusOnStartingField ).toHaveBeenCalled();
            });

            it('sets the internal mode flag to editing and the mode extra flag to the object to edits cache id value', function() {
                editForm.openEditorForEditing(dummyObject);
                expect( editForm._mode ).toBe( 'editing' );
                expect( editForm._modeEx ).toBe( dummyObject.id );
            });

        });

        describe('when closing the editor', function() {

            var dummyObject;

            beforeEach(function() {
                loadBasicMocksAndInit();
                editForm.clearForm.calls.reset(); // Reset spy created earlier
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                spyOn(manager, 'clearSelection');
                spyOn(manager, 'placeFocusOnNewButton');
                spyOn(manager, 'placeFocusOnEditActionOfGivenId');
                dummyObject = {
                    id: 13,
                    name: 'Johnny'
                };
                editForm.openEditorForEditing(dummyObject);
            });

            it('clears the editing title', function() {
                editForm.closeEditor();
                expect( editForm.$editingTitle ).toHaveText('');
            });

            it('calls clearForm', function() {
                editForm.closeEditor();
                expect( editForm.clearForm ).toHaveBeenCalled();
            });

            describe('allows returning focus', function() {
                describe('from creating', function() {
                    it('which calls manager\'s placeFocusOnNewButton function', function() {
                        editForm.closeEditor();
                        editForm.openEditorForCreation();
                        expect( editForm._mode ).not.toBeNull();
                        expect( editForm._modeEx ).toBeNull();
                        editForm.closeEditor(true);
                        expect( manager.placeFocusOnNewButton ).toHaveBeenCalled();
                    });
                });

                describe('from editing', function() {
                    it('which calls manager\'s placeFocusOnEditActionOfGivenId fcn with internal mode extra flag', function() {
                        expect( editForm._mode ).not.toBeNull();
                        expect( editForm._modeEx ).not.toBeNull();
                        editForm.closeEditor(true);
                        expect( manager.placeFocusOnEditActionOfGivenId ).toHaveBeenCalled();
                        expect( manager.placeFocusOnEditActionOfGivenId ).toHaveBeenCalledWith( dummyObject.id );
                    });
                });
            });

            it('sets internal mode flag to null and mode extra flag to null', function() {
                editForm.closeEditor();
                expect( editForm._mode ).toBeNull();
                expect( editForm._modeEx ).toBeNull();
            });

            it('hides the edit form container', function() {
                expect( editForm.$container ).toBeVisible();
                editForm.closeEditor();
                expect( editForm.$container ).toBeHidden();
            });

        });


    });


});
