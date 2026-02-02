import * as WebBrowser from 'expo-web-browser';
import { API_URL, APP_TOKEN } from './env';

export async function connectSpotify(): Promise<void> {
  await WebBrowser.openBrowserAsync(`${API_URL}/spotify/authorize`);
}

export async function isSpotifyConnected(): Promise<boolean> {
  const response = await fetch(`${API_URL}/api/spotify/status`, {
    headers: { Authorization: APP_TOKEN },
  });

  if (!response.ok) return false;

  const data = await response.json();
  return data.connected;
}
