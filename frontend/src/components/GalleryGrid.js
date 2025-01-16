import React from 'react';
import useFetchImages from '../hooks/useFetchImages';
import { FaSpinner } from 'react-icons/fa';
import ImageItem from './ImageItem';

const GalleryGrid = (
  category = '',
  sortBy = '',
  searchTerm = '',
  pageNumber = 1,
  pageSize = 10
) => {
  const { images, loading, error } = useFetchImages({ category, sortBy });

  if (loading) {
    return (
      <div className='flex justify-center items-center min-h-screen bg-animated'>
        <FaSpinner className='animate-spin text-white text-4xl' />
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
      <div className='columns-1 sm:columns-2 md:columns-3 lg:columns-3 gap-4'>
        {images.map((image) => (
          <ImageItem key={image.id} image={image} />
        ))}
      </div>
    </div>
  );
};

export default GalleryGrid;
