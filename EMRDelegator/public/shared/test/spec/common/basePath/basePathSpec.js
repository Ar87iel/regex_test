describe('nsCommon', function() {

    describe('wpt.basePath()', function() {

        var basePath = wpt.nsCommon.basePath,
            global = (function(){ return this; })(),

            // Hold global to prevent stomping it
            existingBasePath;

        beforeEach(function() {
            // Cache global
            existingBasePath = wpt.onload.basePath;
        });

        afterEach(function() {
            // Restore global
            wpt.onload.basePath = existingBasePath;
        });

        it('uses wpt.onload.basePath', function() {

            wpt.onload.basePath = '/s/something';

            var url = basePath('asset.bmp');

            expect(url).toEqual( '/s/something/asset.bmp' );

        });

        it('falls back to empty string basePath', function() {

            delete wpt.onload.basePath;

            var url = basePath('asset.bmp');

            expect(url).toEqual( 'asset.bmp' );

        });

    });

});