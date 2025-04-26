import React, { useState } from 'react';
import { FaBook } from 'react-icons/fa';

const BookCard = ({ book }) => {
  const [imageLoaded, setImageLoaded] = useState(false);
  const [imageError, setImageError] = useState(false);

  const handleImageLoad = () => {
    setImageLoaded(true);
  };

  const handleImageError = () => {
    setImageError(true);
  };

  const renderPlaceholder = () => (
    <div className="bg-gray-200 dark:bg-gray-700 flex items-center justify-center w-full h-48 rounded-t-lg">
      <FaBook className="text-gray-400 dark:text-gray-500 text-4xl" />
    </div>
  );

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:scale-[1.02]">
      {book.coverUrl && !imageError ? (
        <div className="relative h-48 overflow-hidden bg-gray-200 dark:bg-gray-700">
          <img
            src={book.coverUrl}
            alt={`${book.title} cover`}
            className={`w-full h-full object-cover transition-opacity duration-300 ${
              imageLoaded ? 'opacity-100' : 'opacity-0'
            }`}
            onLoad={handleImageLoad}
            onError={handleImageError}
          />
          {!imageLoaded && renderPlaceholder()}
        </div>
      ) : (
        renderPlaceholder()
      )}
      
      <div className="p-4">
        <h3 className="text-lg font-semibold text-gray-800 dark:text-white truncate" title={book.title}>
          {book.title}
        </h3>
        <p className="text-sm text-gray-600 dark:text-gray-300 mt-1 truncate" title={`By ${book.author}`}>
          By {book.author}
        </p>
      </div>
    </div>
  );
};

export default BookCard; 
