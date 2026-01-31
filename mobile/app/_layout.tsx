import { Stack } from 'expo-router';

export default function RootLayout() {
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
