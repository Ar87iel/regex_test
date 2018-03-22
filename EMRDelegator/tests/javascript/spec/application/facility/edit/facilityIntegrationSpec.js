/**
 * Add/Edit Facility's Integration Field Tests
 *
 * @tests js/application/facility/edit/integrations.js
 */

wpt.namespace('wpt.page.vars.facility');
wpt.namespace('wpt.page.vars.userIdentity.privileges');

describe('FacilityIntegration', function() {

    var sut,
        $container,
        persistence,
        persistenceSpy = {},
        dialog,
        dialogSpy = {},
        $integrations,
        $newIntegrationButton,
        saveIntegrationButton,
        $integrationName,
        $integrationToken,
        fakeDialog = function ($content, title, alert, buttons) {
            saveIntegrationButton = buttons;
            $integrationName = $content.find('input.integration-name');
            $integrationToken = $content.find('input.integration-token');
        };

    function showDialog()
    {
        $newIntegrationButton.click();
    }
    
    beforeEach(function () {
        
        // Facility ID is required by Edit Facility components.
        wpt.page.vars.facility.id = 1234;
        
        // Enable admin privileges to interact with Integration components.
        wpt.page.vars.userIdentity.privileges = [
            'AS-Outbound-Documentation'
        ];

        // Load fixture DOM.
        jasmine.getFixtures().fixturesPath = '../../module/Application/view/application/facility/edit';
        loadFixtures('bfm-section.html');
        $container = $('#bfm-section');

        // Create SUT dependencies.
        persistence = new wpt.nsIntegrations.IntegrationPersistence({});
        dialog = new wpt.nsDialog.Dialog();

        // Create SUT-dependency spies.
        persistenceSpy.getFacilityIntegration = spyOn(persistence, 'getFacilityIntegration');
        persistenceSpy.getIntegrations = spyOn(persistence, 'getIntegrations');
        persistenceSpy.createIntegration = spyOn(persistence, 'createIntegration');
        persistenceSpy.createFacilityIntegration = spyOn(persistence, 'createFacilityIntegration');
        persistenceSpy.deleteFacilityIntegration = spyOn(persistence, 'deleteFacilityIntegration');
        dialogSpy.showDialog = spyOn(dialog, 'showDialog').and.callFake(fakeDialog);
        dialogSpy.closeDialog = spyOn(dialog, 'closeDialog');
    });

    function createSubjectUnderTest() {

        // Create subject under test.
        sut = new wpt.nsIntegrations.Integration($container, persistence, dialog);

        $integrations = $container.find('select#integrationSelection');
        $newIntegrationButton = $container.find('button#newIntegration');
    }
    
    describe('When I click the `New Integration` button', function () {
        beforeEach(function() {
            createSubjectUnderTest();
            showDialog();
        });
        
        it('Then the `New Integration` modal is displayed', function () {
            expect(dialog.showDialog).toHaveBeenCalled();
        });
    });
    
    describe(
        'Given the `New Integration` modal is displayed ' +
        'And I filled out the form ',
        function () {
            var integrationName = 'FooBar',
                integrationToken = 'FizzBuzz';
            
            beforeEach(function() {
                createSubjectUnderTest();
                showDialog();
                $integrationName.val(integrationName);
                $integrationToken.val(integrationToken);
            });
    
            describe('When I click `Save` on the `New Integration` modal', function () {
                beforeEach(function() {
                    saveIntegrationButton.click();
                });
                
                it('Then the `New Integration` modal is dismissed', function () {
                    expect(dialog.closeDialog).toHaveBeenCalled();
                });
                
                it('Then the `New Integration` button is disabled', function () {
                    expect($newIntegrationButton.prop('disabled')).toBeTruthy();
                });
                
                it('Then the new integration is created', function () {
                    expect(persistence.createIntegration).toHaveBeenCalled();
                });
            });

            describe('And I clicked `Save` on the `New Integration` modal', function () {
                var integrationId = 'de3aa0ca-c258-420f-a76f-984d9f79c411',
                    isIntegrationSelectionChanged = false,
                    facilityIntegrationId = '916748bd-f92f-4972-bca0-a6366fea0cff';

                beforeEach(function() {

                    // Call fake persistence method so that we can invoke callbacks with a mock response.
                    persistenceSpy.createIntegration.and.callFake(function (name, token, successCb, completeCb) {
                        successCb({
                            id: integrationId,
                            name: integrationName,
                            token: integrationToken
                        });
                        completeCb();
                    });

                    // Call fake persistence method so that we can invoke callbacks with a mock response.
                    persistenceSpy.createFacilityIntegration.and.callFake(
                        function (facilityId, integrationId, associationId, successCb, completeCb) {
                            successCb({
                                id: facilityIntegrationId
                            });
                            completeCb();
                        }
                    );

                    // Setup a signal.
                    sut.on.integrationSelectionChanged.add(function() {
                        isIntegrationSelectionChanged = true;
                    });

                    saveIntegrationButton.click();
                });

                describe('When the new integration is created', function () {
                    beforeEach(function() {
                        expect(persistence.createIntegration).toHaveBeenCalled();
                    });

                    it('Then the `New Integration` button is enabled', function () {
                        expect($newIntegrationButton.prop('disabled')).toBeFalsy();
                    });

                    it('Then the new integration is selected And a signal is dispatched', function () {
                        expect($integrations.val()).toEqual(integrationId);

                        expect(persistence.createFacilityIntegration).toHaveBeenCalled();

                        // This dispatched signal drives functionality of other components, e.g. Outbound Doc Config.
                        expect(isIntegrationSelectionChanged).toBeTruthy();
                    });

                    it('Then a success indicator is displayed', function () {
                        expect($container.find('#integration-selection-confirmation')).toBeVisible();
                    });
                });
            });
        }
    );
    
    describe('Given an integration is pre-selected', function () {
        var integrationId = 'de3aa0ca-c258-420f-a76f-984d9f79c411',
            integrationName = 'FooBar',
            integrationToken = 'FizzBuzz',
            facilityIntegrationId = '916748bd-f92f-4972-bca0-a6366fea0cff',
            isIntegrationSelectionChanged = false;

        beforeEach(function() {
            // Call fake persistence method so that we can invoke callbacks with a mock response.
            persistenceSpy.getFacilityIntegration.and.callFake(
                function (facilityId, successCb) {
                    successCb({
                        id: facilityIntegrationId,
                        facilityId: facilityId,
                        _embedded: {
                            integration: {
                                id: integrationId,
                                name: integrationName,
                                token: integrationToken
                            }
                        }
                    });
                }
            );
            
            // Call fake persistence method so that we can invoke callbacks with a mock response.
            persistenceSpy.getIntegrations.and.callFake(
                function (successCb) {
                    successCb([
                        {
                            id: integrationId,
                            name: integrationName,
                            token: integrationToken
                        }
                    ]);
                }
            );

            // Call fake persistence method so that we can invoke callbacks with a mock response.
            persistenceSpy.deleteFacilityIntegration.and.callFake(
                function (associationId, successCb, completeCb) {
                    successCb();
                    completeCb();
                }
            );

            // Call the shared setup, which will create new fixtures and SUT.
            createSubjectUnderTest();

            // Setup a signal.
            sut.on.integrationSelectionChanged.add(function() {
                isIntegrationSelectionChanged = true;
            });
        });
        
        it('Then integration options are not duplicated', function () {
            expect($integrations.find('option[value=' + integrationId + ']').size()).toEqual(1);
        });

        describe('When I select `None` from the Integration drop-down', function () {
            beforeEach(function() {
                spyOn(window, 'confirm').and.returnValue(true);

                $integrations.val('').change();
            });

            it('Then the integration is removed from the facility And a signal is dispatched', function () {
                expect(persistence.deleteFacilityIntegration).toHaveBeenCalled();

                // This dispatched signal drives functionality of other components, e.g. Outbound Doc Config.
                expect(isIntegrationSelectionChanged).toBeTruthy();
            });

            it('Then a success indicator is displayed', function () {
                expect($container.find('#integration-selection-confirmation')).toBeVisible();
            });
        });
    });
});
