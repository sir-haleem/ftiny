<?php

define('TIME_FORMAT', 'F j, Y H:i:s');

function redirect($url)
{
    $url=site_url($url);
    header("Location:$url");
    exit();
}


function site_url($uri='/')
{
    if (empty($uri)) {
        return trim(base_url,'/');
    } elseif (substr($uri, 0, 2)=='//') {
        return $uri;
    } elseif (substr($uri, 0, 4)=='www.') {
        return 'http://'.$uri;
    } elseif (substr($uri, 0, 5)=='http:') {
        return $uri;
    } elseif (substr($uri, 0, 6)=='https:') {
        return $uri;
    }

    $uri=trim($uri,'/');

    $pi=parse_url($uri);
    if (isset($pi['scheme']) && isset($pi['path'])) {
        return $pi;
    }

    $return=base_url . trim($uri, '/');

    return $return;
}


function set_active_menu($str)
{
    return $_SERVER['QUERY_STRING']==$str ? 'active' : '';
}

function set_active_tab($str)
{
    return $_SERVER['QUERY_STRING']==$str ? 'active in' : '';
}

function randomPassword()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

function get($val) {return isset($_GET["$val"]) ? $_GET["$val"] : '';}
function post($val) {return isset($_POST["$val"]) ? $_POST["$val"] : '';}


function admin_only() {
    global $user;
    if(!$user->admin) {redirect("dashboard");}
}

function strip_query($path)
{
    $p=explode('?',$path);
    return $p[0];
}

function is_active_package($user_id,$package_id)
{
    global $db;
    if($db->dlookup('id','sponsor',' user_id =  ? and package_id = ?',array($user_id,$package_id))) {
        return true;
    }
    return false;
}



/**
 * stdout
 *
 * standard output message processing
 *
 * @param   mixed    $info        The variable to be displayed on screen/console
 * @param   bool  $exit        Should execution be ceased after output?
 *
 * @param   void
 */
function stdout($info,$exit=false)
{
    $bt = debug_backtrace();
    $caller = array_shift($bt);

    $summary="";

    if(isset($caller['file']) && isset($caller['line'])) {
        $summary=$caller['file'].':'.$caller['line']."\n";
    }


    if (PHP_SAPI === 'cli') {
        print_r($info);
        echo "\n";
    } else {
        print '<pre style="padding: 1em; margin: 1em 0;">';
        echo "$summary";
        if(func_num_args() < 2) {
            print_r($info);
        } else {
            print_r($info);
            //print_r(func_get_args());
        }
        print '</pre>';
    }

    if($exit) {exit();}
}

function is_admin($usr=null) {
    if($usr==null) {$usr=$GLOBALS['user'];}

    return $usr->admin ? true : false;
}


function get_avatar_image($usr=null)
{
    if($usr==null) {$usr=$GLOBALS['user'];}

    $usr=(object) $usr;

    if($usr->avatar>0) {
        $src=get_file_url($usr->avatar);
        if($src!=null) {return $src;}
    }

    $src= $usr->gender==1 ? base_url.'assets/img/avatar_m.png' :  base_url.'assets/img/avatar_f.png';

    return $src;
}

/**
 * specify name of input field
 *
 */
function upload_file($name)
{
    global $user, $db;

    $result=0;
    if(isset($_FILES[$name]) && $_FILES[$name]['size'] > 0)
    {
        $fileName = $_FILES[$name]['name'];
        $tmpName  = $_FILES[$name]['tmp_name'];
        $fileSize = $_FILES[$name]['size'];
        $fileType = $_FILES[$name]['type'];

        $ext=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

        $location=IMAGES.sha1_file($tmpName).".{$ext}";

        if(!file_exists($location)) {

            $fp      = fopen($tmpName, 'r');
            $fileContent = fread($fp, filesize($tmpName));
            fclose($fp);

            file_put_contents($location,$fileContent);
        }


        $location=str_replace(FCPATH,'',$location);


        $post=array();

        $post['owner']=$user->id;
        $post['name']=$fileName;
        $post['size']=$fileSize;
        $post['type']=$fileType;
        $post['ext']=$ext;
        $post['location']=$location;
        $post['time']=time();

        if($result=$db->dlookup('id',"files","location=? and owner=?",array($post['location'],$post['owner']))){
            //echo "previous upload $result";
        } else {
            $db->insert("files",$post);
            $result=$db->insert_id();
        }
    }
    return $result;
}

function get_file_url($id=0)
{
    global $db;
    if($location=$db->dlookup('location',"files","id=?",array($id))){
        if(file_exists(FCPATH.$location)) {
            return base_url . $location;
        }
    }
    return null;
}


function intCodeRandom($length = 6)
{
    //$intMin = (10 * $length) / 10; // 100...
    //$intMax = (10 * $length) - 1;  // 999...

    $codeRandom = mt_rand($intMin, $intMax);

    return $codeRandom;
}

function generatePin() {
    global $db;
    $pin  = intCodeRandom();
    while($db->dlookup('pin', 'pins', 'pin = ?', array($pin))) {
        $pin  = intCodeRandom();
    }
    return $pin;
}

function agent_balance($id = null, $format = true) {
    global $user;
    $id = is_null($id) ? $user->id : $id;
    $result = total_recharges($id,false) - total_transactions($id,false);
    return $format ? number_format($result) : $result;
}

function total_recharges($id = null,$format = true) {
    global $db;
    $cond = $id==null?'':"user_id = $id";
    $result = $db->dlookup('count(id)', 'bookings', $cond);
    return $format ? number_format($result) : $result;
}

function total_bookings() {
    global $db;
    return $db->dlookup('count(id)', 'bookings');

}

function total_transactions($id = null, $format = true) {
    global $db;
    $cond = $id==null?'':"user_id = $id";
    $result = $db->dlookup('sum(amount)', 'bookings', $cond);
    return $format ? number_format($result) : $result;
}

function moneyFormat ($amount) {
    return "N " . number_format($amount) . " NGN";
}

function route_match($request_uri, $routes) {
    foreach ($routes as $route) {
        if($route['uri'] === $request_uri)
            return $route;
    }
}

function os_path ($path) {
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function prevent_d_access() {
    defined('EXEC') or die('Unauthorized Access/Accessing through unverified route');
}
