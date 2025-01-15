import React, { useEffect, useState } from "react"
import "./header.css"
import { nav, navLogined } from "../../data/Data"
import { Link, useNavigate } from "react-router-dom"


const Header = () => {
  const [navList, setNavList] = useState(false)
  const [login, setLogin] = useState(false)
  const navigate = useNavigate();

  useEffect(() => {
    const token = localStorage.getItem('token')
    if (token != null) {
      setLogin(true)
    } else {
      setLogin(false)
    }
  }, []);


  const handleLogout = () => {
    localStorage.clear();
    navigate("/login")
  }

  return (
    <>
      <header>
        <div className='container flex'>
          <div className='logo'>
            <img src='./images/logo1.png' alt='' />
          </div>
          <div className='nav'>
            <ul className={navList ? "small" : "flex"}>
              {
                login ? (
                  navLogined.map((list, index) => (
                    <li key={index}>
                      <Link to={list.path}>{list.text}</Link>
                    </li>
                  ))
                ) : (
                  nav.map((list, index) => (
                    <li key={index}>
                      <Link to={list.path}>{list.text}</Link>
                    </li>
                  ))
                )
              }

            </ul>
          </div>
          <div className='button flex'>
            {login ? (
              <button className='btn1' onClick={handleLogout}>
                <i className='fa fa-sign-out'></i> Logout
              </button>
            ) : (
              <Link to="/login">
                <button className='btn1'>
                  <i className='fa fa-sign-in'></i> Sign In
                </button>
              </Link>
            )}
          </div>
          <div className='toggle'>
            <button onClick={() => setNavList(!navList)}>{navList ? <i className='fa fa-times'></i> : <i className='fa fa-bars'></i>}</button>
          </div>
        </div>
      </header>
    </>
  )
}

export default Header
