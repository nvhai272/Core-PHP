<?php

if (!isset($router)) {
    die("❌ Router chưa được khởi tạo!");
}

// danh sách các routes (định nghĩa các URL và hành động tương ứng)
$router->get('/', 'HomeController@home');
$router->get('/home', 'HomeController@home');

$router->get('/login', 'LoginController@index');

$router->get('/logout', 'LoginController@logout');

$router->post('/login', 'LoginController@login');

// Router của user
$router->get('/user/profile', 'UserController@index');


// Router của admin
$router->get('/admin/list-admin', 'AdminController@showAllAdmin');
$router->get('/admin/details-admin', 'AdminController@showDetailAdmin');
$router->get('/admin/edit-admin', 'AdminController@showEditAdmin');
$router->post('/admin/edit-admin', 'AdminController@editAdmin');
$router->post('/admin/delete-admin', 'AdminController@deleteAdmin');
$router->get('/admin/create-admin', 'AdminController@showCreatePageAdmin');
$router->post('/admin/create-admin', 'AdminController@createAdmin');
$router->get('/admin/search-admin', 'AdminController@searchAdmin');
$router->get('/admin/search-user', 'AdminController@searchUser');


$router->get('/admin/list-user', 'AdminController@showAllUser');
$router->get('/admin/details-user', 'AdminController@showDetailUser');

$router->get('/admin/edit-user', 'AdminController@showEditUser');
$router->post('/admin/edit-user', 'AdminController@editUser');

$router->post('/admin/delete-user', 'AdminController@deleteUser');

$router->get('/admin/create-user', 'AdminController@showCreateUser');
$router->post('/admin/create-user', 'AdminController@createUser');


$router->get('/fbcallback', 'FacebookController@callback');
$router->get('/fblogin', 'FacebookController@login');
