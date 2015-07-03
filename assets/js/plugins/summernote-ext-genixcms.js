(function (factory) {
  /* global define */
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module.
    define(['jquery'], factory);
  } else {
    // Browser globals: jQuery
    factory(window.jQuery);
  }
}(function ($){
  // template, editor
  var tmpl = $.summernote.renderer.getTemplate();
  // add plugin
  $.summernote.addPlugin({
    name: 'genixcms', // name of plugin
    buttons: { // buttons
      readmore: function () {
        return tmpl.iconButton('fa fa-long-arrow-right', {
          event: 'readmore',
          title: 'Read more',
          hide: false
        });
      }
    },

    events: { // events
      readmore: function (event, editor, layoutInfo) {
        // var $editable = layoutInfo.editable();
        // editor.insertText($editable, '<!--readmore-->');
        layoutInfo.holder().summernote("insertText", "[[--readmore--]]");
      }
    }
  });
}));
