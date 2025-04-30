import React, { useState, useEffect } from 'react';
import { FaBook } from 'react-icons/fa';
import { useNavigate } from 'react-router-dom';
import { getBookCoverPresignedUrl } from '../services/bookzoneService';

const BookCard = ({ book }) => {
  const navigate = useNavigate();
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

  const handleCardClick = () => {
    navigate(`/bookzone/book/${book.id}`);
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
            {isLoadingPresignedUrl ? (
              renderPlaceholder()
            ) : (
              <img
                src={coverUrl}
                alt={`${book.title} cover`}
                className={`w-full h-full object-cover transition-opacity duration-300 ${
                  imageLoaded ? 'opacity-100' : 'opacity-0'
                }`}
                onLoad={handleImageLoad}
                onError={handleImageError}
              />
            )}
            {!imageLoaded && !isLoadingPresignedUrl && renderPlaceholder()}
          </>
        ) : (
          renderPlaceholder()
        )}
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

export default BookCard;
