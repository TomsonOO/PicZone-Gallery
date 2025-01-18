import React from 'react';
import { FaHeart } from 'react-icons/fa';

export default function FavoriteButton({ isFavorite, onToggleFavorite }) {
  return (
    <button
      onClick={onToggleFavorite}
      className={`focus:outline-none transition-transform ${
        isFavorite ? 'text-red-500' : 'text-gray-400'
      }`}
    >
      <FaHeart />
    </button>
  );
}
