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

    var namedExplanationSelector = 'span.explanation';
    var isEmptyString = function (str) {
      return !str;
    };
    var getExplanationContent = function (elm) {
      var content = elm.getAttribute('data-tooltip');
      return content || '';
    };
    var isExplanation = function (elm) {
      return elm && elm.nodeName.toLowerCase() === 'span';
    };
    var isNamedExplanation = function (elm) {
      return isExplanation(elm) && elm.getAttribute('class') == 'explanation';
    };
    var isEmptySpan = function (elm) {
      return isNamedExplanation(elm) && !elm.firstChild;
    };

    var removeEmptyNamedExplanationInSelection = function (editor) {
      var dom = editor.dom;
      global$1(dom).walk(editor.selection.getRng(), function (nodes) {
        global.each(nodes, function (node) {
          if (isEmptySpan(node)) {
            dom.remove(node, false);
          }
        });
      });
    };
    var getNamedExplanation = function (editor) {
      return editor.dom.getParent(editor.selection.getStart(), namedExplanationSelector);
    };
    var getExplanation = function (editor) {
      var explanation = getNamedExplanation(editor);
      if (explanation) {
        return getExplanationContent(explanation);
      } else {
        return '';
      }
    };

    var updateExplanation = function (editor, explanation, explanationElement) {
      explanationElement.setAttribute('data-tooltip', explanation);
      editor.addVisual();
      editor.undoManager.add();
    };

    var createExplanation = function (editor, explanation) {
      editor.undoManager.transact(function () {
        var selectedElement = editor.selection.getContent({format : 'html'}).trim();
        editor.insertContent(editor.dom.createHTML('span', {'data-tooltip' : explanation.trim(), 'class' : 'explanation'}, editor.dom.encode(selectedElement.trim())));
      });
      editor.focus();
    }

    var insert = function (editor, explanation) {
      var explanationEl = getNamedExplanation(editor);
      if (explanationEl) {
        updateExplanation(editor, explanation, explanationEl);
      } else {
        createExplanation(editor, explanation);
      }
      editor.focus();
    };

    var open = function (editor) {
      var currentValue = getExplanation(editor);
      editor.windowManager.open({
        icon: 'comment-add',
        title: 'Explanation',
        size: 'normal',
        body: {
          type: 'panel',
          items: [{
              type: 'textarea',
              name: 'explanation',
              label: 'Explanation text',
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
        initialData: { explanation: currentValue },
        onSubmit: function (api) {
          insert(editor, api.getData().explanation);
          api.close();
        }
      });
    };

    var register$1 = function (editor) {
      editor.addCommand('mceExplanation', function () {
        open(editor);
      });
    };

    var isNamedExplanationNode = function (node) {
      return node && isEmptyString(!isEmptyString(node.attr('data-tooltip')));
    };
    var isEmptyNamedExplanationNode = function (node) {
      return isNamedExplanationNode(node) && !node.firstChild;
    };
    var setContentEditable = function (state) {
      return function (nodes) {
        for (var i = 0; i < nodes.length; i++) {
          var node = nodes[i];
          if (isEmptyNamedExplanationNode(node)) {
            node.attr('contenteditable', state);
          }
        }
      };
    };
    var setup = function (editor) {
      editor.on('PreInit', function () {
        editor.parser.addNodeFilter('span', setContentEditable('false'));
        editor.serializer.addNodeFilter('span', setContentEditable(null));
      });
    };

    var registerFormats = function (editor) {
      editor.formatter.register('namedExplanation', {
        inline: 'span',
        selector: namedExplanationSelector,
        remove: 'all',
        split: true,
        deep: true,
        attributes: { 'data-tooltip': '%value' },
        onmatch: function (node, _fmt, _itemName) {
          return isNamedExplanation(node);
        }
      });
    };

    var register = function (editor) {
      editor.ui.registry.addToggleButton('explanation', {
        icon: 'comment-add',
        tooltip: 'Explanation',
        onAction: function () {
          return editor.execCommand('mceExplanation');
        },
        onSetup: function (buttonApi) {
          return editor.selection.selectorChangedWithUnbind('span.explanation', buttonApi.setActive).unbind;
        }
      });
      editor.ui.registry.addMenuItem('explanation', {
        icon: 'comment-add',
        text: 'Explanation...',
        onAction: function () {
          return editor.execCommand('mceExplanation');
        }
      });
    };

    function Plugin () {
      global$2.add('explanation', function (editor) {
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
