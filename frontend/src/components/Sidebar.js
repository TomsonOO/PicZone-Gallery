import React, { useState, useEffect } from 'react';
import {
  FaMoon,
  FaSun,
  FaSignInAlt,
  FaUserPlus,
  FaUpload,
} from 'react-icons/fa';
import LoginModal from './modals/LoginModal';
import RegisterModal from './modals/RegisterModal';
import SettingsModal from './modals/SettingsModal';
import { useUser } from '../context/UserContext';
import UserDropdownMenu from './UserDropdownMenu';
import { getProfileImage } from '../services/userProfileService';
import logo from '../assets/images/logo.png';
import logoDarkMode from '../assets/images/logo_darkmode.png';
import { Link } from 'react-router-dom';
import UploadImageModal from './modals/UploadImageModal';

const Sidebar = ({ onCategoryReset }) => {
  const [darkMode, setDarkMode] = useState(true);
  const { isUserLoggedIn, state } = useUser();
  const [isLoginOpen, setLoginOpen] = useState(false);
  const [isRegisterOpen, setRegisterOpen] = useState(false);
  const [isSettingsOpen, setSettingsOpen] = useState(false);
  const [isUploadImageOpen, setUploadImageOpen] = useState(false);
  const [profileImage, setProfileImage] = useState({ presignedUrl: '' });
  const [isUserDropdownMenuOpen, setUserDropdownMenuOpen] = useState(false);

  useEffect(() => {
    const root = window.document.documentElement;
    root.classList.remove(darkMode ? 'light' : 'dark');
    root.classList.add(darkMode ? 'dark' : 'light');
  }, [darkMode]);

  useEffect(() => {
    if (state.user && state.user.profileImageId) {
      getProfileImage(state.user.profileImageId)
        .then((imageData) => setProfileImage(imageData))
        .catch((error) => {
          console.error('Failed to fetch profile image:', error);
        });
    }
  }, [state.user]);

  const toggleDarkMode = () => {
    setDarkMode(!darkMode);
  };

  const toggleDropdownMenu = () => {
    setUserDropdownMenuOpen(!isUserDropdownMenuOpen);
  };

  const openSettingsModal = () => {
    setSettingsOpen(true);
  };

  const openUploadImageModal = () => {
    setUploadImageOpen(true);
  };

  const handleCloseDropdownMenu = () => {
    setUserDropdownMenuOpen(false);
  };

  return (
    <aside className='w-48 p-4 sticky top-0 h-screen flex flex-col items-center justify-between bg-gray-200 dark:bg-gradient-to-b dark:from-[#0a152e] dark:to-[#152969] z-50'>
      <div className='w-full flex flex-col items-center'>
        <Link onClick={() => onCategoryReset()} to='/gallery' className='mb-6'>
          <img
            src={darkMode ? logoDarkMode : logo}
            alt='PicZone Logo'
            className='h-24'
          />
        </Link>
        <button
          onClick={toggleDarkMode}
          className='mb-4 w-full p-2 rounded hover:bg-gray-300 dark:hover:bg-sky-900 dark:text-gray-300 flex items-center justify-center'
        >
          {darkMode ? (
            <FaSun className='inline mr-2' />
          ) : (
            <FaMoon className='inline mr-2' />
          )}
          {darkMode ? 'Light Mode' : 'Dark Mode'}
        </button>
        <button
          onClick={isUserLoggedIn ? openUploadImageModal : null}
          className={`mb-4 w-full p-2 rounded transition duration-300
                  'hover:bg-gray-300 dark:hover:bg-sky-900 dark:text-gray-300 flex items-center justify-center'
              ${!isUserLoggedIn ? 'opacity-50' : ''}
              `}
        >
          <FaUpload className='inline mr-1' />
          Upload an Image
        </button>
      </div>
      <div className='w-full flex flex-col items-center'>
        {state.user ? (
          <div className='relative flex flex-col items-center'>
            <button
              onClick={toggleDropdownMenu}
              className='focus:outline-none transition-transform transform hover:scale-105'
            >
              <img
                src={profileImage.presignedUrl}
                alt='Profile'
                className='w-24 h-24 rounded-full border-2 border-gray-300 dark:border-gray-600'
              />
            </button>
            <UserDropdownMenu
              isUserDropdownMenuOpen={isUserDropdownMenuOpen}
              onCategoryReset={onCategoryReset}
              onSettings={openSettingsModal}
              onClose={handleCloseDropdownMenu}
            />
          </div>
        ) : (
          <div className='w-full flex flex-col items-center'>
            <div className='mb-2 w-full'>
              <button
                onClick={() => setLoginOpen(true)}
                className='w-full p-2 rounded hover:bg-gray-300 dark:hover:bg-sky-900 dark:text-gray-300 flex items-center justify-center'
              >
                <FaSignInAlt className='inline mr-2' />
                Login
              </button>
              <LoginModal
                isLoginOpen={isLoginOpen}
                onRequestClose={() => setLoginOpen(false)}
              />
            </div>
            <div className='w-full'>
              <button
                onClick={() => setRegisterOpen(true)}
                className='w-full p-2 rounded hover:bg-gray-300 dark:hover:bg-sky-900 dark:text-gray-300 flex items-center justify-center'
              >
                <FaUserPlus className='inline mr-2' />
                Register
              </button>
              <RegisterModal
                isRegisterOpen={isRegisterOpen}
                onRequestClose={() => setRegisterOpen(false)}
              />
            </div>
          </div>
        )}
      </div>
      <SettingsModal
        isSettingsOpen={isSettingsOpen}
        onRequestClose={() => setSettingsOpen(false)}
      />
      <UploadImageModal
        isOpen={isUploadImageOpen}
        onRequestClose={() => setUploadImageOpen(false)}
      />
    </aside>
  );
};

export default Sidebar;
