/**
 * GxEditor Utils Library - Enhanced Multi-Attribute Protection
 */

function escHtml(s) {
    if (!s) return '';
    return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
window.escHtml = escHtml;

function utob(str) {
    try { return btoa(unescape(encodeURIComponent(str))); } catch(e) { return ""; }
}
window.utob = utob;

function btou(str) {
    try { return decodeURIComponent(escape(atob(str))); } catch (e) { return str; }
}
window.btou = btou;

function htmlToShortcode(html) {
    if (!html) return '';
    var div = document.createElement('div');
    div.innerHTML = html;
    
    var foundCount = 0;
    
    // Scan all possible elements for the container class
    var containers = Array.from(div.querySelectorAll('*')).filter(function(el) {
        return el.classList && el.classList.contains('custom-code-container');
    });
    
    // Check root level
    Array.from(div.children).forEach(function(c) {
        if (c.classList.contains('custom-code-container') && !containers.includes(c)) containers.push(c);
    });

    containers.forEach(function(el) {
        var code = '';
        var attrVal = el.getAttribute('data-gx-code') || el.getAttribute('data-code') || '';
        
        if (attrVal) {
            var b64Data = (attrVal.indexOf('base64:') === 0) ? attrVal.substring(7) : utob(attrVal);
            el.outerHTML = '[raw_html]base64:' + b64Data + '[/raw_html]';
            foundCount++;
        }
    });

    var result = div.innerHTML;
    
    // Regex fallback
    if (foundCount === 0 && result.toLowerCase().includes('custom-code-container')) {
        var pattern = /<div\b[^>]*class=['"][^'"]*custom-code-container[^'"]*['"][^>]*>([\s\S]*?)<\/div>/gi;
        result = result.replace(pattern, function(match, inner) {
             var attrMatch = match.match(/data-(gx-)?code=['"]([^'"]*)['"]/i);
             var code = attrMatch ? attrMatch[2] : '';
             if (code) {
                var b64Data = (code.indexOf('base64:') === 0) ? code.substring(7) : utob(code.replace(/&quot;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&'));
                foundCount++;
                return '[raw_html]base64:' + b64Data + '[/raw_html]';
             }
             return match;
        });
    }

    return result;
}
window.htmlToShortcode = htmlToShortcode;

function shortcodeToHtml(html) {
    if (!html) return '';
    var out = html;
    out = out.replace(/\[raw_html\]([\s\S]*?)\[\/raw_html\]/ig, function(match, inner) {
        var code = inner.trim();
        var b64 = (code.indexOf('base64:') === 0) ? code : 'base64:' + code;
        return '<div class="custom-code-container" data-gjs-type="gx-custom-code" data-gx-code="' + b64 + '">' +
                 '<div class="p-4 bg-dark text-white rounded-3 text-center" style="border: 3px dashed rgba(255,255,255,0.4); cursor:pointer;">' +
                   '<i class="bi bi-code-square fs-1 d-block mb-2 text-warning"></i>' +
                   '<span class="small fw-bold">RAW HTML CONTENT</span>' +
                 '</div>' +
               '</div>';
    });
    return out;
}
window.shortcodeToHtml = shortcodeToHtml;
