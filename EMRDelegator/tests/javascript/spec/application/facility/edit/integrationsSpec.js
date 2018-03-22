/**
 * Add/Edit Facility's Integration Field Tests
 *
 * @tests js/application/facility/edit/integrations.js
 *
 */
describe('nsIntegrations', function() {

    var $container,
        sut,
        placeholderSpy,
        $inputs,
        $bfmType,
        $externalId,
        $timeToLiveSeconds,
        $effectiveDateTime,
        invalidInputs,
        values;

    function getValues()
    {
        return wpt.nsAjax.packageFormArray($inputs.serializeArray());
    }
    
    function expectAllFieldsToBeEnabled()
    {
        // Serialize inputs.
        values = getValues();
        
        // Make sure all fields do not have the `disabled` attribute.
        expect($container.find(':input')).not.toBeDisabled();
        
        // All fields should be present.
        expect('bfmExternalId' in values).toBeTruthy();
        expect('bfmApiKey' in values).toBeTruthy();
        expect('bfmEffectiveDateTime' in values).toBeTruthy();
        expect('bfmGroupName' in values).toBeTruthy();
        expect('bfmPassword' in values).toBeTruthy();
        expect('bfmTerminateDateTime' in values).toBeTruthy();
        expect('bfmTimeToLiveSeconds' in values).toBeTruthy();
        expect('bfmType' in values).toBeTruthy();
        expect('bfmUserName' in values).toBeTruthy();
    }
    
    function expectAllFieldsToBeDisabledExceptExternalId()
    {
        // Serialize inputs.
        values = getValues();
        
        // external-id should not have the `disabled` attribute.
        expect($externalId).not.toBeDisabled();

        // external-id should be present.
        expect('bfmExternalId' in values).toBeTruthy();
        
        // Every other field should have the `disabled` attribute.
        expect($container.find(':input:not([name="bfmExternalId"])')).toBeDisabled();
        
        // Every other field should be excluded.
        expect('bfmApiKey' in values).toBeFalsy();
        expect('bfmEffectiveDateTime' in values).toBeFalsy();
        expect('bfmGroupName' in values).toBeFalsy();
        expect('bfmPassword' in values).toBeFalsy();
        expect('bfmTerminateDateTime' in values).toBeFalsy();
        expect('bfmTimeToLiveSeconds' in values).toBeFalsy();
        expect('bfmType' in values).toBeFalsy();
        expect('bfmUserName' in values).toBeFalsy();
    }

    /**
     * 
     * @param $elements
     */
    function validateElements($elements)
    {
        return wpt.nsValidation.validateElements($elements);
    }

    /**
     * 
     * @param $element
     * @returns {*}
     */
    function validateElement($element)
    {
        return validateElements($([$element]));
    }

    /**
     * 
     * @param $element
     * @returns {boolean}
     */
    function isElementValid($element)
    {
        invalidInputs = validateElement($element);

        // Assert validation failed.
        return invalidInputs.length < 1;
    }

    describe('when BFM fields are not present', function() {
        
        beforeEach(function() {
            
            // Create fixture DOM.
            $container = $('<div>');
            
            // Create subject under test.
            sut = new wpt.nsIntegrations.InteroperabilityFields($container);

            // Create placeholder spy. Placeholder plugin is a poly-fill for IE9 placeholder text.
            placeholderSpy = jasmine.createSpy('placeholder');
            $.fn.extend({
                placeholder: placeholderSpy
            });
        });
        
        describe('enabling BFM fields', function() {

            beforeEach(function() {

                // Enable the module.
                sut.toggleAllFieldsByModuleState([wpt.nsModules.bfm.id]);
            });
            
            it('does nothing', function () {
                
                // Should not create new DOM elements.
                expect($container).toBeEmpty();
            });
        });

        describe('disabling BFM fields', function() {

            beforeEach(function() {

                // Disable the module.
                sut.toggleAllFieldsByModuleState([]);
            });
            
            it('does nothing', function () {
                
                // Should not create new DOM elements.
                expect($container).toBeEmpty();
            });
        });
    });
    
    describe('when BFM fields are present', function() {

        beforeEach(function() {
            
            // Load fixture DOM.
            jasmine.getFixtures().fixturesPath = '../../module/Application/view/application/facility/edit';
            loadFixtures('bfm-section.html');
            $container = $('#bfm-section');
            $inputs = $container.find(':input');
            $bfmType = $container.find(':input[name="bfmType"]');
            $externalId = $container.find(':input[name="bfmExternalId"]');
            $timeToLiveSeconds = $container.find(':input[name="bfmTimeToLiveSeconds"]');
            $effectiveDateTime = $container.find(':input[name="bfmEffectiveDateTime"]');
            
            // Create subject under test.
            sut = new wpt.nsIntegrations.InteroperabilityFields($container);

            // Create placeholder spy. Placeholder plugin is a poly-fill for IE9 placeholder text.
            placeholderSpy = jasmine.createSpy('placeholder');
            $.fn.extend({
                placeholder: placeholderSpy
            });
        });
        
        describe('enabling BFM fields', function() {

            beforeEach(function() {

                // Set BFM type.
                $bfmType.append(
                    $('<option value="2" selected>SomePartner</option>')
                );
                
                // Enable the module.
                sut.toggleAllFieldsByModuleState([wpt.nsModules.bfm.id]);
            });
            
            it('enables all fields', function() {

                expectAllFieldsToBeEnabled();
            });

            it('applies validation to external_id', function() {

                $externalId.val('').change();

                expect(isElementValid($externalId)).toBeFalsy();
            });

            it('applies validation to time_to_live', function() {

                $timeToLiveSeconds.val('').change();

                expect(isElementValid($timeToLiveSeconds)).toBeFalsy();
            });

            it('applies validation to effective_datetime', function() {

                $effectiveDateTime.val('').change();

                expect(isElementValid($effectiveDateTime)).toBeFalsy();
            });
        });

        describe('disabling BFM fields', function() {

            beforeEach(function() {

                // Set BFM type.
                $bfmType.append(
                    $('<option value="2" selected>SomePartner</option>')
                );

                // Disable the module.
                sut.toggleAllFieldsByModuleState([]);
            });
            
            it('disables all fields except external-id', function() {

                expectAllFieldsToBeDisabledExceptExternalId();
            });
    
            it('removes validation from external_id', function() {

                $externalId.val('').change();
                
                expect(isElementValid($externalId)).toBeTruthy();
            });
    
            it('removes validation from time_to_live', function() {

                $timeToLiveSeconds.val('').change();
                
                expect(isElementValid($timeToLiveSeconds)).toBeTruthy();
            });
    
            it('removes validation to effective_datetime', function() {

                $effectiveDateTime.val('').change();
                
                expect(isElementValid($effectiveDateTime)).toBeTruthy();
            });
        });

        it('all BFM fields except for external-id are disabled', function() {
            
            expectAllFieldsToBeDisabledExceptExternalId();
        });
        
        describe('disabling BFM module', function() {
            
            beforeEach(function() {

                // Set BFM type.
                $bfmType.append(
                    $('<option value="2" selected>SomePartner</option>')
                );

                // Disable the module.
                sut.toggleAllFieldsByModuleState([]);
            });
            
            //GIVEN i am an implementation Analyst
            //WHEN i am adding a new clinic without BFM module
            //THEN all fields in BFM section should be disabled except external id
            //AND all fields are not required
            it('disables all BFM fields except for external-id', function() {
                
                expectAllFieldsToBeDisabledExceptExternalId();
            });

            //GIVEN i am an implementation Analyst
            //AND i am adding a new clinic without BFM module
            //WHEN i do not enter external id
            //THEN I should be able to add a clinic
            it('does not require external-id by default', function() {
                
                expect(isElementValid($externalId)).toBeTruthy();
            });
        });
        
        describe('enabling BFM fields', function() {
    
            beforeEach(function() {

                // Set BFM type.
                $bfmType.append(
                    $('<option value="2" selected>SomePartner</option>')
                );

                // Enable the module.
                sut.toggleAllFieldsByModuleState([wpt.nsModules.bfm.id]);
            });
    
            //GIVEN i am an implementation Analyst
            //WHEN i am adding a new clinic with BFM module
            //THEN all fields in BFM section should be enabled
            it('enables all BFM fields', function() {
    
                expectAllFieldsToBeEnabled();
            });
        });
    });
});
