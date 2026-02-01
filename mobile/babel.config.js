module.exports = function (api) {
  const env = process.env.APP_ENV || 'development';
  api.cache.using(() => env);
  return {
    presets: ['babel-preset-expo'],
    plugins: [
      ['module:react-native-dotenv', {
        moduleName: '@env',
        path: `.env.${env}`,
      }],
    ],
  };
};
