const config = {
    baseUrl: import.meta.env.VITE_BASE_URL || 'http://localhost:3000',
    apiBaseUrl: import.meta.env.VITE_API_BASE_URL || 'http://localhost:3000/api',
    environment: (import.meta.env.VITE_ENV as 'development' | 'production' | 'staging') || 'development',
    loginPath: 'login'
    // featureFlags: {
    //     enableNewDashboard: import.meta.env.VITE_ENABLE_NEW_DASHBOARD === 'true',
    //     enableBetaFeatures: import.meta.env.VITE_ENABLE_BETA_FEATURES === 'true',
    // },
    //appName: import.meta.env.VITE_APP_NAME || 'My React App',
};

export default config;
