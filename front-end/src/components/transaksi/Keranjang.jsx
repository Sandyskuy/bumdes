import React, { useState, useEffect } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom"; // Menggunakan useNavigate untuk mengarahkan pengguna

const Keranjang = () => {
  const [cartItems, setCartItems] = useState([]);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const navigate = useNavigate(); // Inisialisasi useNavigate

  useEffect(() => {

    const token = localStorage.getItem('token')

    const fetchCartItems = async () => {
      console.log(token)
      try {
        const response = await axios.get(`http://localhost:8080/transaksi/Cart`, {
          headers: {
            "Authorization": `Bearer ${token}`
          }
        });
        setCartItems(response.data);
        let total = 0
        response.data.map((item, index) => {
          total += parseInt(item.harga)
        });
        setTotal(total);
      } catch (error) {
        setError(error.message);
      } finally {
        setLoading(false);
      }
    };
    fetchCartItems();
  }, []);



  const handleCheckout = () => {
    // Mengarahkan pengguna ke halaman checkout
    navigate("/checkout", { state: { cartItems, total } });
  };

  if (loading) return <p>Loading...</p>;
  if (error) return <p>Error: {error}</p>;

  return (
    <div className="container mt-3">
      <h2>Keranjang Belanja</h2>
      {cartItems.length === 0 ? (
        <p>Cart empty</p> // Pesan "Cart empty" untuk keranjang kosong
      ) : (
        <div>
          {cartItems.map((item) => (
            <div key={item.barang_id} className="card mb-3">
              <div className="row g-0">
                <div className="col-md-4">
                  <img
                    src={`http://localhost:8080/uploads/${item.gambar_barang}`}
                    alt={item.nama_barang}
                    className="img-fluid"
                  />
                </div>
                <div className="col-md-8">
                  <div className="card-body">
                    <h5 className="card-title">{item.nama_barang}</h5>
                    <p className="card-text">Harga: Rp. {item.harga}</p>
                    <p className="card-text">Jumlah: {item.jumlah}</p>
                  </div>
                </div>
              </div>
            </div>
          ))}
          <div className="text-end">
            <h5>Total: Rp. {total}</h5>
            {/* Tambahkan onClick untuk menangani klik tombol Checkout */}
            <button className="btn btn-primary" onClick={handleCheckout}>
              Checkout
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default Keranjang;
