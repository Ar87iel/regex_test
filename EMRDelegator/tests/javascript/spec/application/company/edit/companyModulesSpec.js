/**
 * Company modules initialization functionality tests
 *
 * @require ../../../../../../public/js/application/company/companyModules.js
 */

describe('Company Edition', function() {
    describe('Checks the company modules fieldset behaviour', function () {
        var companyModules;

        beforeEach(function() {
            companyModules = new wpt.nsAdmin.nsCompany.CompanyModules();

            jQuery('<div id="company-modules-fieldset">' +
                '<table id="company-modules-group"><tbody></tbody></table>' +
                '</div>').appendTo('body');
        });

        afterEach(function () {
            jQuery('#company-modules-fieldset').remove();
        });

        it('The company modules fieldset must not be displayed', function () {
            companyModules.initCompanyModules([]);

            expect(document.getElementById('company-modules-fieldset').style.display).toEqual('none');
        });

        it('A company module row must be created', function () {
            var module = [];
            module["name"] = 'Modulemon';
            module["description"] = 'Modulemon the virtual pet who was created to be tested only';

            companyModules.initCompanyModules([module]);
            expect(
                document.getElementById('company-modules-group').getElementsByTagName('tr').length
            ).toBeGreaterThan(0);
        });

        it('A company module row must be created even when it is checked as false', function () {
            var module = [];
            module["name"] = 'Modulemon';
            module["description"] = 'Modulemon the virtual pet who was created to be tested only';
            module["checked"] = 'false';

            companyModules.initCompanyModules([module]);
            expect(
                document.getElementById('company-modules-group').getElementsByTagName('tr').length
            ).toBeGreaterThan(0);
        });

        it('A company module row must be created even when it is checked as true', function () {
            var module = [];
            module["name"] = 'Modulemon';
            module["description"] = 'Modulemon the virtual pet who was created to be tested only';
            module["checked"] = 'true';

            companyModules.initCompanyModules([module]);
            expect(
                document.getElementById('company-modules-group').getElementsByTagName('tr').length
            ).toBeGreaterThan(0);
        });
    });

    describe('Verifies the company modules list is correctly generated', function () {
        var companyModules;

        beforeEach(function() {
            companyModules = new wpt.nsAdmin.nsCompany.CompanyModules();
        });

        afterEach(function () {
            companyModules = null;
        });

        it('No modules were selected', function () {
            var modulesList = companyModules.generateModulesList([]);

            expect(modulesList).toEqual([]);
        });

        it('At least one modules was checked', function () {
            var moduleIdValue = 'module-1';
            var modulesList = companyModules.generateModulesList([{'name' : moduleIdValue}]);

            expect(modulesList).toEqual([moduleIdValue.replace(/^\D+/g, '')]);
        });
    });
});
