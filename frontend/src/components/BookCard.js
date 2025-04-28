import React, { useState, useEffect } from 'react';
import { FaBook } from 'react-icons/fa';
import { getBookCoverPresignedUrl } from '../services/bookzoneService';

const BookCard = ({ book }) => {
  const [imageLoaded, setImageLoaded] = useState(false);
  const [imageError, setImageError] = useState(false);
  const [coverUrl, setCoverUrl] = useState(book.coverUrl || '');
  const [isLoadingPresignedUrl, setIsLoadingPresignedUrl] = useState(false);

  useEffect(() => {
    const fetchPresignedUrl = async () => {
      if (book.needsPresignedUrl && book.coverUrl) {
        try {
          setIsLoadingPresignedUrl(true);
          if (book.objectKey) {
            const presignedUrl = await getBookCoverPresignedUrl(book.objectKey);
            setCoverUrl(presignedUrl);
          } else {
            const objectKeyMatch = book.coverUrl.match(/amazonaws\.com\/(.+)$/);
            if (objectKeyMatch && objectKeyMatch[1]) {
              const objectKey = objectKeyMatch[1];
              console.log('Extracted objectKey:', objectKey);
              const presignedUrl = await getBookCoverPresignedUrl(objectKey);
              setCoverUrl(presignedUrl);
            }
          }
        } catch (error) {
          console.error('Error fetching presigned URL:', error);
          setImageError(true);
        } finally {
          setIsLoadingPresignedUrl(false);
        }
      }
    };

    fetchPresignedUrl();
  }, [book]);

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
      {coverUrl && !imageError ? (
        <div className="relative h-48 overflow-hidden bg-gray-200 dark:bg-gray-700">
          {isLoadingPresignedUrl ? (
            renderPlaceholder()
          ) : (
            <img
              src={coverUrl}
              alt={`${book.title} cover`}
              className={`w-full h-full object-cover transition-opacity duration-300 ${imageLoaded ? 'opacity-100' : 'opacity-0'
                }`}
              onLoad={handleImageLoad}
              onError={handleImageError}
            />
          )}
          {!imageLoaded && !isLoadingPresignedUrl && renderPlaceholder()}
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
