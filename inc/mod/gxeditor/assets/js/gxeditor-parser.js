/**
 * GxEditor Engine Module
 * Handles Block-to-HTML and HTML-to-Block parsing and serialization
 */

/**
 * Main function to insert a new block into the editor state
 */
window.addBlock = function(state, type, content, afterId) {
    var blockId = 'blk-' + Math.random().toString(36).slice(2, 10);
    
    // Default content for specific new blocks
    if (!content) {
        if (type === 'table') {
            content = '<table class="table table-bordered"><thead><tr><th>Header 1</th><th>Header 2</th></tr></thead><tbody><tr><td>Data 1</td><td>Data 2</td></tr></tbody></table>';
        } else if (type === 'icon_list') {
            content = 'List item 1\nList item 2';
        }
    }

    var newBlock = { id: blockId, type: type, content: content || '' };
    
    if (afterId) {
        var idx = state.blocks.findIndex(function(b){ return b.id === afterId; });
        if (idx !== -1) {
            state.blocks.splice(idx + 1, 0, newBlock);
        } else {
            state.blocks.push(newBlock);
        }
    } else {
        state.blocks.push(newBlock);
    }
    return newBlock;
};

/**
 * Remove a block ID from state and refresh rendering
 */
window.deleteBlock = function(state, id) {
    state.blocks = state.blocks.filter(function(b){ return b.id !== id; });
    if (state.blocks.length === 0) {
        window.addBlock(state, 'paragraph', '', null);
    }
    if (typeof renderAllBlocks === 'function') renderAllBlocks(state);
    else if (window.renderAllBlocks) window.renderAllBlocks(state);
};

/**
 * Move a block up or down in the array
 */
window.moveBlock = function(state, id, dir) {
    var idx = state.blocks.findIndex(function(b){ return b.id === id; });
    if (idx === -1) return;
    var newIdx = idx + dir;
    if (newIdx < 0 || newIdx >= state.blocks.length) return;
    
    var moved = state.blocks.splice(idx, 1)[0];
    state.blocks.splice(newIdx, 0, moved);
    if (typeof renderAllBlocks === 'function') renderAllBlocks(state);
    else if (window.renderAllBlocks) window.renderAllBlocks(state);
};

/**
 * Converts internal block array into HTML string and updates the base textarea
 */
window.serializeToTextarea = function(state) {
    if (!state || !state.textarea) return;
    var html = '';
    state.blocks.forEach(function(b) {
        var typeDef = GxEditor._blocks[b.type];
        if (typeDef && typeDef.serialize && typeof typeDef.serialize === 'function') {
            html += typeDef.serialize(b) + '\n';
            return;
        }
        switch (b.type) {
            case 'paragraph': 
                var style = '';
                if (b.textAlign) style += 'text-align:' + b.textAlign + ';';
                if (b.lineHeight) style += 'line-height:' + b.lineHeight + ';';
                var styleAttr = style ? ' style="' + style + '"' : '';
                html += '<p' + styleAttr + '>' + htmlToShortcode(b.content || '<br>') + '</p>\n'; 
                break;
            case 'h1': 
            case 'h2': 
            case 'h3': 
                var style = '';
                if (b.textAlign) style += 'text-align:' + b.textAlign + ';';
                if (b.lineHeight) style += 'line-height:' + b.lineHeight + ';';
                var styleAttr = style ? ' style="' + style + '"' : '';
                html += '<' + b.type + styleAttr + '>' + b.content + '</' + b.type + '>\n'; 
                break;
            case 'quote': html += '<blockquote><p>' + htmlToShortcode(b.content) + '</p></blockquote>\n'; break;
            case 'code':  html += '<pre><code>' + escHtml(b.content) + '</code></pre>\n'; break;
            case 'ul':
                var items_u = b.content.indexOf('<li>') !== -1 ? b.content : b.content.split('\n').map(function(l){ return '<li>' + htmlToShortcode(l) + '</li>'; }).join('');
                html += '<ul>' + items_u + '</ul>\n'; break;
            case 'ol':
                var items_o = b.content.indexOf('<li>') !== -1 ? b.content : b.content.split('\n').map(function(l){ return '<li>' + htmlToShortcode(l) + '</li>'; }).join('');
                html += '<ol>' + items_o + '</ol>\n'; break;
            case 'image':
                if (b.content) {
                    var sc = '[image';
                    sc += ' src="' + b.content + '"';
                    sc += ' width="' + (b.imgWidth || '') + '"';
                    sc += ' align="' + (b.imgAlign || '') + '"';
                    sc += ' style="' + (b.imgStyle || 'rounded') + '"';
                    sc += ' caption="' + escHtml(b.caption || '') + '"';
                    sc += ']';
                    html += sc + '\n';
                }
                break;
            case 'icon_list':
                var sc = '[icon_list icon="' + (b.listIcon || 'bi bi-check2-circle') + '" color="' + (b.listColor || '#6366f1') + '"]';
                var items = b.content.split('\n').map(function(it){ return '<li>' + it + '</li>'; }).join('\n');
                html += sc + '\n' + items + '\n[/icon_list]\n';
                break;
            case 'table':
                var sc = '[table border="' + (b.tableBorder || 'yes') + '" striped="' + (b.tableStriped || 'no') + '"]';
                html += sc + '\n' + b.content + '\n[/table]\n';
                break;
            case 'grid2':
            case 'grid2x2':
                var rows  = b.rowCount || (b.type === 'grid2x2' ? 2 : 1);
                var cols  = b.colCount || 2;
                var r     = (b.colRatio || '6:6').split(':');
                var bsCol = Math.floor(12 / cols);
                for (var rowIdx = 0; rowIdx < rows; rowIdx++) {
                    html += '<div class="row gx-3 gy-3 mb-1">\n';
                    for (var colIdx = 0; colIdx < cols; colIdx++) {
                        var cellIdx = (rowIdx * cols) + (colIdx + 1);
                        var cVal = (cols == 2) ? r[colIdx] : bsCol;
                        var dataKey = 'col' + cellIdx;
                        var cellContent = b[dataKey] || '';
                        if (state.shell) {
                            var innerTa = state.shell.querySelector('#gxb-inner-' + b.id + '-' + dataKey);
                            if (innerTa) {
                                var innerState = window.GxEditor._editors.find(function(e){ return e.textarea === innerTa; }) || (typeof editors !== 'undefined' ? editors.find(function(e){ return e.textarea === innerTa; }): null);
                                if (innerState) window.serializeToTextarea(innerState);
                                cellContent = innerTa.value;
                                b[dataKey] = cellContent;
                            }
                        }
                        html += '  <div class="col-12 col-md-' + cVal + '">' + htmlToShortcode(cellContent) + '</div>\n';
                    }
                    html += '</div>\n';
                }
                html += '<div class="mb-3"></div>\n';
                break;
            case 'card': 
                html += '<div class="card mb-3">\n';
                if (b.hasHeader) html += '  <div class="card-header">' + htmlToShortcode(b.header || '') + '</div>\n';
                var bodyContent = b.content || '';
                if (state.shell) {
                    var innerTa = state.shell.querySelector('#gxb-inner-' + b.id + '-content');
                    if (innerTa) {
                        var innerState = window.GxEditor._editors.find(function(e){ return e.textarea === innerTa; }) || (typeof editors !== 'undefined' ? editors.find(function(e){ return e.textarea === innerTa; }): null);
                        if (innerState) window.serializeToTextarea(innerState);
                        bodyContent = innerTa.value;
                        b.content = bodyContent;
                    }
                }
                html += '  <div class="card-body">\n    ' + htmlToShortcode(bodyContent) + '\n  </div>\n';
                if (b.hasFooter) html += '  <div class="card-footer">' + htmlToShortcode(b.footer || '') + '</div>\n';
                html += '</div>\n';
                break;
            case 'button':
                var target = (b.btnUrl && b.btnUrl.startsWith('http')) ? ' target="_blank"' : '';
                html += '<div class="mb-3 text-center"><a href="' + (b.btnUrl||'#') + '" class="btn ' + (b.btnClass||'btn-primary') + '"' + target + '>' + escHtml(b.content||'Click Here') + '</a></div>\n';
                break;
            case 'icon':
                var sc = '[icon class="' + (b.iconClass || 'bi bi-star') + '" size="' + (b.iconSize || '2.5rem') + '" color="' + (b.iconColor || '#6366f1') + '"]';
                html += '<div class="gx-icon-block mb-3 text-center">' + sc + '</div>\n';
                break;
            case 'single_post':
                html += '<div class="gx-single-post mb-4" data-postid="' + (b.content || '0') + '">[post id="' + (b.content || '0') + '"]</div>\n';
                break;
            case 'toc':
                var sc = '[toc title="' + (b.tocTitle || 'Daftar Isi') + '" float="' + (b.tocFloat || 'none') + '" width="' + (b.tocWidth || '450px') + '" collapse="' + (b.tocCollapse || 'no') + '"]';
                html += '<div class="gx-toc mb-4">' + sc + '</div>\n';
                break;
            case 'recent_posts': html += '<div class="gx-recent-posts mb-3">[recent_posts count="5"]</div>\n'; break;
            case 'random_posts': html += '<div class="gx-random-posts mb-3">[random_posts count="5"]</div>\n'; break;
            case 'divider': html += '<hr class="my-4">\n'; break;
        }
    });
    state.textarea.value = html;
};

/**
 * Initial parser to convert existing HTML content into block array
 */
window.parseHTML = function(html, state) {
    if (!html || !html.trim()) return;
    var div = document.createElement('div');
    div.innerHTML = html;
    
    div.childNodes.forEach(function(node) {
        var tag = node.nodeType === 1 ? node.tagName.toLowerCase() : '';
        var content = (node.textContent || '').trim();
        var innerHTML = (node.innerHTML || '').trim();
        var isSimple = (node.nodeType === 3) || (node.nodeType === 1 && (tag === 'p' || tag === 'div') && node.classList.length === 0);

        if (isSimple) {
            var imgMatch = content.match(/^\[image\b([^\]]*)\]$/i);
            if (imgMatch) {
                var attrStr = imgMatch[1];
                var src = (attrStr.match(/src="([^"]*)"/) || [0, ''])[1];
                if (src) {
                    var b = window.addBlock(state, 'image', src, null);
                    b.imgWidth = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '';
                    b.imgAlign = (attrStr.match(/align="([^"]*)"/) || [0, ''])[1] || '';
                    b.imgStyle = (attrStr.match(/style="([^"]*)"/) || [0, ''])[1] || 'rounded';
                    b.alt = (attrStr.match(/alt="([^"]*)"/) || [0, ''])[1] || '';
                    b.caption = (attrStr.match(/caption="([^"]*)"/) || [0, ''])[1] || '';
                    return;
                }
            }
            var tocMatch = content.match(/^\[toc\b([^\]]*)\]$/i);
            if (tocMatch) {
                var b = window.addBlock(state, 'toc', '', null);
                var attrStr = tocMatch[1];
                b.tocTitle = (attrStr.match(/title="([^"]*)"/) || [0, ''])[1] || 'Daftar Isi';
                b.tocFloat = (attrStr.match(/float="([^"]*)"/) || [0, ''])[1] || 'none';
                b.tocWidth = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '450px';
                b.tocCollapse = (attrStr.match(/collapse="([^"]*)"/) || [0, ''])[1] || 'no';
                return;
            }
            var postMatch = content.match(/^\[post id="(\d+)"\]$/i);
            if (postMatch) { window.addBlock(state, 'single_post', postMatch[1], null); return; }
            if (content === '[recent_posts]') { window.addBlock(state, 'recent_posts', '', null); return; }
            if (content === '[random_posts]') { window.addBlock(state, 'random_posts', '', null); return; }
            var iconMatch = content.match(/^\[icon\b([^\]]*)\]$/i);
            if (iconMatch) {
                var attrStr = iconMatch[1];
                var b = window.addBlock(state, 'icon', '', null);
                b.iconClass = (attrStr.match(/class="([^"]*)"/) || [0, 'bi bi-star'])[1];
                b.iconSize  = (attrStr.match(/size="([^"]*)"/) || [0, '2.5rem'])[1];
                b.iconColor = (attrStr.match(/color="([^"]*)"/) || [0, '#6366f1'])[1];
                return;
            }
            if (content.indexOf('[icon_list') === 0) {
                var m = content.match(/\[icon_list\b([^\]]*)\]([\s\S]*)\[\/icon_list\]/i);
                if (m) {
                    var b = window.addBlock(state, 'icon_list', m[2].trim(), null);
                    b.listIcon = (m[1].match(/icon="([^"]*)"/) || [0, 'bi bi-check2-circle'])[1];
                    b.listColor = (m[1].match(/color="([^"]*)"/) || [0, '#6366f1'])[1];
                    return;
                }
            }
            if (content.indexOf('[table') === 0) {
                var m = content.match(/\[table\b([^\]]*)\]([\s\S]*)\[\/table\]/i);
                if (m) {
                    var b = window.addBlock(state, 'table', m[2].trim(), null);
                    b.tableBorder = (m[1].match(/border="([^"]*)"/) || [0, 'yes'])[1];
                    b.tableStriped = (m[1].match(/striped="([^"]*)"/) || [0, 'no'])[1];
                    return;
                }
            }
        }
        
        if (node.nodeType !== 1) {
            if (content) window.addBlock(state, 'paragraph', content, null);
            return;
        }

        var spWrap = node.classList && (node.classList.contains('gx-single-post') ? node : node.querySelector('.gx-single-post'));
        if (spWrap) {
            var tid = (spWrap.textContent.match(/id="(\d+)"/) || [0, '0'])[1];
            if (tid === '0') tid = spWrap.getAttribute('data-postid') || '0';
            window.addBlock(state, 'single_post', tid, null);
            return;
        }
        
        if (node.classList && (node.classList.contains('gx-recent-posts') || node.querySelector('.gx-recent-posts'))) {
            window.addBlock(state, 'recent_posts', '', null); return;
        }
        if (node.classList && (node.classList.contains('gx-random-posts') || node.querySelector('.gx-random-posts'))) {
            window.addBlock(state, 'random_posts', '', null); return;
        }

        var tocWrap = node.classList && (node.classList.contains('gx-toc') ? node : node.querySelector('.gx-toc'));
        if (tocWrap) {
            var b = window.addBlock(state, 'toc', '', null);
            var tMatch = tocWrap.textContent.match(/\[toc\b([^\]]*)\]/i);
            if (tMatch) {
                var attrStr = tMatch[1];
                b.tocTitle = (attrStr.match(/title="([^"]*)"/) || [0, ''])[1] || 'Daftar Isi';
                b.tocFloat = (attrStr.match(/float="([^"]*)"/) || [0, ''])[1] || 'none';
                b.tocWidth = (attrStr.match(/width="([^"]*)"/) || [0, ''])[1] || '450px';
                b.tocCollapse = (attrStr.match(/collapse="([^"]*)"/) || [0, ''])[1] || 'no';
            }
            return;
        }

        if (tag === 'h1' || tag === 'h2' || tag === 'h3') {
            var b = window.addBlock(state, tag, node.innerHTML, null);
            if (node.style.textAlign) b.textAlign = node.style.textAlign;
            if (node.style.lineHeight) b.lineHeight = node.style.lineHeight;
        } else if (tag === 'blockquote') {
            var inner = node.querySelector('p') || node;
            window.addBlock(state, 'quote', inner.innerHTML, null);
        } else if (tag === 'pre') {
            var code = node.querySelector('code') || node;
            window.addBlock(state, 'code', code.textContent, null);
        } else if (tag === 'ul' || tag === 'ol') {
            var items = [].slice.call(node.querySelectorAll('li')).map(function(li){ return li.innerHTML; }).join('\n');
            window.addBlock(state, tag, items, null);
        } else if (tag === 'hr') {
            window.addBlock(state, 'divider', '', null);
        } else if (tag === 'div' && node.classList.contains('row')) {
            var cols = node.querySelectorAll('.col-12');
            if (cols.length > 2) {
                var b = window.addBlock(state, 'grid2x2', '', null);
                b.col1 = cols[0] ? cols[0].innerHTML : ''; b.col2 = cols[1] ? cols[1].innerHTML : '';
                b.col3 = cols[2] ? cols[2].innerHTML : ''; b.col4 = cols[3] ? cols[3].innerHTML : '';
                b.colCount = cols.length;
            } else {
                var b = window.addBlock(state, 'grid2', '', null);
                b.col1 = cols[0] ? cols[0].innerHTML : ''; b.col2 = cols[1] ? cols[1].innerHTML : '';
                b.colCount = cols.length;
                if (cols.length == 2) {
                    var cl1 = cols[0].className.match(/col-[a-z]+-(\d+)/) || cols[0].className.match(/col-(\d+)/);
                    var cl2 = cols[1].className.match(/col-[a-z]+-(\d+)/) || cols[1].className.match(/col-(\d+)/);
                    if(cl1 && cl2) b.colRatio = cl1[1] + ':' + cl2[1];
                }
            }
        } else if (tag === 'div' && node.classList.contains('gx-icon-block')) {
            var b = window.addBlock(state, 'icon', '', null);
            var match = node.innerHTML.match(/\[icon\b([^\]]*)\]/i);
            if (match) {
                var attrStr = match[1];
                b.iconClass = (attrStr.match(/class="([^"]*)"/) || [0, ''])[1];
                b.iconSize  = (attrStr.match(/size="([^"]*)"/) || [0, ''])[1];
                b.iconColor = (attrStr.match(/color="([^"]*)"/) || [0, ''])[1];
            }
        } else if (tag === 'ul' && node.classList.contains('gx-icon-list')) {
            var b = window.addBlock(state, 'icon_list', '', null);
            var items = [].slice.call(node.querySelectorAll('li')).map(function(li){ return li.textContent.trim(); }).join('\n');
            b.content = items;
            var firstIcon = node.querySelector('i');
            if (firstIcon) { b.listIcon = firstIcon.className; b.listColor = firstIcon.style.color; }
        } else if (tag === 'div' && node.classList.contains('table-responsive')) {
            var b = window.addBlock(state, 'table', '', null);
            var tbl = node.querySelector('table');
            if (tbl) {
                b.content = tbl.innerHTML;
                b.tableBorder = tbl.classList.contains('table-bordered') ? 'yes' : 'no';
                b.tableStriped = tbl.classList.contains('table-striped') ? 'yes' : 'no';
            }
        } else if (tag === 'div' && node.classList.contains('card')) {
            var b = window.addBlock(state, 'card', '', null);
            var head = node.querySelector('.card-header'); var body = node.querySelector('.card-body'); var foot = node.querySelector('.card-footer');
            if (head) { b.hasHeader = true; b.header = shortcodeToHtml(head.innerHTML); }
            if (foot) { b.hasFooter = true; b.footer = shortcodeToHtml(foot.innerHTML); }
            b.content = body ? body.innerHTML : node.innerHTML;
        } else if (tag === 'a' && node.classList.contains('btn')) {
            var b = window.addBlock(state, 'button', node.innerHTML.trim(), null);
            b.btnUrl = node.getAttribute('href');
            b.btnClass = node.className.replace('btn', '').trim() || 'btn-primary';
        } else {
            var pContent = shortcodeToHtml(node.innerHTML.trim());
            if (pContent) {
                var b = window.addBlock(state, 'paragraph', pContent, null);
                if (node.style.textAlign) b.textAlign = node.style.textAlign;
                if (node.style.lineHeight) b.lineHeight = node.style.lineHeight;
            }
        }
    });
};
