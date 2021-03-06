<?php

namespace EduAdventure\Views;

use EduAdventure\Utils\HtmlHelper;
use EduAdventure\Utils\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';
class Register
{
    static function getPage()
    {
        $err = "";
        if (array_key_exists("ERRORS", $_SESSION) &&  count($_SESSION["ERRORS"]) > 0) {
            error_log("Have errors");
            $err .=  HtmlHelper::makeList(...array_map(fn ($error) => HtmlHelper::makeError($error), $_SESSION["ERRORS"]));

            unset($_SESSION["ERRORS"]);
        }

        $additionalHead = '<style>        
        .form-control {
            font-size: 15px;
        }
        .form-control, .form-control:focus, .input-group-text {
            border-color: #e1e1e1;
        }
        .form-control, .btn {        
            border-radius: 3px;
        }
        .signup-form {
            width: 400px;
            margin: 0 auto;
            padding: 30px 0;		
        }
        .signup-form form {
            color: #999;
            border-radius: 3px;
            margin-bottom: 15px;
            background: #fff;
            box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
            padding: 30px;
        }
        .signup-form h2 {
            color: #333;
            font-weight: bold;
            margin-top: 0;
        }
        .signup-form hr {
            margin: 0 -30px 20px;
        }
        .signup-form .form-group {
            margin-bottom: 20px;
        }
        .signup-form label {
            font-weight: normal;
            font-size: 15px;
        }
        .signup-form .form-control {
            min-height: 38px;
            box-shadow: none !important;
        }	
        .signup-form .input-group-addon {
            max-width: 42px;
            text-align: center;
        }	
        .signup-form .btn, .signup-form .btn:active {        
            font-size: 16px;
            font-weight: bold;
            
            border: none;
            min-width: 140px;
        }
        .signup-form .btn:hover, .signup-form .btn:focus {
           
        }
        .signup-form a {
            color: #fff;	
            text-decoration: underline;
        }
        .signup-form a:hover {
            text-decoration: none;
        }
        .signup-form form a {
            color: blue;
            text-decoration: none;
        }	
        .signup-form form a:hover {
            text-decoration: underline;
            color: lightblue;
        }
        .signup-form .fa {
            font-size: 21px;
        }
        .signup-form .fa-paper-plane {
            font-size: 18px;
        }
        .signup-form .fa-check {
            color: #fff;
            left: 17px;
            top: 18px;
            font-size: 7px;
            position: absolute;
        }
        </style>';


        $content = '<div class="signup-form"><form action="/api/register" method="post">
		<h2>Nutzerregistrierung</h2>
		<p>Bitte f??lle diese Form aus um dich zu Registrieren!</p>
		<hr>' . $err . '
        
        <div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<span class="fa fa-user"></span>
					</span>                    
				</div>
				<input type="text" class="form-control" name="username" placeholder="Nutzername" required="required">
			</div>
        </div>
        <div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fa fa-paper-plane"></i>
					</span>                    
				</div>
				<input type="email" class="form-control" name="email" placeholder="Email Adresse" required="required">
			</div>
        </div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fa fa-lock"></i>
					</span>                    
				</div>
				<input type="text" class="form-control" name="password" placeholder="Passwort" required="required">
			</div>
        </div>
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fa fa-lock"></i>
						<i class="fa fa-check"></i>
					</span>                    
				</div>
				<input type="text" class="form-control" name="confirm_password" placeholder="Password best??tigen" required="required">
			</div>
        </div>
        <div class="form-group">
			<label class="form-check-label"><input type="checkbox" required="required"> I akzeptiere die <a href="#">Nutzungsbedingungen</a> &amp; <a href="#">Datenschutz Richtlinie</a></label>
		</div>
		<div class="form-group">
            <button type="submit" class="btn btn-primary btn-lg">Sign Up</button>
        </div>
        <input type="hidden" name="method"  value="register">
    </form>
    </div>';

        echo HtmlHelper::MakePage($content, " ", " ", $additionalHead); //No 
    }
}
