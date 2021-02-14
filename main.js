const photoCollection = [
    {
        src: 'https://picsum.photos/id/0/200/100',
        title: 'foto 0'
    },
    {
        src: 'https://picsum.photos/id/1/200/100',
        title: 'foto 1'
    },
    {
        src: 'https://picsum.photos/id/2/200/100',
        title: 'foto 2'
    },
    {
        src: 'https://picsum.photos/id/3/200/100',
        title: 'foto 3'
    },
    {
        src: 'https://picsum.photos/id/4/200/100',
        title: 'foto 4'
    },
    {
        src: 'https://picsum.photos/id/5/200/100',
        title: 'foto 5'
    },
    {
        src: 'https://picsum.photos/id/6/200/100',
        title: 'foto 6'
    },
    {
        src: 'https://picsum.photos/id/7/200/100',
        title: 'foto 7'
    },
    {
        src: 'https://picsum.photos/id/8/200/100',
        title: 'foto 8'
    },
];

// Enganche con los elementos de la página
const photoSelectorLeft = document.getElementById('photoLeft');
const photoSelectorRight = document.getElementById('photoRight');
const resultado = document.getElementById('resultado');

const setPhoto = function(img, photo) {
    img.src = photo.src;
    img.alt = photo.title;
}
const setNewPhoto = function (img) {
    // Si hacen clic en la foto0 cambio la foto1
    const imgChange = (img.id === 'photoLeft')
        ? photoSelectorRight
        : photoSelectorLeft;

    // Si aún hay fotos en la bolsa
    if (photoCollection.length > 0) {
        setPhoto(imgChange, photoCollection.pop());
    }
    else {
        imgChange.classList.add('descartada');
        resultado.innerHTML = 'Fin te quedas con ' + img.alt;
    }
}
photoSelectorLeft.addEventListener('click', (e) => setNewPhoto(e.target))
photoSelectorRight.addEventListener('click', (e) => setNewPhoto(e.target))

// Asignar las fotos por primera vez
setPhoto(photoSelectorLeft, photoCollection.pop());
setPhoto(photoSelectorRight, photoCollection.pop());


