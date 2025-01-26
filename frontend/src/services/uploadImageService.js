export async function uploadImage({ file, description, token }) {
  const formData = new FormData();
  formData.append('image', file);
  formData.append('description', description);

  const response = await fetch(
    `${process.env.REACT_APP_BACKEND_URL}/api/images/upload`,
    {
      method: 'POST',
      headers: {
        Authorization: token ? `Bearer ${token}` : '',
      },
      body: formData,
    }
  );
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.message || 'Failed to upload image');
  }
  return data;
}
