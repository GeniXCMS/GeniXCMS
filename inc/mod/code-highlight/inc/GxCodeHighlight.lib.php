<?php


class GxCodeHighlight
{
    public function __construct()
    {
        Hooks::attach('footer_load_lib', array('GxCodeHighlight', 'show'));
    }

    public static function show()
    {
        echo "
        <link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/styles/monokai-sublime.min.css\">
        <script src=\"//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/highlight.min.js\"></script>
        <script>
            hljs.configure({
                useBR: false,
                tabReplace: '    ',
            });
            $('pre').each(function(i, block) {
              hljs.highlightBlock(block);
            });
        </script>
        ";
    }
}
