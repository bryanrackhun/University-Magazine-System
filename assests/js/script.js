//nav toggle
document.addEventListener("DOMContentLoaded", function () {
    const menuicn = document.querySelector(".menuicn");
    const nav = document.querySelector(".navcontainer");


    if (menuicn && nav) {
        menuicn.addEventListener("click", () => {
            nav.classList.toggle("navclose");
        });
    }

//student upload
    const uploadModal = document.getElementById("uploadModal");
    const openUploadBtn = document.getElementById("openModalBtn");
    const closeUploadBtn = document.getElementById("closeModalBtn");
    const uploadForm = document.getElementById("uploadForm");

    // Only run if the upload button exists
    if (openUploadBtn && uploadModal) {
        openUploadBtn.onclick = function () {
            uploadModal.style.display = "flex";
        };
    }

    if (closeUploadBtn && uploadModal) {
        closeUploadBtn.onclick = function () {
            uploadModal.style.display = "none";
            if (uploadForm) uploadForm.reset();
        };
    }

//preview 
    const previewModal = document.getElementById("documentPreviewModal");
    const closePreviewBtn = document.getElementById("closePreviewBtn");
    const previewButtons = document.querySelectorAll(".open-preview-btn");

    if (previewButtons.length > 0 && previewModal) {
        previewButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                previewModal.style.display = "flex";
            });
        });
    }

    if (closePreviewBtn && previewModal) {
        closePreviewBtn.onclick = function () {
            previewModal.style.display = "none";
        };
    }

    window.addEventListener('click', function (event) {
        if (uploadModal && event.target === uploadModal) {
            uploadModal.style.display = "none";
            if (uploadForm) uploadForm.reset();
        }
        if (previewModal && event.target === previewModal) {
            previewModal.style.display = "none";
        }
    });

});