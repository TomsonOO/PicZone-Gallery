import React, { useState } from 'react';
import { FaBook, FaPlus } from 'react-icons/fa';

const BookSearchResultItem = ({ book, onImport, isImporting }) => {
  const [imageLoaded, setImageLoaded] = useState(false);
  const [imageError, setImageError] = useState(false);

  const handleImageLoad = () => {
    setImageLoaded(true);
  };

  const handleImageError = () => {
    setImageError(true);
  };

  const handleImportClick = () => {
    if (!isImporting && book.openLibraryKey) {
      onImport(book.openLibraryKey);
    }
  };

  const renderPlaceholder = () => (
    <div className="bg-gray-200 dark:bg-gray-700 flex items-center justify-center w-full h-48 rounded-t-lg">
      <FaBook className="text-gray-400 dark:text-gray-500 text-4xl" />
    </div>
  );

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg">
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
        <p className="text-sm text-gray-600 dark:text-gray-300 mt-1 mb-3 truncate" title={`By ${book.author}`}>
          By {book.author}
        </p>
        
        <button
          onClick={handleImportClick}
          disabled={isImporting || !book.openLibraryKey}
          className={`w-full flex items-center justify-center py-2 px-4 rounded text-sm font-medium transition duration-200 ${
            isImporting
              ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
              : 'bg-blue-600 hover:bg-blue-700 text-white'
          }`}
        >
          {isImporting ? (
            <>
              <span className="mr-2">Importing...</span>
              <span className="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
            </>
          ) : (
            <>
              <FaPlus className="mr-2" /> Import Book
            </>
          )}
        </button>
      </div>
    </div>
  );
};

export default BookSearchResultItem; 