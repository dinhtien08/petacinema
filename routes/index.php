<?php

$action = $_GET['action'] ?? '/';

match ($action) {
    '/',
    'movies'
        => (new MovieController)->index(),
};
