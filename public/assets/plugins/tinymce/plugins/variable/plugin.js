/**
 * plugin.js
 *
 * Copyright, BuboBox
 * Released under MIT License.
 *
 * License: https://www.bubobox.com
 * Contributing: https://www.bubobox.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('variable', function (editor, pluginUrl) {

    tinymceVersion = tinymce.majorVersion;

    console.log(tinymceVersion);

    var configs = editor.getParam('variable_config', {});
    /**
     * Object that is used to replace the variable string to be used
     * in the HTML view
     * @type {object}
     */
    var mapper = {};
    if (typeof editor.settings.variable_mapper !== "undefined" && editor.settings.variable_mapper) {
        mapper = editor.settings.variable_mapper;
    } else if (typeof configs.mapper !== "undefined" && configs.mapper) {
        mapper = configs.mapper;
    }

    /**
     * define a list of variables that are allowed
     * if the variable is not in the list it will not be automatically converterd
     * by default no validation is done
     * @type {array}
     */
    var valid_variables = null;
    if (typeof editor.settings.variable_valid !== "undefined" && editor.settings.variable_valid) {
        valid_variables = editor.settings.variable_valid;
    } else if (typeof configs.valid_variables !== "undefined" && configs.valid_variables) {
        valid_variables = configs.valid_variables;
    }

    /**
     * Get custom variable class name
     * @type {string}
     */
    var className = '';
    if (typeof editor.settings.variable_class !== "undefined" && editor.settings.variable_class) {
        className = editor.settings.variable_class;
    } else if (typeof configs.class !== "undefined" && configs.class) {
        className = configs.class;
    }

    /**
     * Prefix to use to mark a variable
     * @type {string}
     */
    var prefix = '{';
    if (typeof editor.settings.variable_prefix !== "undefined" && editor.settings.variable_prefix) {
        prefix = editor.settings.variable_prefix;
    } else if (typeof configs.prefix !== "undefined" && configs.prefix) {
        prefix = configs.prefix;
    }

    /**
     * Suffix to use to mark a variable
     * @type {string}
     */
    var suffix = '}';
    if (typeof editor.settings.variable_suffix !== "undefined" && editor.settings.variable_suffix) {
        suffix = editor.settings.variable_suffix;
    } else if (typeof configs.suffix !== "undefined" && configs.suffix) {
        suffix = configs.suffix;
    }

    /**
     * RegExp is not stateless with '\g' so we return a new variable each call
     * @return {RegExp}
     */
    function getStringVariableRegex() {
        return new RegExp(prefix + '([a-zA-Z0-9._ \\u00C0-\\u017F]*)?' + suffix, "g");
    }

    //var stringVariableRegex = new RegExp(prefix + '([a-zA-Z0-9._]*)?' + suffix, "g");

    /**
     * check if a certain variable is valid
     * @param {string} name
     * @return {bool}
     */
    function isValid(name) {
        if (!valid_variables || valid_variables.length === 0) {
            return true;
        }
        var validString = '|' + valid_variables.join('|') + '|';
        return validString.indexOf('|' + name + '|') > -1 ? true : false;
    }

    function isMappedValue(cleanValue) {
        return mapper.hasOwnProperty(cleanValue) ? true : false;
    }

    function getMappedValue(cleanValue) {
        if (typeof mapper === 'function') {
            return mapper(cleanValue);
        }
        return mapper.hasOwnProperty(cleanValue) ? mapper[cleanValue] : cleanValue;
    }

    /**
     * Strip variable to keep the plain variable string
     * @example "{test}" => "test"
     * @param {string} value
     * @return {string}
     */
    function cleanVariable(value) {
        return value.replace(/[^a-zA-Z0-9._\s\u00C0-\u017F]/g, '');
    }

    /**
     * convert a text variable "x" to a span with the needed
     * attributes to style it with CSS
     * @param  {string} value
     * @return {string}
     */
    function createHTMLVariable(value) {

        var cleanValue = cleanVariable(value);

        // check if variable is valid
        if (!isValid(cleanValue)) {
            return value;
        }

        if (!isMappedValue(cleanValue)) {
            return value;
        }

        var cleanMappedValue = getMappedValue(cleanValue);

        editor.fire('variableToHTML', {
            value: value,
            cleanValue: cleanValue
        });

        var _class = 'variable';
        if (className && className !== 'variable') {
            _class += ' ' + className;
        }

        var variable = prefix + cleanValue + suffix;
        return '<span class="' + _class + '" data-original-variable="' + variable + '" contenteditable="false">' + cleanMappedValue + '</span>';
    }


    /**
     * convert variable strings into html elements
     * @return {void}
     */
    function stringToHTML() {
        var nodeList = [],
            nodeValue,
            node,
            div;

        // find nodes that contain a string variable
        tinymce.walk(editor.getBody(), function (n) {
            if (n.nodeType === 3 && n.nodeValue && getStringVariableRegex().test(n.nodeValue)) {
                if (containVars(n.nodeValue)) {
                    nodeList.push(n);
                }
            }
        }, 'childNodes');

        // loop over all nodes that contain a string variable
        for (var i = 0; i < nodeList.length; i++) {
            nodeValue = nodeList[i].nodeValue.replace(getStringVariableRegex(), createHTMLVariable);
            if (containVars(nodeValue)) {
                //div = editor.dom.create('div', null, nodeValue.replace(/&nbsp;/g, '').trim());
                div = editor.dom.create('div', null, nodeValue);
                while ((node = div.lastChild)) {
                    editor.dom.insertAfter(node, nodeList[i]);
                    if (isVariable(node)) {
                        var next = node.nextSibling;
                        editor.selection.setCursorLocation(node, node.endOffset + 1);
                        //editor.selection.setCursorLocation(next);
                    }
                }
                editor.dom.remove(nodeList[i]);
            }
        }
    }

    /**
     * convert HTML variables back into their original string format
     * for example when a user opens source view
     * @return {void}
     */
    function htmlToString() {
        var nodeList = [],
            nodeValue,
            node,
            div;

        // find nodes that contain a HTML variable
        tinymce.walk(editor.getBody(), function (n) {
            if (n.nodeType == 1) {
                var original = n.getAttribute('data-original-variable');
                if (original !== null) {
                    nodeList.push(n);
                }
            }
        }, 'childNodes');

        // loop over all nodes that contain a HTML variable
        for (var i = 0; i < nodeList.length; i++) {
            nodeValue = nodeList[i].getAttribute('data-original-variable');
            div = editor.dom.create('div', null, nodeValue);
            while ((node = div.lastChild)) {
                editor.dom.insertAfter(node, nodeList[i]);
            }

            // remove HTML variable node
            // because we now have an text representation of the variable
            editor.dom.remove(nodeList[i]);
        }
    }

    function containVars(str) {

        var vars = [];
        $.each(mapper, function (i, name) {
            vars.push(prefix + i + suffix);
        });

        for (var i = 0; i != vars.length; i++) {
            var substring = vars[i];
            if (str.indexOf(substring) != -1) {
                return substring;
            }
        }

        return null;
    }

    function removeNode(elm) {
        var nodeValue,
            node,
            div;
        nodeValue = elm.getAttribute('data-original-variable');
        div = editor.dom.create('div', null, nodeValue);
        while ((node = div.lastChild)) {
            editor.dom.insertAfter(node, elm);
        }

        // remove HTML variable node
        // because we now have an text representation of the variable
        editor.dom.remove(elm);
    }

    /**
     * handle formatting the content of the editor based on
     * the current format. For example if a user switches to source view and back
     * @param  {object} e
     * @return {void}
     */
    function handleContentRerender(e) {
        return e.format === 'raw' ? stringToHTML() : htmlToString();
    }

    /**
     * insert a variable into the editor at the current cursor location
     * @param {string} value
     * @return {void}
     */
    function addVariable(value) {
        var htmlVariable = createHTMLVariable(value);
        editor.selection.setContent(htmlVariable);
    }

    function isVariable(element) {
        if (typeof element.getAttribute === 'function' && element.hasAttribute('data-original-variable')) {
            return true;
        }

        return false;
    }

    /**
     * Trigger special event when user clicks on a variable
     * @return {void}
     */
    function handleClick(e) {
        var target = e.target;

        if (!isVariable(target)) {
            return null;
        }

        // put the cursor right after the variable
        if (e.target.nextSibling) {
            editor.selection.setCursorLocation(e.target.nextSibling, 0);
        } else {
            editor.selection.select(e.target);
            editor.selection.collapse();
        }

        // and trigger event if we want to do something special
        var value = target.getAttribute('data-original-variable');
        editor.fire('variableClick', {
            value: cleanVariable(value),
            target: target
        });
    }

    function preventDrag(e) {
        var target = e.target;

        if (!isVariable(target)) {
            return null;
        }

        e.preventDefault();
        e.stopImmediatePropagation();
    }

    function loadCss(editor, pluginUrl) {
        var linkElm = editor.dom.create('link', {
            rel: 'stylesheet',
            href: pluginUrl + '/style.css'
        });
        editor.getDoc().getElementsByTagName('head')[0].appendChild(linkElm);
    }

    if (Object.keys(mapper).length) {
        if (tinymceVersion > 4) {
            editor.ui.registry.addMenuButton('variable', {
                text: 'Variables',
                type: 'menubutton',
                tooltip: 'Insert variables.',
                fetch: function (callback) {
                    var items = [];
                    $.each(mapper, function (i, name) {
                        items.push({
                            type: 'menuitem',
                            text: name,
                            onAction: function () {
                                addVariable(i);
                            }
                        });
                    });
                    callback(items);
                }
            });
        } else {
        editor.addButton('variable', {
            text: 'Variables',
            type: 'menubutton',
            tooltip: 'Insert variables.',
            menu: function (callback) {
                var items = [];
                $.each(mapper, function (i, name) {
                    items.push({
                        type: 'menuitem',
                        text: name,
                        onclick: function () {
                            addVariable(i);
                        }
                    });
                });
                callback(items);
            }
        });
    }
    }

    var currentDirection;

    function keyDown(e) {
        currentDirection = e.keyCode;
        if (e.keyCode == 37) {
            currentDirection = 'left';
        } else if (e.keyCode == 39) {
            currentDirection = 'right';
        } else if (e.keyCode == 38) {
            currentDirection = 'up';
        } else if (e.keyCode == 40) {
            currentDirection = 'down';
        } else if (e.keyCode == 8) {
            var r = editor.selection.getRng();
            if (r.collapsed && r.startOffset >= 1 && r.startContainer.textContent.charCodeAt(r.startOffset) == 65279) {
                // work around TinyMCE failing to delete a character when it's got some zero-width non-blocking char
                r.startContainer.textContent =
                    r.startContainer.textContent.slice(0, r.startOffset - 1) +
                    r.startContainer.textContent.slice(r.startOffset + 1);

                e.preventDefault();
                e.stopImmediatePropagation();
            }
        }
    }

    function nodeChange(e) {

        stringToHTML();

        var target = e.element;
        if (!isVariable(target)) {
            return null;
        }

        var originalVar = target.getAttribute('data-original-variable');
        var _originalVar = cleanVariable(originalVar);
        var _innerText = target.innerText;
        var _mappedValue = getMappedValue(_originalVar);

        if (_mappedValue !== _innerText) {
            if (_mappedValue.length > _innerText.length) {
                editor.dom.remove(target);
                target = null;
            } else {
                var node;
                var div = editor.dom.create('div', null, prefix + cleanVariable(_innerText.replace(/\s+/g, '')) + suffix);
                while ((node = div.lastChild)) {
                    editor.dom.insertAfter(node, target);
                    editor.selection.setCursorLocation(node, editor.selection.getRng().startOffset);
                }
                editor.dom.remove(target);
                target = null;
            }
        }

        e.preventDefault();
        e.stopImmediatePropagation();

        if (target) {
            switch (currentDirection) {
                case 'left':
                case 'up':
                    editor.selection.select(target);
                    editor.selection.collapse(true);
                    break;

                case 'down':
                case 'right':
                    editor.selection.select(target);
                    editor.selection.collapse();
                    break;
            }
        }
    }


    editor.on('init', function () {
        loadCss(editor, pluginUrl);
        stringToHTML();
    });
    editor.on('beforegetcontent', handleContentRerender);
    editor.on('getcontent', stringToHTML);
    editor.on('click', handleClick);
    editor.on('mousedown', preventDrag);
    editor.on('keyup', stringToHTML);
    editor.on('keydown', keyDown);
    //editor.on('nodechange', stringToHTML);
    editor.on('NodeChange', nodeChange);

    this.addVariable = addVariable;

});
