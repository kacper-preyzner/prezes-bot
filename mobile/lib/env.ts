import Constants from 'expo-constants';

const extra = Constants.expoConfig?.extra ?? {};

export const API_URL: string = extra.API_URL;
export const APP_TOKEN: string = extra.APP_TOKEN;
