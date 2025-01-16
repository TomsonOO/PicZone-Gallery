import React from 'react';

const categories = [
  { id: 1, name: 'Most liked', categoryValue: 'mostLiked' },
  { id: 2, name: 'Latest', categoryValue: 'newest' },
];

const CategoriesNavbar = ({ onCategoryChange }) => {
  return (
    <div className='bg-gray-150  dark:bg-[#111f4a] dark:text-white text-black-100 p-3 top-0 z-50'>
      <div className='flex justify-center space-x-4'>
        {categories.map((cat) => (
          <button
            key={cat.id}
            className='px-3 py-1 rounded-lg hover:bg-blue-300 transition duration-300'
            onClick={() => onCategoryChange(cat.categoryValue)}
          >
            {cat.name}
          </button>
        ))}
      </div>
    </div>
  );
};

export default CategoriesNavbar;
