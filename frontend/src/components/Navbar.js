import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import LoginModal from './modals/LoginModal';
import RegisterModal from './modals/RegisterModal';

const Navbar = () => {
    const isAuthenticated = false;
    const [isLoginOpen, setLoginOpen] = useState(false);
    const [isRegisterOpen, setRegisterOpen] = useState(false);

    return (
        <nav className="bg-gray-800 text-white p-4">
            <div className="container mx-auto flex justify-between items-center">
                <h1 className="text-lg font-bold">Web Gallery</h1>
                <div>
                    <Link to="/" className="px-4">Home</Link>
                    {isAuthenticated ? (
                        <>
                            <Link to="/profile" className="px-4">Profile</Link>
                            <Link to="/logout" className="px-4">Logout</Link>
                        </>
                    ) : (
                        <>
                            <button onClick={() => setLoginOpen(true)} className="px-4">Login</button>
                            <button onClick={() => setRegisterOpen(true)} className="px-4">Register</button>
                        </>
                    )}
                </div>
            </div>
            <LoginModal isOpen={isLoginOpen} onRequestClose={() => setLoginOpen(false)} />
            <RegisterModal isOpen={isRegisterOpen} onRequestClose={() => setRegisterOpen(false)} />
        </nav>
    );
};

export default Navbar;
