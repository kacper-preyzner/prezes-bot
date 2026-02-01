export type Message = {
  id: number;
  role: 'user' | 'assistant' | 'timer';
  content: string;
};

export type Action = {
  type: 'set_timer';
  seconds: number;
  message: string;
};
