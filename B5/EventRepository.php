<?php
require_once 'db.php';

function getAllEvents() {
    $stmt = db()->prepare("SELECT * FROM event ORDER BY start_time DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getEventById($id) {
    $stmt = db()->prepare("SELECT * FROM event WHERE event_id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch();
}

function createEvent($data) {
    $sql = "INSERT INTO event (event_id, event_name, start_time, location, slots, description, status) 
            VALUES (:id, :name, :time, :loc, :slots, :desc, 0)";
    $stmt = db()->prepare($sql);
    return $stmt->execute([
        'id'    => 'EV' . rand(100, 999),
        'name'  => trim($data['event_name']),
        'time'  => $data['start_time'],
        'loc'   => trim($data['location']),
        'slots' => (int)$data['slots'],
        'desc'  => trim($data['description'])
    ]);
}

function updateEvent($id, $data) {
    $sql = "UPDATE event SET event_name = :name, start_time = :time, location = :loc, slots = :slots, description = :desc WHERE event_id = :id";
    $stmt = db()->prepare($sql);
    return $stmt->execute([
        'id'    => $id,
        'name'  => trim($data['event_name']),
        'time'  => $data['start_time'],
        'loc'   => trim($data['location']),
        'slots' => (int)$data['slots'],
        'desc'  => trim($data['description'])
    ]);
}

function deleteEvent($id) {
    $stmt = db()->prepare("DELETE FROM event WHERE event_id = :id");
    return $stmt->execute(['id' => $id]);
}
?>