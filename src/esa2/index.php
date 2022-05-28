<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    console_log("starting session");
}

$page_id = "p0";
if (isset($_SESSION['page_id'])) {
    $page_id = $_SESSION['page_id'];
}

console_log("PageId: " . $page_id);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($page_id) {
        case 'p0':
            handleStartPageInput();
            break;
        case 'p1':
            handleShippingPageInput();
            break;
        case 'p2':
            displayOverview();
            break;
        default:
            displayStartPage(null);
    }
    return;
} else {
    displayStartPage(null);
}

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
function validateSpecialChars($input)
{
    return preg_match("/^[\da-zA-Z-' ]*$/", $input);
}
function validateFnLn($input)
{
    $split = explode(" ", $input);
    return count($split) > 1;
}
function validatePlzOrt($input)
{
    $split = explode(" ", $input);
    return count($split) > 1 && strlen($split[0]) == 5 && is_numeric($split[0]);
}

function validateTel($input)
{
    // + zulassen
    if (preg_match('/^[+][0-9]/', $input)) {
        $count = 1;
        $input = str_replace(['+'], '', $input, $count); //remove +
    }

    // formatierung entfernen
    $input = str_replace([' ', '.', '-', '(', ')'], '', $input);

    // true wenn eine zahl zwischen 9 und 14 Zeichen Länge rauskommt
    return preg_match('/^[0-9]{9,14}\z/', $input);
}
function handleStartPageInput()
{
    $anySet = false;
    $errors = [];
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["w" . $i]) && strlen(trim($_POST["w" . $i])) > 0) {
            $anySet = true;
            if (!validateSpecialChars($_POST["w" . $i])) {
                $errors["w" . $i] = "Sonderzeichen sind nicht erlaubt";
            }
        }
    }

    if (!$anySet) {
        $errors["common"] = "Bitte mindestens ein Wunsch eingeben";
        displayStartPage($errors);
        return;
    }

    if (count($errors) != 0) {
        displayStartPage($errors);
        return;
    }
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["w" . $i]) && strlen(trim($_POST["w" . $i])) > 0) {
            $_SESSION["w" . $i] = $_POST["w" . $i];
        }
    }
    $_SESSION["page_id"] = "p1";
    displayShipping(null);
}

function handleShippingPageInput()
{
    console_log("handleShippingPageInput");
    $setCount = 0;
    $errors = [];
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_POST["s" . $i]) && strlen(trim($_POST["s" . $i])) > 0) {
            $setCount++;
            if (!validateSpecialChars($_POST["s" . $i])) {
                $errors["s" . $i] = "Sonderzeichen sind nicht erlaubt";
            }
        }
    }
    if (isset($_POST["s1"])) {
        if (!validateFnLn($_POST["s1"])) {
            if (isset($errors["s1"])) {
                $errors["s1"] .= "<br /> , Bitte Vor UND Nachnamen eingeben";
            } else {
                $errors["s1"] = "Bitte Vor UND Nachnamen eingeben";
            }
        }
    }
    if (isset($_POST["s2"])) {
        if (!validatePlzOrt($_POST["s2"])) {
            if (isset($errors["s2"])) {
                $errors["s2"] .= "<br /> , Bitte PLZ UND Ort eingeben";
            } else {
                $errors["s2"] = "Bitte PLZ UND Ort eingeben";
            }
        }
    }

    if (isset($_POST["s3"])) {
        if (!validateTel($_POST["s3"])) {
            if (isset($errors["s3"])) {
                $errors["s3"] .= "<br /> , Bitte eine gültige Telefonnummer eingeben";
            } else {
                $errors["s3"] = "Bitte eine gültige Telefonnummer eingeben";
            }
        }
    }

    if ($setCount == 3 && count($errors) == 0) {
        console_log("overview");
        $_SESSION["s1"] = $_POST["s1"];
        $_SESSION["s2"] = $_POST["s2"];
        $_SESSION["s3"] = $_POST["s3"];
        $_SESSION["page_id"] = "p2";
        displayOverview();
    } else {
        console_log("shipping with errors");
        displayShipping($errors);
    }
}
function displayStartPage($errors)
{

    $output = makeHeader("Deine Wünsche:");
    if (isset($errors["common"])) {
        $output .= makeError($errors["common"]);
    }
    $output .= makeInput("w1", "1. Wunsch:", false, isset($errors["w1"]) ? $errors["w1"] : null);
    $output .= makeInput("w2", "2. Wunsch:", false, isset($errors["w2"]) ? $errors["w2"] : null);
    $output .= makeInput("w3", "3. Wunsch:", false, isset($errors["w3"]) ? $errors["w3"] : null);
    htmlWrapper(makeForm($output));
}

function displayShipping($errors)
{

    $output = makeHeader("Deine Wünsche:");
    $wishes = [];
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_SESSION["w" . $i])) {
            array_push($wishes, $_SESSION["w" . $i]);
        }
    }
    $output .= makeList($wishes);
    $output .= makeHeader("Deine Lieferadresse:");

    $output .= makeInput("s1", "Vor und Nachname:", false, isset($errors["s1"]) ? $errors["s1"] : null);
    $output .= makeInput("s2", "PLZ und Ort:", false, isset($errors["s2"]) ? $errors["s2"] : null);
    $output .= makeInput("s3", "Telefonnummer:", false, isset($errors["s3"]) ? $errors["s3"] : null);

    htmlWrapper(makeForm($output));
}

function displayOverview()
{

    $output = makeHeader("Wunschübersicht");
    $output .= makeSubHeader("Deine Wünsche:");
    $wishes = [];
    for ($i = 1; $i <= 3; $i++) {
        if (isset($_SESSION["w" . $i])) {
            array_push($wishes, $_SESSION["w" . $i]);
        }
    }
    $output .= makeList($wishes);
    $output .= makeSubHeader("Deine Lieferadresse:");
    $output .= makeList(array($_SESSION["s1"], $_SESSION["s2"], $_SESSION["s3"]));
    htmlWrapper($output);
    session_destroy();
}

function makeForm($content)
{
    $output = '<form action="index.php" method="post">';
    $output .= $content;
    $output .= '<button type="submit">Absenden</button>';
    $output .= '</form>';
    return $output;
}
function makeInput($name, $label, $required, $error)
{
    $output = "<div>";
    $output .= '<label for="' . $name . '">' . $label . '</label>';
    if ($required) {
        $output .= '<input type="text" name="' . $name . '" required/>';
    } else {
        $output .= '<input type="text" name="' . $name . '"/>';
    }
    if (isset($error)) {
        $output .= makeError($error);
    }
    $output .= "</div>";
    return $output;
}
function makeError($error)
{
    return '<p style="color:red; font-size:smaller;">' . $error . '<p>';
}
function makeList($texts)
{

    $output = '<ul>';
    foreach ($texts as $v) {
        $output .= "<li>" . $v . "</li>";
    }
    $output .= '</ul>';
    return $output;
}

function makeHeader($text)
{
    return "<h1>" . $text . "</h1>";
}
function makeSubHeader($text)
{
    return "<h2>" . $text . "</h2>";
}
function htmlWrapper($content)
{
    echo '<html>
    <head>
     <title>PHP Test</title>
    </head>
    <body>' . $content . '    
    </body>
   </html>';
}
