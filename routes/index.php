<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'         => (new HomeController)->index(),
    'movies'         => (new MovieController)->index(),

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
