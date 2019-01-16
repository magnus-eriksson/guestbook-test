app.onload(function () {
    // Add new entry
    app.addEvent('#show-add-new-entry', 'click', function (e) {
        e.preventDefault();

        modal.newEntry();
    });

    // Add a reply
    app.addEvent('.show-reply-entry', 'click', function (e) {
        e.preventDefault();

        modal.replyEntry(this.getAttribute('data-id'));
    });

    // Edit an entry
    app.addEvent('.show-edit-entry', 'click', function (e) {
        e.preventDefault();

        modal.editEntry(this.getAttribute('data-id'));
    });

    // Delete entry
    app.addEvent('.delete-entry-btn', 'click', function (e) {
        e.preventDefault();

        var id    = this.getAttribute("data-id");
        var token = this.getAttribute("data-token");
        var data  = 'id=' + id + '&token=' + token;

        app.ajax('/entry/delete', 'POST', data).then(function (r) {
            if (r.success) {
                location.reload();
                return;
            }

            alert(r.error || 'An unknown error occurred');
        }, function (error) {
            app.enable('#new-entry-form-btn');
        });
    });
});
