import React, { useState } from 'react';
import { BsStarFill, BsStar } from 'react-icons/bs';

export default function FavoriteButton({ isFavorite, onToggleFavorite }) {
    const [animating, setAnimating] = useState(false);

    const handleClick = () => {
        setAnimating(true);
        onToggleFavorite();
        setTimeout(() => setAnimating(false), 300);
    };

    return (
        <button
            onClick={handleClick}
            className={`focus:outline-none transition-transform transform duration-300 rounded-full bg-opacity-50 bg-black ${
                isFavorite ? 'text-yellow-400' : 'text-gray-400'
            } shadow-lg p-2 ${animating ? 'scale-125' : 'scale-105'}`}
        >
            {isFavorite ? <BsStarFill /> : <BsStar />}
        </button>
    );
}
