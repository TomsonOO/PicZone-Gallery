import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { getBookById, getBookCoverPresignedUrl } from '../../services/bookzoneService';
import Sidebar from '../../components/Sidebar';
import { toast } from 'react-toastify';
import './BookDetailPage.css';

const BookDetailPage = () => {
  const { bookId } = useParams();
  const [bookData, setBookData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  const [coverUrl, setCoverUrl] = useState('');

  useEffect(() => {
    const fetchBookData = async () => {
      setIsLoading(true);
      setError(null);
      
      try {
        const data = await getBookById(bookId);
        setBookData(data);
        
        if (data.needsPresignedUrl && data.objectKey) {
          try {
            const presignedUrl = await getBookCoverPresignedUrl(data.objectKey);
            setCoverUrl(presignedUrl);
          } catch (coverError) {
            console.error('Error fetching cover URL:', coverError);
            setCoverUrl(data.coverUrl || '');
          }
        } else {
          setCoverUrl(data.coverUrl || '');
        }
      } catch (err) {
        setError(err.message || 'Failed to load book details');
        toast.error('Error loading book details. Please try again later.');
      } finally {
        setIsLoading(false);
      }
    };

    if (bookId) {
      fetchBookData();
    }
  }, [bookId]);

  return (
    <div className="flex flex-col min-h-screen">
      <div className="flex flex-grow">
        <Sidebar />
        <main className="flex flex-col flex-grow p-4 bg-gray-150 dark:bg-gradient-to-b from-[#111f4a] to-[#1a327e]">
          {isLoading ? (
            <div className="text-center py-6">
              <p className="text-gray-600 dark:text-gray-300">Loading book details...</p>
            </div>
          ) : error ? (
            <div className="text-center py-6">
              <p className="text-red-500 dark:text-red-400">Error: {error}</p>
            </div>
          ) : !bookData ? (
            <div className="text-center py-6">
              <p className="text-gray-600 dark:text-gray-300">Book not found</p>
            </div>
          ) : (
            <div className="book-detail-container">
              <div className="book-detail-left-column">
                <div className="book-cover-container">
                  {coverUrl ? (
                    <img 
                      src={coverUrl} 
                      alt={`Cover for ${bookData.title}`} 
                      className="book-detail-cover-image" 
                    />
                  ) : (
                    <div className="book-cover-placeholder">
                      No cover available
                    </div>
                  )}
                </div>
                <h2 className="book-title">{bookData.title}</h2>
                <h2 className="book-author">by {bookData.author}</h2>
                
                <div className="book-metadata">
                  <p className="book-date">Added on: {new Date(bookData.createdAt).toLocaleDateString()}</p>
                  {bookData.olKey && <p className="book-key">Open Library Key: {bookData.olKey}</p>}
                </div>
                
                <p className="book-description">{bookData.description || "The Great Gatsby is a 1925 novel by American writer F. Scott Fitzgerald. Set in the Jazz Age on Long Island, near New York City, the novel depicts first-person narrator Nick Carraway's interactions with mysterious millionaire Jay Gatsby and Gatsby's obsession to reunite with his former lover, Daisy Buchanan."}</p>
              </div>
              
              <div className="book-detail-right-column">
                <section className="character-visualizer-section">
                  <h3>Character Visualizer</h3>
                  <select className="character-select">
                    <option disabled selected>Select Character</option>
                  </select>
                  <button 
                    className="generate-button"
                    onClick={() => console.log('Generate image clicked')}
                  >
                    Generate Visual Interpretation
                  </button>
                  <div className="image-placeholder"></div>
                </section>

                <section className="character-deep-dive-section">
                  <h3>Character Deep Dive</h3>
                  <select className="character-select">
                    <option disabled selected>Select Character</option>
                  </select>
                  <div className="analysis-placeholder">Personality & Role analysis will appear here...</div>
                  <div className="motivation-placeholder">Motivations & Philosophy analysis will appear here...</div>
                </section>

                <section className="thematic-exploration-section">
                  <h3>Thematic Exploration</h3>
                  <button 
                    className="analyze-button"
                    onClick={() => console.log('Analyze themes clicked')}
                  >
                    Analyze Key Themes
                  </button>
                  <div className="themes-placeholder">Thematic analysis will appear here...</div>
                </section>
              </div>
            </div>
          )}
        </main>
      </div>
    </div>
  );
};

export default BookDetailPage; 