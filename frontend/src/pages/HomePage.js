import React from 'react';
import Sidebar from '../components/Sidebar';
import Footer from '../components/Footer';
import GalleryGrid from '../components/GalleryGrid';

const HomePage = () => {
    return (
        <div className="flex flex-col min-h-screen bg-gray-100 dark:bg-gray-800">
            <div className="flex flex-grow">
                <Sidebar />
                <main className="flex flex-col flex-grow p-0 bg-white dark:bg-gray-600">
                    <header className="bg-blue-200  p-6 text-center shadow-sm">
                        <h1 className="text-3xl font-bold text-gray-900">Welcome to PicZone Gallery</h1>
                        <p className="text-gray-600">Explore a world of AI art</p>
                    </header>
                    <GalleryGrid />
                </main>
            </div>
            <Footer />
        </div>
    );
};



export default HomePage;
