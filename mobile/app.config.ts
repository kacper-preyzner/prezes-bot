import { ExpoConfig, ConfigContext } from 'expo/config';
import * as dotenv from 'dotenv';
import path from 'path';

const env = process.env.APP_ENV || 'development';
const envPath = path.resolve(__dirname, `.env.${env}`);
const result = dotenv.config({ path: envPath, override: true });
console.log(`[app.config] APP_ENV=${env}, loaded ${envPath}, API_URL=${process.env.API_URL}`);

export default ({ config }: ConfigContext): ExpoConfig => ({
  ...config,
  name: 'Prezes Bot',
  slug: 'prezes-bot',
  scheme: 'prezes-bot',
  version: '1.0.0',
  orientation: 'portrait',
  icon: './assets/icon.png',
  userInterfaceStyle: 'automatic',
  newArchEnabled: true,
  splash: {
    image: './assets/splash-icon.png',
    resizeMode: 'contain',
    backgroundColor: '#ffffff',
  },
  ios: {
    supportsTablet: true,
    infoPlist: {
      NSSpeechRecognitionUsageDescription:
        'Allow $(PRODUCT_NAME) to use speech recognition.',
      NSMicrophoneUsageDescription:
        'Allow $(PRODUCT_NAME) to use the microphone.',
    },
    bundleIdentifier: 'com.prezes.prezesbot',
  },
  android: {
    adaptiveIcon: {
      foregroundImage: './assets/adaptive-icon.png',
      backgroundColor: '#ffffff',
    },
    edgeToEdgeEnabled: true,
    softwareKeyboardLayoutMode: 'pan',
    permissions: [
      'android.permission.RECORD_AUDIO',
      'android.permission.MODIFY_AUDIO_SETTINGS',
      'com.android.alarm.permission.SET_ALARM',
    ],
    package: 'com.prezes.prezesbot',
    googleServicesFile: './google-services.json',
  },
  web: {
    favicon: './assets/favicon.png',
  },
  plugins: [
    'expo-router',
    'expo-speech-recognition',
    'expo-audio',
    [
      'expo-notifications',
      {
        sounds: ['./assets/notification.wav'],
      },
    ],
  ],
  extra: {
    API_URL: process.env.API_URL,
    APP_TOKEN: process.env.APP_TOKEN,
    router: {},
    eas: {
      projectId: '934e79af-ae95-4a40-ae92-442fad29df67',
    },
  },
  owner: 'prezes',
});
