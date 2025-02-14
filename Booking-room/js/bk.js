function openRoomDetails(roomId) {
    // เปลี่ยน URL ไปยังหน้ารายละเอียดห้อง พร้อมส่งค่า room_id ไป
    window.location.href = `room_detail.php?room_id=${roomId}`;
}
