import { API_URL, APP_TOKEN } from './env';
import { Message } from '../types/chat';

export async function sendMessage(
  prompt: string,
): Promise<{ userMessage: Message; assistantMessage: Message }> {
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
  return {
    userMessage: data.user_message,
    assistantMessage: data.assistant_message,
  };
}

export async function fetchMessages(
  cursor?: number,
): Promise<{ data: Message[]; next_cursor: number | null }> {
  const url = new URL(`${API_URL}/api/messages`);
  if (cursor !== undefined) {
    url.searchParams.set('cursor', cursor.toString());
  }

  const response = await fetch(url.toString(), {
    headers: {
      Authorization: APP_TOKEN,
    },
  });

  if (!response.ok) {
    throw new Error(`API error: ${response.status}`);
  }

  return response.json();
}

export async function fetchNewMessages(
  afterId: number,
): Promise<{ data: Message[] }> {
  const url = new URL(`${API_URL}/api/messages`);
  url.searchParams.set('after', afterId.toString());

  const response = await fetch(url.toString(), {
    headers: {
      Authorization: APP_TOKEN,
    },
  });

  if (!response.ok) {
    throw new Error(`API error: ${response.status}`);
  }

  return response.json();
}
