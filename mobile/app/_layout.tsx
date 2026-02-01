import { useEffect } from 'react';
import { Stack } from 'expo-router';
import {
  configureNotifications,
  syncPushTokenWithBackend,
  setupNotificationListeners,
} from '../lib/notifications';

configureNotifications();

export default function RootLayout() {
  useEffect(() => {
    syncPushTokenWithBackend();
    const cleanup = setupNotificationListeners();
    return cleanup;
  }, []);

  return (
    <Stack>
      <Stack.Screen
        name="index"
        options={{
          title: 'Prezes Bot',
          headerStyle: { backgroundColor: '#1C1C1E' },
          headerTintColor: '#FFFFFF',
        }}
      />
    </Stack>
  );
}
