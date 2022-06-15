<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.7.0
 */

namespace Modules\Web\Controllers;

use Quantum\Factory\ServiceFactory;
use Quantum\Factory\ViewFactory;
use Shared\Services\AuthService;
use Shared\Services\PostService;
use Quantum\Mvc\QtController;
use Quantum\Http\Response;
use Quantum\Http\Request;


/**
 * Class PostController
 * @package Modules\Web\Controllers
 */
class PostController extends QtController
{

    /**
     * Post service
     * @var \Shared\Services\PostService
     */
    public $postService;

    /**
     * Post service
     * @var \Shared\Services\AuthService
     */
    public $userService;

    /**
     * Works before an action
     * @param \Quantum\Factory\ServiceFactory $serviceFactory
     * @param \Quantum\Factory\ViewFactory $view
     */
    public function __before(ServiceFactory $serviceFactory, ViewFactory $view)
    {
        $this->postService = $serviceFactory->get(PostService::class);
        $this->userService = $serviceFactory->get(AuthService::class);
        $view->setLayout('layouts/main');
    }

    /**
     * Get posts action
     * @param \Quantum\Http\Response $response
     * @param \Quantum\Factory\ViewFactory $view
     */
    public function getPosts(Response $response, ViewFactory $view)
    {
        $usersPosts = $this->postService->getPosts();

        $view->setParam('title', 'Posts | ' . config()->get('app_name'));
        $view->setParam('users_posts', $usersPosts);
        $view->setParam('langs', config()->get('langs'));
        $response->html($view->render('post/post'));
    }

    /**
     *Get my posts action
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @param \Quantum\Factory\ViewFactory $view
     */
    public function getMyPosts(Request $request, Response $response, ViewFactory $view, string $lang)
    {
        $userId = auth()->user()->getFieldValue('id');
        if (!empty($userId)){
            $posts = $this->postService->getMyPosts($userId);
        }

        $view->setParam('title', 'My Posts | ' . config()->get('app_name'));
        $view->setParam('posts', $posts);
        $view->setParam('langs', config()->get('langs'));
        $response->html($view->render('post/my-posts'));
    }

    /**
     * Get post action
     * @param string $lang
     * @param string $uuid
     * @param \Quantum\Http\Response $response
     * @param \Quantum\Factory\ViewFactory $view
     */
    public function getPost(string $lang, string $uuid, Response $response, ViewFactory $view)
    {
        if (!$uuid && $lang) {
            $uuid = $lang;
        }

        $post = $this->postService->getPost($uuid);
        $user = $this->userService->get('id', $post['user_id']);
        $author = $user->getFieldValue('firstname') . ' ' . $user->getFieldValue('lastname');
        if (!$post) {
            stop(function () use ($response){
                $response->html(partial('errors/404'), 404);
            });
        }

        $view->setParam('title', $post['title'] . ' | ' . config()->get('app_name'));
        $view->setParam('post', $post);
        $view->setParam('uuid', $uuid);
        $view->setParam('author', $author);
        $view->setParam('langs', config()->get('langs'));

        $response->html($view->render('post/single'));
    }

    /**
     * Create post action
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @param \Quantum\Factory\ViewFactory $view
     */
    public function createPost(Request $request, Response $response, ViewFactory $view)
    {
        if ($request->isMethod('post')) {
            $postData = [
                'user_id' => (int)auth()->user()->getFieldValue('id'),
                'title' => $request->get('title', null, true),
                'content' => $request->get('content', null, true),
                'image' => '',
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($request->hasFile('image')) {
                $imageName = $this->postService->saveImage($request->getFile('image'), slugify($request->get('title')));
                $postData['image'] = base_url() . '/uploads/' . $imageName;
            }

            $this->postService->addPost($postData);
            redirect(base_url() . '/' . current_lang() . '/my-posts');
        } else {
            $view->setParam('title', 'New post | ' . config()->get('app_name'));
            $view->setParam('langs', config()->get('langs'));

            $response->html($view->render('post/form'));
        }
    }

    /**
     * Amend post action
     * @param \Quantum\Http\Request $request
     * @param \Quantum\Http\Response $response
     * @param \Quantum\Factory\ViewFactory $view
     * @param string $lang
     * @param int|null $id
     */
    public function amendPost(Request $request, Response $response, ViewFactory $view, string $lang, string $uuid = null)
    {
        if ($request->isMethod('post')) {
            $postData = [
                'title' => $request->get('title', null, true),
                'content' => $request->get('content', null, true),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $post = $this->postService->getPost($uuid);

            if (!empty($post) && $post['user_id'] == auth()->user()->getFieldValue('id')){
                if ($request->hasFile('image')) {
                    if ($post['image']) {
                        $this->postService->deleteImage($post['image']);
                    }

                    $imageName = $this->postService->saveImage($request->getFile('image'), slugify($request->get('title')));
                    $postData['image'] = base_url() . '/uploads/' . $imageName;
                }

                $this->postService->updatePost($uuid, $postData);
                redirect(base_url() . '/' . current_lang() . '/my-posts');
            } else{
                redirect(base_url() . '/' . current_lang() . '/my-posts');
            }


        } else {
            $post = $this->postService->getPost($uuid);
            $view->setParam('uuid', $uuid);

            if (!empty($post) && $post['user_id'] == auth()->user()->getFieldValue('id')){
                $view->setParam('title', $post['title'] . ' | ' . config()->get('app_name'));
                $view->setParam('langs', config()->get('langs'));

                $response->html($view->render('post/form', ['post' => $post]));
            } else{
                stop(function () use($response){
                    $response->html(partial('errors/404'), 404);
                });
            }

        }
    }

    /**
     * Delete post action
     * @param string $lang
     * @param string $uuid
     */
    public function deletePost(string $lang, string $uuid)
    {
        $post = $this->postService->getPost($uuid);

        if ($post['image']) {
            $this->postService->deleteImage($post['image']);
        }

        $this->postService->deletePost($uuid);

        redirect(base_url() . '/' . current_lang() . '/my-posts');
    }

    /**
     * Delete post image action
     * @param string $lang
     * @param string $uuid
     */
    public function deletePostImage(string $lang, string $uuid)
    {
        $post = $this->postService->getPost($uuid);

        if ($post['image']) {
            $this->postService->deleteImage($post['image']);
        }

        $post['image'] = '';

        $this->postService->updatePost($uuid, $post);

        redirect(base_url() . '/' . current_lang() . '/my-posts');
    }

}
