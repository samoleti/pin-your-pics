const map = L.map('map');
map.fitWorld().zoomIn();

const osmTileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
})
osmTileLayer.addTo(map);

const collection = document.getElementsByClassName("current-pic");


while (collection.length > 0) {
    let lat = collection[0].children[1].textContent,
        lng = collection[0].children[2].textContent;

    const marker = L.marker([lat, lng]);
    marker.addTo(map);

    const content = collection[0].children[0];
    marker.bindPopup(content);

    collection[0].remove();
}

map.on('click', function(e) {
    const popLocation = e.latlng;
    document.cookie = "lat=" + popLocation.lat;
    document.cookie = "lng=" + popLocation.lng;

    const content = '<button class="upload-button-map" role="button" onclick="openNavMap()">Upload pic</button>'

    const popup = L.popup()
        .setLatLng(popLocation)
        .setContent(content)
    popup.openOn(map);
});