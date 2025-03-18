//Ссылка на скачивание

import { useState, useEffect } from "react";

const CLIENT_ID = "cfccbe2a17574a87ab4379df39c18007";
const REDIRECT_URI = "https://mytoolkit.loc:3000/demo/yandex";

const YandexUploader = () => {
    const [token, setToken] = useState<string | null>(null);
    const [file, setFile] = useState<File | null>(null);
    const [fileUrl, setFileUrl] = useState<string | null>(null);
    const [isPublic, setIsPublic] = useState<boolean>(true);

    // Получаем токен из URL после авторизации
    useEffect(() => {
        const hash = window.location.hash;
        if (hash.includes("access_token")) {
            const params = new URLSearchParams(hash.substring(1));
            const accessToken = params.get("access_token");
            if (accessToken) {
                localStorage.setItem("ya_token", accessToken);
                setToken(accessToken);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        } else {
            const savedToken = localStorage.getItem("ya_token");
            if (savedToken) setToken(savedToken);
        }
    }, []);

    // Функция авторизации
    const handleLogin = () => {
        window.location.href = `https://oauth.yandex.ru/authorize?response_type=token&client_id=${CLIENT_ID}&redirect_uri=${REDIRECT_URI}`;
    };

    // Обработчик выбора файла
    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            setFile(e.target.files[0]);
        }
    };

    // Функция загрузки файла
    const handleUpload = async () => {
        if (!file || !token) return alert("Выберите файл и войдите в Яндекс.Диск");

        try {
            // 1️⃣ Получаем URL для загрузки
            const uploadRes = await fetch(
                `https://cloud-api.yandex.net/v1/disk/resources/upload?path=app:/${file.name}`,
                {
                    method: "GET",
                    headers: { Authorization: `OAuth ${token}` },
                }
            );
            const uploadData = await uploadRes.json();
            if (!uploadData.href) throw new Error("Ошибка получения ссылки загрузки");

            // 2️⃣ Загружаем файл
            await fetch(uploadData.href, {
                method: "PUT",
                body: file,
                headers: { "Content-Type": file.type },
            });

            let finalUrl = "";

            if (isPublic) {
                // 3️⃣ Делаем файл публичным
                await fetch(`https://cloud-api.yandex.net/v1/disk/resources/publish?path=app:/${file.name}`, {
                    method: "PUT",
                    headers: { Authorization: `OAuth ${token}` },
                });

                // 4️⃣ Получаем публичную ссылку
                const publicRes = await fetch(
                    `https://cloud-api.yandex.net/v1/disk/resources?path=app:/${file.name}`,
                    {
                        method: "GET",
                        headers: { Authorization: `OAuth ${token}` },
                    }
                );
                const publicData = await publicRes.json();
                if (!publicData.public_url) throw new Error("Ошибка получения публичной ссылки");

                // 5️⃣ Получаем ПРЯМУЮ ссылку с подписью
                const directRes = await fetch(
                    `https://cloud-api.yandex.net/v1/disk/public/resources?public_key=${encodeURIComponent(publicData.public_url)}`,
                    {
                        method: "GET",
                        headers: { Authorization: `OAuth ${token}` },
                    }
                );
                const directData = await directRes.json();
                if (!directData.file) throw new Error("Ошибка получения прямой ссылки");

                // Прямая ссылка
                finalUrl = directData.file;
            } else {
                // Если файл приватный — получаем URL для скачивания
                const privateRes = await fetch(
                    `https://cloud-api.yandex.net/v1/disk/resources/download?path=app:/${file.name}`,
                    {
                        method: "GET",
                        headers: { Authorization: `OAuth ${token}` },
                    }
                );
                const privateData = await privateRes.json();
                if (!privateData.href) throw new Error("Ошибка получения приватной ссылки");

                finalUrl = privateData.href;
            }

            setFileUrl(finalUrl);
        } catch (error) {
            console.error(error);
            alert("Ошибка загрузки файла");
        }
    };


    // Функция выхода
    const handleLogout = () => {
        localStorage.removeItem("ya_token");
        setToken(null);
        setFileUrl(null);
    };

    return (
        <div>
            {!token ? (
                <button onClick={handleLogin}>Войти через Яндекс</button>
            ) : (
                <>
                    <button onClick={handleLogout}>Выйти</button>
                    <input type="file" onChange={handleFileChange} />
                    <label>
                        <input
                            type="checkbox"
                            checked={isPublic}
                            onChange={(e) => setIsPublic(e.target.checked)}
                        />
                        Сделать публичным
                    </label>
                    <button onClick={handleUpload}>Загрузить</button>

                    {fileUrl && (
                        <div>
                            <p>Ссылка на файл:</p>
                            <a href={fileUrl} target="_blank" rel="noopener noreferrer">
                                {fileUrl}
                            </a>
                            {isPublic && <img src={fileUrl} alt="Загруженный файл" style={{ maxWidth: "100%" }} />}
                        </div>
                    )}
                </>
            )}
        </div>
    );
};

export default YandexUploader;
