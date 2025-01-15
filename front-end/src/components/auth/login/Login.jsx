import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom'; // Import Link from react-router-dom
import axios from 'axios';
import "./login.css";

function Login() {
  const navigate = useNavigate();
  const [email, setEmail] = useState(''); // Mengganti state username menjadi email
  const [password, setPassword] = useState('');
  const [errorMessage, setErrorMessage] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleLogin = async (e) => {
    e.preventDefault();

    if (!email || !password) {
      setErrorMessage('Email dan password harus diisi.'); // Mengubah pesan error
      return;
    }

    setIsLoading(true);

    try {
      const response = await axios.post('http://localhost:8080/api/login', {
        email, // Menggunakan email sebagai username
        password
      });

      if (response.status === 200) {
        localStorage.setItem('token', response.data.token);
        navigate('/');
      } else {
        setErrorMessage(response.data.error);
      }
    } catch (error) {
      console.error('Error:', error);
      setErrorMessage('Terjadi kesalahan saat melakukan login.');
    }

    setIsLoading(false);
  };

  return (
    <div className="container-login">
      <h2>Login</h2>
      {errorMessage && <p className="error-message">{errorMessage}</p>}
      <form onSubmit={handleLogin}>
        <div>
          <label>Email:</label> {/* Mengubah label menjadi Email */}
          <input type="email" value={email} onChange={(e) => setEmail(e.target.value)} /> {/* Mengubah input type menjadi email */}
        </div>
        <div>
          <label>Password:</label>
          <input type="password" value={password} onChange={(e) => setPassword(e.target.value)} />
        </div>
        <button type="submit">Login</button>
      </form>
      <p>Belum punya akun? <Link to="/register">Daftar di sini</Link></p>
    </div>
  );
}

export default Login;
