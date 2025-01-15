// DetailBarang.jsx

import React, { useState, useEffect } from "react";
import { useNavigate, useParams, Link } from "react-router-dom";
import axios from "axios";
import "./Detailproduk.css";

const DetailBarang = () => {
  const { id } = useParams();
  const [barang, setBarang] = useState(null);
  const [loading, setLoading] = useState(true); // State untuk menunjukkan status loading
  const [error, setError] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchBarang = async () => {
      try {
        const response = await axios.get(`http://localhost:8080/barang/detail/${id}`);
        setBarang(response.data);
      } catch (error) {
        setError(error.message);
      } finally {
        setLoading(false); // Setelah data berhasil dimuat atau terjadi kesalahan, atur status loading menjadi false
      }
    };

    fetchBarang();
  }, [id]);

  const addToCart = async () => {
    try {
      const token = localStorage.getItem("token");
      const response = await axios.post(
        "http://localhost:8080/transaksi/Cart/",
        {
          barang_id: id,
          jumlah: quantity
        },
        {
          headers: {
            Authorization: `Bearer ${token}`
          }
        }
      );
      console.log(response.data);
      navigate('/keranjang');
    } catch (error) {
      console.log(error.message);
      setError(error.message);
    }
  };

  // Jika loading, tampilkan animasi loading
  if (loading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading...</p>
      </div>
    );
  }

  // Tampilkan pesan error jika terjadi kesalahan
  if (error) return <p>Error: {error}</p>;

  // Tampilkan detail barang setelah data berhasil dimuat
  return (
    <div className="container-detail mt-3">
      <div className="row">
        {/* Kolom gambar barang */}
        <div className="col-md-6">
          <img src={`http://localhost:8080/uploads/${barang.gambar}`} alt={barang.nama} className="img-fluid" />
        </div>
        {/* Kolom detail barang */}
        <div className="col-md-6">
          <div className="detail-barang">
            <h2 className="detail-nama">{barang.nama}</h2>
            <p className="detail-harga">Rp. {barang.harga}</p>
            <hr className="divider" />
            <p className="detail-deskripsi">{barang.deskripsi}</p>
            <p><strong>Stock:</strong> {barang.stok}</p> {/* Menampilkan informasi stok barang */}
            <div className="mb-3 d-flex align-items-center justify-content-center">
              <div className="quantity-control">
                <button className="quantity-btn" onClick={() => setQuantity(prevQuantity => Math.max(prevQuantity - 1, 1))}>-</button>
                <input type="number" id="quantity" className="form-control quantity-input" min="1" value={quantity} onChange={(e) => setQuantity(e.target.value)} />
                <button className="quantity-btn" onClick={() => setQuantity(prevQuantity => prevQuantity + 1)}>+</button>
              </div>
            </div>
            <button className="btn btn-primary" onClick={addToCart}>Add to Cart</button>
            {error && <p className="text-danger mt-2">{error}</p>}
          </div>
        </div>
      </div>
    </div>
  );
};

export default DetailBarang;
