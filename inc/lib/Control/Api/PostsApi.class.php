<?php

defined('GX_LIB') or die('Direct Access Not Allowed!');

/**
 * Posts API Resource Controller.
 * 
 * @since 1.1.0
 */
class PostsApi
{
    /**
     * GET /api/v1/posts/
     * GET /api/v1/posts/{id}
     */
    public function index($id = null)
    {
        if ($id) {
            $post = Posts::find($id);
            if (!$post) return Api::error(404, 'Post not found');
            return Api::success($post);
        }

        $posts = Posts::where('status', '1')->orderBy('date', 'desc')->get();
        return Api::success($posts);
    }

    /**
     * POST /api/v1/posts/
     */
    public function submit()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return Api::error(400, 'Invalid JSON input');

        // Validation logic here...
        if (empty($data['title'])) return Api::error(422, 'Title is required');

        $res = Posts::create($data);
        if ($res) {
            return Api::success(['id' => Db::$last_id], 'Post created successfully');
        } else {
            return Api::error(500, 'Failed to create post');
        }
    }

    /**
     * PUT /api/v1/posts/{id}
     */
    public function update($id)
    {
        $post = Posts::find($id);
        if (!$post) return Api::error(404, 'Post not found');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) return Api::error(400, 'Invalid JSON input');

        foreach ($data as $k => $v) {
            $post->{$k} = $v;
        }

        if ($post->save()) {
            return Api::success(null, 'Post updated successfully');
        } else {
            return Api::error(500, 'Failed to update post');
        }
    }

    /**
     * DELETE /api/v1/posts/{id}
     */
    public function delete($id)
    {
        $post = Posts::find($id);
        if (!$post) return Api::error(404, 'Post not found');

        if ($post->destroy()) {
            return Api::success(null, 'Post deleted successfully');
        } else {
            return Api::error(500, 'Failed to delete post');
        }
    }
}
