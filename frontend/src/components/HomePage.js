import React, { useState, useEffect } from 'react';

const HomePage = () => {
    const [images, setImages] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchImages = async () => {
            setLoading(true);
            try {
                const response = await fetch('/api/images');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const data = await response.json();
                setImages(data);
            } catch (error) {
                setError(error.message);
            } finally {
                setLoading(false);
            }
        };

        fetchImages();
    }, []);

    if (loading) return <div>Loading...</div>;
    if (error) return <div>Error: {error}</div>;

    return (
        <div className="image-gallery">
            {images.map(image => (
                <div key={image.id} className="image-item">
                    <img src={image.url} alt={image.filename} />
                </div>
            ))}
        </div>
    );
};

export default HomePage;
