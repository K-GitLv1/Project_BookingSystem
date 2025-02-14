function deleteBookings() {
    const selectedBookings = [];
    document.querySelectorAll('.booking-checkbox:checked').forEach(checkbox => {
        selectedBookings.push(checkbox.value);
    });

    if (selectedBookings.length > 0) {
        if (confirm('คุณต้องการลบการจองที่เลือกหรือไม่?')) {
            fetch('delete_bookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    booking_ids: JSON.stringify(selectedBookings)
                })
            })
            .then(response => response.json()) // แปลง response เป็น JSON
            .then(data => {
                alert(data.message);
                if (data.status === "success") {
                    location.reload(); // รีเฟรชหน้าเมื่อสำเร็จ
                }
            })
            .catch(error => {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                console.error(error);
            });
        }
    } else {
        alert('กรุณาเลือกการจองที่ต้องการลบ');
    }
}
