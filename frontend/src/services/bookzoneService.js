const BOOKZONE_BASE_URL = `${process.env.REACT_APP_BOOKZONE_BACKEND_URL}/books`;

export async function getCuratedBooks() {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to fetch books');
    }

    const books = await response.json();

    return books.map(book => {
      if (book.coverUrl && book.coverUrl.includes('s3.amazonaws.com')) {
        book.needsPresignedUrl = true;
      }
      return book;
    });
  } catch (error) {
    console.error('Error fetching curated books:', error);
    throw error;
  }
}

export async function getBookCoverPresignedUrl(objectKey) {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/covers/${encodeURIComponent(objectKey)}/presigned-url`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to get presigned URL');
    }
    const data = await response.json();
    return data.presignedUrl;
  } catch (error) {
    console.error('Error getting presigned URL:', error);
    throw error;
  }
}

export async function searchOpenLibraryBooks(searchTerm) {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/search?q=${encodeURIComponent(searchTerm)}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to search books');
    }

    return await response.json();
  } catch (error) {
    console.error('Error searching books:', error);
    throw error;
  }
}

export async function importBook(bookOrKey) {
  try {
    let key;
    if (typeof bookOrKey === 'string') {
      key = bookOrKey;
    } else if (bookOrKey && typeof bookOrKey === 'object') {
      key = bookOrKey.key || bookOrKey.olKey || bookOrKey.openLibraryKey;
    }

    if (!key) {
      throw new Error('Invalid book key');
    }

    const response = await fetch(`${BOOKZONE_BASE_URL}/import`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ openLibraryKey: key }),
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to import book');
    }

    const importedBook = await response.json();
    
    if (importedBook.coverUrl && importedBook.coverUrl.includes('s3.amazonaws.com')) {
      importedBook.needsPresignedUrl = true;
    }
    
    return importedBook;
  } catch (error) {
    console.error('Error importing book:', error);
    throw error;
  }
}

export const getBookById = async (bookId) => {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/details/${bookId}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to fetch book details');
    }

    const book = await response.json();
    
    if (book.coverUrl && book.coverUrl.includes('s3.amazonaws.com')) {
      book.needsPresignedUrl = true;
    }
    
    return book;
  } catch (error) {
    console.error('Error fetching book details:', error);
    throw error;
  }
}

export const queryBookInfo = async (title, author, query) => {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/ai/query-book`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ title, author, query }),
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to query book information');
    }

    const result = await response.json();
    return result.answer;
  } catch (error) {
    console.error('Error querying book information:', error);
    throw error;
  }
};

export const generateVisualPrompt = async (title, author, subject) => {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/ai/generate-visual-prompt`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ title, author, subject }),
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to generate visual prompt');
    }

    const result = await response.json();
    return result.imagePrompt;
  } catch (error) {
    console.error('Error generating visual prompt:', error);
    throw error;
  }
};

export const getBookScenes = async (title, author) => {
  try {
    const query = `List 5 iconic or memorable scenes from the book "${title}" by ${author}. Format the response as JSON with this structure: [{"title": "Brief scene title", "description": "Short description of the scene (2-3 sentences)"}]. Return just the JSON.`;
    
    const scenesData = await queryBookInfo(title, author, query);
    
    try {
      let parsedScenes = [];
      
      try {
        parsedScenes = JSON.parse(scenesData);
      } catch (jsonError) {
        const jsonMatch = scenesData.match(/\[\s*\{.*\}\s*\]/s);
        if (jsonMatch) {
          parsedScenes = JSON.parse(jsonMatch[0]);
        } else {
          throw new Error("Could not extract JSON");
        }
      }
      
      if (Array.isArray(parsedScenes) && parsedScenes.length > 0) {
        return parsedScenes.map((scene, index) => ({
          id: index + 1,
          title: scene.title,
          description: scene.description
        }));
      } else {
        throw new Error("No valid scenes returned");
      }
    } catch (parseError) {
      console.error('Error parsing scenes data:', parseError);
      throw parseError;
    }
  } catch (error) {
    console.error('Error fetching book scenes:', error);
    throw error;
  }
};

export const getCharacterAnalysis = async (title, author, character) => {
  try {
    const personalityQuery = `Analyze the character ${character} from the book "${title}" by ${author}. Provide insights about their personality, role in the story, key traits, and development. Keep the analysis concise but insightful.`;
    
    const personalityAnalysis = await queryBookInfo(title, author, personalityQuery);
    
    const motivationsQuery = `Analyze the motivations and philosophy of the character ${character} from the book "${title}" by ${author}. What drives them? What beliefs guide their actions? What internal conflicts do they face? Keep the analysis concise but meaningful.`;
    
    const motivationsAnalysis = await queryBookInfo(title, author, motivationsQuery);
    
    return {
      personality: personalityAnalysis,
      motivations: motivationsAnalysis
    };
  } catch (error) {
    console.error('Error fetching character analysis:', error);
    throw error;
  }
};

export const getThematicAnalysis = async (title, author) => {
  try {
    const query = `Analyze the key themes in the book "${title}" by ${author}. Identify 3-5 major themes and provide a brief explanation of how each theme is developed throughout the narrative. Focus on the most significant and profound themes.`;
    
    const thematicAnalysis = await queryBookInfo(title, author, query);
    
    return thematicAnalysis;
  } catch (error) {
    console.error('Error fetching thematic analysis:', error);
    throw error;
  }
};

export const generateImage = async (prompt) => {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/ai/generate-image`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ imagePrompt: prompt }),
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to generate image');
    }

    const result = await response.json();
    return result.imageUrl;
  } catch (error) {
    console.error('Error generating image:', error);
    throw error;
  }
}; 
