import React, { useState, useEffect } from 'react';
import { getCuratedBooks, searchOpenLibraryBooks, importBook } from '../services/bookzoneService';
import Sidebar from '../components/Sidebar';
import SearchBar from '../components/SearchBar';
import BookCard from '../components/BookCard';
import BookSearchResultItem from '../components/BookSearchResultItem';
import { toast } from 'react-toastify';

const BookZonePage = () => {
  const [curatedBooks, setCuratedBooks] = useState([]);
  const [searchResults, setSearchResults] = useState([]);
  const [isLoadingCuratedBooks, setIsLoadingCuratedBooks] = useState(false);
  const [isLoadingSearchResults, setIsLoadingSearchResults] = useState(false);
  const [isImportingBook, setIsImportingBook] = useState(false);
  const [importingBookKey, setImportingBookKey] = useState(null);
  const [searchTerm, setSearchTerm] = useState('');

  const fetchCuratedBooks = async () => {
    setIsLoadingCuratedBooks(true);
    try {
      const booksData = await getCuratedBooks();
      setCuratedBooks(booksData);
    } catch (error) {
      console.error('Error fetching curated books:', error);
      toast.error('Failed to load books. Please try again later.');
    } finally {
      setIsLoadingCuratedBooks(false);
    }
  };

  useEffect(() => {
    fetchCuratedBooks();
  }, []);

  const handleSearch = async (searchTermValue) => {
    setSearchTerm(searchTermValue);
    if (!searchTermValue.trim()) {
      setSearchResults([]);
      return;
    }

    setIsLoadingSearchResults(true);
    try {
      const results = await searchOpenLibraryBooks(searchTermValue);
      setSearchResults(results);
    } catch (error) {
      console.error('Error searching books:', error);
      toast.error('Failed to search books. Please try again.');
      setSearchResults([]);
    } finally {
      setIsLoadingSearchResults(false);
    }
  };

  const handleImportBook = async (openLibraryKey) => {
    setIsImportingBook(true);
    setImportingBookKey(openLibraryKey);
    try {
      await importBook(openLibraryKey);
      toast.success('Book imported successfully!');
      await fetchCuratedBooks();
    } catch (error) {
      console.error('Error importing book:', error);
      toast.error('Failed to import book. Please try again.');
    } finally {
      setIsImportingBook(false);
      setImportingBookKey(null);
    }
  };

  return (
    <div className="flex flex-col min-h-screen">
      <div className="flex flex-grow">
        <Sidebar />
        <main className="flex flex-col flex-grow p-4 bg-gray-150 dark:bg-gradient-to-b from-[#111f4a] to-[#1a327e]">
          <h1 className="text-2xl font-bold mb-4 text-gray-800 dark:text-white">BookZone Library</h1>
          <div className="mb-6">
            <SearchBar onSearch={handleSearch} placeholder="Search for books by title or author..." />
          </div>

          {isLoadingCuratedBooks ? (
            <div className="text-center py-6">
              <p className="text-gray-600 dark:text-gray-300">Loading books...</p>
            </div>
          ) : (
            <div className="mb-8">
              <h2 className="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">
                Your Books
              </h2>
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                {curatedBooks.length > 0 ? (
                  curatedBooks.map(book => (
                    <BookCard key={book.id} book={book} />
                  ))
                ) : (
                  <p className="text-gray-600 dark:text-gray-400 col-span-full text-center">
                    No books yet. Search and import books to build your collection!
                  </p>
                )}
              </div>
            </div>
          )}

          {searchTerm && (
            <div className="mt-6">
              <h2 className="text-xl font-semibold mb-4 text-gray-700 dark:text-gray-200">
                Search Results
              </h2>
              {isLoadingSearchResults ? (
                <div className="text-center py-6">
                  <p className="text-gray-600 dark:text-gray-300">Searching books...</p>
                </div>
              ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                  {searchResults.length > 0 ? (
                    searchResults.map((book, index) => (
                      <BookSearchResultItem
                        key={`${book.openLibraryKey || index}`}
                        book={book}
                        onImport={handleImportBook}
                        isImporting={isImportingBook && importingBookKey === book.openLibraryKey}
                      />
                    ))
                  ) : (
                    <p className="text-gray-600 dark:text-gray-400 col-span-full text-center">
                      No books found matching your search.
                    </p>
                  )}
                </div>
              )}
            </div>
          )}
        </main>
      </div>
    </div>
  );
};

export default BookZonePage; 
