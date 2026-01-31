import { API_URL, APP_TOKEN } from '@env';

export async function sendMessage(prompt: string): Promise<string> {
  const response = await fetch(`${API_URL}/api/ask`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      Authorization: APP_TOKEN,
    },
    body: JSON.stringify({ prompt }),
  });

  if (!response.ok) {
    throw new Error(`API error: ${response.status}`);
  }

  const data = await response.json();
  return data.message.trim();
}
