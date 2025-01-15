import React, { useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import axios from "axios";
import Gopay from "./Gopay";

const Checkout = () => {
  const location = useLocation();
  const navigate = useNavigate();

  const [cartItems, setCartItems] = useState(location.state.cartItems);
  const [total, setTotal] = useState(location.state.total);
  const [showModal, setShowModal] = useState(false);
  const [gopay, setGopay] = useState(false)
  const [imageUrl, setImageUrl] = useState(""); // State untuk menyimpan URL gambar

  const handleBackToCart = () => {
    navigate("/keranjang");
  };

  const handleConfirmOrder = () => {
    setShowModal(true);
  };

  const handleModalClose = () => {
    setShowModal(false);
  };

  const handlePaymentMethodSelect = async (paymentType) => {

    let cartItemsRequest = [];
    cartItems.forEach((item, index) => {

      let itemName = item.nama_barang;
      let itemQuantity = item.jumlah ?? 1;

      let newItem = {
        name: itemName,
        barang_id: item.barang_id,
        quantity: itemQuantity
      };
      // Masukkan newItem ke dalam cartItemsRequest
      cartItemsRequest.push(newItem);
    });

    try {
      const token = localStorage.getItem("token");
      const response = await axios.post("http://localhost:8080/transaksi/checkout", {
        order_details: cartItemsRequest,
        payment_methode: paymentType
      },
        {
          headers: {
            Authorization: `Bearer ${token}`
          }
        });
      // Handle respons dari server jika diperlukan
      console.log("Order confirmed:", response.data);
      if (response.data.payments.payment_type == 'gopay') {
        console.log(response.data.payments.actions[0].url)
        setImageUrl(response.data.payments.actions[0].url)
        setGopay(true)
        setShowModal(false)
      } else {
        navigate("/sukses");
      }
    } catch (error) {
      // Handle kesalahan jika terjadi
      console.error("Error confirming order:", error);
      // Tampilkan pesan kesalahan kepada pengguna jika diperlukan
    }
  };

  return (
    <div className="container mt-3">
      <h2>Checkout</h2>
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
          <button className="btn btn-primary me-2 mr-2" onClick={handleBackToCart}>
            Kembali ke Keranjang
          </button>
          <button className="btn btn-success" onClick={handleConfirmOrder}>
            Konfirmasi Pesanan
          </button>
        </div>
      </div>
      {showModal && (
        <div className="modal">
          <div className="modal-content">
            <span className="close" onClick={handleModalClose}>&times;</span>
            <h2>Pilih Metode Pembayaran</h2>
            <button onClick={() => handlePaymentMethodSelect("bank_transfer")}>Transfer Bank</button>
            <button onClick={() => handlePaymentMethodSelect("gopay")}>Gopay</button>
          </div>
        </div>
      )}
      {/* Render Gopay component if gopay is true and imageUrl is not empty */}
      {gopay && imageUrl && <Gopay imageUrl={imageUrl} />}
      {gopay && <img src={imageUrl} alt="" />}
    </div>
  );
};


export default Checkout;
