import React, { useState, useEffect } from 'react';

const HomePage = () => {
    const [images, setImages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const backendUrl = process.env.REACT_APP_BACKEND_URL;

    useEffect(() => {
        const fetchImages = async () => {
            try {
                const response = await fetch(`${backendUrl}/api/images`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const imageData = await response.json();
                const imagesWithPresignedUrls = await Promise.all(imageData.map(async (image) => {
                    if (!image.objectKey) {
                        console.error('objectKey is undefined for image', image);
                        // Handle the missing objectKey scenario, e.g., skip this image or use a fallback URL
                    }
                    const presignedUrlResponse = await fetch(`${backendUrl}/images/presigned-url/${image.objectKey}`);
                    if (!presignedUrlResponse.ok) {
                        throw new Error(`Failed to fetch presigned URL for ${image.objectKey}`);
                    }
                    const presignedUrl = await presignedUrlResponse.text();
                    return { ...image, url: presignedUrl };
                }));
                setImages(imagesWithPresignedUrls);
            } catch (error) {
                setError(error.message);
            } finally {
                setLoading(false);
            }
        };

        fetchImages();

    }, [backendUrl]);

    if (loading) return <div>Loading...</div>;
    if (error) return <div>Error: {error}</div>;




    return (
        <div className="image-gallery">
            {images.map(image => (
                <div key={image.id} className="image-item">
                    <img src={image.url} alt={image.filename} /> {}
                </div>
            ))}
        </div>
    );
};

export default HomePage;
