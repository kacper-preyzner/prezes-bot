import { File, Paths } from 'expo-file-system';
import { createAudioPlayer } from 'expo-audio';
import * as Speech from 'expo-speech';
import { API_URL, APP_TOKEN } from './env';

export async function speakText(text: string): Promise<void> {
  try {
    const response = await fetch(`${API_URL}/api/tts`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: APP_TOKEN,
      },
      body: JSON.stringify({ text }),
    });

    if (!response.ok) {
      throw new Error(`TTS API error: ${response.status}`);
    }

    const data = await response.json();
    const file = new File(Paths.cache, 'tts.mp3');
    if (file.exists) {
      file.delete();
    }
    file.create();
    file.write(data.audio, { encoding: 'base64' });

    const player = createAudioPlayer(file.uri);
    player.addListener('playbackStatusUpdate', (status) => {
      if (!status.playing && status.currentTime > 0) {
        player.remove();
      }
    });
    player.play();
  } catch (e) {
    console.warn('ElevenLabs TTS failed, falling back to expo-speech:', e);
    Speech.speak(text, { language: 'pl-PL', pitch: 1.1 });
  }
}
