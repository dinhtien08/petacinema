<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'         => (new HomeController)->index(),
    'movies'         => (new MovieController)->index(),
    'users_list'     => (new UserController)->users_list(),
    'users_add'      => (new UserController)->users_add(),
    'users_addUser'  => (new UserController)->users_addUser(),
    'users_edit'     => (new UserController)->users_edit(),
    'users_editUser' => (new UserController)->users_editUser(),
    'users_delete'   => (new UserController)->users_delete(),
};
