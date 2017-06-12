<?php

/**
 * This is an array for all routes in the application.
 *
 * It is an array of arrays
 *
 * the structure of the inner array looks like this
 * <code>
 *
 * [
 *      'uri' => Uri for the route, can be different from the file name(recommended both filename and uri should have same name)
 *      'name'=> Name of the route (Just for distinguishing),
 *      'for' => Either front or back (Front should be loaded for all the users while back is for admin only
 *      'file'=> The file to be loaded from app/front/
 * ]
 *
 * </code>
 */

$routes = array([
        'uri' => '/',
        'name'=> 'MainPage',
        'for' => 'front',
        'file' => 'home'
    ], [
        'uri' => 'admin',
        'name'=> 'AdminHome',
        'for' => 'admin',
        'file' => 'home'
    ], [
        'uri' => 'admin/login',
        'name'=> 'AdminLogin',
        'for' => 'admin',
        'file' => 'login'
    ] , [
        'uri' => 'login',
        'name'=> 'AdminLogin',
        'for' => '',
        'file' => 'login'
    ]
);

return $routes;