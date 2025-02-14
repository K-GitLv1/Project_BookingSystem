function openSidebar() {
    document.getElementById("sidebar").style.left = "0";
}

function closeSidebar() {
    document.getElementById("sidebar").style.left = "-250px";
}
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById("sidebar");
    const menuIcon = document.querySelector('.menu-icon');
    const closeBtn = document.querySelector('.close-btn');

    // ถ้าคลิกที่ sidebar หรือปุ่มปิด ก็ไม่ต้องทำอะไร
    if (sidebar.contains(event.target) || closeBtn.contains(event.target)) {
        return;
    }

    // ถ้าคลิกนอก sidebar หรือ hamburger icon ให้ปิด sidebar
    if (!menuIcon.contains(event.target)) {
        sidebar.style.left = "-250px"; // ปิด sidebar
    }
});
// ทำให้เมนู dropdown เปิด/ปิดเมื่อคลิก
document.querySelectorAll('.dropdown-toggle').forEach(function(dropdownToggle) {
    dropdownToggle.addEventListener('click', function(event) {
        var dropdown = this.closest('.dropdown');
        dropdown.classList.toggle('open');  // เปิดหรือปิดเมนู dropdown
        event.stopPropagation();  // หยุดการแพร่กระจายของคลิก
    });
});

// ปิดเมนู dropdown เมื่อคลิกที่ส่วนอื่นของหน้า
document.addEventListener('click', function(event) {
    if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.open').forEach(function(dropdown) {
            dropdown.classList.remove('open');
        });
    }
});
