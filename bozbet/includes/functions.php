<?php

define('DATA_PATH', __DIR__ . '/../../data');
define('USERS_PATH', DATA_PATH . '/users');
define('EVENTS_PATH', DATA_PATH . '/events');
define('BETS_PATH', DATA_PATH . '/bets');

// Ensure directories exist
if (!is_dir(USERS_PATH)) mkdir(USERS_PATH, 0777, true);
if (!is_dir(EVENTS_PATH)) mkdir(EVENTS_PATH, 0777, true);
if (!is_dir(BETS_PATH)) mkdir(BETS_PATH, 0777, true);


function get_user($username) {
    $user_file = USERS_PATH . "/$username.json";
    if (!file_exists($user_file)) {
        return null;
    }
    return json_decode(file_get_contents($user_file), true);
}

function save_user($user_data) {
    $user_file = USERS_PATH . "/{$user_data['username']}.json";
    $fp = fopen($user_file, 'w');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, json_encode($user_data, JSON_PRETTY_PRINT));
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}

function register_user($username, $password) {
    if (get_user($username)) {
        return "Username already exists.";
    }
    if (preg_match('/[^a-zA-Z0-9_]/', $username)) {
        return "Username can only contain letters, numbers, and underscores.";
    }

    $user_data = [
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'balance' => 1000.00 // Starting balance
    ];

    save_user($user_data);
    return true;
}

function login_user($username, $password) {
    $user = get_user($username);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function get_all_events() {
    $events = [];
    $event_files = glob(EVENTS_PATH . '/*.json');
    foreach ($event_files as $file) {
        $events[] = json_decode(file_get_contents($file), true);
    }
    // Sort events by start time, if available
    usort($events, function($a, $b) {
        return ($a['start_time'] ?? 0) <=> ($b['start_time'] ?? 0);
    });
    return $events;
}

function get_event($event_id) {
    $event_file = EVENTS_PATH . "/$event_id.json";
    if (!file_exists($event_file)) {
        return null;
    }
    return json_decode(file_get_contents($event_file), true);
}

function save_event($event_data) {
    $event_file = EVENTS_PATH . "/{$event_data['id']}.json";
    file_put_contents($event_file, json_encode($event_data, JSON_PRETTY_PRINT));
}

function get_user_bets($username) {
    $bets = [];
    $bet_files = glob(BETS_PATH . "/bet_*.json");
    foreach ($bet_files as $file) {
        $bet_data = json_decode(file_get_contents($file), true);
        if ($bet_data['username'] === $username) {
            $bets[] = $bet_data;
        }
    }
    return $bets;
}
