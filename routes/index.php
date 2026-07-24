<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'         => (new HomeController)->index(),
    // Rooms
    'rooms'               => (new RoomController)->list(),
    'roomAdd'             => (new RoomController)->add(),
    'roomAddProcess'      => (new RoomController)->addProcess(),
    'roomEdit'            => (new RoomController)->edit(),
    'roomEditProcess'     => (new RoomController)->editProcess(),
    'roomDelete'          => (new RoomController)->delete(),

    // Room Types
    'room-types'          => (new RoomTypeController)->list(),
    'roomTypeAdd'         => (new RoomTypeController)->add(),
    'roomTypeAddProcess'  => (new RoomTypeController)->addProcess(),
    'roomTypeEdit'        => (new RoomTypeController)->edit(),
    'roomTypeEditProcess' => (new RoomTypeController)->editProcess(),
    'roomTypeDelete'      => (new RoomTypeController)->delete(),

    // Seat Types
    'seat-types'          => (new SeatTypeController)->list(),
    'seatTypeAdd'         => (new SeatTypeController)->add(),
    'seatTypeAddProcess'  => (new SeatTypeController)->addProcess(),
    'seatTypeEdit'        => (new SeatTypeController)->edit(),
    'seatTypeEditProcess' => (new SeatTypeController)->editProcess(),
    'seatTypeDelete'      => (new SeatTypeController)->delete(),
    // Movie
    'movie_list'         => (new MovieController)->list(),
    'movie_delete' => (new MovieController)->delete(),
    'movie_create' => (new MovieController)->create(),
    'movie_store' => (new MovieController)->store(),
    'movie_edit' => (new MovieController)->edit(),
    'movie_update' => (new MovieController)->update(),
    'movie_show' => (new MovieController)->show(),
    
    //Dashboard
    'dashboard' => (new DashboardController)->dashboard(),
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

    // Booking
    'booking_list'         => (new BookingController)->list(),
    'booking_add'          => (new BookingController)->add(),
    'booking_addPost'      => (new BookingController)->addPost(),
    'booking_edit'         => (new BookingController)->edit(),
    'booking_editPost'     => (new BookingController)->editPost(),
    'booking_delete'       => (new BookingController)->delete(),

    // Payment
    'payment_list'         => (new PaymentController)->payment_list(),
    'payment_detail'       => (new PaymentController)->payment_detail(),
    'payment_edit'         => (new PaymentController)->payment_edit(),
    'payment_update'       => (new PaymentController)->payment_update(),
};
