var app = {
    /**
     * Add an onload event
     *
     * @param  function func
     */
    onload: function(func) {
        this.addEvent(window, 'load', func);
    },

    /**
     * Select elements using the query selector
     *
     * @param  string  selector
     * @param  boolean all
     * @return HTMLElement
     */
    qs: function(selector, single) {
        //instanceof HTMLElement
        if (typeof selector != 'string') {
            return selector;
        }

        return single
            ? document.querySelector(selector)
            : document.querySelectorAll(selector);
    },


    /**
     * Add content before another element
     *
     * @param  string selector
     * @param  mixed  content
     */
    prepend: function (selector, content) {
        var element = this.qs(selector);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.prepend(el, content);
            });

            return;
        }

        element.prepend(this.createDOM(content));
    },

    /**
     * Add content before another element
     *
     * @param  string selector
     * @param  mixed  content
     */
    before: function (selector, content) {
        var element = this.qs(selector);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.before(el, content);
            });

            return;
        }

        element.parentNode.insertBefore(this.createDOM(content), element);
    },

    /**
     * Add content after another element
     *
     * @param  string selector
     * @param  mixed  content
     */
    after: function (selector, content) {
        var element = this.qs(selector);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.after(el, content);
            });

            return;
        }
        element.parentNode.insertBefore(this.createDOM(content), element.nextSibling);
    },

    /**
     * Create a DOM object from a string
     *
     * @param  string string
     * @return DOMElement
     */
    createDOM: function (content) {
        if (typeof content !== 'string') {
            return content;
        }

        var doc = new DOMParser().parseFromString('<div>' + content + '</div>', "text/html");
        return doc.querySelector('div');
    },

    /**
     * Disable an element
     *
     * @param  string element
     */
    disable: function (element) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.disable(el);
            });

            return;
        }

        element.disabled = true;
    },

    /**
     * Enable an element
     *
     * @param  string element
     */
    enable: function (element) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.enable(el);
            });

            return;
        }

        element.disabled = false;
    },

    /**
     * Check if an element is a NodeList or a HTMLCollection
     *
     * @param  NodeList|HTMLCollection|HTMLElement  element
     * @return booklean
     */
    ifList: function(element, func) {
        return element instanceof NodeList || element instanceof HTMLCollection;
    },

    /**
     * Add event listener
     *
     * @param HTMLElement element
     * @param string      event
     * @param function    func
     */
    addEvent: function(element, event, func) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.addEvent(el, event, func);
            });

            return;
        }

        element.addEventListener(event, func, false);
    },

    /**
     * Remove event listener
     *
     * @param HTMLElement element
     * @param string      event
     * @param function    func
     */
    removeEvent: function(element, event, func) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.removeEvent(el, event, func);
            });

            return;
        }

        element.removeEventListener(event, func, false);
    },

    /**
     * Check if an element has a specific css class
     *
     * @param  HTMLElement element
     * @param  string      className
     * @return boolean
     */
    hasClass: function(element, className) {
        element = this.qs(element);

        if (element.classList) {
            return element.classList.contains(className);
        } else {
            return element.className && (-1 < element.className.indexOf(className));
        }
    },

    /**
     * Add a css class to an element
     *
     * @param  HTMLElement element
     * @param  string      className
     */
    addClass: function(element, className) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.addClass(el, className);
            });

            return;
        }

        if (element.classList) {
            element.classList.add(className);
        } else if (!this.hasClass(element, className)) {
            var classes = element.className.split(" ");
            classes.push(className);
            element.className = classes.join(" ");
        }
    },

    /**
     * Remove a css class from an element
     *
     * @param  HTMLElement element
     * @param  string      className
     */
    removeClass: function(element, className) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.removeClass(el, className);
            });

            return;
        }

        if (element.classList) {
            element.classList.remove(className);
        } else {
            var classes = element.className.split(" ");
            classes.splice(classes.indexOf(className), 1);
            element.className = classes.join(" ");
        }
    },

    /**
     * Toggle a css class on an element
     *
     * @param  HTMLElement element
     * @param  string      className
     */
    toggleClass: function (element, className) {
        element = this.qs(element);

        if (this.ifList(element)) {
            element.forEach(function (el) {
                app.toggleClass(el, className);
            });

            return;
        }

        if (this.hasClass(element, className)) {
            this.removeClass(element, className);
        } else {
            this.addClass(element, className);
        }
    },

    /**
     * Submit a form using ajax
     *
     * @param  string formId
     * @return Promise
     */
    ajaxForm: function(formId) {
        var $form  = this.qs(formId, true);
        var url    = $form.getAttribute("action");
        var method = $form.getAttribute("method").toUpperCase();
        var data   = this.serializeForm($form);

        return this.ajax(url, method, data);
    },

    /**
     * Make an ajax request
     *
     * @param  string url
     * @param  method
     * @param  data
     * @return Promise
     */
    ajax: function(url, method, data) {
        return new Promise(function(resolve, reject) {
            if (method == 'GET' && data) {
                url += '?' + data;
            }

            var postData = null;
            var req = new XMLHttpRequest();
            req.open(method, url);

            if (method == 'POST') {
                req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                postData = data;
            }

            req.onload = function() {
                if (req.status == 200) {
                    var response = req.response;

                    try {
                        response = JSON.parse(response);
                    } catch {}

                    resolve(response);
                } else {
                    reject(Error(req.statusText));
                }
            };

            req.onerror = function() {
                reject(Error("Network Error"));
            };

            req.send(data);
        });
    },

    /**
     * Serialize form data
     *
     * @param  HTMLElement form
     * @return string
     */
    serializeForm: function (form) {
        var serialized = [];

        for (var i = 0; i < form.elements.length; i++) {

            var field = form.elements[i];

            if (!field.name || field.disabled || field.type === 'file') {
                continue;
            }

            if (field.type === 'select-multiple') {
                for (var n = 0; n < field.options.length; n++) {
                    if (!field.options[n].selected) continue;
                    serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.options[n].value));
                }
            }

            else if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
                serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
            }
        }

        return serialized.join('&');
    }
};

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


