/**
 * Copyright (c) Tiny Technologies, Inc. All rights reserved.
 * Licensed under the LGPL or a commercial license.
 * For LGPL see License.txt in the project root for license information.
 * For commercial licenses see https://www.tiny.cloud/
 *
 * Version: 5.10.1 (2021-11-03)
 */
(function () {
    'use strict';

    var global$2 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.dom.RangeUtils');

    var global = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var namedAnchorSelector = 'a.toc:not([href])';
    var isEmptyString = function (str) {
      return !str;
    };
    var getIdFromToc = function (elm) {
      var id = elm.getAttribute('id') || elm.getAttribute('name');
      return id || '';
    };
    var isAnchor = function (elm) {
      return elm && elm.nodeName.toLowerCase() === 'a';
    };
    var isNamedAnchor = function (elm) {
      return isAnchor(elm) && !elm.getAttribute('href') && getIdFromToc(elm) !== '';
    };
    var isEmptyNamedAnchor = function (elm) {
      return isNamedAnchor(elm) && !elm.firstChild;
    };

    var removeEmptyNamedAnchorsInSelection = function (editor) {
      var dom = editor.dom;
      global$1(dom).walk(editor.selection.getRng(), function (nodes) {
        global.each(nodes, function (node) {
          if (isEmptyNamedAnchor(node)) {
            dom.remove(node, false);
          }
        });
      });
    };
    var isValidId = function (id) {
      return (/^[A-Za-z][A-Za-z0-9\-:._]*$/.test(id) && id.includes('-') && id.charAt(0) != '-' && id.charAt(id.length - 1) != '-') || (!id.includes(' ') && !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/.test(id));
    };
    var getNamedToc = function (editor) {
      return editor.dom.getParent(editor.selection.getStart(), namedAnchorSelector);
    };
    var getId = function (editor) {
      var toc = getNamedToc(editor);
      console.log(toc);
      if (toc) {
        return getIdFromToc(toc);
      } else {
        return '';
      }
    };
    var createTocRecord = function (editor, id) {
      editor.undoManager.transact(function () {
        if (editor.selection.isCollapsed()) {
          editor.insertContent(editor.dom.createHTML('a', { id: id, 'class':'toc' }));
        } else {
          removeEmptyNamedAnchorsInSelection(editor);
          editor.formatter.remove('namedToc', null, null, true);
          editor.formatter.apply('namedToc', { value: id });
          editor.addVisual();
        }
      });
    };
    var updateToc = function (editor, id, anchorElement) {
      anchorElement.removeAttribute('name');
      anchorElement.id = id;
      editor.addVisual();
      editor.undoManager.add();
    };
    var insert = function (editor, id) {
      var toc = getNamedToc(editor);
      if (toc) {
        updateToc(editor, id, toc);
      } else {
        createTocRecord(editor, id);
      }
      editor.focus();
    };

    var insertToc = function (editor, newId) {
      if (!isValidId(newId)) {
        editor.windowManager.alert("Id should not have white spaces. Instead you should use the character '-'. Whatever you add here will be displayed to the navigation area of the article without the caharacter '-'.");
        return false;
      } else {
        insert(editor, newId);
        return true;
      }
    };
    var open = function (editor) {
      var currentId = getId(editor);
      editor.windowManager.open({
        icon: 'table-row-numbering',
        title: 'Table of contents',
        size: 'normal',
        body: {
          type: 'panel',
          items: [{
              type: 'input',
              name: 'id',
              label: 'Navigation text',
              placeholder: 'Example',
              flex: true
            }]
        },
        buttons: [
          {
            type: 'cancel',
            name: 'cancel',
            text: 'Cancel'
          },
          {
            type: 'submit',
            name: 'save',
            text: 'Save',
            primary: true
          }
        ],
        initialData: { id: currentId },
        onSubmit: function (api) {
          if (insertToc(editor, api.getData().id)) {
            api.close();
          }
        }
      });
    };

    var register$1 = function (editor) {
      editor.addCommand('mceToc', function () {
        open(editor);
      });
    };

    var isNamedAnchorNode = function (node) {
      return node && isEmptyString(node.attr('href')) && !isEmptyString(node.attr('id') || node.attr('name'));
    };
    var isEmptyNamedAnchorNode = function (node) {
      return isNamedAnchorNode(node) && !node.firstChild;
    };
    var setContentEditable = function (state) {
      return function (nodes) {
        for (var i = 0; i < nodes.length; i++) {
          var node = nodes[i];
          if (isEmptyNamedAnchorNode(node)) {
            node.attr('contenteditable', state);
          }
        }
      };
    };
    var setup = function (editor) {
      editor.on('PreInit', function () {
        editor.parser.addNodeFilter('a', setContentEditable('false'));
        editor.serializer.addNodeFilter('a', setContentEditable(null));
      });
    };

    var registerFormats = function (editor) {
      editor.formatter.register('namedToc', {
        inline: 'a',
        selector: namedAnchorSelector,
        remove: 'all',
        split: true,
        deep: true,
        attributes: { id: '%value' },
        onmatch: function (node, _fmt, _itemName) {
          return isNamedAnchor(node);
        }
      });
    };

    var register = function (editor) {
      editor.ui.registry.addToggleButton('toc', {
        icon: 'table-row-numbering',
        tooltip: 'Table of contents',
        onAction: function () {
          return editor.execCommand('mceToc');
        },
        onSetup: function (buttonApi) {
          return editor.selection.selectorChangedWithUnbind('a.toc:not([href])', buttonApi.setActive).unbind;
        }
      });
      editor.ui.registry.addMenuItem('toc', {
        icon: 'table-row-numbering',
        text: 'Table of contents...',
        onAction: function () {
          return editor.execCommand('mceToc');
        }
      });
    };

    function Plugin () {
      global$2.add('toc', function (editor) {
        setup(editor);
        register$1(editor);
        register(editor);
        editor.on('PreInit', function () {
          registerFormats(editor);
        });
      });
    }

    Plugin();

}());
