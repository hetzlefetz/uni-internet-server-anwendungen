<?php

namespace EduAdventure\Views;

use EduAdventure\Utils\HtmlHelper;

require_once __DIR__ . '/../../vendor/autoload.php';
class Login
{
    static function getPage()
    {

        $additionalHead = '<style>
        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
          }
          
          .form-signin .checkbox {
            font-weight: 400;
          }
          
          .form-signin .form-floating:focus-within {
            z-index: 2;
          }
          
          .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
          }
          
          .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
          }
        </style>';

        $content = '<div class="form-signin">
        <form  method="post" action="/api/login">        
        <h1 class="h3 mb-3 fw-normal">Bitte anmelden</h1>
    
        <div class="form-floating">
          <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
          <label for="floatingInput">Email Adresse</label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="floatingPassword" placeholder="Password">
          <label for="floatingPassword">Passwort</label>
        </div>
    
        <div class="checkbox mb-3">
          <label>
            <input type="checkbox" value="remember-me"> Remember me
          </label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit">Einloggen</button>        
      </form>
      <a href="/register">Registrieren</a></div>';
        echo HtmlHelper::MakePage($content, " ", " ", $additionalHead); //No header, No footer
    }
}