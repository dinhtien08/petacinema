<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/'                   => (new MovieController)->index(),
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
};
