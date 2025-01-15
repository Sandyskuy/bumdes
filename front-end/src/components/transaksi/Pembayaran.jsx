import React, { useEffect, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import axios from "axios";
import "./Pembayaran.css"; // Import file CSS untuk gaya modal

const Pembayaran = () => {
    const location = useLocation();
    const navigate = useNavigate();

    const [data, setData] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [selectedItem, setSelectedItem] = useState(null); // Menyimpan item yang dipilih untuk bayar


    useEffect(() => {
        const token = localStorage.getItem('token');

        const fetchCartItems = async () => {
            try {
                const response = await axios.get(`http://localhost:8080/transaksi/not-complete`, {
                    headers: {
                        "Authorization": `Bearer ${token}`
                    }
                });
                console.log(response.data)
                setData(response.data);
            } catch (error) {

            }
        };
        fetchCartItems();
    }, []);

    const handleBayarClick = (item) => {
        setSelectedItem(item); // Menetapkan item yang dipilih untuk bayar
        setShowModal(true);
    };

    const handleCloseModal = () => {
        setShowModal(false);
        setSelectedItem(null); // Me-reset item yang dipilih
    };

    const handleBayar = () => {
        // Implementasi logika pembayaran
        console.log("Pembayaran berhasil untuk item:", selectedItem);
        handleCloseModal(); // Menutup modal setelah pembayaran berhasil
    };
    return (
        <div className="container mt-3">
            <h2>Pembayaran</h2>
            <h3 style={{ textAlign: "center", marginBottom: 10 }}>Daftar Pesanan Belum Dibayar</h3>
            {data && Array.isArray(data) && data.length > 0 ? (
                data.map((item, index) => (
                    <div key={index} className="pesanan-card">
                        <h3>Kode TR  : {item.id}</h3>
                        <p>Metode Pembayaran : {item.midtrans.payment_type}</p>
                        <p>Status : {item.midtrans.transaction_status}</p>
                        <p>Lakukan pembayaran sebelum : {item.midtrans.expiry_time}</p>
                        <button onClick={() => handleBayarClick(item)}>Bayar</button>
                    </div>
                ))
            ) : (
                <p>Tidak ada pesanan yang belum dibayar.</p>
            )}
            {showModal && (
                <div className="modal">
                    <div className="modal-content">
                        <span className="close" onClick={handleCloseModal}>&times;</span>
                        <h2>Konfirmasi Pembayaran</h2>
                        <p>Kode TR: {selectedItem.id}</p>
                        <p>Metode Pembayaran: {selectedItem.midtrans.payment_type}</p>
                        <p>Status: {selectedItem.status}</p>
                        <p>Bank: {selectedItem.midtrans.va_numbers[0].bank}</p>
                        <p>No rekening: {selectedItem.midtrans.va_numbers[0].va_number}</p>
                        <p>Total: {selectedItem.midtrans.gross_amount}</p>
                    </div>
                </div>
            )}
        </div>
    );
};

export default Pembayaran;
