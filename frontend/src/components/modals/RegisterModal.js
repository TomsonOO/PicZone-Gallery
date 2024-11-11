import React, { useState, useEffect } from 'react';
import Modal from 'react-modal';
import { IoMdClose } from 'react-icons/io';
import { toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

Modal.setAppElement('#root');

const RegisterModal = ({ isRegisterOpen, onRequestClose }) => {
    const [opacity, setOpacity] = useState(false);
    const [username, setUsername] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const [usernameError, setUsernameError] = useState('');
    const [emailError, setEmailError] = useState('');
    const [passwordError, setPasswordError] = useState('');

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    const handleRegister = async (event) => {
        event.preventDefault();
        setLoading(true);
        setError('');
        setUsernameError('');
        setEmailError('');
        setPasswordError('');

        if (!passwordRegex.test(password)) {
            setPasswordError('Password must be at least 8 characters, include uppercase, lowercase, and a number.');
            setLoading(false);
            return;
        }

        if (username.length < 6) {
            setUsernameError('Username must be at least 6 characters long.');
            setLoading(false);
            return;
        }

        const formData = { username, email, password };

        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/user`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const data = await response.json();
            setLoading(false);

            if (response.ok) {
                setUsername('');
                setEmail('');
                setPassword('');
                handleClose();
                setTimeout(() => {
                    toast.success(`🚀 ${formData.username}, you're all set! Your account has been successfully created!`, {
                        position: 'top-right',
                        autoClose: 7000,
                        className: 'custom-toast custom-toast-success',
                    });
                }, 500);
            } else {
                if (data.errors && Array.isArray(data.errors)) {
                    data.errors.forEach((err) => {
                        if (err.field === 'username') {
                            setUsernameError(err.message);
                        } else if (err.field === 'email') {
                            setEmailError(err.message);
                        } else if (err.field === 'password') {
                            setPasswordError(err.message);
                        } else {
                            setError(err.message);
                        }
                    });
                } else if (data.message) {
                    setError(data.message);
                } else {
                    setError('Failed to register');
                }
            }
        } catch (error) {
            setLoading(false);
            setError('Registration failed: An unexpected error occurred');
        }
    };

    useEffect(() => {
        if (isRegisterOpen) {
            setError('');
            setUsernameError('');
            setEmailError('');
            setPasswordError('');
            setTimeout(() => setOpacity(true), 10);
        }
    }, [isRegisterOpen]);

    const handleAfterOpen = () => {
        setOpacity(true);
    };

    const handleAfterClose = () => {
        setOpacity(false);
    };

    const handleClose = () => {
        setOpacity(false);
        setTimeout(() => {
            onRequestClose();
            setError('');
            setUsernameError('');
            setEmailError('');
            setPasswordError('');
        }, 300);
    };

    return (
        <Modal
            isOpen={isRegisterOpen}
            onRequestClose={handleClose}
            className={`fixed inset-0 z-50 transition-opacity duration-300 ${opacity ? 'opacity-100' : 'opacity-0'}`}
            overlayClassName="fixed inset-0 bg-black bg-opacity-70 overflow-y-auto h-full w-full transition-opacity duration-300"
            onAfterOpen={handleAfterOpen}
            onAfterClose={handleAfterClose}
            contentLabel="Register"
        >
            <div className="flex justify-center items-center min-h-screen p-4 sm:p-6 md:p-8 lg:p-10">
                <div className={`relative p-10 pt-12 rounded-lg shadow-lg max-w-2xl w-full transform-gpu transition-transform duration-300 ${opacity ? 'translate-y-0' : '-translate-y-10'} bg-white dark:bg-gradient-to-b dark:from-[#111f4a] dark:to-[#1a327e]`}>
                    <button onClick={handleClose} className="absolute top-4 right-4 text-2xl text-gray-700 dark:text-white hover:text-gray-500 dark:hover:text-gray-300">
                        <IoMdClose />
                    </button>
                    <h2 className="text-2xl font-bold text-gray-800 dark:text-white mb-6">Register</h2>
                    {error && (
                        <div className="text-red-500 text-sm p-2 bg-red-100 dark:bg-red-200 rounded mb-4 transition-opacity duration-300">
                            {error}
                        </div>
                    )}
                    <form className="space-y-6" onSubmit={handleRegister}>
                        <div>
                            <input
                                type="text"
                                name="username"
                                placeholder="Username"
                                className={`block w-full p-3 border ${usernameError ? 'border-red-500' : 'border-gray-300'} rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800`}
                                value={username}
                                onChange={(e) => setUsername(e.target.value)}
                                required
                                disabled={loading}
                            />
                            {usernameError && (
                                <p className="text-red-500 text-sm mt-1">{usernameError}</p>
                            )}
                        </div>
                        <div>
                            <input
                                type="email"
                                name="email"
                                placeholder="Email"
                                className={`block w-full p-3 border ${emailError ? 'border-red-500' : 'border-gray-300'} rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800`}
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                required
                                disabled={loading}
                            />
                            {emailError && (
                                <p className="text-red-500 text-sm mt-1">{emailError}</p>
                            )}
                        </div>
                        <div>
                            <input
                                type="password"
                                name="password"
                                placeholder="Password"
                                className={`block w-full p-3 border ${passwordError ? 'border-red-500' : 'border-gray-300'} rounded text-lg bg-gray-50 focus:border-blue-500 focus:bg-white dark:bg-gray-700 dark:text-gray-300 dark:focus:bg-gray-800`}
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                                disabled={loading}
                            />
                            {passwordError && (
                                <p className="text-red-500 text-sm mt-1">{passwordError}</p>
                            )}
                        </div>
                        <button type="submit" className="w-full p-3 bg-blue-600 text-white rounded text-lg hover:bg-blue-700 dark:bg-green-600 dark:hover:bg-green-700" disabled={loading}>
                            {loading ? 'Registering...' : 'Register'}
                        </button>
                    </form>
                </div>
            </div>
        </Modal>
    );
};

export default RegisterModal;
