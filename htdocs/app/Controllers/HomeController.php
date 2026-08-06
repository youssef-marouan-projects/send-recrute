<?php

class HomeController extends Controller
{
    public function index()
    {
        $this->view('home/index', ['title' => 'Home Page']);
    }

    public function about()
    {
        echo "About page";
    }

    // Method with parameters
    // URL: /home/user/15/John
    public function user($id = null, $name = null)
    {
        echo "User ID: " . htmlspecialchars($id) . "<br>";
        echo "Name: " . htmlspecialchars($name);
    }
}