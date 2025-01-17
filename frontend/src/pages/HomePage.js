import React, { useState } from 'react';
import Sidebar from '../components/Sidebar';
import GalleryGrid from '../components/GalleryGrid';
import CategoriesNavbar from '../components/CategoriesNavbar';

const HomePage = () => {
  const [category, setCategory] = useState('');
  const [sortBy, setSortBy] = useState('');
  const [categoryResetTrigger, setCategoryResetTrigger] = useState(0);

  const handleCategoryChange = (newCategory) => {
    setCategory(newCategory);
  };

  const handleSortChange = (sortBy) => {
    setSortBy(sortBy);
  };

  const handleCategoryReset = () => {
    setCategory('');
    setSortBy('');
    setCategoryResetTrigger((prev) => prev + 1);
  };

  return (
    <div className='flex flex-col min-h-screen '>
      <div className='flex flex-grow'>
        <Sidebar onCategoryReset={handleCategoryReset} />
        <main className='flex flex-col flex-grow p-0 bg-gray-150 dark:bg-gradient-to-b from-[#111f4a] to-[#1a327e]'>
          <CategoriesNavbar
            key={categoryResetTrigger}
            onCategoryChange={handleCategoryChange}
          />
          <GalleryGrid category={category} sortBy={sortBy} />
        </main>
      </div>
    </div>
  );
};

export default HomePage;
