import React, { useState, useEffect } from "react";
import axios from "axios";

const YandexUpload: React.FC = () => {
    const CLIENT_ID = "cfccbe2a17574a87ab4379df39c18007";
    const REDIRECT_URI = "https://mytoolkit.loc:3000/demo/yandex";
    const [token, setToken] = useState<string | null>(null);
    const [file, setFile] = useState<File | null>(null);
    const [imageUrl, setImageUrl] = useState<string | null>(null);
    const [isPublic, setIsPublic] = useState<boolean>(true);

    useEffect(() => {
        const hash = new URLSearchParams(window.location.hash.substring(1));
        const accessToken = hash.get("access_token");
        if (accessToken) {
            setToken(accessToken);
            localStorage.setItem("yandex_token", accessToken);
        } else {
            const savedToken = localStorage.getItem("yandex_token");
            if (savedToken) setToken(savedToken);
        }
    }, []);

    const handleLogin = () => {
        window.location.href = `https://oauth.yandex.ru/authorize?response_type=token&client_id=${CLIENT_ID}&redirect_uri=${REDIRECT_URI}`;
    };

    const handleFileChange = (event: React.ChangeEvent<HTMLInputElement>) => {
        if (event.target.files?.length) {
            setFile(event.target.files[0]);
        }
    };

    const handleUpload = async () => {
        if (!file || !token) return;

        try {
            const filePath = `app:/MyToolKit/${file.name}`;

            // 1️⃣ Проверяем, существует ли файл и является ли он публичным
            let publicKey = "";
            try {
                const fileInfoResponse = await axios.get(
                    `https://cloud-api.yandex.net/v1/disk/resources?path=${encodeURIComponent(filePath)}`,
                    { headers: { Authorization: `OAuth ${token}` } }
                );

                // Если у файла уже есть `public_key`, используем его
                if (fileInfoResponse.data.public_key) {
                    publicKey = fileInfoResponse.data.public_key;
                }
            } catch (err) {
                console.log("Файл ещё не загружен, загружаем...");
            }

            // 2️⃣ Если файла нет → загружаем
            if (!publicKey) {
                const uploadResponse = await axios.get(
                    `https://cloud-api.yandex.net/v1/disk/resources/upload?path=${encodeURIComponent(filePath)}`,
                    { headers: { Authorization: `OAuth ${token}` } }
                );

                await axios.put(uploadResponse.data.href, file);
            }

            // 3️⃣ Если файл должен быть публичным → публикуем
            if (isPublic && !publicKey) {
                try {
                    await axios.put(
                        `https://cloud-api.yandex.net/v1/disk/resources/publish?path=${encodeURIComponent(filePath)}`,
                        {},
                        { headers: { Authorization: `OAuth ${token}` } }
                    );

                    // 4️⃣ Получаем `public_key` после публикации
                    const publicData = await axios.get(
                        `https://cloud-api.yandex.net/v1/disk/resources?path=${encodeURIComponent(filePath)}`,
                        { headers: { Authorization: `OAuth ${token}` } }
                    );
                    publicKey = publicData.data.public_key;
                } catch (publishError) {
                    console.error("Ошибка публикации файла", publishError);
                }
            }

            // 5️⃣ Если файл публичный, получаем постоянную ссылку
            if (publicKey) {
                const publicUrlResponse = await axios.get(
                    `https://cloud-api.yandex.net/v1/disk/resources/download?public_key=${publicKey}`
                );
                setImageUrl(publicUrlResponse.data.href);
            }
        } catch (error) {
            console.error("Ошибка загрузки", error);
        }
    };



    return (
        <div>
            {!token ? (
                <button onClick={handleLogin}>Войти через Яндекс</button>
            ) : (
                <>
                    <input type="file" onChange={handleFileChange} />
                    <label>
                        <input type="checkbox" checked={isPublic} onChange={() => setIsPublic(!isPublic)} />
                        Сделать публичным
                    </label>
                    <button onClick={handleUpload}>Загрузить</button>

                    {imageUrl && (
                        <div>
                            <p>Ссылка на изображение:</p>
                            <a href={imageUrl} target="_blank" rel="noopener noreferrer">
                                {imageUrl}
                            </a>
                            <br />
                            <img src={imageUrl} alt="Uploaded" style={{ maxWidth: "300px", marginTop: "10px" }} />
                        </div>
                    )}
                </>
            )}
        </div>
    );
};

export default YandexUpload;
