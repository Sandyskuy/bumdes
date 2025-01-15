import React, { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import axios from "axios";
import "./Pembayaran.css"; // Import file CSS untuk gaya modal

const Gopay = ({ imageUrl }) => {

    return (
        <div className="container mt-3">
            <h2>Pembayaran Qris</h2>
            <h3 style={{ textAlign: "center", marginBottom: 10 }}>Silahkan Scan Gambar DIbawah</h3>
            <p>Gambr ini hanya bisa diakses satu kali</p>
        </div>
    );
};

export default Gopay;
