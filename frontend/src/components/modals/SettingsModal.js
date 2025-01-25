import React, { useState, useEffect } from 'react';
import Modal from 'react-modal';
import { IoMdClose } from 'react-icons/io';
import { toast } from 'react-toastify';
import { useUser } from '../../context/UserContext';
import { getUserProfile, updateUserProfile } from '../../services/userProfileService';

Modal.setAppElement('#root');

export default function SettingsModal({ isSettingsOpen, onRequestClose }) {
  const { state, updateUser } = useUser();
  const [opacity, setOpacity] = useState(false);
  const [username, setUsername] = useState('');
  const [email, setEmail] = useState('');
  const [biography, setBiography] = useState('');
  const [isProfilePublic, setIsProfilePublic] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    async function fetchProfile() {
      try {
        const data = await getUserProfile(state.token);
        setUsername(data.username || '');
        setEmail(data.email || '');
        setBiography(data.biography || '');
        setIsProfilePublic(data.isProfilePublic || false);
      } catch (err) {
        setError(err.message);
      }
    }
    if (isSettingsOpen) {
      setError('');
      fetchProfile();
      setTimeout(() => setOpacity(true), 10);
    }
  }, [isSettingsOpen, state.token]);

  async function handleUpdate(e) {
    e.preventDefault();
    setLoading(true);
    setError('');
    const formData = {
      username,
      email,
      biography,
      isProfilePublic,
    };
    try {
      const data = await updateUserProfile(state.token, formData);
      setLoading(false);
      toast.success('Your settings have been successfully updated!', {
        position: 'top-right',
        autoClose: 5000,
        className: 'custom-toast custom-toast-success',
      });
      updateUser(data);
      handleClose();
    } catch (err) {
      setLoading(false);
      setError(err.message);
    }
  }

  function handleAfterOpen() {
    setOpacity(true);
  }

  function handleAfterClose() {
    setOpacity(false);
  }

  function handleClose() {
    setOpacity(false);
    setTimeout(() => {
      onRequestClose();
      setError('');
    }, 300);
  }

  return (
      <Modal
          isOpen={isSettingsOpen}
          onRequestClose={handleClose}
          className={`fixed inset-0 z-50 transition-opacity duration-300 ${
              opacity ? 'opacity-100' : 'opacity-0'
          }`}
          overlayClassName="fixed inset-0 bg-black bg-opacity-70 overflow-y-auto h-full w-full transition-opacity duration-300"
          onAfterOpen={handleAfterOpen}
          onAfterClose={handleAfterClose}
          contentLabel="Settings"
      >
        <div className="flex justify-center items-center min-h-screen p-4 sm:p-6 md:p-8 lg:p-10">
          <div
              className={`relative p-10 pt-12 rounded-lg shadow-lg max-w-2xl w-full transform-gpu transition-transform duration-300 ${
                  opacity ? 'translate-y-0' : '-translate-y-10'
              } bg-[#e0e0e0] dark:bg-gradient-to-b dark:from-[#111f4a] dark:to-[#1a327e]`}
          >
            <button
                onClick={handleClose}
                className="absolute top-4 right-4 text-2xl text-gray-700 dark:text-white hover:text-gray-500 dark:hover:text-gray-300"
            >
              <IoMdClose />
            </button>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-white mb-6">
              Settings
            </h2>
            {error && (
                <div className="text-red-500 text-sm p-2 bg-red-100 dark:bg-red-200 rounded mb-4 transition-opacity duration-300">
                  {error}
                </div>
            )}
            <form className="space-y-6" onSubmit={handleUpdate}>
              <input
                  type="text"
                  name="username"
                  placeholder="Username"
                  className="block w-full p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  required
                  disabled={loading}
              />
              <input
                  type="email"
                  name="email"
                  placeholder="Email"
                  className="block w-full p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  disabled={loading}
              />
              <textarea
                  name="biography"
                  placeholder="Biography"
                  className="block w-full p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                  value={biography}
                  onChange={(e) => setBiography(e.target.value)}
                  disabled={loading}
              />
              <div className="flex items-center">
                <input
                    type="checkbox"
                    name="isProfilePublic"
                    className="mr-2"
                    checked={isProfilePublic}
                    onChange={() => setIsProfilePublic(!isProfilePublic)}
                    disabled={loading}
                />
                <label className="text-lg text-gray-800 dark:text-gray-300">
                  Profile Public
                </label>
              </div>
              <button
                  type="submit"
                  className="w-full p-3 bg-blue-600 text-white rounded text-lg hover:bg-blue-700 dark:bg-green-600 dark:hover:bg-green-700"
                  disabled={loading}
              >
                {loading ? 'Updating...' : 'Update Settings'}
              </button>
            </form>
          </div>
        </div>
      </Modal>
  );
}
