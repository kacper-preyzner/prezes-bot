import * as Notifications from 'expo-notifications';
import * as Device from 'expo-device';
import Constants from 'expo-constants';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Platform } from 'react-native';
import { API_URL, APP_TOKEN } from './env';

const PUSH_TOKEN_KEY = 'push_token';

export function configureNotifications() {
  Notifications.setNotificationHandler({
    handleNotification: async () => ({
      shouldShowBanner: true,
      shouldShowList: true,
      shouldPlaySound: true,
      shouldSetBadge: false,
    }),
  });

  if (Platform.OS === 'android') {
    Notifications.setNotificationChannelAsync('default', {
      name: 'Default',
      importance: Notifications.AndroidImportance.HIGH,
      sound: 'notification.wav',
    });
  }
}

export async function registerForPushNotifications(): Promise<string | null> {
  if (!Device.isDevice) {
    console.warn('Push notifications require a physical device');
    return null;
  }

  const { status: existingStatus } = await Notifications.getPermissionsAsync();
  let finalStatus = existingStatus;

  if (existingStatus !== 'granted') {
    const { status } = await Notifications.requestPermissionsAsync();
    finalStatus = status;
  }

  if (finalStatus !== 'granted') {
    console.warn('Push notification permission not granted');
    return null;
  }

  const projectId = Constants.expoConfig?.extra?.eas?.projectId;
  const tokenData = await Notifications.getExpoPushTokenAsync({ projectId });
  return tokenData.data;
}

export async function syncPushTokenWithBackend(): Promise<void> {
  try {
    const token = await registerForPushNotifications();
    if (!token) {
      console.warn('No push token obtained');
      return;
    }

    const storedToken = await AsyncStorage.getItem(PUSH_TOKEN_KEY);
    if (storedToken === token) return;

    const response = await fetch(`${API_URL}/api/register-push-token`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Authorization: APP_TOKEN,
      },
      body: JSON.stringify({ token }),
    });

    if (response.ok) {
      await AsyncStorage.setItem(PUSH_TOKEN_KEY, token);
    }
  } catch (error) {
    console.error('Failed to sync push token:', error);
  }
}

export function setupNotificationListeners() {
  const foregroundSubscription = Notifications.addNotificationReceivedListener((notification) => {
    console.log('Notification received:', notification.request.content);
  });

  const responseSubscription = Notifications.addNotificationResponseReceivedListener((response) => {
    console.log('Notification tapped:', response.notification.request.content);
  });

  return () => {
    foregroundSubscription.remove();
    responseSubscription.remove();
  };
}
