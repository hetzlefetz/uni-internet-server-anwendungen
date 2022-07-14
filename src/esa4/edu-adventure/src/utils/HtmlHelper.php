<?php

namespace EduAdventure\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';

class HtmlHelper
{

    static function makeForm(string $content): string
    {
        $output = '<form action="index.php" method="post">';
        $output .= $content;
        $output .= '<button type="submit">Absenden</button>';
        $output .= '</form>';
        return $output;
    }
    static function makeError(string $error): string
    {
        return '<p style="color:red; font-size:smaller;">' . $error . '<p>';
    }
    static function makeInput(
        string $name,
        string $label,
        string $required,
        string $error
    ): string {
        $output = "<div>";
        $output .= '<label for="' . $name . '">' . $label . '</label>';
        if ($required) {
            $output .= '<input type="text" name="' . $name . '" required/>';
        } else {
            $output .= '<input type="text" name="' . $name . '"/>';
        }
        if (isset($error)) {
            $output .= HtmlHelper::makeError($error);
        }
        $output .= "</div>";
        return $output;
    }
    static function makeList(string ...$texts): string
    {

        $output = '<ul>';
        foreach ($texts as $v) {
            $output .= "<li>" . $v . "</li>";
        }
        $output .= '</ul>';
        return $output;
    }

    static function makeHeader(string $text): string
    {
        return "<h1>" . $text . "</h1>";
    }
    static function makeSubHeader(string $text): string
    {
        return "<h2>" . $text . "</h2>";
    }
    static function MakePage(string $content, ?string $header = null, ?string $footer = null, $additionalHead = ""): string
    {
        if (!Utils::isDefined($footer)) {
            $footer = '<span class="text-muted">Edu Adventure 2022.</span>';
        }
        if (!Utils::isDefined($header)) {
            $header = '<div class="py-2 bg-light"><div class="container d-flex flex-wrap">
            <ul class="nav me-auto">
              <li class="nav-item"><a href="/" class="nav-link link-dark px-2 active" aria-current="page">Home</a></li>
              <li class="nav-item"><a href="/" class="nav-link link-dark px-2">Features</a></li>
              <li class="nav-item"><a href="#" class="nav-link link-dark px-2">Pricing</a></li>
              <li class="nav-item"><a href="#" class="nav-link link-dark px-2">FAQs</a></li>
              <li class="nav-item"><a href="#" class="nav-link link-dark px-2">About</a></li>
            </ul>
            <ul class="nav">
              <li class="nav-item"><a href="/login" class="nav-link link-dark px-2">Login</a></li>
              <li class="nav-item"><a href="/register" class="nav-link link-dark px-2">Sign up</a></li>
            </ul>
            </div></div>';
        }

        $ret = <<<HTML
<html>
    <head>
        <title>Edu - Adventure</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        $additionalHead
    </head>
    <body class="d-flex flex-column h-100">
        <header>
            $header
        </header>
        <main class="flex-shrink-0">
            <div class="container">
                $content
            </div>
        </main>
        <footer class="footer mt-auto py-3 bg-light">
            <div class="container">
                $footer
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>
HTML;
        return $ret;
    }
}