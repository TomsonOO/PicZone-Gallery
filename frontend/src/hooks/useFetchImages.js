import { useState, useEffect } from 'react';
import { useUser } from '../context/UserContext';

const useFetchImages = ({
  category = '',
  sortBy = '',
  searchTerm = '',
  pageNumber = 1,
  pageSize = 20,
}) => {
  const backendUrl = process.env.REACT_APP_BACKEND_URL;
  const [images, setImages] = useState([]);
  const [pagination, setPagination] = useState();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const { isUserLoggedIn } = useUser();

  useEffect(() => {
    let isCanceled = false;
    const fetchImages = async () => {
      try {
        setLoading(true);
        setError(null);
        let url;
        if (isUserLoggedIn && category === 'favorites') {
          url = `${backendUrl}/api/images/favorites`;
        } else {
          const params = new URLSearchParams();
          if (category) params.append('category', category);
          if (sortBy) params.append('sortBy', sortBy);
          if (searchTerm) params.append('searchTerm', searchTerm);
          params.append('pageNumber', pageNumber);
          params.append('pageSize', pageSize);

          url = `${backendUrl}/api/images?${params.toString()}`;
        }

        const token = localStorage.getItem('token');
        const response = await fetch(url, {
          headers: {
            Authorization: token ? `Bearer ${token}` : '',
          },
        });
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const imageData = await response.json();

        if (!isCanceled) {
          if (imageData.images && Array.isArray(imageData.images)) {
            setImages(imageData.images);

            setPagination({
              currentPage: imageData.currentPage,
              pageSize: imageData.pageSize,
              totalCount: imageData.totalCount,
            });
          } else {
            setImages([]);
            setError('Invalid response format');
          }
        }
      } catch (error) {
        if (!isCanceled) {
          setError(error.message);
        }
      } finally {
        if (!isCanceled) {
          setLoading(false);
        }
      }
    };

    fetchImages();
    return () => {
      isCanceled = true;
    };
  }, [category, isUserLoggedIn]);

  return { images, loading, error, pagination };
};

export default useFetchImages;
