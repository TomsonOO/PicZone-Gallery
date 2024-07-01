import React, { useState, useEffect } from 'react';

const useFetchImages = () => {
    const backendUrl = process.env.REACT_APP_BACKEND_URL;
    const [images, setImages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

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
                        return image;
                    }
                    const presignedUrlResponse = await fetch(`${backendUrl}/api/images/presigned-url/${image.objectKey}`);
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

    return { images, loading, error };
};

export default useFetchImages;
