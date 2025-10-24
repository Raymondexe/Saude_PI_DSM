function abrirNoMaps(lat, lng) {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;

            const mapsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${lat},${lng}&travelmode=driving`;
            window.open(mapsUrl, "_blank");
        }, function (error) {
            alert("Não foi possível obter sua localização.");
        });
    } else {
        alert("Geolocalização não é suportada pelo seu navegador.");
    }
}


const slider = document.querySelector('.ubs-list');
const btnLeft = document.querySelector('.ubs-nav-left');
const btnRight = document.querySelector('.ubs-nav-right');

const scrollAmount = 320; // largura aproximada do card + gap

btnRight.addEventListener('click', () => {
    slider.scrollBy({ left: scrollAmount, behavior: 'smooth' });
});

btnLeft.addEventListener('click', () => {
    slider.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
});