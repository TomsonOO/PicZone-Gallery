import React, { useState } from 'react';

const categories = [
    { id: 1, name: 'Most Liked', categoryValue: 'mostLiked' },
    { id: 2, name: 'Latest', categoryValue: 'newest' },
];

const CategoriesNavbar = ({ onCategoryChange }) => {
    const [activeCategory, setActiveCategory] = useState('');

    const handleCategoryClick = (categoryValue) => {
        setActiveCategory(categoryValue);
        onCategoryChange(categoryValue);
    };

    return (
        <div className="bg-gray-200 dark:bg-[#0e1a3b] dark:text-white text-black-100 p-3 top-0 z-50">
            <div className="flex justify-center space-x-10">
                {categories.map((cat) => (
                    <button
                        key={cat.id}
                        className={`px-4 py-2 border rounded-lg transition duration-300 
              ${activeCategory === cat.categoryValue
                            ? 'bg-blue-500 border-blue-600 text-white'
                            : 'bg-gray-250 border-gray-300 dark:bg-[#162b54] dark:border-[#1e3a72]'} 
              hover:bg-blue-400 hover:border-blue-500`}
                        onClick={() => handleCategoryClick(cat.categoryValue)}
                    >
                        {cat.name}
                    </button>
                ))}
            </div>
        </div>
    );
};

export default CategoriesNavbar;
