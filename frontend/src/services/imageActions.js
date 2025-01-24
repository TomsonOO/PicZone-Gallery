import { postImageAction } from './postImageAction';

export async function toggleLike(imageId, backendUrl) {
    const url = `${backendUrl}/api/images/like/${imageId}`;
    return postImageAction(url, localStorage.getItem('token'));
}

export async function toggleFavorite(imageId, backendUrl) {
    const url = `${backendUrl}/api/user/favorites/${imageId}`;
    return postImageAction(url, localStorage.getItem('token'));
}