import React, { useState, useEffect } from 'react';
import Modal from 'react-modal';
import { IoMdClose } from 'react-icons/io';
import { toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

Modal.setAppElement('#root');

const SettingsModal = ({ isOpen, onRequestClose, user, updateUser }) => {
    const [opacity, setOpacity] = useState(false);
    const [username, setUsername] = useState('');
    const [email, setEmail] = useState('');
    const [biography, setBiography] = useState('');
    const [isProfilePublic, setIsProfilePublic] = useState(false);
    const [avatar, setAvatar] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        if (isOpen) {
            console.log(user)
            setUsername(user?.username || '');
            setEmail(user?.email || '');
            setBiography(user?.biography || '');
            setIsProfilePublic(user?.isProfilePublic || false);
            setError('');
            setTimeout(() => setOpacity(true), 10);
        }
    }, [isOpen, user]);

    const handleAfterOpen = () => {
        setOpacity(true);
        console.log(user);
    };

    const handleAfterClose = () => {
        setOpacity(false);
    };

    const handleClose = () => {
        setOpacity(false);
        setTimeout(() => {
            onRequestClose();
            setError('');
        }, 300);
    };

    const handleAvatarChange = (e) => {
        setAvatar(e.target.files[0]);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');

        const formData = new FormData();
        formData.append('username', username);
        formData.append('email', email);
        formData.append('biography', biography);
        formData.append('isProfilePublic', isProfilePublic);
        if (avatar) {
            formData.append('image', avatar);
        }

        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/update/info`, {
                method: 'PATCH',
                body: formData,
            });

            const data = await response.json();
            setLoading(false);
            if (response.ok) {
                updateUser(data);
                handleClose();
                toast.success('User information updated successfully', {
                    position: 'top-right',
                    autoClose: 5000,
                    className: 'custom-toast custom-toast-success',
                });
            } else {
                setError(data.message || 'Failed to update user information');
            }
        } catch (error) {
            setLoading(false);
            setError('An unexpected error occurred');
        }
    };

    return (
        <>
            <Modal
                isOpen={isOpen}
                onRequestClose={handleClose}
                className={`fixed inset-0 z-50 transition-opacity duration-300 ${opacity ? 'opacity-100' : 'opacity-0'}`}
                overlayClassName="fixed inset-0 bg-black bg-opacity-70 overflow-y-auto h-full w-full transition-opacity duration-300"
                onAfterOpen={handleAfterOpen}
                onAfterClose={handleAfterClose}
                contentLabel="Settings"
            >
                <div className="flex justify-center items-center min-h-screen p-4 sm:p-6 md:p-8 lg:p-10">
                    <div className={`relative p-10 pt-12 rounded-lg shadow-lg max-w-2xl w-full transform-gpu transition-transform duration-300 ${opacity ? 'translate-y-0' : '-translate-y-10'} bg-white dark:bg-gradient-to-b dark:from-[#111f4a] dark:to-[#1a327e]`}>
                        <button onClick={handleClose} className="absolute top-4 right-4 text-2xl text-gray-700 dark:text-white hover:text-gray-500 dark:hover:text-gray-300">
                            <IoMdClose />
                        </button>
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-2xl font-bold text-gray-800 dark:text-white mt-1">Settings</h2>
                            <div className="min-w-max ml-4">
                                {error && (
                                    <div className="text-red-500 text-sm p-2 bg-red-100 dark:bg-red-200 rounded transition-opacity duration-300">
                                        {error}
                                    </div>
                                )}
                            </div>
                        </div>
                        <form className="space-y-6" onSubmit={handleSubmit}>
                            <input
                                type="text"
                                placeholder="Username"
                                className="block w-full p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                                value={username}
                                onChange={(e) => setUsername(e.target.value)}
                                required
                                disabled={loading}
                            />
                            <input
                                type="email"
                                placeholder="Email"
                                className="block w-full mt-1 p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                                disabled={loading}
                            />
                            <textarea
                                placeholder="Biography"
                                className="block w-full mt-1 p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                                value={biography}
                                onChange={(e) => setBiography(e.target.value)}
                                disabled={loading}
                            />
                            <div className="flex items-center mt-1">
                                <input
                                    type="checkbox"
                                    className="mr-2"
                                    checked={isProfilePublic}
                                    onChange={(e) => setIsProfilePublic(e.target.checked)}
                                    disabled={loading}
                                />
                                <label className="text-lg text-gray-700 dark:text-gray-300">Public Profile</label>
                            </div>
                            <input
                                type="file"
                                className="block w-full mt-1 p-3 border border-gray-300 rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800"
                                onChange={handleAvatarChange}
                                disabled={loading}
                            />
                            <button type="submit" className="w-full p-3 bg-blue-600 text-white rounded text-lg hover:bg-blue-700 dark:bg-green-600 dark:hover:bg-green-700" disabled={loading}>
                                {loading ? 'Updating...' : 'Update'}
                            </button>
                        </form>
                    </div>
                </div>
            </Modal>
        </>
    );
};

export default SettingsModal;
