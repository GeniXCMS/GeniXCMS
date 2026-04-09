<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Pages API Resource Controller.
 * 
 * @since 2.0.0
 * @version 2.1.1
 * @link https://github.com/GeniXCMS/GeniXCMS
 * @author Puguh Wijayanto <[EMAIL_ADDRESS]>
 * @author GeniXCMS <genixcms@gmail.com>
 * @copyright 2014-2023 Puguh Wijayanto
 * @copyright 2023-2026 GeniXCMS
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */

class PagesApi
{
    /**
     * GET /api/v1/pages/
     * GET /api/v1/pages/{id}
     */
    public function index($id = null)
    {
        if ($id) {
            $page = Pages::find($id);
            if (!$page)
                return Api::error(404, 'Page not found');
            return Api::success($page);
        }

        $pages = Pages::where('status', '1')->where('type', 'page')->orderBy('id', 'desc')->get();
        return Api::success($pages);
    }

    /**
     * POST /api/v1/pages/
     */
    public function submit()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data)
            return Api::error(400, 'Invalid JSON input');

        if (empty($data['title']))
            return Api::error(422, 'Title is required');
        $data['type'] = 'page';

        $res = Pages::create($data);
        if ($res) {
            return Api::success(['id' => Db::$last_id], 'Page created successfully');
        } else {
            return Api::error(500, 'Failed to create page');
        }
    }

    /**
     * PUT /api/v1/pages/{id}
     */
    public function update($id)
    {
        $page = Pages::find($id);
        if (!$page)
            return Api::error(404, 'Page not found');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data)
            return Api::error(400, 'Invalid JSON input');

        foreach ($data as $k => $v) {
            $page->{$k} = $v;
        }

        if ($page->save()) {
            return Api::success(null, 'Page updated successfully');
        } else {
            return Api::error(500, 'Failed to update page');
        }
    }

    /**
     * DELETE /api/v1/pages/{id}
     */
    public function delete($id)
    {
        $page = Pages::find($id);
        if (!$page)
            return Api::error(404, 'Page not found');

        if ($page->destroy()) {
            return Api::success(null, 'Page deleted successfully');
        } else {
            return Api::error(500, 'Failed to delete page');
        }
    }
}
