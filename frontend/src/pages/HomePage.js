import React, { useState } from 'react';
import Sidebar from '../components/Sidebar';
import GalleryGrid from '../components/GalleryGrid';
import CategoriesNavbar from '../components/CategoriesNavbar';

const HomePage = () => {
  const [category, setCategory] = useState('');
  const [sortBy, setSortBy] = useState('');

  const handleCategoryChange = (newCategory) => {
    setCategory(newCategory);
  };

  const handleSortChange = (sortBy) => {
    setSortBy(sortBy);
  };

  return (
    <div className='flex flex-col min-h-screen '>
      <div className='flex flex-grow'>
        <Sidebar />
        <main className='flex flex-col flex-grow p-0 bg-gray-150 dark:bg-gradient-to-b from-[#111f4a] to-[#1a327e]'>
          <CategoriesNavbar onCategoryChange={handleCategoryChange} />
          <GalleryGrid category={category} sortBY={sortBy} />
        </main>
      </div>
    </div>
  );
};

export default HomePage;
