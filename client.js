const socket = new WebSocket("ws://localhost:8080");

socket.onopen = () => {
  socket.send(JSON.stringify({
    type: "join",
    order_id: 12,
    role: "client"
  }));
};

socket.onmessage = (event) => {
  const data = JSON.parse(event.data);
  console.log("Position livreur :", data.lat, data.lng);
};
