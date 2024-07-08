import React, { useState, useEffect } from 'react';
import Modal from 'react-modal';
import { IoMdClose } from 'react-icons/io';
import { useUser } from '../../context/UserContext';
import { ToastContainer, toast } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

Modal.setAppElement('#root');

const LoginModal = ({ isLoginOpen, onRequestClose }) => {
    const [opacity, setOpacity] = useState(false);
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const { login } = useUser();

    useEffect(() => {
        if (isLoginOpen) {
            setTimeout(() => setOpacity(true), 10);
        } else {
            setOpacity(false);
        }
    }, [isLoginOpen]);

    const handleAfterOpen = () => {
        setOpacity(true);
    };

    const handleAfterClose = () => {
        setOpacity(false);
    };

    const handleLogin = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/login`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password }),
            });

            const data = await response.json();
            setLoading(false);
            if (response.ok) {
                if (!data.user) {
                    setError('Login failed: No user data in response');
                    return;
                }
                login(data.user, data.token);
                toast.success('Login successful!');
                setUsername('');
                setPassword('');
                onRequestClose();
            } else {
                setError(`Login failed: ${data.message}`);
                toast.error(`Login failed: ${data.message}`);
            }
        } catch (error) {
            setLoading(false);
            setError('Login failed: An unexpected error occurred');
            toast.error('Login failed: An unexpected error occurred');
        }
    };

    return (
        <>
            <ToastContainer />
            <Modal
                isOpen={isLoginOpen}
                onRequestClose={() => {
                    onRequestClose();
                    setOpacity(false);
                }}
                className={`fixed inset-0 z-50 transition-opacity duration-300 ${opacity ? 'opacity-100' : 'opacity-0'}`}
                overlayClassName="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full transition-opacity duration-300"
                onAfterOpen={handleAfterOpen}
                onAfterClose={handleAfterClose}
                contentLabel="Login"
            >
                <div className="flex justify-center items-center min-h-screen">
                    <div className={`bg-white p-8 rounded-lg shadow-lg max-w-lg w-full mx-auto transform-gpu transition-transform duration-300 ${opacity ? 'translate-y-0' : '-translate-y-10'}`}>
                        <button onClick={onRequestClose} className="absolute top-4 right-4 text-2xl">
                            <IoMdClose />
                        </button>
                        <h2 className="text-lg font-bold mb-6">Log In</h2>
                        <form className="space-y-6" onSubmit={handleLogin}>
                            {error && <div className="text-red-500 text-sm">{error}</div>}
                            <input
                                type="text"
                                placeholder="Username"
                                className="block w-full mt-1 p-3 border rounded text-lg"
                                value={username}
                                onChange={(e) => setUsername(e.target.value)}
                                required
                                disabled={loading}
                            />
                            <input
                                type="password"
                                minLength="6"
                                placeholder="Password"
                                className="block w-full mt-1 p-3 border rounded text-lg"
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                required
                                disabled={loading}
                            />
                            <button type="submit" className="w-full p-3 bg-blue-500 text-white rounded text-lg hover:bg-blue-600" disabled={loading}>
                                {loading ? 'Logging in...' : 'Log In'}
                            </button>
                        </form>
                    </div>
                </div>
            </Modal>
        </>
    );
};

export default LoginModal;
