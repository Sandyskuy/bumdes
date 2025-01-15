import React from "react"
import Heading from "../../common/Heading"
import { location } from "../../data/Data"
import "./style.css"

const PasarSindon = () => {
  return (
    <>
<section className='about'>
        <div className='container flex mtop'>
          <div className='left row'>
            <Heading title='Pasar Sindon' subtitle='CJGC+4PG, Sindon, Sidomulyo, Kec. Wonoasri, Kabupaten Madiun, Jawa Timur 63157' />

            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
            <a href="https://pasarsindon.com/" className="btn2" target="_blank" rel="noopener noreferrer"><button className='btn2'>Kunjungi</button></a>
          </div>
          <div className='right row'>
            <img src='./immio1.png' alt='' />
          </div>
        </div>
      </section>
    </>
  )
}

export default PasarSindon
