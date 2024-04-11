import React from 'react';
import ImageSlider from '../components/ImageSlider';
import useFetchImages from '../hooks/useFetchImages';

const HomePage = () => {
    const backendUrl = process.env.REACT_APP_BACKEND_URL;
    const { images, loading, error } = useFetchImages(backendUrl);

    if (loading) return <div className="flex justify-center items-center h-screen">Loading...</div>;
    if (error) return <div className="flex justify-center items-center h-screen">Error: {error}</div>;

    return (
        <div className="pt-16 pb-24">
            <ImageSlider images={images} />
        </div>
    );
};

export default HomePage;
