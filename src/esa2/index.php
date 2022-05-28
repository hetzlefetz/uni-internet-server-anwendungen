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

/**
 * exists: check if a variable is existing and stringish
 *
 * @param  mixed $var the variable to check
 * @return bool true if the variable isset() and would cast to an string of length >0
 */
function exists($var): bool
{
    return isset($var) && strlen(trim($var)) > 0;
}

/**
 * validate : performs validation
 *
 * @param  mixed $val the value to validate
 * @param  callable[] $validators Validation function that return true if passed false if not
 * @param  string[] $errors : adds the n-th entry of this array if the n-th validator fails
 * @return iterable : an array of strings for every failed validator, empty array if nothing failed
 */
function validate($val, $validators, $errors): iterable
{
    //We need an error for each validator
    if (count($validators) != count($errors)) {
        throw new Exception("Need an Error for each validator");
    }
    $ret = [];
    $i = 0;
    //Iterate over validators
    foreach ($validators as $validator) {

        if (!$validator($val)) {
            //return as soon as the first fails
            array_push($ret, $errors[$i]);
        }
        $i++;
    }
    //All passed-> return true
    return $ret;
}
/**
 * hasErrors
 *
 * @param  mixed $errors an associative array of arrays 
 * @return bool true if any error object
 */
function hasErrors($errors): bool
{
    foreach ($errors as $key => $value) {
        if (count($errors[$key])) {
            return true;
        }
    }
    return false;
}
function handleShippingPageInput()
{
    //Save functions in variables ... php meh
    $valSpecial = 'validateSpecialChars';
    $valFnLn = 'validateFnLn';
    $valTel = 'validateTel';
    $valLoc = 'validatePlzOrt';
    $valNonEmpty = "exists";


    $errors = [];

    //validate values from POST
    $errors["s1"] = validate(
        $_POST["s1"],
        array($valNonEmpty, $valSpecial, $valFnLn),
        array(
            "Der wert darf nicht leer sein",
            "Sonderzeichen sind nicht erlaubt",
            "Bitte Vor UND Nachnamen eingeben"
        )
    );

    $errors["s2"] = validate(
        $_POST["s2"],
        array($valNonEmpty, $valSpecial, $valLoc),
        array(
            "Der wert darf nicht leer sein",
            "Sonderzeichen sind nicht erlaubt",
            "Bitte PLZ und Ort eingeben"
        )
    );

    $errors["s3"] = validate(
        $_POST["s3"],
        array($valNonEmpty, $valSpecial, $valTel),
        array(
            "Der wert darf nicht leer sein",
            "Sonderzeichen sind nicht erlaubt",
            "Bitte Telefonnummer eingeben"
        )
    );

    //go back to shipping if has errors
    if (hasErrors($errors)) {
        displayShipping($errors);
    } else {
        //Save shipping info to session
        $_SESSION["s1"] = $_POST["s1"];
        $_SESSION["s2"] = $_POST["s2"];
        $_SESSION["s3"] = $_POST["s3"];
        $_SESSION["page_id"] = "p2";

        //go to overview
        displayOverview();
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

    $output .= makeInput("s1", "Vor und Nachname:", false, isset($errors["s1"]) && count($errors["s1"]) > 0 ? implode(",", $errors["s1"]) : null);
    $output .= makeInput("s2", "PLZ und Ort:", false, isset($errors["s2"]) && count($errors["s2"]) > 0 ? implode(",", $errors["s2"]) : null);
    $output .= makeInput("s3", "Telefonnummer:", false, isset($errors["s3"]) && count($errors["s3"]) > 0 ? implode(",", $errors["s3"]) : null);

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
