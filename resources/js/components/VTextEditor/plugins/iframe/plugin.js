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

    var namedIframeSelector = 'span[data-mce-object="iframe"]';
    var isEmptyString = function (str) {
      return !str;
    };
    var getSourceFromIframe = function (elm) {
      return elm.getAttribute('src') || '';
    };
    var isIframe = function (elm) {
      return elm && elm.nodeName.toLowerCase() === 'iframe';
    };
    var isNamedIframe = function (elm) {
      return isIframe(elm) && !elm.getAttribute('src') && getSourceFromIframe(elm) !== '';
    };
    var isEmptyNamedIframe = function (elm) {
      return isNamedIframe(elm) && !elm.firstChild;
    };

    var removeEmptyNamedIframeInSelection = function (editor) {
      var dom = editor.dom;
      global$1(dom).walk(editor.selection.getRng(), function (nodes) {
        global.each(nodes, function (node) {
          if (isEmptyNamedIframe(node)) {
            dom.remove(node, false);
          }
        });
      });
    };
    var getNamedIframe = function (editor) {
      var span = editor.dom.getParent(editor.selection.getStart(), namedIframeSelector);
      if(span) {
        return span.querySelector('iframe.iframe-obj');
      }
      return '';
    };
    var getSource = function (editor) {
      var iframe = getNamedIframe(editor);
      if (iframe) {
        return getSourceFromIframe(iframe);
      } else {
        return '';
      }
    };
    var createIframe = function (editor, src) {
      editor.undoManager.transact(function () {
        if (editor.selection.isCollapsed()) {
          editor.insertContent(editor.dom.createHTML('iframe', { src: src, style: 'width: 100%; height: 100vh', class: 'iframe-obj' }));
        } else {
          removeEmptyNamedIframeInSelection(editor);
          editor.formatter.remove('namedIframe', null, null, true);
          editor.formatter.apply('namedIframe', { value: src });
          editor.addVisual();
        }
      });
    };
    var updateIframe = function (editor, src, element) {
      editor.insertContent(editor.dom.createHTML('iframe', { src: src, style: 'width: 100%; height: 100vh', class: 'iframe-obj' }));
      editor.addVisual();
      editor.undoManager.add();
    };
    var insert = function (editor, src) {
      var iframe = getNamedIframe(editor);
      if (iframe) {
        updateIframe(editor, src, iframe);
      } else {
        createIframe(editor, src);
      }
      editor.focus();
    };

    var insertIframe = function (editor, newSource) {
      insert(editor, newSource);
      return true;
    };
    var open = function (editor) {
      var currentSource = getSource(editor);
      editor.windowManager.open({
        title: 'Insert/Edit Iframe',
        size: 'normal',
        body: {
          type: 'panel',
          items: [{
              name: 'src',
              type: 'input',
              label: 'Source',
              placeholder: ''
            }
          ]
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
        initialData: { src: currentSource },
        onSubmit: function (api) {
          if (insertIframe(editor, api.getData().src)) {
            api.close();
          }
        }
      });
    };

    var register$1 = function (editor) {
      editor.addCommand('mceIframe', function () {
        open(editor);
      });
    };

    var isNamedIframeNode = function (node) {
      return node && isEmptyString(node.attr('src'));
    };
    var isEmptyNamedIframeNode = function (node) {
      return isNamedIframeNode(node) && !node.firstChild;
    };
    var setContentEditable = function (state) {
      return function (nodes) {
        for (var i = 0; i < nodes.length; i++) {
          var node = nodes[i];
          if (isEmptyNamedIframeNode(node)) {
            node.attr('contenteditable', state);
          }
        }
      };
    };
    var setup = function (editor) {
      editor.on('PreInit', function () {
        editor.parser.addNodeFilter('iframe', setContentEditable('false'));
        editor.serializer.addNodeFilter('iframe', setContentEditable(null));
      });
    };

    var registerFormats = function (editor) {
      editor.formatter.register('namedIframe', {
        inline: 'iframe',
        selector: namedIframeSelector,
        remove: 'all',
        split: true,
        deep: true,
        attributes: { src: '%value' },
        onmatch: function (node, _fmt, _itemName) {
          return isNamedIframe(node);
        }
      });
    };

    var register = function (editor) {
      editor.ui.registry.addToggleButton('iframe', {
        icon: 'embed-page',
        tooltip: 'Embed iframe',
        onAction: function () {
          return editor.execCommand('mceIframe');
        },
        onSetup: function (buttonApi) {

          function nodeChangeHandler(){
            const selectedNode = editor.selection.getNode();
            var iframe = selectedNode.querySelector('iframe.iframe-obj')
            return buttonApi.setActive(iframe !== null);
          }
          editor.on('NodeChange', nodeChangeHandler);
        }
      });
      editor.ui.registry.addMenuItem('iframe', {
        icon: 'embed-page',
        text: 'Iframe...',
        onAction: function () {
          return editor.execCommand('mceIframe');
        }
      });
    };

    function Plugin () {
      global$2.add('iframe', function (editor) {
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
