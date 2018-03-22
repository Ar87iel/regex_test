wpt.nsUi.PasswordStrengthValidator = function (passwordConfiguration) {
    this.getStrengthLengthScore = function (password) {
        return 6;
    }
};
/**
 * Describe tests for PasswordValidator.js javascript file.
 *
 * @tests js/application/user/PasswordValidator/PasswordValidator.js
 */
describe('Test suite for Password Validator', function () {

    it('Verifies if an Error exception is throw if a undefined password is provide', testThrowAnException);
    it('Verifies if update the indicator message for password provide', testUpdateIndicator);

    /**
     * Set all variables required to test PasswordValidator.js
     */
    beforeEach(function () {
        wpt.page.vars.minPasswordLengthValue = 6;
    });

    /**
     * Clean all variables set in the testing process.
     */
    afterEach(function () {
        wpt.nsUi.PasswordStrengthValidator = undefined;
    });

    /**
     * Verifies if an Error exception is throw if a undefined password is provide.
     */
    function testThrowAnException() {
        var updatingIndicator = function () {
            var password;
            wpt.user.passwordValidator.passwordStrengthValidator(password, 'Message', {})
        };
        expect(updatingIndicator).toThrowError();
    }

    /**
     * Verifies if update the indicator message for password provide.
     */
    function testUpdateIndicator() {
        wpt.page.addEditUserForm = {
            passwordStrengthIndicator: {
                updateIndicator: function () {
                }
            }
        };
        spyOn(wpt.page.addEditUserForm.passwordStrengthIndicator, 'updateIndicator');
        wpt.user.passwordValidator.passwordStrengthValidator('Password', 'Message', wpt.page.addEditUserForm);

        expect(wpt.page.addEditUserForm.passwordStrengthIndicator.updateIndicator).toHaveBeenCalled();
    }
});
