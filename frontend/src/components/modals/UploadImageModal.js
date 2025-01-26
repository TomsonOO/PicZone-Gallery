import React, { useState } from 'react';
import Modal from 'react-modal';
import { IoMdClose } from 'react-icons/io';
import { toast } from 'react-toastify';
import { useUser } from '../../context/UserContext';
import { uploadImage } from '../../services/uploadImageService';

Modal.setAppElement('#root');

export default function UploadImageModal({ isOpen, onRequestClose }) {
    const { state } = useUser();
    const [opacity, setOpacity] = useState(false);
    const [selectedFile, setSelectedFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState('');
    const [description, setDescription] = useState('');
    const [tags, setTags] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const commonTags = ['nature', 'food', 'architecture', 'people', 'fantasy', 'cinematic', 'abstract', 'portrait', 'symmetry', 'retro', 'ancient'];

    function handleFileChange(e) {
        const file = e.target.files[0];
        setSelectedFile(file);
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                setPreviewUrl(reader.result);
            };
            reader.readAsDataURL(file);
        } else {
            setPreviewUrl('');
        }
    }

    function handleTagClick(tag) {
        setTags((prevTags) => {
            const tagList = prevTags.split(',').map(t => t.trim());
            if (!tagList.includes(tag)) {
                return prevTags ? `${prevTags}, ${tag}` : tag;
            }
            return prevTags;
        });
    }

    async function handleUpload(e) {
        e.preventDefault();
        if (!selectedFile) {
            setError('Please select a file first');
            return;
        }
        setLoading(true);
        setError('');
        try {
            await uploadImage({
                file: selectedFile,
                description,
                tags: tags.split(',').map(tag => tag.trim()),
                token: state.token
            });
            toast.success('Image uploaded successfully', {
                position: 'top-right',
                autoClose: 5000
            });
            handleClose();
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
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
            setSelectedFile(null);
            setPreviewUrl('');
            setDescription('');
            setTags('');
            setError('');
            onRequestClose();
        }, 300);
    }

    return (
        <Modal
            isOpen={isOpen}
            onRequestClose={handleClose}
            onAfterOpen={handleAfterOpen}
            onAfterClose={handleAfterClose}
            overlayClassName="fixed inset-0 bg-black bg-opacity-70 z-[999] transition-opacity duration-300"
            className={`fixed inset-0 z-[1000] flex items-center justify-center p-4 transition-opacity duration-300 ${opacity ? 'opacity-100' : 'opacity-0'}`}
            contentLabel="Upload Image"
        >
            <div className={`bg-[#e0e0e0] dark:bg-gradient-to-b dark:from-[#111f4a] dark:to-[#1a327e] p-6 rounded shadow-lg max-w-5xl w-full flex flex-col md:flex-row md:space-x-6 relative transform-gpu transition-transform duration-300 ${opacity ? 'translate-y-0' : '-translate-y-10'}`}>
                <button
                    onClick={handleClose}
                    className="absolute top-4 right-4 text-2xl text-gray-700 dark:text-white hover:text-gray-500 dark:hover:text-gray-300"
                >
                    <IoMdClose />
                </button>
                <div className="flex flex-col items-center w-full md:w-3/5">
                    {previewUrl ? (
                        <img
                            src={previewUrl}
                            alt="Preview"
                            className="w-full max-w-[80%] max-h-[70vh] object-contain border dark:border-gray-700 mb-4"
                        />
                    ) : (
                        <div className="flex items-center justify-center w-full max-w-[80%] h-96 bg-gray-200 dark:bg-gray-700 mb-4">
                            <span className="text-gray-600 dark:text-gray-300">No image selected</span>
                        </div>
                    )}
                    <label className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded cursor-pointer">
                        Choose File
                        <input
                            type="file"
                            accept="image/*"
                            onChange={handleFileChange}
                            className="hidden"
                            disabled={loading}
                        />
                    </label>
                </div>
                <form className="w-full md:w-2/5 flex flex-col space-y-6 mt-10" onSubmit={handleUpload}>
                    <textarea
                        placeholder="Description (visible on hover)"
                        className="p-2 border border-gray-300 rounded dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        disabled={loading}
                    />
                    <textarea
                        placeholder="Tags (comma-separated)"
                        className="p-2 border border-gray-300 rounded dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                        value={tags}
                        onChange={(e) => setTags(e.target.value)}
                        disabled={loading}
                    />
                    <div>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-2">Most popular tags (click to add):</p>
                        <div className="flex flex-wrap gap-2">
                            {commonTags.map((tag) => (
                                <button
                                    key={tag}
                                    type="button"
                                    className="px-3 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-full text-sm shadow-sm dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300"
                                    onClick={() => handleTagClick(tag)}
                                >
                                    {tag}
                                </button>
                            ))}
                        </div>
                    </div>
                    {error && (
                        <div className="text-red-500 text-sm p-1">{error}</div>
                    )}
                    <button
                        type="submit"
                        className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:bg-gray-400"
                        disabled={loading}
                    >
                        {loading ? 'Uploading...' : 'Upload'}
                    </button>
                </form>
            </div>
        </Modal>
    );
}
