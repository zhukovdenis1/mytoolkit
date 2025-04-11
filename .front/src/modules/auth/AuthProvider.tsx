import React, { createContext, useState, useEffect, ReactNode } from "react";
import { api, apiAxios } from "@/services/api"; // импортируем API для взаимодействия с сервером
import Cookies from "js-cookie"; // для работы с cookies
import axios from "axios"; // Импортируем axios, чтобы корректно проверить isAxiosError

// Интерфейс для пользователя
interface User {
    id: number;
    name: string;
    email: string;
}

// Тип для контекста аутентификации
interface AuthContextType {
    user: User | null; // Пользователь может быть либо объектом User, либо null
    loading: boolean; // Флаг загрузки
    signin: (credentials: { email: string; password: string }, cb: () => void) => Promise<void>; // Функция для входа
    signout: (cb: () => void) => Promise<void>; // Функция для выхода
}

// Создаем контекст аутентификации с типом AuthContextType
export const AuthContext = createContext<AuthContextType | null>(null);

// Тип для свойств компонента AuthProvider
interface AuthProviderProps {
    children: ReactNode; // Дети компонента, которые будут обернуты в этот провайдер
}

// AuthProvider — компонент провайдер для аутентификации
export const AuthProvider: React.FC<AuthProviderProps> = ({ children }) => {
    const [user, setUser] = useState<User | null>(null); // Стейт для пользователя
    const [loading, setLoading] = useState(true); // Стейт для отслеживания загрузки

    // Функция для получения данных о пользователе с сервера
    const fetchUser = async () => {
        try {
            const { data } = await api.request("me") as { data: User }; // Запрос на получение данных о пользователе
            setUser(data); // Сохраняем пользователя в стейт
        } catch (error) {
            console.error("Failed to fetch user:", error); // Логируем ошибку, если запрос не удался
        } finally {
            setLoading(false); // Завершаем загрузку, даже если произошла ошибка
        }
    };

    // Функция для обновления токенов
    /*
    const refreshAccessToken = async () => {

        const refreshToken = Cookies.get("refresh_token"); // Получаем refresh токен из cookies
        if (!refreshToken) {

            console.error("No refresh token available.");
            handleSignOut();
            return;
        }

        try {

            const response = await api.request("auth.refresh", { refresh_token: refreshToken }) as {
                data: { access_token: string; refresh_token: string };
                status: number;
            };


            if (response.status !== 200 || !response.data) {
                console.error("Token refresh failed. Logging out...");
                handleSignOut();
                return;
            } else {
                console.log('xxx')
            }

            const { access_token, refresh_token } = response.data;
            localStorage.setItem("access_token", access_token);
            Cookies.set("refresh_token", refresh_token);
            apiAxios.defaults.headers.common["Authorization"] = `Bearer ${access_token}`;
        } catch (error) {
            if (axios.isAxiosError(error) && error.response?.status === 401) {
                console.error("401 Unauthorized during token refresh. Logging out...");
            } else {
                console.error("Failed to refresh access token:", error);
            }
            handleSignOut();
        }
    };*/

    // Функция для выхода из системы
    const handleSignOut = () => {
        localStorage.removeItem("access_token");
        Cookies.remove("refresh_token");
        setUser(null);
    };

    // Функция для настройки перехватчиков запросов (для обработки 401 ошибок)
    /*
    const setupInterceptors = () => {
        let isRefreshing = false; // Флаг для проверки, обновляется ли токен
        let failedQueue: Array<(error?: Error) => void> = []; // Очередь неудачных запросов

        // Функция для обработки очереди запросов после обновления токена
        const processQueue = (error: Error | null) => {
            failedQueue.forEach((callback) => callback(error ?? undefined)); // Обрабатываем все запросы в очереди
            failedQueue = []; // Очищаем очередь
        };

        // Перехватчик для ответа
        apiAxios.interceptors.response.use(
            (response) => response,
            async (error) => {
                const originalRequest = error.config!;

                if (error.response?.status === 401 && !originalRequest._retry) {
                    if (isRefreshing) {
                        return new Promise((resolve, reject) => {
                            failedQueue.push((refreshError) => {
                                if (refreshError) {
                                    reject(refreshError);
                                } else {
                                    console.log('auth provider asdf');
                                    resolve(apiAxios.request(originalRequest));
                                }
                            });
                        });
                    }

                    originalRequest._retry = true;
                    isRefreshing = true;

                    try {
                        await refreshAccessToken();
                        processQueue(null);
                        return apiAxios.request(originalRequest);
                    } catch (refreshError) {
                        processQueue(refreshError as Error);
                        handleSignOut();
                        return Promise.reject(refreshError);
                    } finally {
                        isRefreshing = false;
                    }
                } else if (error.response?.data?.error) {
                    console.error("API Error:", error.response.data.error);
                    handleSignOut();
                }

                return Promise.reject(error);
            }
        );
    };
*/
    // Функция для входа пользователя
    const signin = async (credentials: { email: string; password: string }, cb: () => void) => {
        try {
            const { data } = await api.request("auth.login", credentials) as {
                data: { access_token: string; refresh_token: string };
            };

            localStorage.setItem("access_token", data.access_token);
            Cookies.set("refresh_token", data.refresh_token);
            apiAxios.defaults.headers.common["Authorization"] = `Bearer ${data.access_token}`;

            await fetchUser(); // Получаем данные пользователя
            cb(); // Вызываем callback после успешного входа
        } catch (error) {
            alert("Login failed"); // Выводим ошибку (alert принимает только 1 аргумент)
            console.error(error); // Логируем ошибку в консоль
        }
    };

    // Функция для выхода из системы
    const signout = async (cb: () => void) => {
        try {
            const refreshToken = Cookies.get("refresh_token");
            await api.request("auth.logout", { refresh_token: refreshToken });
        } catch (error) {
            console.error("Logout failed:", error);
        } finally {
            handleSignOut();
            cb();
        }
    };

    useEffect(() => {
        //setupInterceptors();

        const accessToken = localStorage.getItem("access_token");
        if (accessToken) {
            apiAxios.defaults.headers.common["Authorization"] = `Bearer ${accessToken}`;
            fetchUser();
        } else {
            setLoading(false);
        }
    }, []);

    return (
        <AuthContext.Provider value={{ user, loading, signin, signout }}>
            {children}
        </AuthContext.Provider>
    );
};
