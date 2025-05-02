import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { getBookById, getBookCoverPresignedUrl, generateVisualPrompt, generateImage, queryBookInfo } from '../services/bookzoneService';
import Sidebar from '../components/Sidebar';
import { toast } from 'react-toastify';

const BookDetailPage = () => {
  const { bookId } = useParams();
  const [bookData, setBookData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  const [coverUrl, setCoverUrl] = useState('');
  
  // Character-related state
  const [characters, setCharacters] = useState([]);
  const [isLoadingCharacters, setIsLoadingCharacters] = useState(false);
  const [selectedCharacter, setSelectedCharacter] = useState('');
  const [isGeneratingPrompt, setIsGeneratingPrompt] = useState(false);
  const [isGeneratingImage, setIsGeneratingImage] = useState(false);
  const [generatedImageUrl, setGeneratedImageUrl] = useState('');

  useEffect(() => {
    const fetchBookData = async () => {
      setIsLoading(true);
      setError(null);
      
      try {
        const data = await getBookById(bookId);
        setBookData(data);
        
        if (data.coverUrl) {
          if (data.needsPresignedUrl) {
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

  useEffect(() => {
    const fetchCharacters = async () => {
      if (!bookData) return;
      
      setIsLoadingCharacters(true);
      try {
        // Temporary fallback for The Great Gatsby
        if (bookData.title.includes("Gatsby")) {
          setCharacters([
            { id: 1, name: 'Jay Gatsby' },
            { id: 2, name: 'Daisy Buchanan' },
            { id: 3, name: 'Nick Carraway' },
            { id: 4, name: 'Tom Buchanan' },
            { id: 5, name: 'Jordan Baker' }
          ]);
        }
        // To Kill a Mockingbird
        else if (bookData.title.includes("Mockingbird")) {
          setCharacters([
            { id: 1, name: 'Atticus Finch' },
            { id: 2, name: 'Scout Finch' },
            { id: 3, name: 'Jem Finch' },
            { id: 4, name: 'Boo Radley' },
            { id: 5, name: 'Tom Robinson' }
          ]);
        }
        // 1984
        else if (bookData.title.includes("1984")) {
          setCharacters([
            { id: 1, name: 'Winston Smith' },
            { id: 2, name: 'Julia' },
            { id: 3, name: 'O\'Brien' },
            { id: 4, name: 'Big Brother' },
            { id: 5, name: 'Emmanuel Goldstein' }
          ]);
        }
        // Any other book - create some sample characters
        else {
          setCharacters([
            { id: 1, name: 'Main Protagonist' },
            { id: 2, name: 'Antagonist' },
            { id: 3, name: 'Supporting Character' },
            { id: 4, name: 'Mentor Figure' },
            { id: 5, name: 'Side Character' }
          ]);
        }
      } catch (err) {
        console.error('Error fetching characters:', err);
        toast.error('Could not load character information');
      } finally {
        setIsLoadingCharacters(false);
      }
    };

    fetchCharacters();
  }, [bookData]);

  const handleCharacterChange = (e) => {
    setSelectedCharacter(e.target.value);
  };

  const handleGenerateImage = async () => {
    if (!selectedCharacter || !bookData) {
      toast.error('Please select a character first');
      return;
    }

    setIsGeneratingPrompt(true);
    setGeneratedImageUrl('');

    try {
      const subject = `character: ${selectedCharacter}`;
      const visualPrompt = await generateVisualPrompt(
        bookData.title,
        bookData.author,
        subject
      );

      setIsGeneratingPrompt(false);
      setIsGeneratingImage(true);

      const imageUrl = await generateImage(visualPrompt);
      setGeneratedImageUrl(imageUrl);
      toast.success('Image generated successfully!');
    } catch (err) {
      toast.error(err.message || 'Failed to generate image');
      console.error('Error generating image:', err);
    } finally {
      setIsGeneratingPrompt(false);
      setIsGeneratingImage(false);
    }
  };

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
            <div className="flex flex-col md:flex-row gap-8 p-4 text-gray-100">
              <div className="md:w-1/3">
                <div className="flex justify-center mb-6">
                  {coverUrl ? (
                    <img 
                      src={coverUrl} 
                      alt={`Cover for ${bookData.title}`} 
                      className="max-w-full rounded-lg shadow-lg" 
                    />
                  ) : (
                    <div className="w-60 h-90 flex items-center justify-center bg-gray-700 rounded-lg text-gray-400">
                      No cover available
                    </div>
                  )}
                </div>
                <h2 className="text-2xl font-bold text-white mb-1">{bookData.title}</h2>
                <h3 className="text-xl text-gray-300 mb-5">by {bookData.author}</h3>
                
                <div className="my-4 py-2 border-t border-b border-gray-700 text-sm text-gray-400">
                  <p className="my-1">Added on: {new Date(bookData.createdAt).toLocaleDateString()}</p>
                  {bookData.olKey && <p className="my-1">Open Library Key: {bookData.olKey}</p>}
                </div>
                
                <p className="leading-relaxed mb-8 text-gray-300">{bookData.description || "No description available for this book."}</p>
              </div>
              
              <div className="md:w-2/3">
                <section className="bg-blue-900/30 rounded-lg p-6 mb-8">
                  <h3 className="text-xl font-bold mb-4 text-gray-200">Character Visualizer</h3>
                  <select 
                    className="w-full bg-gray-700 text-white p-2 rounded mb-4 text-base"
                    value={selectedCharacter}
                    onChange={handleCharacterChange}
                    disabled={isLoadingCharacters || characters.length === 0}
                  >
                    <option value="" disabled>
                      {isLoadingCharacters 
                        ? 'Loading characters...' 
                        : characters.length === 0 
                          ? 'No characters available' 
                          : 'Select Character'}
                    </option>
                    {characters.map(character => (
                      <option key={character.id} value={character.name}>
                        {character.name}
                      </option>
                    ))}
                  </select>
                  <button 
                    className={`w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded mb-4 text-base cursor-pointer transition-colors ${
                      (isGeneratingPrompt || isGeneratingImage || !selectedCharacter) ? 'opacity-50 cursor-not-allowed' : ''
                    }`}
                    onClick={handleGenerateImage}
                    disabled={isGeneratingPrompt || isGeneratingImage || !selectedCharacter}
                  >
                    {isGeneratingPrompt 
                      ? 'Generating Description...' 
                      : isGeneratingImage 
                        ? 'Creating Image...' 
                        : 'Generate Visual Interpretation'}
                  </button>
                  <div className="w-full h-64 bg-gray-700 rounded mt-4 flex items-center justify-center">
                    {generatedImageUrl ? (
                      <img 
                        src={generatedImageUrl} 
                        alt={`AI visualization of ${selectedCharacter}`}
                        className="max-w-full max-h-full rounded" 
                      />
                    ) : (
                      <p className="text-gray-400">
                        {isGeneratingPrompt || isGeneratingImage 
                          ? 'Creating your visualization...' 
                          : 'Character visualization will appear here'}
                      </p>
                    )}
                  </div>
                </section>

                <section className="bg-blue-900/30 rounded-lg p-6 mb-8">
                  <h3 className="text-xl font-bold mb-4 text-gray-200">Character Deep Dive</h3>
                  <select className="w-full bg-gray-700 text-white p-2 rounded mb-4 text-base">
                    <option value="" disabled selected>
                      {isLoadingCharacters 
                        ? 'Loading characters...' 
                        : characters.length === 0 
                          ? 'No characters available' 
                          : 'Select Character'}
                    </option>
                    {characters.map(character => (
                      <option key={character.id} value={character.name}>
                        {character.name}
                      </option>
                    ))}
                  </select>
                  <div className="bg-gray-700 p-4 rounded mt-4 text-gray-400 min-h-[100px]">
                    Personality & Role analysis will appear here...
                  </div>
                  <div className="bg-gray-700 p-4 rounded mt-4 text-gray-400 min-h-[100px]">
                    Motivations & Philosophy analysis will appear here...
                  </div>
                </section>

                <section className="bg-blue-900/30 rounded-lg p-6 mb-8">
                  <h3 className="text-xl font-bold mb-4 text-gray-200">Thematic Exploration</h3>
                  <button 
                    className="w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded mb-4 text-base cursor-pointer transition-colors"
                    onClick={() => console.log('Analyze themes clicked')}
                  >
                    Analyze Key Themes
                  </button>
                  <div className="bg-gray-700 p-4 rounded mt-4 text-gray-400 min-h-[100px]">
                    Thematic analysis will appear here...
                  </div>
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