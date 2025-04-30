<?php

namespace App\Controllers;

use App\Models\UserModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AuthController {
    private $userModel;
    private $twig;

    public function __construct() {
        $this->userModel = new UserModel();
        $loader = new FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Environment($loader, [
            'autoescape' => 'html',
        ]);
    }

    public function showLogin() {
        echo $this->twig->render('auth/login.twig');
    }

    public function login() {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user'] = $user;
            header('Location: /posts');
        } else {
            echo $this->twig->render('auth/login.twig', ['error' => 'Invalid email or password.']);
        }
    }

    public function showRegister() {
        echo $this->twig->render('auth/register.twig');
    }

    public function register() {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if ($password !== $confirmPassword) {
            echo $this->twig->render('auth/register.twig', ['error' => 'Passwords do not match.']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        header('Location: /login');
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /login');
    }
}