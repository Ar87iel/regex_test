/**
 * Add/Edit Facility's Outbound Documentation Component Tests
 *
 * @tests js/application/facility/edit/outboundDocumentation.js
 *
 */

describe('outboundDocumentation', function() {

    var $container,
        sut,
        getSchemaSpy,
        getConfigSpy,
        showDialogSpy,
        closeDialogSpy,
        persistence,
        saveButton,
        $content,
        integrationId = 'testIntegrationId',
        alpacaFormSpy,
        updateConfigSpy,
        dialog;

    beforeEach(function () {

        wpt.namespace('wpt.page.vars.userIdentity.privileges');

        wpt.page.vars.userIdentity.privileges = [
            'AS-Outbound-Documentation'
        ];

        var fakeDialog = function ($content, title, alert, buttons) {
            saveButton = buttons;

            alpacaFormSpy = jasmine.createSpyObj('alpacaForm', ['refreshValidationState', 'isValid', 'getValue']);
            alpacaFormSpy.refreshValidationState.and.callFake(function (bool, callback) {
                callback();
            });
            $content.alpaca = function (object) {
                if (object && object.postRender) {
                    object.postRender();
                }
                return alpacaFormSpy;
            }
        };

        // Create fixture DOM.
        $container = $('<div>');

        persistence = new wpt.nsIntegrations.outboundDocumentation.Persistence();
        dialog = new wpt.nsDialog.Dialog();

        getSchemaSpy = spyOn(persistence, 'getSchema').and.callFake(schemaSetter);
        getConfigSpy = spyOn(persistence, 'getConfig').and.callFake(configFaker);
        updateConfigSpy = spyOn(persistence, 'updateConfig');
        showDialogSpy = spyOn(dialog, 'showDialog').and.callFake(fakeDialog);
        closeDialogSpy = spyOn(dialog, 'closeDialog');

        // Create subject under test.
        sut = new wpt.nsIntegrations.outboundDocumentation.open(integrationId, 'Test', persistence, dialog);
    });


    describe('Opened Dialog', function () {

        it('calls showDialog', function () {
            expect(showDialogSpy).toHaveBeenCalled();
        });
    });

    describe('Custom regexps', function () {

        it('sets ipv6 regex', function () {
            expect($.alpaca.regexps['ipv6']).toBeTruthy();
        });

        it('sets hostname regex', function () {
            expect($.alpaca.regexps['hostname']).toBeTruthy();
        });
    });

    describe('Save', function () {
        it('updates config and closes dialog', function () {
            alpacaFormSpy.isValid.and.returnValue(true);
            var config = {documentTypes: ['DN']};
            alpacaFormSpy.getValue.and.returnValue(config);
            saveButton.click();
            expect(updateConfigSpy).toHaveBeenCalledWith(integrationId, config, dialog);
            expect(closeDialogSpy).toHaveBeenCalled();
        });

        it('alerts if config is empty', function () {
            alpacaFormSpy.isValid.and.returnValue(true);
            var config = {};
            alpacaFormSpy.getValue.and.returnValue(config);
            var alertSpy = spyOn(window, 'alert');
            saveButton.click();
            expect(alertSpy).toHaveBeenCalled();
            expect(updateConfigSpy).not.toHaveBeenCalled();
            expect(closeDialogSpy).toHaveBeenCalled();
        });

        it('does not close dialog if config is invalid', function () {
            alpacaFormSpy.isValid.and.returnValue(false);
            saveButton.click();
            expect(alpacaFormSpy.getValue).not.toHaveBeenCalled();
            expect(updateConfigSpy).not.toHaveBeenCalled();
            expect(closeDialogSpy).not.toHaveBeenCalled();
        });
    });
});


function schemaSetter(state) {
    state.schema = {
        "$schema": "http://json-schema.org/draft-04/schema#",
        "type": "object",
        "properties": {
            "document_types": {
                "title": "Document Types",
                "type": "array",
                "minItems": 1,
                "uniqueItems": true,
                "items": {
                    "type": "string",
                    "enum": [
                        "DN"
                    ]
                }
            }
        },
        "required": [
        ]
    };
    return $.Deferred().resolve().promise();
}

function configFaker(state) {
    return $.Deferred().resolve().promise();
}