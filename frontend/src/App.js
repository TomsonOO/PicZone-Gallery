import React from 'react';
import { BrowserRouter as Router, Route, Routes } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import WelcomePage from './pages/WelcomePage';
import HomePage from './pages/HomePage';
import BookZonePage from './pages/BookZonePage';

const App = () => {
  return (
    <>
      <ToastContainer />
      <Router>
        <Routes>
          <Route path='/' element={<WelcomePage />} />
          <Route path='/gallery' element={<HomePage />} />
          <Route path='/bookzone' element={<BookZonePage />} />
        </Routes>
      </Router>
    </>
  );
};

export default App;
