const socket = new WebSocket("ws://localhost:8080");

socket.onopen = () => {
  socket.send(JSON.stringify({
    type: "join",
    order_id: 12,
    role: "driver"
  }));

  navigator.geolocation.watchPosition(pos => {
    socket.send(JSON.stringify({
      type: "driver_location",
      lat: pos.coords.latitude,
      lng: pos.coords.longitude
    }));
  });
};
