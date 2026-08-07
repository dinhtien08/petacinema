<?php

$action = $_GET['action'] ?? '/';

// Thực thi Middlewares kiểm tra và phân quyền
GuestMiddleware::handle($action);
AuthMiddleware::handle($action);
RoleMiddleware::handle($action);

match ($action) {
    '/'             => (new HomeController)->index(),
    'movie_detail'  => (new HomeController)->movieDetail(),
    'booking_date'  => (new HomeController)->bookingDate(),
    // Rooms
    'rooms'               => (new RoomController)->list(),
    'roomAdd'             => (new RoomController)->add(),
    'roomAddProcess'      => (new RoomController)->addProcess(),
    'roomEdit'            => (new RoomController)->edit(),
    'roomEditProcess'     => (new RoomController)->editProcess(),
    'roomDelete'          => (new RoomController)->delete(),
    'roomSeats'           => (new RoomController)->seats(),
    'roomGenerateSeats'   => (new RoomController)->generateSeats(),
    'roomToggleSeat' => (new RoomController())->toggleSeat(),

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
    'movies'             => (new MovieController)->list(),
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
    'users'          => (new UserController)->users_list(),
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
    'bookings'             => (new BookingController)->list(),
    'booking_list'         => (new BookingController)->list(),
    'booking_show'         => (new BookingController)->show(),
    'booking_detail'       => (new BookingController)->show(),
    'booking_add'          => (new BookingController)->add(),
    'booking_addPost'      => (new BookingController)->addPost(),
    'booking_edit'         => (new BookingController)->edit(),
    'booking_editPost'     => (new BookingController)->editPost(),
    'booking_delete'       => (new BookingController)->delete(),

    // Payment
    'payment_list'         => (new PaymentController)->payment_list(),
    'payment_detail'       => (new PaymentController)->payment_detail(),

    // showtime
    'showtimes' => (new ShowtimeController)->list(),
    'showtime_show' => (new ShowtimeController)->show(),
    'showtime_create' => (new ShowtimeController)->create(),
    'showtime_store' => (new ShowtimeController)->store(),
    'showtime_edit' => (new ShowtimeController)->edit(),
    'showtime_update' => (new ShowtimeController)->update(),
    'showtime_delete' => (new ShowtimeController)->delete(),
    'showtimeSeats' => (new ShowtimeController)->seats(),
    //login logout
    'login'         => (new AuthController)->login(),
    'loginPost'     => (new AuthController)->loginPost(),
    'logout'        => (new AuthController)->logout(),
    'register'      => (new AuthController)->register(),
    'registerStore' => (new AuthController)->registerStore(),

    //staff
    // Room
    'staff_rooms'        => (new StaffRoomController)->list(),
    'staff_roomSeats'    => (new StaffRoomController)->seats(),

    // Room Types
    'staff_room-types'   => (new StaffRoomTypeController)->list(),

    // Seat Types
    'staff_seat-types'   => (new StaffSeatTypeController)->list(),

    // Movie
    'staff_movies'       => (new StaffMovieController)->list(),
    'staff_movie_list'   => (new StaffMovieController)->list(),
    'staff_movie_show'   => (new StaffMovieController)->show(),

    // Dashboard
    'staff_dashboard'    => (new StaffDashboardController)->dashboard(),

    // Food
    'staff_food_list'    => (new StaffFoodController)->list(),

    // Food Variant
    'staff_food_variant_list' => (new StaffFoodVariantController)->list(),

    // Food Order
    'staff_food_order_list' => (new StaffFoodOrderController)->list(),

    // Booking
    'staff_bookings'      => (new StaffBookingController)->list(),
    'staff_booking_list'  => (new StaffBookingController)->list(),
    'staff_booking_show'  => (new StaffBookingController)->show(),
    'staff_booking_detail'=> (new StaffBookingController)->show(),

    // Payment
    'staff_payment_list'   => (new StaffPaymentController)->payment_list(),
    'staff_payment_detail' => (new StaffPaymentController)->payment_detail(),

    // Showtime
    'staff_showtimes'      => (new StaffShowtimeController)->list(),
    'staff_showtime_show'  => (new StaffShowtimeController)->show(),
    'staff_showtimeSeats'  => (new StaffShowtimeController)->seats(),
};
