const likeImage = async (imageId, backendUrl) => {
  const token = localStorage.getItem('token');

  if (!token) {
    throw new Error('User not authenticated');
  }

  try {
    const response = await fetch(`${backendUrl}/api/images/like/${imageId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
    });

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to like image');
    }

    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      return response.json();
    }

    return { success: true };
  } catch (error) {
    console.error('Error in likeImage:', error);
    throw error;
  }
};

export default likeImage;
