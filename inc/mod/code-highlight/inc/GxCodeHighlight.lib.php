<?php

class GxCodeHighlight
{
    public function __construct() {
        Hooks::attach('footer_load_lib', array('GxCodeHighlight', 'show'));
    }
    public static function show() {
        echo "
        <link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/default.min.css\">
        <script src=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js\"></script>
        <script>
            hljs.configure({
                useBR: true,
                tabReplace: '    ',
            });
            $('pre').each(function(i, block) {
              hljs.highlightBlock(block);
            });
        </script>
        ";
    }
}
