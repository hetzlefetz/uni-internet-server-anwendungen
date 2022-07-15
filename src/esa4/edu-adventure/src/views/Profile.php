<?php

namespace EduAdventure\Views;

use EduAdventure\Db\DbWrapper;
use EduAdventure\Models\User;
use EduAdventure\Utils\HtmlHelper;
use EduAdventure\Utils\Utils;

require_once __DIR__ . '/../../vendor/autoload.php';
class Profile
{
    static function getPage()
    {
        $wrapper = new DbWrapper();
        if (!$wrapper->isUserLoggedIn()) {
            Utils::addErrorToSession("Forbidden");
            Utils::redirect("http://localhost", 401);
            exit;
            return;
        }
        $err = "";
        if (array_key_exists("ERRORS", $_SESSION) &&  count($_SESSION["ERRORS"]) > 0) {
            error_log("Have errors");
            $err .=  HtmlHelper::makeList(...array_map(fn ($error) => HtmlHelper::makeError($error), $_SESSION["ERRORS"]));

            unset($_SESSION["ERRORS"]);
        }
        error_log(session_encode());
        $user = $wrapper->getSession($_SESSION["SID"])->getUser();
        $userContent = '<h1 class="mt-5">Welcome, ' . $user->handle . '</h1>';

        $games = $wrapper->getAllGames();
        $userContent .= '<h1 class="mt-5">Spiele</h1>';
        $userContent .= '
            <div class="table-responsive">
                <table class="table ">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Titel</th>                    
                            <th scope="col">Author</th>                    
                            <th scope="col">Aktion</th>                    
                        </tr>
                    </thead>
                <tbody>';
        foreach ($games as $game) {
            if ($game->published == 0) {
                continue;
            }
            $userContent .= '
            <tr>
                <th scope="row">' . $game->id . '</th>
                <td>' . $game->title . '</td>                    
                <td>' . $game->getAuthor() . '</td>                    
                <td><button type="button" class="btn btn-primary">Jetzt Spielen</button></td>
            </tr>';
        }
        $userContent .= '
                </tbody>
                </table>
            </div>';


        $adminContent = '';
        if ($user->isAdmin()) {
            $users = $wrapper->getAllUsers();
            $adminContent .= '<h1 class="mt-5">Nutzerverwaltung</h1>';
            $adminContent .= '<div class="table-responsive">
            <table class="table ">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nutzername</th>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Aktion</th>
                </tr>
                </thead>
                <tbody>';

            foreach ($users as $user) {
                $adminContent .= '
                    <tr>
                    <th scope="row">' . $user->id . '</th>
                    <td>' . $user->handle . '</td>
                    <td>' . $user->email . '</td>
                    <td>
                    <form method="post" action="/api/register">
                    <input type="hidden" name="method"  value="delete"> 
                    <input type="hidden" name="id"  value="' . $user->id . '"> 
                    <input  type="submit" class="btn btn-danger" value="Löschen">
                    </form>
                    <form action="/api/register" method="post">
                    <input type="hidden" name="method"  value="makeAuthor"> 
                    <input type="hidden" name="id"  value="' . $user->id . '"> 
                    <input  type="submit" class="btn btn-primary" value="Zum Author befördern">
                    </form>
                    <form method="post" action="/api/register">
                    <input type="hidden" name="method"  value="makeAdmin"> 
                    <input type="hidden" name="id"  value="' . $user->id . '"> 
                    <input  type="submit" class="btn btn-warning" value="Zum Admin befördern">
                    </form>
                    </td>
                    </tr>';
            }
            $adminContent .= '
                </tbody>
            </table>
            </div>';
        }

        $authorContent = '';
        if ($user->isAuthor()) {
            $games = $user->getGames();
            $authorContent .= '<h1 class="mt-5">Spiele Verwaltung</h1>';
            $authorContent .= '
            <div class="table-responsive">
                <table class="table ">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Veröffentlicht</th>                    
                            <th scope="col">Aktion</th>                    
                        </tr>
                    </thead>
                <tbody>';
            foreach ($games as $game) {
                $authorContent .= '
            <tr>
                <th scope="row">' . $game->title . '</th>
                <td>' . $game->published . '</td>                    
                <td>
                    <form method="post" action="/api/game">
                    <input type="hidden" name="method"  value="delete"> 
                    <input type="hidden" name="id"  value="' . $game->id . '"> 
                    <input  type="submit" class="btn btn-danger" value="Löschen">
                    </form>
                    <form method="post" action="/api/game">
                    <input type="hidden" name="method"  value="publish"> 
                    <input type="hidden" name="id"  value="' . $game->id . '"> 
                    <input  type="submit" class="btn btn-primary" value="Veröffentlichen">
                    </form>
                    <form method="post" action="/api/game">
                    <input type="hidden" name="method"  value="unpublish"> 
                    <input type="hidden" name="id"  value="' . $game->id . '"> 
                    <input  type="submit" class="btn btn-warning" value="Zurück ziehen">
                    </form>
                    </td>
            </tr>';
            }
            $authorContent .= '
                </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
            Neues Spiel erstellen
          </button>
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <form method="post" action="/api/game">
                <input type="hidden" name="method"  value="create"> 
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Neues Spiel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div class="form-group">
                <label for="title">Name des zu erstellenden Spiels</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="title">
              </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
                </div>
                </form>
            </div>
            </div>
           ';
        }

        echo HtmlHelper::MakePage($userContent . $authorContent . $adminContent);
    }
}
