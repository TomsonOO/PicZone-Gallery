import React from 'react';
import { FaThumbsUp } from 'react-icons/fa';

export default function LikeButton({
  userLiked,
  likeCount,
  onLike,
  animating,
}) {
  return (
    <div className='flex items-center'>
      <button
        onClick={onLike}
        className={`focus:outline-none transform transition-all duration-300 scale-125 flex items-center space-x-1 ${
          userLiked ? 'text-blue-500' : 'text-gray-400'
        } ${animating ? 'scale-150' : ''}`}
      >
        <FaThumbsUp />
      </button>
      <span
        className={`ml-1 text-white text-sm ${animating ? 'opacity-50' : 'opacity-100'}`}
      >
        {likeCount}
      </span>
    </div>
  );
}
