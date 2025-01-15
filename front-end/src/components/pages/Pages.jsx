import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Home from "../home/Home";
import Footer from "../common/footer/Footer";
import About from "../about/About";
import Pricing from "../pricing/Pricing";
import Blog from "../blog/Blog";
import Services from "../services/Services";
import Contact from "../contact/Contact";
import Detail from "../services/DetailProduk";
import Keranjang from "../transaksi/Keranjang";
import Header from "../common/header/Header";
import Login from "../auth/login/Login";
import Register from "../auth/register/Register";
import Checkout from "../transaksi/Checkout.jsx";
import Sukses from "../transaksi/Sukses";
import Pembayaran from "../transaksi/Pembayaran";
import Selesai from "../transaksi/Selesai";

const Pages = () => {
  return (
    <>
      <Router>
        <Header />
        <Routes>
          <Route exact path="/" element={<Home />} />
          <Route exact path="/about" element={<About />} />
          <Route exact path="/product" element={<Services />} />
          <Route exact path="/blog" element={<Blog />} />
          <Route exact path="/pricing" element={<Pricing />} />
          <Route exact path="/contact" element={<Contact />} />
          <Route exact path="/barang/detail/:id" element={<Detail />} />
          <Route exact path="/keranjang" element={<Keranjang />} />
          <Route exact path="/login" element={<LoginWithoutHeaderFooter />} />
          <Route exact path="/register" element={<RegisterWithoutHeaderFooter />} />
          <Route exact path="/checkout" element={<Checkout />} />
          <Route exact path="/sukses" element={<Sukses />} />
          <Route exact path="/payments" element={<Pembayaran />} />
          <Route exact path="/riwayat" element={<Selesai />} />

        </Routes>
        <Footer />
      </Router>
    </>
  );
};

// Komponen untuk halaman login tanpa header dan footer
const LoginWithoutHeaderFooter = () => {
  return <Login />;
};

// Komponen untuk halaman register tanpa header dan footer
const RegisterWithoutHeaderFooter = () => {
  return <Register />;
};

export default Pages;
