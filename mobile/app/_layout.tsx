import { useEffect } from 'react';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useFonts, SpaceMono_400Regular, SpaceMono_700Bold } from '@expo-google-fonts/space-mono';
import {
  configureNotifications,
  syncPushTokenWithBackend,
  setupNotificationListeners,
} from '../lib/notifications';

configureNotifications();

export default function RootLayout() {
  const [fontsLoaded] = useFonts({
    SpaceMono_400Regular,
    SpaceMono_700Bold,
  });

  useEffect(() => {
    syncPushTokenWithBackend();
    const cleanup = setupNotificationListeners();
    return cleanup;
  }, []);

  if (!fontsLoaded) return null;

  return (
    <>
      <StatusBar style="light" />
      <Stack screenOptions={{ headerShown: false }} />
    </>
  );
}
