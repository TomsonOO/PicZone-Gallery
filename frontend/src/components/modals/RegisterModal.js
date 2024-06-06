import React from 'react';
import Modal from 'react-modal';
import { IoMdClose } from 'react-icons/io';

Modal.setAppElement('#root');

const RegisterModal = ({ isOpen, onRequestClose }) => {
    const [opacity, setOpacity] = React.useState(false);

    const handleRegister = async (event) => {
        event.preventDefault();

        const formData = {
            username: event.target.elements.username.value,
            email: event.target.elements.email.value,
            password: event.target.elements.password.value,
        };

        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_URL}/api/user`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            if (response.ok) {
                const data = await response.json();
                onRequestClose();
            } else {
                throw new Error('Failed to register');
            }
        } catch (error) {
            console.error('Registration error:', error);
        }
    };



    React.useEffect(() => {
        if (isOpen) {
            setTimeout(() => setOpacity(true), 10);
        } else {
            setOpacity(false);
        }
    }, [isOpen]);

    const handleAfterOpen = () => {
        setOpacity(true);
    };

    const handleAfterClose = () => {
        setOpacity(false);
    };

    return (
        <Modal
            isOpen={isOpen}
            onRequestClose={() => {
                onRequestClose();
                setOpacity(false);
            }}
            className={`fixed inset-0 z-50 transition-opacity duration-300 ${opacity ? 'opacity-100' : 'opacity-0'}`}
            overlayClassName="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full transition-opacity duration-300"
            contentLabel="Register"
        >
            <div className="flex justify-center items-center min-h-screen">
                <div className="bg-white p-8 rounded-lg shadow-lg max-w-lg w-full mx-auto transform-gpu transition-transform duration-300 ${opacity ? 'translate-y-0' : '-translate-y-10' }">
                    <button
                        onClick={onRequestClose}
                        className="absolute top-4 right-4 text-2xl"
                        aria-label="Close">
                        <IoMdClose />
                    </button>
                    <h2 className="text-lg font-bold mb-6">Register</h2>
                    <form className="space-y-6" onSubmit={handleRegister}>
                        <input type="text" name="username" placeholder="Username" className="block w-full mt-1 p-3 border rounded text-lg" required />
                        <input type="email" name="email" placeholder="Email" className="block w-full mt-1 p-3 border rounded text-lg" required />
                        <input type="password" name="password" placeholder="Password" className="block w-full mt-1 p-3 border rounded text-lg" required />
                        <button type="submit" className="w-full p-3 bg-blue-500 text-white rounded text-lg hover:bg-blue-600">
                            Register
                        </button>
                    </form>
                </div>
            </div>
        </Modal>
    );
};





export default RegisterModal;
