import React from 'react';
import useFetchImages from '../hooks/useFetchImages';
import { FaSpinner } from 'react-icons/fa';
import ImageItem from './ImageItem';

const GalleryGrid = ({
  category = '',
  sortBy = '',
  searchTerm = '',
  pageNumber = 1,
  pageSize = 20,
}) => {
  const { images, loading, error } = useFetchImages({ category, sortBy });

  if (loading) {
    return (
      <div className='flex justify-center items-center min-h-screen bg-animated relative overflow-hidden'>
        <div className='absolute inset-0 bg-gradient-to-r from-gray-300 via-gray-200 to-gray-300 dark:bg-gradient-to-r dark:from-[#162b54] dark:via-[#0e1a3b] dark:to-[#162b54] animate-pulse' />
        <FaSpinner className='animate-spin text-black dark:text-white text-4xl relative z-10' />
      </div>
    );
  }

  if (error) {
    return (
      <div className='flex justify-center items-center min-h-screen bg-animated'>
        Error: {error}
      </div>
    );
  }

  return (
    <div className='container mx-auto mt-3'>
      <div className='columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4'>
        {images.map((image) => (
          <div key={image.id} className='mb-4 break-inside-avoid'>
            <ImageItem image={image} />
          </div>
        ))}
      </div>
    </div>
  );
};

export default GalleryGrid;
