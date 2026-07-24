<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'         => (new HomeController)->index(),
    // Movie
    'movie_list'         => (new MovieController)->list(),
    'movie_delete' => (new MovieController)->delete(),
    'movie_create' => (new MovieController)->create(),
    'movie_store' => (new MovieController)->store(),
    'movie_edit' => (new MovieController)->edit(),
    'movie_update' => (new MovieController)->update(),
    'movie_show' => (new MovieController)->show(),
    

    // User
    'users_list'     => (new UserController)->users_list(),
    'users_add'      => (new UserController)->users_add(),
    'users_addUser'  => (new UserController)->users_addUser(),
    'users_edit'     => (new UserController)->users_edit(),
    'users_editUser' => (new UserController)->users_editUser(),
    'users_delete'   => (new UserController)->users_delete(),

    // Food
    'food_list'         => (new FoodController)->list(),
    'food_add'          => (new FoodController)->add(),
    'food_addPost'      => (new FoodController)->addPost(),
    'food_edit'         => (new FoodController)->edit(),
    'food_editPost'     => (new FoodController)->editPost(),
    'food_delete'       => (new FoodController)->delete(),

    // Food Variant
    'food_variant_list'         => (new FoodVariantController)->list(),
    'food_variant_add'          => (new FoodVariantController)->add(),
    'food_variant_addPost'      => (new FoodVariantController)->addPost(),
    'food_variant_edit'         => (new FoodVariantController)->edit(),
    'food_variant_editPost'     => (new FoodVariantController)->editPost(),
    'food_variant_delete'       => (new FoodVariantController)->delete(),

    // Food Order
    'food_order_list'         => (new FoodOrderController)->list(),
    'food_order_add'          => (new FoodOrderController)->add(),
    'food_order_addPost'      => (new FoodOrderController)->addPost(),
    'food_order_edit'         => (new FoodOrderController)->edit(),
    'food_order_editPost'     => (new FoodOrderController)->editPost(),
    'food_order_delete'       => (new FoodOrderController)->delete(),
};
