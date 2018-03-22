/**
 * Manager's Selector Tests
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
        $selector = $container.find('.selector--selector');

        // Neutralize "Must be overridden" errors
        spyOn(selector, 'setupEvents');
        spyOn(selector, 'selectById');
        spyOn(selector, 'clearSelectionList');
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

    describe('Selector', function() {

        it('is a Selector when instantiated', function() {
            selector = new Selector();
            expect( selector instanceof wpt.page.Selector ).toBeTruthy();
        });

        describe('when initialized', function() {

            beforeEach(function() {
                loadBasicMocks();
                spyOn(selector, "linkToElements");
                selector.selectedId = 19287; // to test it gets nulled
                manager.initialize( $container );
            });

            it('calls linkToElements', function() {
                expect( selector.linkToElements ).toHaveBeenCalledWith( $container.find( manager.selectorContainerClassname ) );
            });

            it('calls setupEvents', function() {
                expect( selector.setupEvents ).toHaveBeenCalled();
            });

            it('sets selectedId to null', function() {
                expect( selector.selectedId ).toBeNull();
            });

            it('records manager reference', function() {
                expect( selector.manager ).toBe( manager );
            });

            it('throws if manager reference not given', function() {
                function testIt() {
                    selector.initialize();
                }

                expect( testIt ).toThrowError();
            });

        });

        describe('when removed', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
                spyOn( selector, 'unlinkElements');
                spyOn( selector, 'removeEvents');
            });

            it('calls unlinkElements', function() {
                expect( selector.unlinkElements ).not.toHaveBeenCalled();
                manager.remove();
                expect( selector.unlinkElements ).toHaveBeenCalled();
            });

            it('calls removeEvents', function() {
                expect( selector.removeEvents ).not.toHaveBeenCalled();
                manager.remove();
                expect( selector.removeEvents ).toHaveBeenCalled();
            });

            it('calls clearSelectionList', function() {
                expect( selector.clearSelectionList ).not.toHaveBeenCalled();
                manager.remove();
                expect( selector.clearSelectionList ).toHaveBeenCalled();
            });
        });

        describe('links to elements', function() {
            it('via linkToElements function', function() {
                loadBasicMocksAndInit();
                expect( $.isFunction( selector.linkToElements ) ).toBeTruthy();
            });

            it('where it stores the given container', function() {
                loadBasicMocksAndInit();

                var $myEl = $container.find( manager.selectorContainerClassname );
                selector = new Selector();
                selector.selectorLatch = jasmine.createSpyObj('selectorLatch', ['trackElement']);
                selector.linkToElements( $myEl );
                expect( selector.$container ).toEqual( $myEl );
            });

            it('where it finds a $selector inside the given container based on its selectorClassname', function() {
                loadBasicMocksAndInit();

                var $myEl = $container.find( manager.selectorContainerClassname );
                selector = new Selector();
                selector.selectorLatch = jasmine.createSpyObj('selectorLatch', ['trackElement']);
                selector.linkToElements( $myEl );
                expect( selector.$selector ).toEqual( $myEl.find( selector.selectorClassname ) );
            });
        });

        it('clears element references when unlinked', function() {
            loadBasicMocksAndInit();

            expect( selector.$container ).not.toBeNull();
            expect( selector.$selector ).not.toBeNull();

            selector.unlinkElements();

            expect( selector.$container ).toBeNull();
            expect( selector.$selector ).toBeNull();
        });

        describe('matches Manager\'s requirement', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
            });

            it('of having a setupEvents function', function() {
                expect( $.isFunction( selector.setupEvents ) ).toBeTruthy();
            });

            it('of having a removeEvents function', function() {
                expect( $.isFunction( selector.removeEvents ) ).toBeTruthy();
            });

            it('of having a lockControls function', function() {
                expect( $.isFunction( selector.lockControls ) ).toBeTruthy();
            });

            it('of having an unLockControls function', function() {
                expect( $.isFunction( selector.unLockControls ) ).toBeTruthy();
            });

            it('of having an addObjectToSelectionList function', function() {
                expect( $.isFunction( selector.addObjectToSelectionList ) ).toBeTruthy();
            });
            it('of having an addObjectsToSelectionList function', function() {
                expect( $.isFunction( selector.addObjectsToSelectionList ) ).toBeTruthy();
            });
            it('of having an clearSelectionList function', function() {
                expect( $.isFunction( selector.clearSelectionList ) ).toBeTruthy();
            });
            it('of having an populateSelectionList function', function() {
                expect( $.isFunction( selector.populateSelectionList ) ).toBeTruthy();
            });
            it('of having an clearSelection function', function() {
                expect( $.isFunction( selector.clearSelection ) ).toBeTruthy();
            });
            it('of having an selectById function', function() {
                expect( $.isFunction( selector.selectById ) ).toBeTruthy();
            });
            it('of having an placeFocusOnEditActionOfGivenId function', function() {
                expect( $.isFunction( selector.placeFocusOnEditActionOfGivenId ) ).toBeTruthy();
            });
        });

        describe('when locked', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
            });

            it('disables $selector', function() {
                expect( $selector ).not.toBeDisabled();
                selector.lockControls();
                expect( $selector ).toBeDisabled();
            });

            it('adds is-disabled class to $selector', function() {
                expect( $selector ).not.toHaveClass('is-disabled');
                selector.lockControls();
                expect( $selector ).toHaveClass('is-disabled');
            });
        });

        describe('when unlocked', function() {

            beforeEach(function() {
                loadBasicMocksAndInit();
            });

            it('enables $selector', function() {
                expect( $selector ).not.toBeDisabled();
                selector.lockControls();
                expect( $selector ).toBeDisabled();
                selector.unLockControls();
                expect( $selector ).not.toBeDisabled();
            });

            it('removes is-disabled class from $selector', function() {
                expect( $selector ).not.toHaveClass('is-disabled');
                selector.lockControls();
                expect( $selector ).toHaveClass('is-disabled');
                selector.unLockControls();
                expect( $selector ).not.toHaveClass('is-disabled');
            });
        });

        it('calls addObjectToSelectionList for each object given to addObjectsToSelectionList', function() {
            var arrObjs = [ 1, 2, 3 ];

            loadBasicMocks();
            spyOn( selector, "addObjectToSelectionList");
            selector.addObjectsToSelectionList( arrObjs );

            expect( selector.addObjectToSelectionList ).toHaveBeenCalledWith(1);
            expect( selector.addObjectToSelectionList ).toHaveBeenCalledWith(2);
            expect( selector.addObjectToSelectionList ).toHaveBeenCalledWith(3);
            expect( selector.addObjectToSelectionList ).not.toHaveBeenCalledWith(4);
            expect( selector.addObjectToSelectionList ).not.toHaveBeenCalledWith(1, 2, 3);
            expect( selector.addObjectToSelectionList ).not.toHaveBeenCalledWith([1, 2, 3]);
            expect( selector.addObjectToSelectionList ).not.toHaveBeenCalledWith([1]);
        });

        describe('populateSelectionList', function() {
            it('calls clearSelectionList', function() {
                var arrObjs = [ 1, 2, 3 ];

                loadBasicMocks();
                // clearSelectionList is mocked in loadBasicMocks
                spyOn( selector, "addObjectsToSelectionList"); // prevent not overridden error
                selector.populateSelectionList( arrObjs );

                expect( selector.clearSelectionList ).toHaveBeenCalled();
            });

            it('calls addObjectsToSelectionList with the given parameters', function() {
                var arrObjs = [ 1, 2, 3 ];

                loadBasicMocks();
                spyOn( selector, "addObjectsToSelectionList");
                selector.populateSelectionList( arrObjs );

                expect( selector.addObjectsToSelectionList ).toHaveBeenCalledWith( arrObjs );
            });

            it('throws if not given an object or array', function() {
                expect( function() {
                    var arrObjs = 1;

                    loadBasicMocks();
                    spyOn( selector, "addObjectsToSelectionList");
                    selector.populateSelectionList( arrObjs );

                    expect( selector.addObjectsToSelectionList ).toHaveBeenCalledWith( [ arrObjs ] );
                } ).toThrowError();
            });

            it('converts an object into an array', function() {
                var arrObjs = {a:1};

                loadBasicMocks();
                spyOn( selector, "addObjectsToSelectionList");
                selector.populateSelectionList( arrObjs );

                expect( selector.addObjectsToSelectionList ).toHaveBeenCalledWith( [ arrObjs ] );
            });
        });

    });

});
