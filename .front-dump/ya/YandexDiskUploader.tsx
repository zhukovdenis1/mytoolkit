import { useState, useEffect } from "react";

const CLIENT_ID = "cfccbe2a17574a87ab4379df39c18007";

const YandexUpload = () => {
    const [token, setToken] = useState<string | null>(localStorage.getItem("yandex_token"));
    const [file, setFile] = useState<File | null>(null);
    const [fileUrl, setFileUrl] = useState<string>("");
    const [isPublic, setIsPublic] = useState<boolean>(false);

    useEffect(() => {
        const hash = window.location.hash;
        if (hash.includes("access_token")) {
            const params = new URLSearchParams(hash.replace("#", "?"));
            const accessToken = params.get("access_token");
            if (accessToken) {
                setToken(accessToken);
                localStorage.setItem("yandex_token", accessToken);
                window.history.replaceState(null, "", window.location.pathname);
            }
        }
    }, []);

    const authenticate = () => {
        const redirectUri = encodeURIComponent(window.location.href);
        const authUrl = `https://oauth.yandex.ru/authorize?response_type=token&client_id=${CLIENT_ID}&redirect_uri=${redirectUri}&scope=cloud_api:disk.app_folder`;
        window.location.href = authUrl;
    };

    const logout = () => {
        setToken(null);
        localStorage.removeItem("yandex_token");
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files.length > 0) {
            setFile(e.target.files[0]);
        }
    };

    const uploadFile = async () => {
        if (!file || !token) {
            alert("Выберите файл и авторизуйтесь!");
            return;
        }

        try {
            const filePath = `app:/${encodeURIComponent(file.name)}`;

            const uploadUrlResponse = await fetch(
                `https://cloud-api.yandex.net/v1/disk/resources/upload?path=${filePath}`,
                {
                    method: "GET",
                    headers: { Authorization: `OAuth ${token}` },
                }
            );

            const uploadUrlData = await uploadUrlResponse.json();
            if (!uploadUrlData.href) throw new Error("Не удалось получить ссылку для загрузки");

            await fetch(uploadUrlData.href, {
                method: "PUT",
                body: file,
            });

            if (isPublic) {
                await fetch(
                    `https://cloud-api.yandex.net/v1/disk/resources/publish?path=${filePath}`,
                    {
                        method: "PUT",
                        headers: { Authorization: `OAuth ${token}` },
                    }
                );

                const fileInfoResponse = await fetch(
                    `https://cloud-api.yandex.net/v1/disk/resources?path=${filePath}&fields=public_url`,
                    {
                        headers: { Authorization: `OAuth ${token}` },
                    }
                );

                const fileInfo = await fileInfoResponse.json();
                if (fileInfo.public_url) {
                    const directUrl = fileInfo.public_url.replace("disk.yandex.ru", "downloader.disk.yandex.ru/disk");
                    setFileUrl(directUrl);
                    return;
                }
            }

            setFileUrl(`https://disk.yandex.ru/client/disk/app/${encodeURIComponent(file.name)}`);
        } catch (error) {
            alert("Ошибка загрузки файла");
        }
    };

    return (
        <div className="p-4 border rounded-md w-96 mx-auto text-center">
            {!token ? (
                <button onClick={authenticate} className="p-2 bg-blue-500 text-white rounded-md">Войти через Яндекс</button>
            ) : (
                <>
                    <button onClick={logout} className="p-2 bg-red-500 text-white rounded-md mb-2">Выйти</button>
                    <input type="file" onChange={handleFileChange} className="block mb-2" />
                    <label className="flex items-center justify-center gap-2">
                        <input type="checkbox" checked={isPublic} onChange={() => setIsPublic(!isPublic)} /> Сделать публичным
                    </label>
                    <button onClick={uploadFile} className="p-2 bg-green-500 text-white rounded-md mt-2">Загрузить</button>
                    {fileUrl && (
                        <div className="mt-2">
                            <input type="text" readOnly value={fileUrl} className="block w-full border p-1 mb-2" />
                            <img src={fileUrl} alt="Uploaded File" className="max-w-full h-auto border" />
                        </div>
                    )}
                </>
            )}
        </div>
    );
};

export default YandexUpload;
