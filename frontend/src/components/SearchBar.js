import React, { useState } from 'react';
import { FaSearch } from 'react-icons/fa';

const SearchBar = ({ onSearch, placeholder }) => {
  const [searchInputValue, setSearchInputValue] = useState('');

  const handleInputChange = (event) => {
    setSearchInputValue(event.target.value);
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    onSearch(searchInputValue);
  };

  const handleClear = () => {
    setSearchInputValue('');
    onSearch('');
  };

  return (
    <form onSubmit={handleSubmit} className="w-full">
      <div className="relative flex items-center">
        <input
          type="text"
          value={searchInputValue}
          onChange={handleInputChange}
          placeholder={placeholder || "Search for books..."}
          className="w-full px-4 py-2 pl-10 pr-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
        />
        <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
          <FaSearch className="text-gray-400 dark:text-gray-300" />
        </div>
        
        {searchInputValue && (
          <button
            type="button"
            onClick={handleClear}
            className="absolute right-12 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100"
          >
            ✕
          </button>
        )}
        
        <button
          type="submit"
          className="absolute right-2 bg-blue-500 hover:bg-blue-600 text-white rounded px-3 py-1 text-sm"
        >
          Search
        </button>
      </div>
    </form>
  );
};

export default SearchBar; 