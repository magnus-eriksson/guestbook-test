app.onload(function () {
    // Login
    app.addEvent('#login-form', 'submit', function (e) {
        e.preventDefault();
        app.disable('#login-form-btn');
        errors.clear();

        app.ajaxForm(this).then(function (r) {
            app.enable('#login-form-btn');

            if (r.success) {
                location.href = "/";
            } else {
                errors.show(r.error || 'An unknown error occurred');
            }
        }, function (error) {
            app.enable('#login-form-btn');
        });
    });

    // Register
    app.addEvent('#register-form', 'submit', function (e) {
        e.preventDefault();
        app.disable('#register-form-btn');
        errors.clear();

        app.ajaxForm(this).then(function (r) {
            app.enable('#register-form-btn');

            if (r.success) {
                location.href = "/";
            } else {
                errors.show(r.error || 'An unknown error occurred');
            }
        }, function (error) {
            app.enable('#register-form-btn');
        });
    });
});

