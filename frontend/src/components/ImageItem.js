import React, { useState, useEffect } from 'react';
import ImageModal from './modals/ImageModal';
import FavoriteButton from './buttons/FavoriteButton';
import LikeButton from './buttons/LikeButton';
import { toast } from 'react-toastify';
import { toggleLike, toggleFavorite } from '../services/imageActions';

let toastId = null;

export default function ImageItem({ image }) {
  const [userLiked, setUserLiked] = useState(image.liked);
  const [likeCount, setLikeCount] = useState(image.likeCount || 0);
  const [imgLoaded, setImgLoaded] = useState(false);
  const [isFavorite, setIsFavorite] = useState(image.favorited);
  const [isModalOpen, setIsModalOpen] = useState(false);

  useEffect(() => {
    setUserLiked(image.liked);
  }, [image.liked]);

  async function handleToggleLike() {
    const token = localStorage.getItem('token');
    if (!token) {
      if (!toastId) {
        toastId = toast.error('You must be logged in to like images.', {
          position: 'top-right',
          autoClose: 3000,
          onClose: () => {
            toastId = null;
          },
        });
      }
      return;
    }
    setUserLiked(!userLiked);
    setLikeCount(prev => (userLiked ? prev - 1 : prev + 1));
    try {
      await toggleLike(image.id, process.env.REACT_APP_BACKEND_URL);
    } catch {
      setUserLiked(userLiked);
      setLikeCount(prev => (userLiked ? prev + 1 : prev - 1));
    }
  }

  async function handleToggleFavorite() {
    const token = localStorage.getItem('token');
    if (!token) {
      if (!toastId) {
        toastId = toast.error('You must be logged in to favorite images.', {
          position: 'top-right',
          autoClose: 3000,
          className: 'toast-notification',
          onClose: () => {
            toastId = null;
          },
        });
      }
      return;
    }
    setIsFavorite(!isFavorite);
    try {
      await toggleFavorite(image.id, process.env.REACT_APP_BACKEND_URL);
    } catch {
      setIsFavorite(isFavorite);
    }
  }

  function openModal() {
    setIsModalOpen(true);
  }

  function closeModal() {
    setIsModalOpen(false);
  }

  return (
      <div className="relative group mb-4">
        {image.url ? (
            <>
              <div
                  className={`relative overflow-hidden transition-colors duration-300 ${
                      imgLoaded ? '' : 'bg-white dark:bg-gray-800'
                  }`}
              >
                <img
                    src={image.url}
                    alt={image.description || 'Image'}
                    loading="lazy"
                    onLoad={() => setImgLoaded(true)}
                    onClick={openModal}
                    className={`w-full h-auto object-cover transition-all duration-300 ${
                        imgLoaded ? 'opacity-100' : 'opacity-0'
                    } hover:scale-105 cursor-pointer`}
                />
              </div>
              <div className="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-70 transition-opacity duration-300 p-2 flex justify-between">
                <p className="text-white text-sm break-words max-w-[70%]">
                  {image.description}
                </p>
                <div className="flex items-center space-x-2 justify-end">
                  <FavoriteButton
                      isFavorite={isFavorite}
                      onToggleFavorite={handleToggleFavorite}
                  />
                  <LikeButton
                      userLiked={userLiked}
                      likeCount={likeCount}
                      onLike={handleToggleLike}
                  />
                </div>
              </div>
            </>
        ) : (
            <div className="animate-pulse bg-gray-300 w-full h-60" />
        )}
        <ImageModal
            isOpen={isModalOpen}
            onClose={closeModal}
            imageUrl={image.url}
            userLiked={userLiked}
            likeCount={likeCount}
            isFavorite={isFavorite}
            onLike={handleToggleLike}
            onFavorite={handleToggleFavorite}
        />
      </div>
  );
}