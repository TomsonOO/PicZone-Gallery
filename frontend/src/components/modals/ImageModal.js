import React from 'react';
import Modal from 'react-modal';
import LikeButton from '../buttons/LikeButton';
import FavoriteButton from '../buttons/FavoriteButton';

Modal.setAppElement('#root');

export default function ImageModal({
  isOpen,
  onClose,
  imageUrl,
  userLiked,
  likeCount,
  animating,
  isFavorite,
  onLike,
  onFavorite,
}) {
  return (
    <Modal
      isOpen={isOpen}
      onRequestClose={onClose}
      overlayClassName='fixed inset-0 bg-black bg-opacity-70 z-50 flex justify-center items-center'
      className='p-0 m-0 border-0 outline-none bg-transparent max-w-full relative'
      contentLabel='Image Modal'
    >
      <button
        onClick={onClose}
        className='absolute top-4 right-4 text-white text-3xl hover:text-gray-300'
      >
        ✕
      </button>
      <div className='flex justify-center items-center w-full h-full'>
        <div className='relative max-w-screen max-h-screen'>
          <img
            src={imageUrl}
            alt='Modal'
            className='max-w-full max-h-[85vh] object-contain'
          />
          <div className='absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-60 px-4 py-2 rounded flex items-center space-x-4'>
            <FavoriteButton
              isFavorite={isFavorite}
              onToggleFavorite={onFavorite}
            />
            <LikeButton
              userLiked={userLiked}
              likeCount={likeCount}
              animating={animating}
              onLike={onLike}
            />
          </div>
        </div>
      </div>
    </Modal>
  );
}
