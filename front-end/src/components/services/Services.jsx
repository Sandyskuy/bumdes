import React, { useState, useEffect } from "react";
import axios from "axios"; // Anda dapat menggunakan Axios untuk melakukan permintaan HTTP
import img from "../images/services1.png"
import Back from "../common/Back";
import "./Service.css";
import "./Servicecard.css";
import { Link } from "react-router-dom";

const Product = () => {
  // State untuk menyimpan daftar barang
  const [products, setProducts] = useState([]);

  // Fungsi untuk mengambil data barang dari backend
  const fetchProducts = async () => {
    try {
      // Lakukan permintaan GET ke endpoint backend
      const response = await axios.get("http://localhost:8080/barang");
      // Setel data barang yang diterima ke dalam state
      setProducts(response.data);
    } catch (error) {
      console.error("Error fetching products:", error);
    }
  };

  // Panggil fungsi fetchProducts saat komponen dipasang
  useEffect(() => {
    fetchProducts();
  }, []);

  return (
    <>
      <section className="services mb">
        <Back name="Products" title="Products - All Products" cover={img} />
        <div className="service container">
          {/* Map setiap barang ke dalam kartu */}
          {products.map((product) => (
            <div key={product.id} className="card" style={{ width: "18rem" }}>
              {/* Gunakan URL gambar produk sebagai sumber gambar */}
              <img
                src={"http://localhost:8080/uploads/" + product.gambar}
                className="card-img-top"
                alt={product.nama}
              />
              <div className="card-body">
                {/* Tampilkan nama barang */}
                <h5 className="card-title">{product.nama}</h5>
                {/* Tampilkan harga barang */}
                <p className="card-text">Rp. {product.harga}</p>
                {/* Tambahkan tombol untuk navigasi ke detail barang */}
                <Link to={`/barang/detail/${product.id}`} className="btn btn-primary">
                  View Details
                </Link>
              </div>
            </div>
          ))}
        </div>
      </section>
    </>
  );
};

export default Product;
