import AsyncStorage from '@react-native-async-storage/async-storage';

const KEY = 'autoReadMessages';

export async function getAutoRead(): Promise<boolean> {
  const value = await AsyncStorage.getItem(KEY);
  return value === 'true';
}

export async function setAutoRead(value: boolean): Promise<void> {
  await AsyncStorage.setItem(KEY, String(value));
}
