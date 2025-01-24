import React, { useState } from 'react';
import { FaThumbsUp } from 'react-icons/fa';

export default function LikeButton({ userLiked, likeCount, onLike }) {
    const [animating, setAnimating] = useState(false);

    const handleClick = () => {
        setAnimating(true);
        onLike();
        setTimeout(() => setAnimating(false), 300);
    };

    return (
        <div className="flex items-center">
            <button
                onClick={handleClick}
                className={`focus:outline-none transform transition-all duration-300 scale-125 flex items-center space-x-1 rounded-full bg-opacity-50 bg-black p-2 shadow-lg ${
                    userLiked ? 'text-blue-500' : 'text-gray-400'
                } ${animating ? 'scale-125' : 'scale-105'}`}
            >
                <FaThumbsUp />
            </button>
            <span
                className={`ml-1 text-white text-sm transition-opacity duration-300 ${
                    animating ? 'opacity-50' : 'opacity-100'
                }`}
            >
        {likeCount}
      </span>
        </div>
    );
}
