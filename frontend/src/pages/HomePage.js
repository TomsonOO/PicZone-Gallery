import React from 'react';
import Sidebar from '../components/Sidebar';
import Footer from '../components/Footer';
import GalleryGrid from '../components/GalleryGrid';
import CategoriesNavbar from '../components/CategoriesNavbar';

const HomePage = () => {
    return (
        <div className="flex flex-col min-h-screen ">
            <div className="flex flex-grow">
                <Sidebar />
                <main className="flex flex-col flex-grow p-0 bg-gray-150 dark:bg-gradient-to-b from-[#111f4a] to-[#1a327e]">
                    <CategoriesNavbar />
                    <GalleryGrid />
                </main>
            </div>
            {/*<Footer />*/}
        </div>
    );
};



export default HomePage;
