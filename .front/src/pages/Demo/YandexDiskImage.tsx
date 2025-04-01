import { useState } from "react";
import axios from "axios";

// const CLIENT_ID = "cfccbe2a17574a87ab4379df39c18007";
// const REDIRECT_URI = "https://mytoolkit.loc:3000/demo/yandex";

const YandexDiscImage = () => {
    const [publicUrl, setPublicUrl] = useState<string>("");
    const [downloadUrl, setDownloadUrl] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);

    // Функция для получения публичного ключа из ссылки
    const extractPublicKey = (url: string): string | null => {
        const match = url.match(/https:\/\/yadi\.sk\/d\/([a-zA-Z0-9_-]+)/);
        return match ? match[1] : null;
    };

    // Функция для получения прямой ссылки на скачивание
    const getDownloadLink = async () => {
        setError(null);
        setDownloadUrl(null);

        try {
            const publicKey = extractPublicKey(publicUrl);
            if (!publicKey) {
                throw new Error("Некорректная ссылка на Яндекс.Диск");
            }

            // Получаем информацию о публичном ресурсе
            const publicRes = await axios.get(
                `https://cloud-api.yandex.net/v1/disk/public/resources?public_key=${publicKey}`,
                {
                    headers: {
                        Authorization: `OAuth ${localStorage.getItem("ya_token")}`,
                    },
                }
            );

            if (!publicRes.data.file) {
                throw new Error("Не удалось получить прямую ссылку на скачивание");
            }

            // Прямая ссылка на скачивание
            setDownloadUrl(publicRes.data.file);
        } catch (err) {
            setError("Ошибка при получении ссылки на скачивание");
            console.error(err);
        }
    };

    return (
        <div>
            <h1>Получить прямую ссылку на скачивание</h1>
            <input
                type="text"
                placeholder="Введите публичную ссылку на Яндекс.Диск"
                value={publicUrl}
                onChange={(e) => setPublicUrl(e.target.value)}
                style={{ width: "400px", marginRight: "10px" }}
            />
            <button onClick={getDownloadLink}>Получить ссылку</button>

            {error && <p style={{ color: "red" }}>{error}</p>}

            {downloadUrl && (
                <div>
                    <p>Прямая ссылка на скачивание:</p>
                    <a href={downloadUrl} target="_blank" rel="noopener noreferrer">
                        {downloadUrl}
                    </a>
                </div>
            )}
        </div>
    );
};

export default YandexDiscImage;
