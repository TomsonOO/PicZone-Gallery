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
    const response = await fetch(`${BOOKZONE_BASE_URL}/covers?objectKey=${objectKey}`, {
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

    return await response.json();
  } catch (error) {
    console.error('Error importing book:', error);
    throw error;
  }
}

export const getBookById = async (bookId) => {
  try {
    const mockBooks = {
      "d580e8eb-37b1-408b-9f08-47c4d852126b": {
        id: "d580e8eb-37b1-408b-9f08-47c4d852126b",
        title: "The Great Gatsby",
        author: "F. Scott Fitzgerald",
        createdAt: "2023-05-15T10:30:00Z",
        olKey: "OL6738583W",
        coverUrl: "https://covers.openlibrary.org/b/id/8850135-L.jpg",
        description: "The Great Gatsby is a 1925 novel by American writer F. Scott Fitzgerald. Set in the Jazz Age on Long Island, near New York City, the novel depicts first-person narrator Nick Carraway's interactions with mysterious millionaire Jay Gatsby and Gatsby's obsession to reunite with his former lover, Daisy Buchanan."
      },
      "a1b2c3d4-e5f6-4a5b-9c8d-7e6f5a4b3c2d": {
        id: "a1b2c3d4-e5f6-4a5b-9c8d-7e6f5a4b3c2d",
        title: "To Kill a Mockingbird",
        author: "Harper Lee",
        createdAt: "2023-05-16T14:20:00Z",
        olKey: "OL7823731W",
        coverUrl: "https://covers.openlibrary.org/b/id/8759162-L.jpg",
        description: "To Kill a Mockingbird is a novel by Harper Lee published in 1960. It was immediately successful, winning the Pulitzer Prize, and has become a classic of modern American literature. The plot and characters are loosely based on Lee's observations of her family, her neighbors and an event that occurred near her hometown of Monroeville, Alabama, in 1936, when she was ten."
      },
      "g7h8i9j0-k1l2-8m9n-0o1p-2q3r4s5t6u7v": {
        id: "g7h8i9j0-k1l2-8m9n-0o1p-2q3r4s5t6u7v",
        title: "1984",
        author: "George Orwell",
        createdAt: "2023-05-17T09:15:00Z",
        olKey: "OL24196626M",
        coverUrl: "https://covers.openlibrary.org/b/id/8575384-L.jpg",
        description: "1984 is a dystopian social science fiction novel by English novelist George Orwell. It was published on 8 June 1949 as Orwell's ninth and final book completed in his lifetime. Thematically, 1984 centres on the consequences of totalitarianism, mass surveillance, and repressive regimentation of persons and behaviours within society."
      }
    };

    if (mockBooks[bookId]) {
      await new Promise(resolve => setTimeout(resolve, 500));
      return mockBooks[bookId];
    }

    /*
    const response = await fetch(`${BOOKZONE_BASE_URL}/${bookId}`, {
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
    */
    
    throw new Error('Book not found');
  } catch (error) {
    console.error('Error fetching book details:', error);
    throw error;
  }
} 
