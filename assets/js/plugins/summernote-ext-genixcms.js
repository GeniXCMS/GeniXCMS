(function (factory) {
    /* global define */
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        // Node/CommonJS
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals
        factory(window.jQuery);
    }
}(function ($) {
    
    // Extends plugins for adding readmore.
    //  - plugin is external module for customizing.
    $.extend($.summernote.plugins, {
        /**
         * @param {Object} context - context object has status of editor.
         */
        'readmore': function (context) {
            var self = this;
            
            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;
            
            // add readmore button
            context.memo('button.readmore', function () {
                // create button
                var button = ui.button({
                    contents: '<i class="fa fa-long-arrow-right"/> Read-More',
                    tooltip: 'readmore',
                    click: function () {

                        context.invoke('editor.insertText', '[[--readmore--]]');
                    }
                });
                
                // create jQuery object from button instance.
                var $readmore = button.render();
                return $readmore;
            });

        },
        'elfinder': function (context) {
            var self = this;
            
            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;
            
            // add elfinder button
            context.memo('button.elfinder', function () {
                // create button
                var button = ui.button({
                    contents: '<i class="fa fa-list-alt"/> File Manager',
                    tooltip: 'elfinder',
                    click: function () {
                        elfinderDialog($(this).closest('.note-editor').parent().children('.summernote'));
                    }
                });
                
                // create jQuery object from button instance.
                var $elfinder = button.render();
                return $elfinder;
            });
            

        },
        'gxcode': function (context) {
            var self = this;
            
            // ui has renders to build ui elements.
            //  - you can create a button with `ui.button`
            var ui = $.summernote.ui;
            
            // add elfinder button
            context.memo('button.gxcode', function () {
                // create button
                var button = ui.button({
                    contents: '<i class="fa fa-code"/> Code',
                    tooltip: 'Code Wrapper',
                    click: function (event) {

                        var highlight = window.getSelection(),  
                            spn = document.createElement('pre'),
                            range = highlight.getRangeAt(0)
                            // highlight = nl2br(highlight, true);
                            highlight = htmlEscape(highlight);
                        spn.innerHTML = highlight;

                        range.deleteContents();
                        range.insertNode(spn);
                    }

                });
                function nl2br (str, is_xhtml) {   
                  var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
                  return (str + '').replace(/([^>\r\n\r\n]+?)(\r\n\r\n|\n\r\n\r|\r\r|\n\n)/g, '$1'+ breakTag +'$2');
                }
                function htmlEscape(str) {
                    return (str+'')
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');
                }
                // create jQuery object from button instance.
                var $codewrapper = button.render();
                return $codewrapper;
            });
            

        },
        
    });
}));
