/**
 * * Manager's Selector's DataTableSelector Tests
 *
 * @tests js/application/acl-manager/Manager.js
 *
 */

describe('nsAdmin', function() {
    var Manager = wpt.page.Manager,
        EditForm = wpt.page.EditForm,
        Selector = wpt.page.Selector,
        DataTableSelector = wpt.page.DataTableSelector;

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
        loadFixtures('table.html');
        jasmine.getStyleFixtures().fixturesPath = 'spec/application/acl-manager/fixtures/';
        loadStyleFixtures('basic.css');

        // Init objects
        editForm = new EditForm();
        selector = new DataTableSelector();
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

    describe('DataTableSelector', function() {


        it('is a Selector and a DataTableSelector', function() {
            selector = new DataTableSelector();

            expect( selector instanceof Selector ).toBeTruthy();
            expect( selector instanceof DataTableSelector ).toBeTruthy();
        });

        describe('overrides linkToElements', function() {

            beforeEach(function() {
                spyOn( Selector.prototype, 'linkToElements' ).and.callThrough();
                spyOn( wpt.nsUi.nsDataTable, 'makeTableIntoDataTable' );
                loadBasicMocks();
            });

            it('calls parent w/ proper context', function() {
                manager.initialize($container);
                expect( Selector.prototype.linkToElements ).toHaveBeenCalled();
                expect( Selector.prototype.linkToElements.calls.argsFor(0)[0] ).toEqual( manager.$selectorContainer );
                expect( Selector.prototype.linkToElements.calls.all()[0].object ).toBe( selector );
            });

            it('instantiates DataTables with $selector as table', function() {
                var dtConfig = selector.dataTableConfig;
                manager.initialize($container);
                expect( wpt.nsUi.nsDataTable.makeTableIntoDataTable ).toHaveBeenCalled();

                expect( wpt.nsUi.nsDataTable.makeTableIntoDataTable.calls.allArgs()[0][0].$table ).toBeDefined();
                expect( wpt.nsUi.nsDataTable.makeTableIntoDataTable.calls.allArgs()[0][0].$table ).toBe( selector.$selector );

            });

            it('instantiates DataTables with dataTableConfig options', function() {
                var fakeInstance = 'instantiatedObject';
                wpt.nsUi.nsDataTable.makeTableIntoDataTable.and.returnValue(fakeInstance);
                var dtConfig = selector.dataTableConfig;
                manager.initialize($container);
                expect( wpt.nsUi.nsDataTable.makeTableIntoDataTable.calls.allArgs()[0][0].dataTableConfig ).toBeDefined();
                expect( wpt.nsUi.nsDataTable.makeTableIntoDataTable.calls.allArgs()[0][0].dataTableConfig.aoColumns ).toBeDefined();
                expect(
                    wpt.nsUi.nsDataTable.makeTableIntoDataTable.calls.allArgs()[0][0].dataTableConfig.aoColumns
                ).toBe(
                    dtConfig.dataTableConfig.aoColumns
                );

                expect( selector._dtInstance ).toBe( fakeInstance );
            });
        });

        describe('overrides unlinkElements', function() {
            beforeEach(function() {
                loadBasicMocksAndInit();
                selector.setupEvents.and.callThrough(); // Alter spy to call through
                selector.removeEvents.and.callThrough(); // Alter spy to call through
                spyOn( selector._dtInstance.dataTable, 'fnDestroy' ).and.callThrough();
                spyOn( Selector.prototype, 'unlinkElements' ).and.callThrough();
                manager.remove();
            });

            it('destroys datatable instance', function() {
                expect( selector._dtInstance.dataTable.fnDestroy ).toHaveBeenCalled();
            });

            it('calls parent w/ proper context', function() {
                expect( Selector.prototype.unlinkElements ).toHaveBeenCalled();
                expect( Selector.prototype.unlinkElements.calls.mostRecent().object ).toBe( selector );

            });
        });

        describe('overrides setupEvents', function() {
            beforeEach(function() {
                loadBasicMocks();
                selector.setupEvents.and.callThrough(); // Alter spy to call through
                selector.removeEvents.and.callThrough(); // Alter spy to call through
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                selector.canDelete = true;
                manager.initialize($container);
                manager.populateSelectionsByArray([
                    {
                        id: 3,
                        name: 'Rawr'
                    },
                    {
                        id: 4,
                        name: 'Bob'
                    }
                ]);
            });

            it('places click handler on container for children edit buttons', function() {
                var evtSpy = spyOnEvent( selector.$selector, 'click.selector' );
                var oneEditLink = selector.$selector.find(selector.editButtonClassname ).eq(0);

                expect( selector.$selector ).toHandleWith( 'click.selector', selector._editActionInitiated );

                var evt = $.Event('click');
                oneEditLink.trigger(evt);

                expect( evtSpy ).toHaveBeenTriggered(  );
                expect( 'click.selector' ).toHaveBeenTriggeredOn( selector.$selector );
            });

            it('places click handler on container for children delete buttons', function() {
                spyOn(manager.dialog, 'showDialog');

                var evtSpy = spyOnEvent( selector.$selector, 'click.deleteButton' );
                var oneDeleteLink = selector.$selector.find(selector.deleteButtonClassname ).eq(0);

                expect( selector.$selector ).toHandleWith( 'click.deleteButton', selector._deleteActionInitiated );

                var evt = $.Event('click');
                oneDeleteLink.trigger(evt);

                expect( evtSpy ).toHaveBeenTriggered(  );
                expect( 'click.deleteButton' ).toHaveBeenTriggeredOn( selector.$selector );
            });
        });

        describe('overrides removeEvents', function() {
            it('clears click handler on container for children edit buttons', function() {
                loadBasicMocks();
                selector.setupEvents.and.callThrough(); // Alter spy to call through
                selector.removeEvents.and.callThrough(); // Alter spy to call through
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                manager.initialize($container);
                manager.populateSelectionsByArray([
                    {
                        id: 3,
                        name: 'Rawr'
                    },
                    {
                        id: 4,
                        name: 'Bob'
                    }
                ]);

                var evtSpy = spyOnEvent( selector.$selector, 'click.selector' );
                var oneEditLink = selector.$selector.find(selector.editButtonClassname ).eq(0);

                expect( selector.$selector ).toHandleWith( 'click.selector', selector._editActionInitiated );
                manager.remove();
                expect( selector.$selector ).not.toHandleWith( 'click.selector', selector._editActionInitiated );
                expect( selector.$selector ).toBeNull();

                var evt = $.Event('click');
                oneEditLink.trigger(evt);

                expect( evtSpy ).not.toHaveBeenTriggered();
            });

            it('clears click handler on container for children delete buttons', function() {
                loadBasicMocks();
                selector.setupEvents.and.callThrough(); // Alter spy to call through
                selector.removeEvents.and.callThrough(); // Alter spy to call through
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                selector.canDelete = true;
                manager.initialize($container);
                manager.populateSelectionsByArray([
                    {
                        id: 3,
                        name: 'Rawr'
                    },
                    {
                        id: 4,
                        name: 'Bob'
                    }
                ]);

                var evtSpy = spyOnEvent( selector.$selector, 'click.deleteButton' );
                var oneEditLink = selector.$selector.find(selector.deleteButtonClassname ).eq(0);

                expect( selector.$selector ).toHandleWith( 'click.deleteButton', selector._deleteActionInitiated );
                manager.remove();
                expect( selector.$selector ).not.toHandleWith( 'click.deleteButton', selector._deleteActionInitiated );
                expect( selector.$selector ).toBeNull();

                var evt = $.Event('click');
                oneEditLink.trigger(evt);

                expect( evtSpy ).not.toHaveBeenTriggered();
            });
        });

        describe('edit button click handler', function() {

            var oneEditLink,
                evt;

            beforeEach(function() {
                loadBasicMocks();

                spyOn( selector, '_editActionInitiated' ).and.callThrough();
                spyOn( DataTableSelector.prototype, '_editActionInitiated' ).and.callThrough();

                selector.setupEvents.and.callThrough(); // Alter spy to call through
                selector.removeEvents.and.callThrough(); // Alter spy to call through
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                manager.initialize($container);
                manager.populateSelectionsByArray([
                    {
                        id: 3,
                        name: 'Rawr'
                    },
                    {
                        id: 4,
                        name: 'Bob'
                    }
                ]);

                oneEditLink = selector.$selector.find(selector.editButtonClassname ).eq(0);
                evt = $.Event('click');
            });

            it('calls _editActionInitiated', function() {
                oneEditLink.trigger(evt);
                expect( selector._editActionInitiated ).toHaveBeenCalledWith(jasmine.any(Object));
            });

            it('passes reference to itself to _editActionInitiated callback', function() {
                oneEditLink.trigger(evt);
                expect( selector._editActionInitiated.calls.mostRecent().args[0].data ).toBe( selector );
            });

            it('adds loading classes', function() {
                spyOn(manager, 'selectObject'); // Disable selectObject and therefore our callback

                expect( selector.$loadingIndicator ).toBeHidden();
                expect( oneEditLink ).not.toHaveClass('is-loading');

                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( oneEditLink ).toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeVisible();
            });

            it('sets callback for selectObject to clear loading classes if successfully selected object', function() {

                spyOn(manager, 'selectObject' ).and.callThrough();

                expect( oneEditLink ).not.toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeHidden();

                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( oneEditLink ).not.toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeHidden()
            });
            it('sets callback for selectObject to clear loading classes if failed to select object', function() {

                spyOn(manager, 'selectObject' ).and.callFake(function(selectedId, cb) { cb(false); });

                expect( oneEditLink ).not.toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeHidden();

                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( oneEditLink ).not.toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeHidden();
            });


            it('does not add loading classes if is-disabled class is present', function() {
                expect( oneEditLink ).not.toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeHidden();

                oneEditLink.addClass('is-disabled');
                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( oneEditLink ).not.toHaveClass('is-loading');
                expect( selector.$loadingIndicator ).toBeHidden();
            });

            it('calls manager\'s selectObject function with the item\'s id data attribute', function() {
                spyOn( manager, 'selectObject' );

                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( manager.selectObject ).toHaveBeenCalledWith( 3, jasmine.any(Function) );
            });

            it('does not call manager\'s selectObject function with the item\'s id data attribute if is-disabled class is present', function() {
                spyOn( manager, 'selectObject' );

                oneEditLink.addClass('is-disabled');
                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( manager.selectObject ).not.toHaveBeenCalled();
            });

            it('calls EditForm\'s closeEditor function if object is clicked twice', function() {

                spyOn( editForm, 'closeEditor' ).and.callThrough();

                selector._editActionInitiated.call( $(oneEditLink), { data: selector });
                selector._editActionInitiated.call( $(oneEditLink), { data: selector });

                expect( editForm.closeEditor ).toHaveBeenCalled();
            });
        });

        describe('overrides lockControls', function() {
            beforeEach(function() {
                loadBasicMocks();
                selector.canDelete = true;
                spyOn( Selector.prototype, 'lockControls' ).and.callThrough();
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                manager.initialize($container);
                manager.populateSelectionsByArray([
                    {
                        id: 3,
                        name: 'Rawr'
                    },
                    {
                        id: 4,
                        name: 'Bob'
                    }
                ]);
                selector.lockControls();
            });

            it('calls parent w/ it as context', function() {
                expect( Selector.prototype.lockControls ).toHaveBeenCalled();
                expect( Selector.prototype.lockControls.calls.mostRecent().object ).toBe( selector );
            });

            it('adds is-disabled class to all edit buttons', function() {
                expect( selector.$selector.find( selector.editButtonClassname ).eq(0) ).toHaveClass('is-disabled');
                expect( selector.$selector.find( selector.editButtonClassname ).eq(1) ).toHaveClass('is-disabled');
            });

            it('adds is-disabled class to all delete buttons', function() {
                expect( selector.$selector.find( selector.deleteButtonClassname ).eq(0) ).toHaveClass('is-disabled');
                expect( selector.$selector.find( selector.deleteButtonClassname ).eq(1) ).toHaveClass('is-disabled');
            });
        });

        describe('overrides unLockControls', function() {
            beforeEach(function() {
                loadBasicMocks();
                selector.canDelete = true;
                spyOn( Selector.prototype, 'unLockControls' ).and.callThrough();
                spyOn(editForm, 'populateForm');
                spyOn(editForm, 'placeFocusOnStartingField');
                manager.initialize($container);
                manager.populateSelectionsByArray([
                    {
                        id: 3,
                        name: 'Rawr'
                    },
                    {
                        id: 4,
                        name: 'Bob'
                    }
                ]);
                selector.lockControls();
                selector.unLockControls();
            });

            it('calls parent w/ it as context', function() {
                expect( Selector.prototype.unLockControls ).toHaveBeenCalled();
                expect( Selector.prototype.unLockControls.calls.mostRecent().object ).toBe( selector );
            });

            it('removes is-disabled class from all edit buttons', function() {
                expect( selector.$selector.find( selector.editButtonClassname ).eq(0) ).not.toHaveClass('is-disabled');
                expect( selector.$selector.find( selector.editButtonClassname ).eq(1) ).not.toHaveClass('is-disabled');
            });

            it('removes is-disabled class from all delete buttons', function() {
                expect( selector.$selector.find( selector.deleteButtonClassname ).eq(0) ).not.toHaveClass('is-disabled');
                expect( selector.$selector.find( selector.deleteButtonClassname ).eq(1) ).not.toHaveClass('is-disabled');
            });

        });

        describe('delete button clicked event', function() {

            var $deleteButton;
            beforeEach(function() {
                loadBasicMocks();
                selector.setupEvents.and.callThrough(); // Alter spy to call through
                selector.removeEvents.and.callThrough(); // Alter spy to call through
                selector.canDelete = true;
                manager.initialize($container);
                manager.populateSelectionsByArray(
                    {
                        id: 3,
                        name: 'Fake'
                    }
                );
                $deleteButton = selector.$selector.find(selector.deleteButtonClassname ).eq(0);
                spyOn(manager.dialog, 'showDialog');
            });

            it('gets set in setupEvents', function() {
                expect( selector.$selector ).toHandle('click.deleteButton');
                expect( selector.$selector ).toHandleWith( 'click.deleteButton', selector._deleteActionInitiated );
            });

            it('gets cleared in removeEvents', function() {
                expect( selector.$selector ).toHandle('click.deleteButton');
                manager.remove();
                expect( selector.$selector ).not.toHandle('click.deleteButton');
            });

            it('displays a confirmation dialog', function() {
                selector._getTitleFrom$Row = function($row) {
                    return $row.text();
                };

                $deleteButton.trigger('click');
                expect( manager.dialog.showDialog ).toHaveBeenCalled();

                // Verify body
                expect( manager.dialog.showDialog.calls.mostRecent().args[0] ).toContain("Are you sure you want to delete");
                expect( manager.dialog.showDialog.calls.mostRecent().args[0] ).toContain("3");
                expect( manager.dialog.showDialog.calls.mostRecent().args[0] ).toContain("Fake");

                // Verify title
                expect( manager.dialog.showDialog.calls.mostRecent().args[1] ).toContain("Delete object");

                // Verify alert status
                expect( manager.dialog.showDialog.calls.mostRecent().args[2] ).toBeTruthy();
            });

            it('does not call deleteObject if confirmation dialosg is cancelled', function() {
                spyOn(manager.dialog, 'closeDialog');
                spyOn(manager, 'deleteObject');
                $deleteButton.trigger('click');

                var cancelCallback = manager.dialog.showDialog.calls.mostRecent().args[3][0]['click'];
                cancelCallback.call(manager.dialog);
                expect( manager.deleteObject ).not.toHaveBeenCalled();
                expect( manager.dialog.closeDialog ).toHaveBeenCalled();
            });

            it('calls deleteObject if confirmation dialog is confirmed', function() {
                spyOn(manager, 'deleteObject');
                spyOn(manager.dialog, 'closeDialog');
                $deleteButton.trigger('click');

                var deleteCallback = manager.dialog.showDialog.calls.mostRecent().args[3][1]['click'];
                deleteCallback.call(manager.dialog);
                expect( manager.deleteObject ).toHaveBeenCalled();
                expect( manager.dialog.closeDialog ).toHaveBeenCalled();
            });
        });


        // left off on _convertToDataTablesReadyObject



    });


});
