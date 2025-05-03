import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import { getBookById, getBookCoverPresignedUrl, generateVisualPrompt, generateImage, queryBookInfo, getBookScenes, getCharacterAnalysis, getThematicAnalysis } from '../services/bookzoneService';
import Sidebar from '../components/Sidebar';
import { toast } from 'react-toastify';

const BookDetailPage = () => {
  const { bookId } = useParams();
  const [bookData, setBookData] = useState(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);
  const [coverUrl, setCoverUrl] = useState('');
  
  const [characters, setCharacters] = useState([]);
  const [isLoadingCharacters, setIsLoadingCharacters] = useState(false);
  const [selectedCharacter, setSelectedCharacter] = useState('');
  const [customCharacter, setCustomCharacter] = useState('');
  const [usingCustomCharacter, setUsingCustomCharacter] = useState(false);
  const [isGeneratingPrompt, setIsGeneratingPrompt] = useState(false);
  const [isGeneratingImage, setIsGeneratingImage] = useState(false);
  const [generatedImageUrl, setGeneratedImageUrl] = useState('');
  
  const [scenes, setScenes] = useState([]);
  const [isLoadingScenes, setIsLoadingScenes] = useState(false);
  const [selectedScene, setSelectedScene] = useState('');
  const [customScene, setCustomScene] = useState('');
  const [usingCustomScene, setUsingCustomScene] = useState(false);
  const [isGeneratingScenePrompt, setIsGeneratingScenePrompt] = useState(false);
  const [isGeneratingSceneImage, setIsGeneratingSceneImage] = useState(false);
  const [generatedSceneImageUrl, setGeneratedSceneImageUrl] = useState('');
  const [selectedSceneData, setSelectedSceneData] = useState(null);
  
  const [selectedDeepDiveCharacter, setSelectedDeepDiveCharacter] = useState('');
  const [characterAnalysis, setCharacterAnalysis] = useState(null);
  const [isLoadingCharacterAnalysis, setIsLoadingCharacterAnalysis] = useState(false);
  
  const [thematicAnalysis, setThematicAnalysis] = useState('');
  const [isLoadingThematicAnalysis, setIsLoadingThematicAnalysis] = useState(false);

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
      setCharacters([]);
      
      try {
        const bookDescription = bookData.description || `${bookData.title} by ${bookData.author}`;
        
        const charactersQuery = `Based on the book "${bookData.title}" by ${bookData.author}, extract 6 real character names from the book. If you don't know the exact characters, provide your best guess based on the genre and setting. Format the response as JSON array like this: [{"name": "Character Name", "role": "Main/Secondary/Supporting"}]. Do not add any explanations, just return the JSON.`;
        
        const charactersData = await queryBookInfo(
          bookData.title,
          bookData.author,
          charactersQuery
        );
        
        try {
          let parsedCharacters = [];
          
          try {
            parsedCharacters = JSON.parse(charactersData);
          } catch (jsonError) {
            const jsonMatch = charactersData.match(/\[\s*\{.*\}\s*\]/s);
            if (jsonMatch) {
              parsedCharacters = JSON.parse(jsonMatch[0]);
            } else {
              throw new Error("Could not extract JSON");
            }
          }
          
          if (Array.isArray(parsedCharacters) && parsedCharacters.length > 0) {
            const formattedCharacters = parsedCharacters.map((char, index) => ({
              id: index + 1,
              name: char.name,
              role: char.role || 'Character'
            }));
            
            setCharacters(formattedCharacters);
          } else {
            setDefaultCharacters();
          }
        } catch (parseError) {
          console.error('Error processing character data:', parseError);
          setDefaultCharacters();
        }
      } catch (err) {
        console.error('Error fetching characters:', err);
        toast.error('Could not load character information');
        setDefaultCharacters();
      } finally {
        setIsLoadingCharacters(false);
      }
    };

    const setDefaultCharacters = () => {
      setCharacters([
        { id: 1, name: 'Main Character', role: 'Main' },
        { id: 2, name: 'Secondary Character', role: 'Secondary' },
        { id: 3, name: 'Supporting Character', role: 'Supporting' },
        { id: 4, name: 'Side Character', role: 'Side' }
      ]);
    };

    fetchCharacters();
  }, [bookData]);
  
  useEffect(() => {
    const fetchScenes = async () => {
      if (!bookData) return;
      
      setIsLoadingScenes(true);
      setScenes([]);
      
      try {
        const scenesData = await getBookScenes(bookData.title, bookData.author);
        setScenes(scenesData);
      } catch (err) {
        console.error('Error fetching scenes:', err);
        setDefaultScenes();
      } finally {
        setIsLoadingScenes(false);
      }
    };
    
    const setDefaultScenes = () => {
      setScenes([
        { id: 1, title: 'Opening Scene', description: 'The beginning of the story that sets the stage.' },
        { id: 2, title: 'Climactic Moment', description: 'The most intense and important moment in the story.' },
        { id: 3, title: 'Plot Twist', description: 'A surprising turn of events that changes the direction of the story.' },
        { id: 4, title: 'Character Development', description: 'A key moment of growth or revelation for a main character.' },
        { id: 5, title: 'Conclusion', description: 'The final scene that resolves the story.' }
      ]);
    };
    
    fetchScenes();
  }, [bookData]);

  const handleCharacterChange = (e) => {
    if (e.target.value === 'custom') {
      setUsingCustomCharacter(true);
      setSelectedCharacter('');
    } else {
      setUsingCustomCharacter(false);
      setSelectedCharacter(e.target.value);
    }
  };

  const handleCustomCharacterChange = (e) => {
    setCustomCharacter(e.target.value);
  };
  
  const handleSceneChange = (e) => {
    if (e.target.value === 'custom') {
      setUsingCustomScene(true);
      setSelectedScene('');
      setSelectedSceneData(null);
    } else {
      setUsingCustomScene(false);
      setSelectedScene(e.target.value);
      
      const sceneData = scenes.find(scene => scene.title === e.target.value);
      setSelectedSceneData(sceneData);
    }
  };
  
  const handleCustomSceneChange = (e) => {
    setCustomScene(e.target.value);
  };

  const handleGenerateImage = async () => {
    const characterToVisualize = usingCustomCharacter ? customCharacter : selectedCharacter;
    
    if ((!characterToVisualize || characterToVisualize.trim() === '') || !bookData) {
      toast.error('Please select or enter a character name');
      return;
    }

    setIsGeneratingPrompt(true);
    setGeneratedImageUrl('');

    try {
      const subject = `character: ${characterToVisualize}`;
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
  
  const handleGenerateSceneImage = async () => {
    const sceneToVisualize = usingCustomScene 
      ? customScene 
      : (selectedSceneData ? `${selectedSceneData.title} - ${selectedSceneData.description}` : selectedScene);
    
    if ((!sceneToVisualize || sceneToVisualize.trim() === '') || !bookData) {
      toast.error('Please select or enter a scene description');
      return;
    }

    setIsGeneratingScenePrompt(true);
    setGeneratedSceneImageUrl('');

    try {
      const subject = `scene: ${sceneToVisualize}`;
      const visualPrompt = await generateVisualPrompt(
        bookData.title,
        bookData.author,
        subject
      );

      setIsGeneratingScenePrompt(false);
      setIsGeneratingSceneImage(true);

      const imageUrl = await generateImage(visualPrompt);
      setGeneratedSceneImageUrl(imageUrl);
      toast.success('Scene image generated successfully!');
    } catch (err) {
      toast.error(err.message || 'Failed to generate scene image');
      console.error('Error generating scene image:', err);
    } finally {
      setIsGeneratingScenePrompt(false);
      setIsGeneratingSceneImage(false);
    }
  };

  const handleCharacterDeepDiveChange = (e) => {
    setSelectedDeepDiveCharacter(e.target.value);
    if (e.target.value) {
      fetchCharacterAnalysis(e.target.value);
    } else {
      setCharacterAnalysis(null);
    }
  };
  
  const fetchCharacterAnalysis = async (character) => {
    if (!bookData || !character) return;
    
    setIsLoadingCharacterAnalysis(true);
    setCharacterAnalysis(null);
    
    try {
      const analysis = await getCharacterAnalysis(
        bookData.title,
        bookData.author,
        character
      );
      
      setCharacterAnalysis(analysis);
    } catch (err) {
      console.error('Error fetching character analysis:', err);
      toast.error('Could not load character analysis');
    } finally {
      setIsLoadingCharacterAnalysis(false);
    }
  };
  
  const handleAnalyzeThemes = async () => {
    if (!bookData) return;
    
    setIsLoadingThematicAnalysis(true);
    setThematicAnalysis('');
    
    try {
      const analysis = await getThematicAnalysis(
        bookData.title,
        bookData.author
      );
      
      setThematicAnalysis(analysis);
    } catch (err) {
      console.error('Error fetching thematic analysis:', err);
      toast.error('Could not load thematic analysis');
    } finally {
      setIsLoadingThematicAnalysis(false);
    }
  };

  const charactersByRole = characters.reduce((acc, character) => {
    const role = character.role || 'Character';
    if (!acc[role]) {
      acc[role] = [];
    }
    acc[role].push(character);
    return acc;
  }, {});

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
                  
                  {isLoadingCharacters ? (
                    <div className="text-center py-10">
                      <div className="animate-pulse flex flex-col items-center justify-center">
                        <div className="h-4 bg-gray-600 rounded w-3/4 mb-4"></div>
                        <div className="h-4 bg-gray-600 rounded w-1/2"></div>
                      </div>
                    </div>
                  ) : (
                    <div className="flex flex-col md:flex-row gap-6">
                      <div className="md:w-1/2">
                        <div className="mb-4">
                          <label className="block text-gray-300 mb-2 font-medium">Choose a character to visualize:</label>
                          <select 
                            className="w-full bg-gray-700 text-white p-3 rounded text-base border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value={usingCustomCharacter ? 'custom' : selectedCharacter}
                            onChange={handleCharacterChange}
                            disabled={isLoadingCharacters || characters.length === 0}
                          >
                            <option value="" disabled>
                              {characters.length === 0 ? 'No characters available' : 'Select a character'}
                            </option>
                            
                            {Object.entries(charactersByRole).map(([role, chars]) => (
                              <optgroup label={role} key={role}>
                                {chars.map(character => (
                                  <option key={character.id} value={character.name}>
                                    {character.name}
                                  </option>
                                ))}
                              </optgroup>
                            ))}
                            
                            <option value="custom">Enter custom character...</option>
                          </select>
                        </div>
                        
                        {usingCustomCharacter && (
                          <div className="mb-4">
                            <label className="block text-gray-300 mb-2 font-medium">Enter character name:</label>
                            <input
                              type="text"
                              value={customCharacter}
                              onChange={handleCustomCharacterChange}
                              placeholder="Enter any character name..."
                              className="w-full bg-gray-700 text-white p-3 rounded text-base border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                          </div>
                        )}
                        
                        <button 
                          className={`w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded text-base font-medium cursor-pointer transition-colors duration-200 ${
                            (isGeneratingPrompt || isGeneratingImage || (usingCustomCharacter ? !customCharacter : !selectedCharacter)) 
                              ? 'opacity-50 cursor-not-allowed' : ''
                          }`}
                          onClick={handleGenerateImage}
                          disabled={isGeneratingPrompt || isGeneratingImage || (usingCustomCharacter ? !customCharacter : !selectedCharacter)}
                        >
                          {isGeneratingPrompt 
                            ? 'Generating Description...' 
                            : isGeneratingImage 
                              ? 'Creating Image...' 
                              : 'Generate Visual Interpretation'}
                        </button>
                      </div>
                      
                      <div className="md:w-1/2">
                        <div className="aspect-square bg-gray-700 rounded-lg shadow-lg overflow-hidden flex items-center justify-center relative">
                          {generatedImageUrl ? (
                            <img 
                              src={generatedImageUrl} 
                              alt={`AI visualization of ${usingCustomCharacter ? customCharacter : selectedCharacter}`}
                              className="object-cover w-full h-full" 
                            />
                          ) : (
                            <div className="text-center p-6 text-gray-400">
                              {isGeneratingPrompt || isGeneratingImage ? (
                                <div className="flex flex-col items-center">
                                  <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
                                  <p>{isGeneratingPrompt ? 'Creating description...' : 'Generating image...'}</p>
                                </div>
                              ) : (
                                <div className="flex flex-col items-center">
                                  <svg className="w-16 h-16 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                  </svg>
                                  <p className="text-sm">Select a character and click generate to visualize</p>
                                </div>
                              )}
                            </div>
                          )}
                        </div>
                        
                        {generatedImageUrl && (
                          <div className="mt-3 text-center">
                            <p className="text-gray-300 text-sm">
                              Visualization of {usingCustomCharacter ? customCharacter : selectedCharacter}
                            </p>
                          </div>
                        )}
                      </div>
                    </div>
                  )}
                </section>
                
                <section className="bg-blue-900/30 rounded-lg p-6 mb-8">
                  <h3 className="text-xl font-bold mb-4 text-gray-200">Scene Visualizer</h3>
                  
                  {isLoadingScenes ? (
                    <div className="text-center py-10">
                      <div className="animate-pulse flex flex-col items-center justify-center">
                        <div className="h-4 bg-gray-600 rounded w-3/4 mb-4"></div>
                        <div className="h-4 bg-gray-600 rounded w-1/2"></div>
                      </div>
                    </div>
                  ) : (
                    <div className="flex flex-col md:flex-row gap-6">
                      <div className="md:w-1/2">
                        <div className="mb-4">
                          <label className="block text-gray-300 mb-2 font-medium">Choose a scene to visualize:</label>
                          <select 
                            className="w-full bg-gray-700 text-white p-3 rounded text-base border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value={usingCustomScene ? 'custom' : selectedScene}
                            onChange={handleSceneChange}
                            disabled={isLoadingScenes || scenes.length === 0}
                          >
                            <option value="" disabled>
                              {scenes.length === 0 ? 'No scenes available' : 'Select a scene'}
                            </option>
                            
                            {scenes.map(scene => (
                              <option key={scene.id} value={scene.title}>
                                {scene.title}
                              </option>
                            ))}
                            
                            <option value="custom">Enter custom scene...</option>
                          </select>
                        </div>
                        
                        {selectedSceneData && !usingCustomScene && (
                          <div className="mb-4 p-3 bg-gray-800/50 rounded-lg border border-gray-700">
                            <p className="text-sm text-gray-300">{selectedSceneData.description}</p>
                          </div>
                        )}
                        
                        {usingCustomScene && (
                          <div className="mb-4">
                            <label className="block text-gray-300 mb-2 font-medium">Describe the scene:</label>
                            <textarea
                              value={customScene}
                              onChange={handleCustomSceneChange}
                              placeholder="Describe any scene from the book..."
                              className="w-full bg-gray-700 text-white p-3 rounded text-base border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 min-h-[100px]"
                            />
                          </div>
                        )}
                        
                        <button 
                          className={`w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded text-base font-medium cursor-pointer transition-colors duration-200 ${
                            (isGeneratingScenePrompt || isGeneratingSceneImage || (usingCustomScene ? !customScene : !selectedScene)) 
                              ? 'opacity-50 cursor-not-allowed' : ''
                          }`}
                          onClick={handleGenerateSceneImage}
                          disabled={isGeneratingScenePrompt || isGeneratingSceneImage || (usingCustomScene ? !customScene : !selectedScene)}
                        >
                          {isGeneratingScenePrompt 
                            ? 'Generating Scene Description...' 
                            : isGeneratingSceneImage 
                              ? 'Creating Scene Image...' 
                              : 'Generate Scene Interpretation'}
                        </button>
                      </div>
                      
                      <div className="md:w-1/2">
                        <div className="aspect-square bg-gray-700 rounded-lg shadow-lg overflow-hidden flex items-center justify-center relative">
                          {generatedSceneImageUrl ? (
                            <img 
                              src={generatedSceneImageUrl} 
                              alt={`AI visualization of scene from ${bookData.title}`}
                              className="object-cover w-full h-full" 
                            />
                          ) : (
                            <div className="text-center p-6 text-gray-400">
                              {isGeneratingScenePrompt || isGeneratingSceneImage ? (
                                <div className="flex flex-col items-center">
                                  <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
                                  <p>{isGeneratingScenePrompt ? 'Creating scene description...' : 'Generating scene image...'}</p>
                                </div>
                              ) : (
                                <div className="flex flex-col items-center">
                                  <svg className="w-16 h-16 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                  </svg>
                                  <p className="text-sm">Select a scene and click generate to visualize</p>
                                </div>
                              )}
                            </div>
                          )}
                        </div>
                        
                        {generatedSceneImageUrl && (
                          <div className="mt-3 text-center">
                            <p className="text-gray-300 text-sm">
                              Visualization of scene: {usingCustomScene 
                                ? (customScene.length > 30 ? customScene.substring(0, 30) + '...' : customScene) 
                                : selectedScene}
                            </p>
                          </div>
                        )}
                      </div>
                    </div>
                  )}
                </section>

                <section className="bg-blue-900/30 rounded-lg p-6 mb-8">
                  <h3 className="text-xl font-bold mb-4 text-gray-200">Character Deep Dive</h3>
                  <select 
                    className="w-full bg-gray-700 text-white p-2 rounded mb-4 text-base border border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value={selectedDeepDiveCharacter}
                    onChange={handleCharacterDeepDiveChange}
                    disabled={isLoadingCharacters || characters.length === 0 || isLoadingCharacterAnalysis}
                  >
                    <option value="">
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
                  
                  <div className="bg-gray-700 p-4 rounded mt-4 text-gray-300 min-h-[100px]">
                    {isLoadingCharacterAnalysis ? (
                      <div className="flex items-center justify-center h-full">
                        <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                      </div>
                    ) : characterAnalysis ? (
                      characterAnalysis.personality
                    ) : (
                      <span className="text-gray-400">Personality & Role analysis will appear here...</span>
                    )}
                  </div>
                  
                  <div className="bg-gray-700 p-4 rounded mt-4 text-gray-300 min-h-[100px]">
                    {isLoadingCharacterAnalysis ? (
                      <div className="flex items-center justify-center h-full">
                        <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                      </div>
                    ) : characterAnalysis ? (
                      characterAnalysis.motivations
                    ) : (
                      <span className="text-gray-400">Motivations & Philosophy analysis will appear here...</span>
                    )}
                  </div>
                </section>

                <section className="bg-blue-900/30 rounded-lg p-6 mb-8">
                  <h3 className="text-xl font-bold mb-4 text-gray-200">Thematic Exploration</h3>
                  <button 
                    className={`w-full bg-blue-600 hover:bg-blue-700 text-white p-2 rounded mb-4 text-base cursor-pointer transition-colors ${
                      isLoadingThematicAnalysis ? 'opacity-50 cursor-not-allowed' : ''
                    }`}
                    onClick={handleAnalyzeThemes}
                    disabled={isLoadingThematicAnalysis || !bookData}
                  >
                    {isLoadingThematicAnalysis ? 'Analyzing...' : 'Analyze Key Themes'}
                  </button>
                  
                  <div className="bg-gray-700 p-4 rounded mt-4 text-gray-300 min-h-[100px] whitespace-pre-line">
                    {isLoadingThematicAnalysis ? (
                      <div className="flex items-center justify-center h-full">
                        <div className="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                      </div>
                    ) : thematicAnalysis ? (
                      thematicAnalysis
                    ) : (
                      <span className="text-gray-400">Thematic analysis will appear here...</span>
                    )}
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