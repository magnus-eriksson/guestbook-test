var errors = {
    /**
     * Show an error message
     *
     * @param  string message
     * @param  string id
     */
    show: function (message, id) {
        id = id || '#errors';
        app.qs(id, true).innerHTML = message;
    },

    /**
     * Clear error messages
     *
     * @param  string id
     */
    clear: function (id) {
        id = id || '#errors';
        app.qs(id, true).innerHTML = '';
    }
};
