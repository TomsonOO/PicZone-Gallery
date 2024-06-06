import React, { useState } from 'react';
import { FaCog, FaSignOutAlt } from 'react-icons/fa';
import { useUser } from '../context/UserContext';
import defaultProfileImage from '../assets/default_profile_image.png';

const UserDropdown = () => {
    const { logout } = useUser();
    const [isOpen, setIsOpen] = useState(false);

    const toggleDropdown = () => {
        setIsOpen(!isOpen);
    };

    const handleLogout = () => {
        logout();
        setIsOpen(false);
    };

    return (
        <div className="relative flex flex-col items-center">
            <button
                onClick={toggleDropdown}
                className="focus:outline-none transition-transform transform hover:scale-105"
            >
                <img
                    src={defaultProfileImage}
                    alt="Profile"
                    className="w-24 h-24 rounded-full border-2 border-gray-300 dark:border-gray-600"
                />
            </button>
            {isOpen && (
                <div className="absolute top-0 -mt-8 left-full ml-2 w-56 bg-gray-200 dark:bg-[#111f4a] dark:text-gray-300 rounded-lg shadow-lg z-50 transition-transform duration-1000 ease-in-out">
                    <div className="p-4">
                        <button
                            className="w-full flex items-center p-2 hover:bg-blue-800 rounded"
                            onClick={() => console.log('Settings clicked')}
                        >
                            <FaCog className="mr-2" /> Settings
                        </button>
                        <div className="border-t border-gray-400 dark:border-blue-800 my-2"></div>
                        <button
                            className="w-full flex items-center p-2 hover:bg-blue-800 rounded"
                            onClick={handleLogout}
                        >
                            <FaSignOutAlt className="mr-2" /> Log out
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default UserDropdown;
