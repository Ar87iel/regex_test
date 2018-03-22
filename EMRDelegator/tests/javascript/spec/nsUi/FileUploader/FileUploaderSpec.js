/**
 * Clinic Attacher Table Component Tests
 *
 * @tests js/nsUi/FileUploader/FileUploader.js
 *
 */

describe('FileUploader Component', function() {

    var
    // Namespaces
        nsUi = wpt.nsUi,
        FileUploader = nsUi.FileUploader,
        fileUpload = null,
        $fileInput = null;

    beforeEach(function() {
        $fileInput = $('<input type="file">');
    });

    afterEach(function() {
        fileUpload = null;
    });

    describe('Constructor', function() {

        it('should be instantiable and an instance of FileUploader', function() {

            expect(fileUpload).toBeNull();

            fileUpload = new FileUploader($fileInput);

            expect(fileUpload).toBeDefined();

            expect(fileUpload instanceof wpt.nsUi.FileUploader).toBeTruthy();

            expect(fileUpload instanceof FileUploader).toBeTruthy();

        });


        describe('should call setInput', function() {

            it('if inputs were provided', function() {

                // Set spy
                spyOn( wpt.nsUi.FileUploader.prototype, 'setInput' ).and.callThrough();

                fileUpload = new FileUploader($fileInput);
                expect(wpt.nsUi.FileUploader.prototype.setInput).toHaveBeenCalled();

            });

            it('unless inputs were not provided', function() {

                // Set spy
                spyOn(wpt.nsUi.FileUploader.prototype, 'setInput').and.callThrough();

                fileUpload = new FileUploader();
                expect(wpt.nsUi.FileUploader.prototype.setInput).not.toHaveBeenCalled();

            });

        });

    });

    describe('getInput (and setInput)', function() {

        it('getInput should return null when not set', function() {

            expect(fileUpload).toBeNull();

            fileUpload = new FileUploader();

            expect(fileUpload.getInput()).toBeNull();


        });

        it('getInput should return $input after using setInput', function() {

            expect(fileUpload).toBeNull();

            fileUpload = new FileUploader();

            expect(fileUpload.getInput()).toBeNull();

            fileUpload.setInput($fileInput);

            expect(fileUpload.getInput()).toBe($fileInput);

        });

    });

    describe('_appendParamsToForm', function() {

        it('_appendParamsToForm should append hidden inputs to a form, based on object', function() {

            expect(fileUpload).toBeNull();

            fileUpload = new FileUploader();

            // New blank form
            var $form = $('<form>');
            // New param Object
            var params = {'userName': 'bob', 'userId': 144};

            fileUpload._appendParamsToForm(params, $form);

            //Get the first input of form
            var $firstInput = $form.find('input')[0];

            expect($firstInput.name).toEqual('userName');
            expect($firstInput.value).toEqual('bob');
            expect($firstInput.type).toEqual('hidden');

            //Get the second input of form
            var $secondInput = $form.find('input')[1];

            expect($secondInput.name).toEqual('userId');
            expect($secondInput.value).toEqual('144');
            expect($secondInput.type).toEqual('hidden');

        });


        it('_appendParamsToForm should fail if params is not an object', function() {

            expect(fileUpload).toBeNull();

            fileUpload = new FileUploader();

            // New blank form
            var $form = $('<form>');
            // New param Object
            var params = 'hey';

            function doWork() {
                fileUpload._appendParamsToForm(params, $form)
            }

            expect(doWork).toThrowError();

        });

    });

    describe('postFile(...)', function() {

        it('should fail if no input was ever provided', function() {

            fileUpload = new FileUploader();

            function doWork() {
                fileUpload.postFile('somewhere', function() {}, {});
            }

            expect( doWork ).toThrowError('Attempted to post file with no input element');

        });

        // Jasmine: Feel free to change these, just sketching them out
        it('should always call its finished callback', function() {

        });

        it('should clean up any created elements when finished', function() {

        });

    });

});

