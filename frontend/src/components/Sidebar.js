import React, { useState, useEffect } from 'react';
import { FaMoon, FaSun, FaSignInAlt, FaUserPlus } from 'react-icons/fa';
import LoginModal from './modals/LoginModal';
import RegisterModal from './modals/RegisterModal';

const Sidebar = () => {
    const [darkMode, setDarkMode] = useState(true);

    useEffect(() => {
        const root = window.document.documentElement;
        root.classList.remove(darkMode ? 'light' : 'dark');
        root.classList.add(darkMode ? 'dark' : 'light');
    }, [darkMode]);

    const [isLoginOpen, setLoginOpen] = useState(false);
    const [isRegisterOpen, setRegisterOpen] = useState(false);

    const toggleDarkMode = () => {
        setDarkMode(!darkMode);
    };

    return (
        <aside
            className="w-48 p-4 sticky top-0 h-screen flex flex-col justify-between bg-gray-200 dark:bg-gradient-to-b dark:from-[#0a152e] dark:to-[#152969]">

            <div>
                <button onClick={toggleDarkMode}
                        className="mb-4 w-full p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-700 dark:text-gray-300 flex items-center justify-center">
                    {darkMode ? <FaSun className="inline mr-2"/> : <FaMoon className="inline mr-2"/>}
                    {darkMode ? 'Light Mode' : 'Dark Mode'}
                </button>
            </div>
            <div>
                <div className="mb-2">
                    <button onClick={() => setLoginOpen(true)}
                            className="w-full p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 dark:text-gray-300  flex items-center justify-start">
                        <FaSignInAlt className="inline mr-2"/>Login
                    </button>
                    <LoginModal isOpen={isLoginOpen} onRequestClose={() => setLoginOpen(false)}/>
                </div>
                <div>
                    <button onClick={() => setRegisterOpen(true)}
                            className="w-full p-2 rounded hover:bg-gray-300 dark:hover:bg-gray-600 dark:text-gray-300  flex items-center justify-start">
                        <FaUserPlus className="inline mr-2"/>Register
                    </button>
                    <RegisterModal isOpen={isRegisterOpen} onRequestClose={() => setRegisterOpen(false)}/>
                </div>
            </div>
        </aside>
    );
};

export default Sidebar;
