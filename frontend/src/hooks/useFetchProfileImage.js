import React, { useState, useEffect } from 'react';

const useFetchProfileImage = (profileImageId) => {
    const backendUrl = process.env.REACT_APP_BACKEND_URL;
    const [profileImage, setProfileImage] = useState([]);
    const [error, setError] = useState(null);

    useEffect(() => {

        if (!profileImageId) {
            profileImageId = 1;  // default profile avatar
        }
            const fetchProfileImage = async () => {
            try {
                const response = await fetch(`${backendUrl}/api/images/profile/${profileImageId}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const profileImageData = await response.json();

                const profileImageWithPresignedUrl = await new Promise(async (resolve, reject) => {
                    if (!profileImageData.objectKey) {
                        console.error('objectKey is undefined for image', profileImageData);
                        resolve(profileImageData);
                    } else {
                        try {
                            const presignedUrlResponse = await fetch(`${backendUrl}/api/images/presigned-url/${profileImageData.objectKey}`);
                            if (!presignedUrlResponse.ok) {
                                throw new Error(`Failed to fetch presigned URL for ${profileImageData.objectKey}`);
                            }
                            const presignedUrl = await presignedUrlResponse.text();
                            resolve({profileImageData, presignedUrl});
                        } catch (error) {
                            reject(error);
                        }
                    }
                });

                setProfileImage(profileImageWithPresignedUrl);
            } catch (error) {
                setError(error.message);
            }
        };

        fetchProfileImage();
    }, [profileImageId]);

    return { profileImage, error };
};

export default useFetchProfileImage;
