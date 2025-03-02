<?php

function currentUrl()
{

    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http' : 'https';
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    $params = $_SERVER['QUERY_STRING'];

    $param = substr($params, stripos($params, "lang="), 7);

    $query = str_replace($param, "", $params);

    $clean = str_replace("&&", "&", $query);

    return $protocol . '://' . $host . $script . '?' . trim($clean, "&");
}

function redirectToCurrentPage()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? "https://" : "http://";
    $currentURL = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    header("Location: $currentURL");
    exit;
}


function lang()
{
    $l = filter_input(INPUT_GET, 'lang');
    return empty($l) ? 'en' : $l;
}

function page()
{
    $p = filter_input(INPUT_GET, 'page');
    return (!isset($p)) ? 1 : $p;
}

function limit()
{
    $l = filter_input(INPUT_GET, 'limit');
    return (!isset($l)) ? 10 : $l;
}

function search()
{
    $s = filter_input(INPUT_GET, 'search');
    return (!isset($s)) ? '' : $s;
}

function filter()
{
    $f = filter_input(INPUT_GET, 'filter');
    return (!isset($f)) ? '' : $f;
}

// expected format column_(desc | asc)
function order()
{
    $o = filter_input(INPUT_GET, 'order');
    return (!isset($o)) ? '' : $o;
}

function getPagin($pageNo)
{
    return "page=$pageNo&lang=" . lang() . "&limit=" . limit();
}

function action()
{
    $a = filter_input(INPUT_GET, 'action');
    return (!isset($a)) ? '' : $a;
}

function timeformat($t)
{
    date_default_timezone_set('America/Phoenix');
    $newTime = strtotime($t);
    $myFormatForView = date("m/d/y g:i A", $newTime);
    return $myFormatForView;
}

function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


function strToCss($value = 'not-started')
{
    switch (strtolower($value)) {
        case 'finished':
            return 'finished';
        case 'in progress':
        case 'inprogress':
            return 'inprogress';
        case 'review needed':
            return 'needs-review';
        case 'review in progress':
            return 'being-reviewed';
        case 'finished no ref found':
            return 'no-ref-found';
        default:
            return 'not-started';
    }
}

function returnStrToCss(){

    return "function strToCss(value)
        {
            value = value.toLowerCase();
            switch (value) {
                case 'finished':
                    return 'finished';
                case 'in progress':
                case 'inprogress':
                    return 'inprogress';
                case 'review needed':
                    return 'needs-review';
                case 'review in progress':
                    return 'being-reviewed';
                case 'finished no ref found':
                    return 'no-ref-found';
                default:
                    return 'not-started';
            }
        }";

}



/**
 * Sorts a given comma-separated string of tracks and removes repeated values.
 * 
 * @param string $trackStr The input string containing comma-separated track identifiers.
 * @return string A string with unique track identifiers sorted naturally and separated by commas.
 */
function fnSortUniqueTracks($trackStr = ""): string
{
    // Split the string into an array
    $tracks = explode(',', $trackStr);

    // Remove any empty array values
    $tracks = array_filter($tracks);

    // Remove duplicates
    $tracks = array_values(array_unique($tracks));

    // Sort the array naturally
    natsort($tracks);

    // If you need to convert it back to a string
    $sortedUniqueString = implode(',', $tracks);

    // Output for demonstration
    return $sortedUniqueString;
}
