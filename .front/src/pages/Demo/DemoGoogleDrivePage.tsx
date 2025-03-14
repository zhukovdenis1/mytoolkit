import { useState, useEffect } from 'react';
import { GoogleLogin } from '@react-oauth/google';
import { gapi } from 'gapi-script';

export const DemoGoogleDrivePage = () => {
    const [file, setFile] = useState<File | null>(null);
    const [fileLink, setFileLink] = useState('');
    const [accessToken, setAccessToken] = useState('');

    // Инициализация Google API
    useEffect(() => {
        const initClient = () => {
            gapi.client.init({
                apiKey: 'AIzaSyDMBagmdNWpC4EDaXKAvbpG7SLTbZI5HTU', // Ваш API-ключ
                clientId: '457997035558-qqkvhr64rkm53d6bui9ssb24lcffosqm.apps.googleusercontent.com', // Ваш Client ID
                discoveryDocs: ['https://www.googleapis.com/discovery/v1/apis/drive/v3/rest'],
                scope: 'https://www.googleapis.com/auth/drive.file',
            });
        };

        gapi.load('client:auth2', initClient);
    }, []);

    // Обработчик выбора файла
    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setFile(e.target.files[0]);
        }
    };

    // Обработчик загрузки файла
    const handleUpload = async () => {
        if (!file || !accessToken) {
            alert('Выберите файл и авторизуйтесь через Google.');
            return;
        }

        const metadata = {
            name: file.name,
            mimeType: file.type,
        };

        const formData = new FormData();
        formData.append('metadata', new Blob([JSON.stringify(metadata)], { type: 'application/json' }));
        formData.append('file', file);

        try {
            // Загружаем файл на Google Drive
            const response = await fetch('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart', {
                method: 'POST',
                headers: {
                    Authorization: `Bearer ${accessToken}`,
                },
                body: formData,
            });

            const result = await response.json();
            const fileId = result.id;

            // Устанавливаем публичный доступ к файлу
            await gapi.client.drive.permissions.create({
                fileId,
                resource: {
                    role: 'reader',
                    type: 'anyone',
                },
            });

            // Получаем ссылку на файл
            const fileLink = `https://drive.google.com/file/d/${fileId}/view`;
            setFileLink(fileLink);
        } catch (error) {
            console.error('Ошибка при загрузке файла:', error);
            alert('Ошибка при загрузке файла.');
        }
    };

    return (
        <div>
            <h1>Загрузка файла на Google Drive</h1>

            {/* Поле для выбора файла */}
            <input type="file" onChange={handleFileChange} />

            {/* Кнопка загрузки */}
            <button onClick={handleUpload} disabled={!file || !accessToken}>
                Загрузить на Google Drive
            </button>

            {/* Поле для ссылки на файл */}
            <input
                type="text"
                value={fileLink}
                placeholder="Ссылка на файл"
                readOnly
            />

            {/* Авторизация через Google */}
            <GoogleLogin
                onSuccess={(credentialResponse) => {
                    if (credentialResponse.credential) {
                        setAccessToken(credentialResponse.credential);
                    }
                }}
                onError={() => {
                    console.log('Ошибка авторизации');
                }}
            />
        </div>
    );
};
