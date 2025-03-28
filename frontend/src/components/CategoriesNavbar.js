import React, { useState } from 'react';
import { useUser } from '../context/UserContext';

const categories = [
  { id: 1, name: 'Most Liked', categoryValue: 'mostLiked' },
  { id: 2, name: 'Latest', categoryValue: 'newest' },
  { id: 3, name: 'Favorites', categoryValue: 'favorites' },
];

const CategoriesNavbar = ({ onCategoryChange }) => {
  const [activeCategory, setActiveCategory] = useState('');
  const { isUserLoggedIn } = useUser();

  const handleCategoryClick = (categoryValue) => {
    if (categoryValue === 'favorites' && !isUserLoggedIn) return;
    setActiveCategory(categoryValue);
    onCategoryChange(categoryValue);
  };

  return (
    <div className='bg-gray-200 dark:bg-[#0e1a3b] dark:text-white text-black-100 p-3 top-0 z-50'>
      <div className='flex justify-center space-x-[5%]'>
        {categories.map((cat) => (
          <button
            key={cat.id}
            className={`px-4 py-2 rounded-lg transition duration-300 
              ${
                activeCategory === cat.categoryValue
                  ? 'bg-gray-300 dark:bg-sky-900'
                  : 'bg-gray-250 hover:bg-gray-300 dark:hover:bg-sky-900'
              } 
              ${!isUserLoggedIn && cat.categoryValue === 'favorites' ? 'opacity-50 cursor-not-allowed' : ''}\`}
                               `}
            onClick={() => handleCategoryClick(cat.categoryValue)}
            style={{
              boxSizing: 'border-box',
            }}
          >
            {cat.name}
          </button>
        ))}
      </div>
    </div>
  );
};

export default CategoriesNavbar;
