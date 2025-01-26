export async function getUserProfile(token) {
  const response = await fetch(
    `${process.env.REACT_APP_BACKEND_URL}/api/user/profile`,
    {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
    }
  );
  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || 'Failed to load profile information');
  }
  return data;
}

export async function updateUserProfile(token, formData) {
  const response = await fetch(
    `${process.env.REACT_APP_BACKEND_URL}/api/user/profile`,
    {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify(formData),
    }
  );
  const data = await response.json();
  if (!response.ok) {
    throw new Error(data.message || 'Failed to update settings');
  }
  return data;
}

export async function updateUserAvatar(token, file) {
  const formData = new FormData();
  formData.append('profile_image', file);

  const response = await fetch(
    `${process.env.REACT_APP_BACKEND_URL}/api/user/update/avatar`,
    {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${token}`,
      },
      body: formData,
    }
  );
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.message || 'Failed to update avatar');
  }
  return data;
}

export async function getProfileImage(profileImageId) {
  if (!profileImageId) {
    profileImageId = 1; // default profile avatar
  }
  const backendUrl = process.env.REACT_APP_BACKEND_URL;
  const res = await fetch(`${backendUrl}/api/images/profile/${profileImageId}`);
  if (!res.ok) {
    const errData = await res.json().catch(() => ({}));
    throw new Error(errData.message || 'Failed to fetch user profile image');
  }
  const profileImageData = await res.json();
  if (!profileImageData.objectKey) {
    return profileImageData;
  }
  const presignedUrlRes = await fetch(
    `${backendUrl}/api/images/presigned-url/${profileImageData.objectKey}`
  );
  if (!presignedUrlRes.ok) {
    const errData = await presignedUrlRes.json().catch(() => ({}));
    throw new Error(
      errData.message ||
        `Failed to fetch presigned URL for ${profileImageData.objectKey}`
    );
  }
  const presignedUrl = await presignedUrlRes.text();
  return { profileImageData, presignedUrl };
}
