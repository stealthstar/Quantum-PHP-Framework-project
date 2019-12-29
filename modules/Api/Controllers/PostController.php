<?php

namespace Modules\Api\Controllers;

use Quantum\Factory\ServiceFactory;
use Quantum\Factory\ViewFactory;
use Quantum\Mvc\Qt_Controller;
use Quantum\Hooks\HookManager;
use Base\Services\PostService;
use Quantum\Http\Response;
use Quantum\Http\Request;

class PostController extends Qt_Controller
{

    public $view;
    public $postService;
    public $csrfVerification = false;

    public function __before(ServiceFactory $serviceFactory, ViewFactory $view)
    {
        $this->view = $view;
        $this->postService = $serviceFactory->get(PostService::class);

        $this->view->setLayout('layouts/main');
    }

    public function getPosts(Response $response)
    {
        $posts = $this->postService->getPosts();
        $response->json([
            'status' => 'success',
            'data' => $posts
        ]);
    }

    public function getPost(Response $response, $id)
    {
        $post = $this->postService->getPost($id);
        if ($post) {
            $response->json([
                'status' => 'success',
                'data' => $post
            ]);
        } else {
            $response->json([
                'status' => 'error',
                'message' => 'Post not found'
            ]);
        }

    }

    public function amendPost(Request $request, Response $response, $id = null)
    {
        $post = [
            'title' => $request->get('title'),
            'content' => $request->get('content'),
        ];

        if ($id) {
            $this->postService->updatePost($id, $post);
            $response->json([
                'status' => 'success',
                'message' => 'Updated successfuly'
            ]);
        } else {
            $this->postService->addPost($post);
            $response->json([
                'status' => 'success',
                'message' => 'Created successfuly'
            ]);
        }
    }

    public function deletePost(Response $response, $id)
    {
        $this->postService->deletePost($id);
        $response->json([
            'status' => 'success',
            'message' => 'Deleted successfuly'
        ]);
    }

}
