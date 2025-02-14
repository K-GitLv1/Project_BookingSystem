<?php
include 'db/db_connection.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT b.id, b.room_id, b.booker_name, b.phone, 
                   b.start_date, b.end_date, b.start_time, b.end_time, 
                   b.purpose, b.other_details, r.room_name
            FROM bookings b 
            JOIN rooms r ON b.room_id = r.id 
            WHERE b.id = $id";
    
    $result = $conn->query($sql);
    $booking = $result->fetch_assoc();

    echo json_encode($booking);
}
?>
