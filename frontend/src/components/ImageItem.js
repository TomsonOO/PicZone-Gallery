import React, { useState } from 'react';
import { FaThumbsUp } from 'react-icons/fa';
import likeImage from '../services/likeImage';

export default function ImageItem({ image }) {
  const [userLiked, setUserLiked] = useState(image.liked);
  const [likeCount, setLikeCount] = useState(image.likeCount || 0);
  const [animating, setAnimating] = useState(false);

  async function handleLike() {
    const token = localStorage.getItem('token');
    if (!token) {
      return;
    }
    setAnimating(true);
    setUserLiked(!userLiked);
    setLikeCount((p) => (userLiked ? p - 1 : p + 1));
    try {
      await likeImage(image.id, process.env.REACT_APP_BACKEND_URL);
    } catch {
      setUserLiked(userLiked);
      setLikeCount((p) => (userLiked ? p + 1 : p - 1));
    } finally {
      setAnimating(false);
    }
  }

  return (
    <div className='relative group mb-4'>
      {image.url ? (
        <>
          <img
            src={image.url}
            alt={image.description || 'Image'}
            className='w-full h-auto object-cover transition-transform duration-300 hover:scale-105'
          />
          <div className='absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-70 transition-opacity duration-300 p-2 flex justify-between items-end'>
            <p className='text-white text-sm break-words max-w-[90%]'>
              {image.description}
            </p>
            <div className='flex items-center'>
              <button
                onClick={handleLike}
                className={`mr-2 focus:outline-none transform transition-all duration-300 scale-125 ${
                  userLiked ? 'text-blue-500' : 'text-gray-400'
                } ${animating ? 'scale-150' : ''}`}
              >
                <FaThumbsUp />
              </button>
              <span
                className={`text-white text-sm transition-opacity duration-300 ${
                  animating ? 'opacity-50' : 'opacity-100'
                }`}
              >
                {likeCount}
              </span>
            </div>
          </div>
        </>
      ) : (
        <div className='animate-pulse bg-gray-300 w-full h-60' />
      )}
    </div>
  );
}
