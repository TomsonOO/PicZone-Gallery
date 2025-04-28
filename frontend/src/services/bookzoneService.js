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

export async function importBook(openLibraryKey) {
  try {
    const response = await fetch(`${BOOKZONE_BASE_URL}/import`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ openLibraryKey }),
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
