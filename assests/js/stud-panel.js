const modal = document.getElementById("uploadModal");
const btn = document.getElementById("openModalBtn");
const span = document.getElementById("closeModalBtn");
const form = document.getElementById("uploadForm"); 


btn.onclick = function() {
    modal.style.display = "flex";
}

span.onclick = function() {
    modal.style.display = "none";
    form.reset(); 
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        form.reset(); 
    }
}