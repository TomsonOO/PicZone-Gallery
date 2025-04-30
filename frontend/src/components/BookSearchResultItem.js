import React, { useState } from 'react';
import { FaBook, FaPlus } from 'react-icons/fa';
import { useNavigate } from 'react-router-dom';

const BookSearchResultItem = ({ book, onImport, isImporting }) => {
  const navigate = useNavigate();
  const [imageLoaded, setImageLoaded] = useState(false);
  const [imageError, setImageError] = useState(false);
  const [coverUrl, setCoverUrl] = useState(book.coverUrl || '');

  const handleImageLoad = () => {
    setImageLoaded(true);
  };

  const handleImageError = () => {
    setImageError(true);
    
    if (coverUrl && coverUrl.includes('openlibrary.org')) {
      const newSizeUrl = coverUrl.includes('-L.jpg') 
        ? coverUrl.replace('-L.jpg', '-M.jpg') 
        : coverUrl.replace('-M.jpg', '-L.jpg');
        
      setCoverUrl(newSizeUrl);
    }
  };

  const handleImportClick = (e) => {
    e.stopPropagation();
    if (!isImporting) {
      onImport(book);
    }
  };

  const handleCardClick = () => {
    if (book.id) {
      navigate(`/bookzone/book/${book.id}`);
    }
  };

  const renderPlaceholder = () => (
    <div className="bg-gray-700 flex items-center justify-center w-full h-full">
      <FaBook className="text-gray-500 text-4xl" />
    </div>
  );

  return (
    <div 
      className="overflow-hidden rounded-lg transition-transform duration-300 hover:scale-105 cursor-pointer"
      onClick={handleCardClick}
    >
      <div className="aspect-square bg-gray-700 relative">
        {coverUrl && !imageError ? (
          <>
            <img
              src={coverUrl}
              alt={`${book.title} cover`}
              className={`w-full h-full object-cover transition-opacity duration-300 ${
                imageLoaded ? 'opacity-100' : 'opacity-0'
              }`}
              loading="lazy"
              onLoad={handleImageLoad}
              onError={handleImageError}
              style={{
                imageRendering: 'auto',
                objectFit: 'cover',
              }}
            />
            {!imageLoaded && renderPlaceholder()}
          </>
        ) : (
          renderPlaceholder()
        )}
        
        <button
          onClick={handleImportClick}
          disabled={isImporting || !(book.openLibraryKey || book.olKey || book.key)}
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