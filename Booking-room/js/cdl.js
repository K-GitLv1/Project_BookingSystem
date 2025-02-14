const days = ["อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์"];
const months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];

const now = new Date();
const day = days[now.getDay()];
const date = now.getDate();
const month = months[now.getMonth()];
const year = now.getFullYear() + 543; // แปลงปี ค.ศ. เป็น พ.ศ.

document.getElementById("dateDisplay").innerText = `วันนี้ ${day} ที่ ${date} ${month}, ${year}`;
