export type Message = {
  id: number;
  role: 'user' | 'assistant' | 'timer' | 'spotify';
  content: string;
};

export type Action =
  | { type: 'set_timer'; seconds: number; message: string }
  | { type: 'spotify_playing'; track: string; artist: string };
