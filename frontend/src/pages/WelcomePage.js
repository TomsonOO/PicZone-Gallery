import React from 'react';
import { Link } from 'react-router-dom';

const WelcomePage = () => {
  return (
    <div className='h-screen flex flex-col items-center justify-center bg-gradient-to-b from-cyan-500 to-blue-800'>
      <h1 className='text-6xl text-white font-bold mb-4'>
        Welcome to PicZone Gallery
      </h1>
      <p className='text-white text-lg'>Discover AI generated images.</p>
      <Link
        to='/gallery'
        className='mt-8 bg-white text-purple-800 px-6 py-2 rounded-full shadow-lg hover:bg-gray-300 transition duration-300'
      >
        Explore Gallery
      </Link>
    </div>
  );
};

export default WelcomePage;
