import { defineConfig } from 'vite';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';


// Эмуляция __dirname для ESM
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// https://vite.dev/config/
export default defineConfig({
    build: {
        outDir: '../public', // Можно поменять, например, на 'build',
        rollupOptions: {
            output: {
                entryFileNames: 'assets/index.js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name].[ext]', // Для CSS и других файлов
            },
        }
    },
    resolve: {
        alias: {
            "ui": path.resolve(__dirname, "src/components/ui/UI"),
            "@ui": path.resolve(__dirname, "./src/components/ui"),
            "api": path.resolve(__dirname, "src/utils/api"),
            '@': path.resolve(__dirname, './src'), // Теперь "@" ссылается на папку src
        },
    },
    server: {
        host: 'mytoolkit.loc', // Ваш домен
        port: 3000,              // Порт для запуска сервера
        https: {
            key: fs.readFileSync(path.resolve(__dirname, 'certs/localhost-key.pem')),
            cert: fs.readFileSync(path.resolve(__dirname, 'certs/localhost.pem')),
        },
    },
});
