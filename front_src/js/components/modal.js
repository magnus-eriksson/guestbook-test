app.onload(function () {
    app.addEvent('#modal', 'click', function (e) {
        if (this !== e.target) {
            return;
        }

        modal.close();
    }, false);
});

var modal = {
    newEntry: function () {
        this.loadContent('/entry/new/form');
    },

    replyEntry: function (id) {
        this.loadContent('/entry/reply/form', 'id=' + id);
    },

    editEntry: function (id) {
        this.loadContent('/entry/edit/form', 'id=' + id);
    },

    loadContent: function (url, data) {
        var _this = this;
        app.addClass('#modal', 'show');

        app.addEvent('body', 'keydown', this.onEscape);

        app.ajax(url, 'GET', data || '').then(function (r) {
            if (!r) {
                _this.close();
                return;
            }

            app.prepend('#modal-content', r);

            app.addEvent('#entry-form', 'submit', function (e) {
                e.preventDefault();
                app.disable('#entry-form-btn');
                errors.clear('#entry-error');

                app.ajaxForm(this).then(function (r) {
                    if (r.success) {
                        location.reload();
                        return;
                    }

                    app.enable('#entry-form-btn');
                    errors.show(r.error || 'An unknown error occurred', '#entry-error');
                }, function (error) {
                    app.enable('#entry-form-btn');
                });
            });

        });
    },

    onEscape: function (e) {
        if (e.key == 'Escape') {
            modal.close();
        }
    },

    close: function () {
        app.removeClass('#modal', 'show');
        app.qs('#modal-content', true).innerHTML = '';
        app.removeEvent('body', 'keydown', this.onEscape);
    }
};