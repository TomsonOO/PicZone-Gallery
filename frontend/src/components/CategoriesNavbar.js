import React from 'react';

const categories = [
    { id: 1, name: "Daily Inspiration" },
    { id: 2, name: "Weekly Highlight" },
    { id: 3, name: "Monthly Feature" },
    { id: 4, name: "Hall of Fame" },
    { id: 5, name: "Popular Today" },
    { id: 6, name: "Top Rated" }
];


const CategoriesNavbar = () => {
    return (
        <div className="bg-gray-150  dark:bg-[#111f4a] dark:text-white text-black-100 p-3 top-0 z-50">
            <div className="flex justify-center space-x-4">
                {categories.map((category) => (
                    <button
                        key={category.id}
                        className="px-3 py-1 rounded-lg hover:bg-blue-300 transition duration-300"
                        onClick={() => {} /* Future implementation */}>
                        {category.name}
                    </button>
                ))}
            </div>
        </div>
    );
};

export default CategoriesNavbar;
