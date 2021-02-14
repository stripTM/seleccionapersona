// Enganche con los elementos de la página
const imgLeft = document.getElementById('photoLeft');
const imgRight = document.getElementById('photoRight');
// Controlar el clic en las fotos
imgLeft.addEventListener('click', (e) => setNewPhoto(e.target));
imgRight.addEventListener('click', (e) => setNewPhoto(e.target));
let ready = false;
let timeoutID = null;

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
        timeoutID = setTimeout(load, 5000);
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
    data.append('a', 'pop');
    data.append('n', imgChange.dataset.id);
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

load();
