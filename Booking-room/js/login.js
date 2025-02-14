function showModal(message) {
    document.getElementById("modal-message").innerText = message;
    document.getElementById("popup-modal").style.display = "block";
}

    function closeModal() {
        document.getElementById("popup-modal").style.display = "none";
    }
