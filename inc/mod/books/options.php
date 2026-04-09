<?php
defined('GX_LIB') or die('Direct Access Not Allowed!');

$act = isset($_GET['act']) ? $_GET['act'] : 'index';
if ($act == 'add' || $act == 'edit') {
    $GLOBALS['editor'] = true;
}

// Fetch routing variables
$alertSuccess = [];
$alertDanger = [];

// Handle Form Submissions
if (isset($_POST['save_book'])) {
    if (!Token::validate($_POST['token'])) {
        $alertDanger[] = _('Invalid security token.');
    } else {
        $title = Typo::cleanX($_POST['title']);
        $content = $_POST['content'];
        
        $params = [
            'book_author' => Typo::cleanX($_POST['book_author']),
            'book_isbn' => Typo::cleanX($_POST['book_isbn']),
            'book_publisher' => Typo::cleanX($_POST['book_publisher']),
            'book_year' => Typo::cleanX($_POST['book_year']),
            'book_cover' => Typo::cleanX($_POST['book_cover']),
        ];

        $vars = [
            'title' => $title,
            'content' => $content,
            'date' => date('Y-m-d H:i:s'),
            'author' => Session::val('username'),
            'type' => 'book',
            'status' => Typo::int($_POST['status']),
            'cat' => 0 // Dynamic generic zero for independent post types
        ];

        if ($act == 'add') {
            $post = Posts::insert($vars);
            if ($post) {
                $pid = Posts::$last_id;
                foreach ($params as $k => $v) {
                    Posts::addParam($k, $v, $pid);
                }
                // Also add as post_image for core compatibility
                Posts::addParam('post_image', $_POST['book_cover'], $pid);

                $alertSuccess[] = _('Book successfully added to the library.');
                $act = 'index';
            } else {
                $alertDanger[] = _('Failed to save book. Please try again.');
            }
        } elseif ($act == 'edit' && isset($_GET['id'])) {
            $id = Typo::int($_GET['id']);
            Posts::update($vars);
            foreach ($params as $k => $v) {
                if (Posts::existParam($k, $id)) {
                    Posts::editParam($k, $v, $id);
                } else {
                    Posts::addParam($k, $v, $id);
                }
            }
            // Update core compatibility
            if (Posts::existParam('post_image', $id)) {
                Posts::editParam('post_image', $_POST['book_cover'], $id);
            } else {
                Posts::addParam('post_image', $_POST['book_cover'], $id);
            }

            $alertSuccess[] = _('Book details successfully updated.');
            $act = 'index';
        }
    }
} elseif ($act == 'del' && isset($_GET['id'])) {
    if (!Token::validate($_GET['token'])) {
        $alertDanger[] = _('Invalid token intercept.');
    } else {
        Posts::delete($_GET['id']);
        $alertSuccess[] = _('Book successfully removed from the library.');
        $act = 'index';
    }
}

// Ensure alerting schema hooks into local environment reliably
if (!empty($alertSuccess) || !empty($alertDanger)) {
    System::alert(['alertSuccess' => empty($alertSuccess) ? null : $alertSuccess, 'alertDanger' => empty($alertDanger) ? null : $alertDanger]);
}

switch($act) {
    case 'add':
    case 'edit':
        $isEdit = ($act == 'edit' && isset($_GET['id']));
        $id = $isEdit ? Typo::int($_GET['id']) : 0;
        $book = $isEdit ? Query::table('posts')->where('id', $id)->first() : null;

        $val_title     = htmlspecialchars($book->title ?? '');
        $val_status    = $book->status ?? 1;
        $val_author    = htmlspecialchars($isEdit ? Posts::getParam('book_author', $id) : '');
        $val_isbn      = htmlspecialchars($isEdit ? Posts::getParam('book_isbn', $id) : '');
        $val_publisher = htmlspecialchars($isEdit ? Posts::getParam('book_publisher', $id) : '');
        $val_year      = htmlspecialchars($isEdit ? Posts::getParam('book_year', $id) : '');
        $val_cover     = htmlspecialchars($isEdit ? Posts::getParam('book_cover', $id) : '');
        $val_content   = htmlspecialchars($book->content ?? '');
        $no_img        = Site::$url . 'assets/images/noimage.png';
        $opt_pub       = $val_status == 1 ? 'selected' : '';
        $opt_drft      = $val_status == 0 ? 'selected' : '';

        $form_html = '
        <form method="post" action="">
          <div class="row g-2 mb-2">
            <div class="col-md-9">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Book Title').'</label>
              <input type="text" name="title" required value="'.$val_title.'"
                     class="form-control rounded-4 bg-light shadow-none border py-2 px-3 fw-bold"
                     placeholder="e.g. Laskar Pelangi">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Status').'</label>
              <select name="status" class="form-select rounded-4 bg-light shadow-none border py-2 px-3">
                <option value="1" '.$opt_pub.'>'._('Published').'</option>
                <option value="0" '.$opt_drft.'>'._('Draft').'</option>
              </select>
            </div>
          </div>

          <div class="row g-2 mb-2">
            <div class="col-md-4">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Author').'</label>
              <input type="text" name="book_author" value="'.$val_author.'"
                     class="form-control rounded-4 bg-light shadow-none border py-2 px-3"
                     placeholder="e.g. Andrea Hirata">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('ISBN').'</label>
              <input type="text" name="book_isbn" value="'.$val_isbn.'"
                     class="form-control rounded-4 bg-light shadow-none border py-2 px-3"
                     placeholder="978-...">
            </div>
            <div class="col-md-3">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Publisher').'</label>
              <input type="text" name="book_publisher" value="'.$val_publisher.'"
                     class="form-control rounded-4 bg-light shadow-none border py-2 px-3"
                     placeholder="e.g. Bentang Pustaka">
            </div>
            <div class="col-md-2">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Year').'</label>
              <input type="number" name="book_year" value="'.$val_year.'"
                     class="form-control rounded-4 bg-light shadow-none border py-2 px-3"
                     placeholder="2024">
            </div>
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-3">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Cover Image').'</label>
              <div class="media-drop-zone rounded-4 border bg-light position-relative text-center overflow-hidden"
                   style="cursor:pointer; border:2px dashed #e2e8f0; transition:all .3s ease; min-height:150px;"
                   onclick="bookCoverPickerOpen()"
                   onmouseover="this.style.borderColor=\'var(--gx-primary)\'"
                   onmouseout="this.style.borderColor=\'#e2e8f0\'">
                '.($val_cover
                  ? '<img id="bookCoverPreview" class="img-fluid" src="'.$val_cover.'" style="max-height:148px; width:100%; object-fit:cover;">'
                  : '<div id="bookCoverPlaceholder" class="py-4">
                       <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                       <p class="text-muted small mt-1 mb-0">'._('Click to upload cover').'</p>
                     </div>
                     <img id="bookCoverPreview" class="img-fluid d-none" style="max-height:148px; width:100%; object-fit:cover;">'
                ).'
              </div>
              <input type="hidden" name="book_cover" id="bookCoverInput" value="'.$val_cover.'">
              <div id="bookCoverActions" class="mt-1 '.($val_cover ? '' : 'd-none').'">
                <button type="button" onclick="bookCoverClear()" class="btn btn-link btn-sm text-danger p-0 extra-small">
                    <i class="bi bi-x-circle me-1"></i>'._('Remove image').'
                </button>
              </div>
            </div>
            <div class="col-md-9">
              <label class="form-label fw-black extra-small text-muted text-uppercase tracking-wider" style="font-size:0.65rem;">'._('Synopsis').'</label>
              <textarea name="content" style="height:150px; resize:none;"
                        class="form-control rounded-4 bg-light shadow-none border py-2 px-3"
                        placeholder="'._('Brief description of the book...').'">'.
                        $val_content.'</textarea>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 pt-2 border-top">
            <a href="index.php?page=mods&mod=books" class="btn btn-light border rounded-pill px-4 fw-bold">'._('Cancel').'</a>
            <input type="hidden" name="token" value="'.TOKEN.'">
            <button type="submit" name="save_book" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
              <i class="bi bi-save me-2"></i>'._('Save Book').'
            </button>
          </div>
        </form>
        <script>
          function bookCoverPickerOpen() {
            var elfinderUrl = "'.Url::ajax('elfinder').'";
            var vendorUrl = "'.Vendor::url().'";
            $("<div/>").dialogelfinder({
              url: elfinderUrl,
              baseUrl: vendorUrl + "/studio-42/elfinder/",
              lang: "en",
              width: 840,
              destroyOnClose: true,
              getFileCallback: function(files) {
                $("#bookCoverInput").val(files.url);
                $("#bookCoverPreview").attr("src", files.url).removeClass("d-none");
                $("#bookCoverPlaceholder").addClass("d-none");
                $("#bookCoverActions").removeClass("d-none");
              },
              commandsOptions: { getfile: { oncomplete: "close", folders: false } }
            }).dialogelfinder("instance");
          }
          function bookCoverClear() {
            $("#bookCoverInput").val("");
            $("#bookCoverPreview").attr("src", "").addClass("d-none");
            $("#bookCoverPlaceholder").removeClass("d-none");
            $("#bookCoverActions").addClass("d-none");
          }
        </script>';

        $page_title = $isEdit ? _('Edit Book') : _('Add New Book');
        $schema = [
            'header' => [
                'title'    => $page_title,
                'subtitle' => _('Fill in the details below and save.'),
                'icon'     => 'bi bi-book',
                'button'   => [
                    'url'   => 'index.php?page=mods&mod=books',
                    'label' => _('Back to Library'),
                    'icon'  => 'bi bi-arrow-left',
                    'class' => 'btn btn-light border rounded-pill fw-bold shadow-sm'
                ]
            ],
            'content' => [
                [
                    'type'          => 'card',
                    'no_padding'    => false,
                    'body_elements' => [
                        ['type' => 'raw', 'html' => $form_html]
                    ]
                ]
            ]
        ];

        $builder = new UiBuilder($schema);
        $builder->render();
        break;

    default: // index
        $page = isset($_GET['paging']) ? $_GET['paging'] : 1;
        $max = 10;
        $offset = ($page - 1) * $max;
        
        $query = Query::table('posts')->where('type', 'book');
        $q = isset($_GET['q']) ? Typo::cleanX($_GET['q']) : '';
        if ($q != '') {
            $qEsc = addslashes($q);
            $query->whereRaw("`title` LIKE '%{$qEsc}%'");
        }
        $books = $query->orderBy('id', 'DESC')->limit($offset, $max)->get();
        // Generate Pagination
        $paging = Paging::create([
            'paging' => $page,
            'table' => 'posts',
            'max' => $max,
            'url' => 'index.php?page=mods&mod=books',
            'type' => 'number',
            'where' => " `type` = 'book' " . ($q != '' ? "AND `title` LIKE '%{$q}%'" : '')
        ]);
        
        $rows = [];
        if ($books) {
            foreach ($books as $b) {
                $statusBadge = $b->status == '1' ? '<span class="badge bg-success bg-opacity-10 py-1 text-success border border-success border-opacity-25 rounded-pill px-2">Live</span>' : '<span class="badge bg-warning bg-opacity-10 py-1 border border-warning border-opacity-25 text-warning rounded-pill px-2">Draft</span>';
                $cover = Posts::getPostImage($b->id) ?: (Posts::getParam('book_cover', $b->id) ?: Site::$url . 'assets/images/noimage.png');
                $author = Posts::getParam('book_author', $b->id) ?: 'Unknown';
                
                $actions = '<div class="btn-group gap-1">';
                $actions .= '<a href="index.php?page=mods&mod=books&act=edit&id='.$b->id.'" class="btn btn-light btn-sm rounded-circle border" title="Edit"><i class="bi bi-pencil-square text-success"></i></a>';
                $actions .= '<a href="index.php?page=mods&mod=books&act=del&id='.$b->id.'&token='.TOKEN.'" class="btn btn-light btn-sm rounded-circle border" onclick="return confirm(\'Purge this book record permanently?\');" title="Delete"><i class="bi bi-trash text-danger"></i></a>';
                $actions .= '</div>';

                $rows[] = [
                    ['content' => "
                        <div class='d-flex align-items-center ps-4 py-2'>
                            <img src='{$cover}' width='45' height='60' class='rounded-2 shadow-sm border me-3' style='object-fit: cover;'>
                            <div>
                                <strong class='text-dark d-block mb-1 ls-n1' style='font-size: 0.95rem;'>{$b->title}</strong>
                                <span class='badge bg-light text-muted border px-2 rounded-pill fw-bold text-uppercase' style='font-size: 0.65rem;'><i class='bi bi-person-fill me-1'></i>{$author}</span>
                            </div>
                        </div>", 'class' => 'p-0'],
                    ['content' => '<span class="fw-bold text-muted small">'.(Posts::getParam('book_isbn', $b->id) ?: '--').'</span>', 'class' => 'text-center'],
                    ['content' => '<span class="fw-bold text-dark small">'.(Posts::getParam('book_year', $b->id) ?: '--').'</span>', 'class' => 'text-center'],
                    ['content' => $statusBadge, 'class' => 'text-center'],
                    ['content' => $actions, 'class' => 'text-center']
                ];
            }
        }

        $schema = [
            'header' => [
                'title' => _('Library'),
                'subtitle' => _('Manage your digital book collection.'),
                'icon' => 'bi bi-book',
                'button' => [
                    'url' => 'index.php?page=mods&mod=books&act=add',
                    'label' => _('Add Book'),
                    'icon' => 'bi bi-plus-lg',
                    'class' => 'btn btn-primary rounded-pill fw-bold shadow-sm px-4'
                ]
            ],
            'content' => [
                [
                    'type' => 'card',
                    'no_padding' => true,
                    'header_action' => '
                        <form action="index.php" method="get" class="d-flex gap-2">
                            <input type="hidden" name="page" value="mods">
                            <input type="hidden" name="mod" value="books">
                            <div class="input-group input-group-sm w-auto shadow-sm rounded-pill overflow-hidden border">
                                <span class="input-group-text bg-white border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" name="q" class="form-control border-0 ps-1 bg-white shadow-none" placeholder="'._("Search index...").'" value="'.$q.'">
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm d-none d-md-block"><i class="bi bi-arrow-right"></i></button>
                        </form>',
                    'body_elements' => [
                        [
                            'type' => 'table',
                            'headers' => [
                                ['content' => _('Book'), 'class' => 'ps-4 py-3'],
                                ['content' => _('ISBN'), 'class' => 'text-center'],
                                ['content' => _('Year'), 'class' => 'text-center'],
                                ['content' => _('Status'), 'class' => 'text-center'],
                                ['content' => _('Actions'), 'class' => 'text-center']
                            ],
                            'rows' => $rows,
                            'empty_message' => _('No books found. Add your first book using the button above.')
                        ]
                    ],
                    'footer' => '<div class="d-flex justify-content-end pagination-wrapper p-2">'.$paging.'</div>'
                ]
            ]
        ];

        $builder = new UiBuilder($schema);
        $builder->render();
        break;
}
?>

<style>
    .ls-n1 { letter-spacing: -0.5px; }
    .pagination-wrapper .pagination { margin-bottom: 0; gap: 5px; }
    .pagination-wrapper .page-link { border-radius: 50% !important; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: 0; background: #f8f9fa; color: #6c757d; font-weight: bold; font-size: 0.85rem; }
    .pagination-wrapper .page-item.active .page-link { background: var(--gx-primary); color: #fff; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    .extra-small { font-size: 0.65rem !important; }
</style>
