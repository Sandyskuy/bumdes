import React, { useState } from 'react';
import axios from 'axios';
import { useNavigate } from 'react-router-dom'; // Import useNavigate untuk melakukan navigasi
import './register.css'; // File CSS untuk styling

function RegisterForm() {
  const [formData, setFormData] = useState({
    email: '',
    username: '',
    password: '',
    pass_confirm: ''
  });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');
  const navigate = useNavigate(); // Inisialisasi useNavigate

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setLoading(true);

    axios.post('http://localhost:8080/api/register', formData)
      .then(response => {
        setLoading(false);
        if (response.data.errors) {
          setErrors(response.data.errors);
          setMessage('Harap perbaiki kesalahan di bawah sebelum melanjutkan.');
        } else if (response.data.message) {
          setErrors({});
          setMessage('Pendaftaran berhasil! Silakan masuk ke akun Anda.');
          setFormData({
            email: '',
            username: '',
            password: '',
            pass_confirm: ''
          });
          // Redirect ke halaman login setelah pendaftaran berhasil
          navigate('/login');
        }
      })
      .catch(error => {
        setLoading(false);
        console.error('Error:', error);
        setMessage('Terjadi kesalahan. Silakan coba lagi nanti.');
      });
  };

  return (
    <div className="register-container">
      <div className="register-content">
        <h2>Daftar Akun</h2>
        <form onSubmit={handleSubmit} className="register-form">
          <div className="form-group">
            <input type="email" id="email" name="email" placeholder="Email" value={formData.email} onChange={handleChange} />
            {errors.email && <p className="error">{errors.email}</p>}
          </div>
          <div className="form-group">
            <input type="text" id="username" name="username" placeholder="Nama Pengguna" value={formData.username} onChange={handleChange} />
            {errors.username && <p className="error">{errors.username}</p>}
          </div>
          <div className="form-group">
            <input type="password" id="password" name="password" placeholder="Kata Sandi" value={formData.password} onChange={handleChange} />
            {errors.password && <p className="error">{errors.password}</p>}
          </div>
          <div className="form-group">
            <input type="password" id="pass_confirm" name="pass_confirm" placeholder="Konfirmasi Kata Sandi" value={formData.pass_confirm} onChange={handleChange} />
            {errors.pass_confirm && <p className="error">{errors.pass_confirm}</p>}
          </div>
          <button type="submit" className="btn" disabled={loading}>{loading ? 'Memuat...' : 'Daftar'}</button>
        </form>
        {message && <p className={errors ? 'success' : 'error'}>{message}</p>}
      </div>
    </div>
  );
}

export default RegisterForm;
