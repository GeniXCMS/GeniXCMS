/**
 * GxEditor Utils Library
 * Pure helper functions extracted from main editor
 */

/**
 * Escape HTML special characters
 */
function escHtml(s) {
    return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

/**
 * Split text into lines, removing empty ones
 */
function splitLines(s) {
    return (s || '').split(/\n+/).filter(Boolean);
}

/**
 * Place cursor (caret) at the end of an element
 */
function placeCaretAtEnd(el) {
    var range = document.createRange();
    var sel = window.getSelection();
    range.selectNodeContents(el);
    range.collapse(false);
    sel.removeAllRanges();
    sel.addRange(range);
}

/**
 * Place cursor (caret) at the start of an element
 */
function placeCaretAtStart(el) {
    var range = document.createRange();
    var sel = window.getSelection();
    range.setStart(el, 0);
    range.collapse(true);
    sel.removeAllRanges();
    sel.addRange(range);
}

/**
 * Convert dynamic HTML content (like images) back to shortcodes for storage
 */
function htmlToShortcode(html) {
    if (!html) return '';
    var div = document.createElement('div');
    div.innerHTML = html;
    div.querySelectorAll('img').forEach(function(img) {
        var cl = img.classList;
        var sc = '[image src="' + img.getAttribute('src') + '"';
        
        if (cl.contains('w-25')) sc += ' width="w-25"';
        else if (cl.contains('w-50')) sc += ' width="w-50"';
        else if (cl.contains('w-75')) sc += ' width="w-75"';
        else if (cl.contains('w-100')) sc += ' width="w-100"';

        if (cl.contains('float-start')) sc += ' align="float-start"';
        else if (cl.contains('float-end')) sc += ' align="float-end"';
        else if (cl.contains('mx-auto')) sc += ' align="mx-auto d-block"';

        if (cl.contains('img-thumbnail')) sc += ' style="img-thumbnail"';
        else if (cl.contains('rounded-circle')) sc += ' style="rounded-circle"';
        else if (cl.contains('rounded')) sc += ' style="rounded"';
        else sc += ' style=""';

        var altText = img.getAttribute('alt') || '';
        var capText = '';
        var wrap = img.closest('.gx-image-wrap, .mb-3, .gx-image-rendered, .gxb-inline-img-wrap');
        var capSpan = wrap ? wrap.querySelector('.gxb-caption-text') : null;
        if (capSpan) capText = capSpan.textContent;

        sc += ' alt="' + altText + '"';
        sc += ' caption="' + capText + '"';
        sc += ']';
        
        if (wrap && wrap.querySelector('img') === img) {
            wrap.outerHTML = sc;
        } else {
            img.outerHTML = sc;
        }
    });
    return div.innerHTML;
}

/**
 * Convert shortcodes from storage back into interactive HTML for the editor
 */
function shortcodeToHtml(html) {
    if (!html) return '';
    return html.replace(/\[image\b([^\]]*)\]/ig, function(match, attrStr) {
        var src = (attrStr.match(/src="([^"]*)"/) || [0, ''])[1];
        var w   = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '';
        var a   = (attrStr.match(/align="([^"]*)"/) || [0, ''])[1] || '';
        var s   = (attrStr.match(/style="([^"]*)"/) || [0, ''])[1] || 'rounded';
        var alt = (attrStr.match(/alt="([^"]*)"/) || [0, ''])[1] || '';
        var c   = (attrStr.match(/caption="([^"]*)"/) || [0, ''])[1] || '';

        var cls = 'img-fluid ' + s + ' ' + w + ' ' + a;
        var wrapCls = 'mb-1';
        if (a === 'mx-auto d-block') wrapCls += ' text-center';
        if (a === 'float-start' || a === 'float-end') wrapCls += ' clearfix';

        if (c) {
            var out = '<span class="gxb-inline-img-wrap d-block ' + wrapCls + '"><img src="' + src + '" class="' + cls + '" alt="' + alt + '">';
            out += '<span class="gxb-caption-text d-block text-muted small mt-1">' + c + '</span></span>';
            return out;
        } else {
            return '<img src="' + src + '" class="' + cls + '" alt="' + alt + '">';
        }
    });
}
