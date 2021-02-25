// Enganche con los elementos de la página
const imgLeft = document.getElementById('photoLeft');
const imgRight = document.getElementById('photoRight');
const admin = document.getElementById('admin');
const resetButton = admin.querySelector("input[value=reset]");
const undoButton = admin.querySelector("input[value=undo]");
const closeButton = admin.querySelector(".close");

// Cargar las fotos del servidor
const load = function () {
    ready = false;
    fetch('api.php').then(function (response) {
        // Convert to JSON
        return response.json();
    }).then(function (res) {
        // Asignar las fotos por primera vez
        setPhoto(imgLeft, res.photoLeft);
        setPhoto(imgRight, res.photoRight);
        ready = true;
        if (res.refreshTime) {
            timeoutID = setTimeout(load, Number(res.refreshTime));
        }
    });
}

const setPhoto = function (img, data) {
    if(data){
        img.src = data.src;
        img.alt = data.title;
        img.dataset.id = data.id;
        img.classList.remove('descartada');
    }
    else {
        img.classList.add('descartada');
    }
}
const setNewPhoto = function (img) {
    if (!ready) return;

    // Si hacen clic en la izquierda cambio la de la derecha
    const imgChange = (img.id === 'photoLeft')
        ? imgRight
        : imgLeft;

    // request options
    const data = new URLSearchParams();
    data.append('a', 'delete');
    data.append('n', imgChange.dataset.id);
    data.append('position', imgChange.id);
    const options = {
        method: 'POST',
        body: data
    }
    // send POST request
    fetch('api.php', options)
        .then(res => res.json())
        .then(res => {
            setPhoto(imgLeft, res.photoLeft);
            setPhoto(imgRight, res.photoRight);
        });
}

const reset = function (e) {
    // request options
    const data = new URLSearchParams();
    data.append('a', 'reset');
    const options = {
        method: 'POST',
        body: data
    }
    // send POST request
    fetch('api.php', options)
        .then(res => res.json())
        .then(res => {
            setPhoto(imgLeft, res.photoLeft);
            setPhoto(imgRight, res.photoRight);
        });
    e.preventDefault();
}

const undo = function (e) {
    // request options
    const data = new URLSearchParams();
    data.append('a', 'undo');
    const options = {
        method: 'POST',
        body: data
    }
    // send POST request
    fetch('api.php', options)
        .then(res => res.json())
        .then(res => {
            setPhoto(imgLeft, res.photoLeft);
            setPhoto(imgRight, res.photoRight);
        });
    e.preventDefault();
}

// Controlar el clic en las fotos
imgLeft.addEventListener('click', (e) => setNewPhoto(e.target));
imgRight.addEventListener('click', (e) => setNewPhoto(e.target));
let ready = false;
let timeoutID = null;

// Formulario de administración
admin.addEventListener('click', (e) => e.target.classList.toggle('active'));
admin.addEventListener('submit', (e) => e.preventDefault());
closeButton.addEventListener('click', (e) => admin.classList.remove('active'));
resetButton.addEventListener('click', reset);
undoButton.addEventListener('click', undo);


load();
