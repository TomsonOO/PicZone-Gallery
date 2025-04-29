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
    <div className="bg-gray-700 flex items-center justify-center w-full h-full">
      <FaBook className="text-gray-500 text-4xl" />
    </div>
  );

  return (
    <div className="overflow-hidden rounded-lg transition-transform duration-300 hover:scale-105">
      <div className="aspect-square bg-gray-700 relative">
        {book.coverUrl && !imageError ? (
          <>
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
          </>
        ) : (
          renderPlaceholder()
        )}
        
        {/* Import button overlay */}
        <button
          onClick={handleImportClick}
          disabled={isImporting || !book.openLibraryKey}
          className={`absolute bottom-2 right-2 flex items-center rounded-full p-2 shadow-lg transition-colors duration-200 ${
            isImporting
              ? 'bg-gray-500 text-gray-300 cursor-not-allowed'
              : 'bg-blue-600 hover:bg-blue-700 text-white'
          }`}
          title="Import Book"
        >
          {isImporting ? (
            <span className="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
          ) : (
            <FaPlus size={14} />
          )}
        </button>
      </div>
      
      <div className="mt-2">
        <h3 className="text-sm font-semibold text-gray-100 truncate" title={book.title}>
          {book.title}
        </h3>
        <p className="text-xs text-gray-400 truncate" title={`By ${book.author}`}>
          By {book.author}
        </p>
      </div>
    </div>
  );
};

export default BookSearchResultItem; 