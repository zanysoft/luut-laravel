/* eslint-disable import/no-extraneous-dependencies */
import * as React from "react";
import { MapContainer, Marker, Popup, TileLayer } from "react-leaflet";

export default function Map({ lat, long, zoom, title }) {
  const position = [lat, long];
  return (
    <MapContainer center={position} zoom={zoom} scrollWheelZoom={false}>
      <TileLayer
        attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      />
      <Marker position={position}>
        <Popup>
          {title || ""}
        </Popup>
      </Marker>
    </MapContainer>
  );
}
