import React from 'react';
import useFetchImages from '../hooks/useFetchImages';
import { FaSpinner } from 'react-icons/fa';

const GalleryGrid = () => {
    const backendUrl = process.env.REACT_APP_BACKEND_URL;
    const { images, loading, error } = useFetchImages(backendUrl);

    if (loading) {
        return (
            <div className="flex justify-center items-center min-h-screen bg-animated">
                <FaSpinner className="animate-spin text-white text-4xl" />
            </div>
        );
    }

    if (error) {
        return (
            <div className="flex justify-center items-center min-h-screen bg-animated">
                Error: {error}
            </div>
        );
    }

    return (
        <div className="container mx-auto mt-3">
            <div className="columns-1 sm:columns-2 md:columns-3 lg:columns-3 gap-4">
                {images.map((image, index) => (
                    <div key={index} className="relative group mb-4">
                        {image.url ? (
                            <>
                                <img
                                    src={image.url}
                                    alt={image.description || 'Image'}
                                    className="w-full h-auto object-cover transition-transform duration-300 hover:scale-105"
                                />

                                <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-70 transition-opacity duration-300">
                                    <p className="text-white text-sm p-2 truncate">{image.description}</p>
                                </div>
                            </>
                        ) : (
                            <div className="animate-pulse bg-gray-300 w-full h-60"></div>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};

export default GalleryGrid;
