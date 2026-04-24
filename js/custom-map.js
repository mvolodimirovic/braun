// Робимо map глобальним
window.map = null;

window.initMap = function () {
  // Ініціалізуємо глобальну карту
  window.map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 48.3794, lng: 31.1656 },
    zoom: 6,
  });

  const customIcon = {
    url: "/wp-content/themes/braun_2025/img/services/point.png",
    scaledSize: new google.maps.Size(40, 40),
  };

  const infoWindow = new google.maps.InfoWindow();

  if (typeof acfMapData !== 'undefined' && Array.isArray(acfMapData.locations)) {
    acfMapData.locations.forEach(loc => {
      const marker = new google.maps.Marker({
        position: { lat: loc.lat, lng: loc.lng },
        map: window.map,
        icon: customIcon,
      });

      marker.addListener("mouseover", () => {
        infoWindow.setContent(loc.content);
        infoWindow.open(window.map, marker);
      });

      // Якщо треба: закривати по mouseout
      // marker.addListener("mouseout", () => {
      //   infoWindow.close();
      // });
    });
  }
};
