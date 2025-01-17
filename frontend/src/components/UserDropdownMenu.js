import React from 'react';
import { FaCog, FaSignOutAlt } from 'react-icons/fa';
import { useUser } from '../context/UserContext';

const UserDropdownMenu = ({ isUserDropdownMenuOpen, onSettings, onClose }) => {
  const { logout } = useUser();

  const handleLogout = () => {
    logout();
    onClose();
  };

  const handleSettings = () => {
    onSettings();
    onClose();
  };

  return (
    <div>
      {isUserDropdownMenuOpen && (
        <div className='absolute top-0 -mt-8 left-full ml-2 w-56 bg-gray-200 dark:bg-[#111f4a] dark:text-gray-300 rounded-lg shadow-lg z-50 transition-transform duration-1000 ease-in-out'>
          <div className='p-4'>
            <button
              className='w-full flex items-center p-2 hover:bg-blue-800 rounded'
              onClick={handleSettings}
            >
              <FaCog className='mr-2' /> Settings
            </button>
            <div className='border-t border-gray-400 dark:border-blue-800 my-2'></div>
            <button
              className='w-full flex items-center p-2 hover:bg-blue-800 rounded'
              onClick={handleLogout}
            >
              <FaSignOutAlt className='mr-2' /> Log out
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default UserDropdownMenu;
