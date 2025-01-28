export async function createRandomUser() {
  try {
    const response = await fetch(
      `${process.env.REACT_APP_BACKEND_URL}/api/user/random`,
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
      }
    );

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new Error(errorData.message || 'Failed to create random user');
    }

    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      return response.json();
    }

    return { success: true };
  } catch (error) {
    console.error('Error in createRandomUser:', error);
    throw error;
  }
}
