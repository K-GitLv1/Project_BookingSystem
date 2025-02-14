document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.delete-checkbox');

    // Select/Deselect all checkboxes
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // เมื่อมีการส่งฟอร์ม
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    bulkDeleteForm.addEventListener('submit', function(e) {
        e.preventDefault(); // ป้องกันการส่งฟอร์มทันที

        // เก็บค่า ID ที่เลือก
        const selectedIds = Array.from(checkboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedIds.length === 0) {
            alert('กรุณาเลือกห้องประชุมที่ต้องการลบ');
            return;
        }

        // ส่งค่าไปยัง PHP ผ่าน POST
        fetch('bulk_delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('ลบห้องประชุมสำเร็จ');
                location.reload(); // โหลดหน้าใหม่
            } else {
                alert('เกิดข้อผิดพลาด: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
