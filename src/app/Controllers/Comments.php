<?php

namespace App\Controllers;

use App\Models\CommentsModel;

class Comments extends BaseController
{
    public function index()
    {
        $model = model(CommentsModel::class);

        $perPage = config('Pager')->perPage;

        $sort = $this->request->getGet('sort');
        $order = $this->request->getGet('order');

        if (! in_array($sort, ['id', 'date'], true)) {
            $sort = 'id';
        }

        if (! in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }

        $model->orderBy($sort, strtoupper($order) === 'ASC' ? 'ASC' : 'DESC');

        $comments_list = $model->paginate($perPage);
        $pager = $model->pager;
        $pager->only(['sort', 'order']);

        $commentsIndexUrl = site_url('comments');
        if ($sort !== 'id' || $order !== 'desc') {
            $commentsIndexUrl .= '?' . http_build_query([
                'sort'  => $sort,
                'order' => $order,
            ]);
        }

        $data = [
            'comments_list'      => $comments_list,
            'pager'              => $pager,
            'sort'               => $sort,
            'order'              => $order,
            'comments_index_url' => $commentsIndexUrl,
            'title'              => 'Comments archive',
        ];

        helper('form');

        return view('templates/header', $data)
            . view('comments/index', $data)
            . view('comments/create', $data)
            . view('templates/footer', $data);
    }

    public function create()
    {
        helper('form');

        $data = $this->request->getPost(['name', 'text']);

        // Checks whether the submitted data passed the validation rules.
        if (! $this->validateData($data, [
            'name' => 'required|valid_email|max_length[128]',
            'text' => 'required|max_length[5000]|min_length[1]',
        ])) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status' => 'error',
                        'errors' => $this->validator->getErrors(),
                    ]);
            }

            // The validation fails, so returns the form.
            return $this->index();
        }

        // Gets the validated data.
        $post = $this->validator->getValidated();

        $model = model(CommentsModel::class);

        $comment = [
            'name' => $post['name'],
            'text'  => $post['text'],
            'date'  => date('Y-m-d H:i:s'),
        ];

        $comment['id'] = $model->insert($comment, true);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'comment' => $comment,
            ]);
        }

        return redirect()->to('/comments');
    }

    public function delete($id = null)
    {
        if ($id === null || ! ctype_digit((string) $id)) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['status' => 'error', 'message' => 'Invalid id']);
            }

            return redirect()->to('/comments');
        }

        $model = model(CommentsModel::class);

        if ($model->find($id) === null) {
            if ($this->request->isAJAX()) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['status' => 'error', 'message' => 'Comment not found']);
            }

            return redirect()->to('/comments');
        }

        $model->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'id'     => (int) $id,
            ]);
        }

        return redirect()->to('/comments');
    }
}