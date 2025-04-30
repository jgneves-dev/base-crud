<?php

namespace App\Controllers;

use App\Models\PostModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class PostController {
    private $postModel;
    private $twig;

    public function __construct() {
        $this->postModel = new PostModel();
        $loader = new FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Environment($loader, [
            'autoescape' => 'html',
        ]);
    }

    private function checkAuth() {
        session_start();
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $this->checkAuth();
        $posts = $this->postModel->getAll();
        echo $this->twig->render('posts/index.twig', [
            'posts' => $posts,
            'user_id' => $_SESSION['user']['id']
        ]);
    }

    public function create() {
        $this->checkAuth();
        echo $this->twig->render('posts/create.twig');
    }

    public function store() {
        $this->checkAuth();
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            echo $this->twig->render('posts/create.twig', ['error' => 'All fields are required.']);
            return;
        }

        $this->postModel->create([
            'title' => $title,
            'content' => $content,
            'user_id' => $_SESSION['user']['id']
        ]);
        header('Location: /posts');
    }

    public function edit($id) {
        $this->checkAuth();
        $post = $this->postModel->getById($id);

        if ($post['user_id'] != $_SESSION['user']['id']) {
            header('Location: /posts');
            exit;
        }

        echo $this->twig->render('posts/edit.twig', ['post' => $post]);
    }

    public function update($id) {
        $this->checkAuth();
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);

        if (empty($title) || empty($content)) {
            $post = $this->postModel->getById($id);
            echo $this->twig->render('posts/edit.twig', ['post' => $post, 'error' => 'All fields are required.']);
            return;
        }

        $this->postModel->update($id, [
            'title' => $title,
            'content' => $content,
            'user_id' => $_SESSION['user']['id']
        ]);
        header('Location: /posts');
    }

    public function delete($id) {
        $this->checkAuth();
        $this->postModel->delete($id, $_SESSION['user']['id']);
        header('Location: /posts');
    }
}