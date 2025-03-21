import axios, { AxiosError, InternalAxiosRequestConfig } from "axios";
import Cookies from "js-cookie";
import routes from "@/services/apiRoutes";
import { message } from "ui";
import config from '@/config/config';

export const apiAxios = axios.create({
    baseURL: config.apiBaseUrl,
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});

// Расширяем тип InternalAxiosRequestConfig, чтобы добавить свойство _retry
interface CustomAxiosRequestConfig extends InternalAxiosRequestConfig {
    _retry?: boolean;
}

// Добавляем Bearer токен в каждый запрос
apiAxios.interceptors.request.use((config) => {
    const token = localStorage.getItem("access_token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Обрабатываем 401 и обновляем токен
apiAxios.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
        const originalRequest = error.config as CustomAxiosRequestConfig;
        if (error.response?.status === 401 && !originalRequest._retry) {
            originalRequest._retry = true;
            try {
                const refreshToken = Cookies.get("refresh_token");
                if (!refreshToken) throw new Error("No refresh token");

                const { data } = await apiAxios.post<{ access_token: string; refresh_token: string }>(
                    "/auth/refresh",
                    { refresh_token: refreshToken }
                );

                localStorage.setItem("access_token", data.access_token);
                Cookies.set("refresh_token", data.refresh_token);

                return apiAxios(originalRequest);
            } catch (refreshError) {
                console.error("Token refresh failed:", refreshError);
                throw refreshError;
            }
        }
        return Promise.reject(error);
    }
);

// Функция для получения пути и параметров
const resolveRoute = (routeKey: string, params: Record<string, string | number> = {}) => {
    const route = routes[routeKey];
    if (!route) {
        console.error(`Api route ${routeKey} was not found`);
        throw new Error(`Api route ${routeKey} was not found`);
    }

    let [path, method] = route;


    const queryParams: Record<string, string | number> = {};

    Object.keys(params).forEach((key) => {
        if (path.includes(`:${key}`)) {
            path = path.replace(`:${key}`, String(params[key]));
        } else {
            queryParams[key] = params[key];
        }
    });

    if (!path) {
        console.error(`route ${routeKey} was not found`);
        throw new Error(`Маршрут "${routeKey}" не найден в routes.ts`);
    }

    return { path, params: queryParams, method };
};

export const api = {
    request: (route: string, data = {}, formData = {}) => {
        const config: any = {};

        let { path, params, method } = resolveRoute(route, data);

        //if (data instanceof FormData) {
        if (Object.keys(formData).length != 0) {
            if (params) {
                //const queryParams = new URLSearchParams(params).toString();
                const queryParams = new URLSearchParams(
                    Object.entries(params).reduce((acc, [key, value]) => {
                        acc[key] = String(value); // Преобразуем все значения в строки
                        return acc;
                    }, {} as Record<string, string>)
                ).toString();
                path = `${path}?${queryParams}`;
            }
            const formDataObj = new FormData();
            Object.entries(formData).forEach(([key, value]) => {
                if (typeof value === 'string' || value instanceof Blob) {
                    formDataObj.append(key, value);
                } else {
                    // Если value не является строкой или Blob, преобразуем его в строку
                    formDataObj.append(key, String(value));
                }
            });

            switch (method) {
                case 'post':
                    return apiAxios.post(path, formDataObj, {headers: {'Content-Type': 'multipart/form-data'}});
                case 'put':

                    return apiAxios.put(path, formDataObj, {headers: {'Content-Type': 'multipart/form-data'}});
                default:
                    throw new Error(`FormData supported only for POST and PUT. Route: "${route}"`);
            }
        } else {// Обычная логика для JSON-объектов
            switch (method) {
                case 'get':
                    config.params = params;
                    return apiAxios.get(path, config);
                case 'post':
                    return apiAxios.post(path, params, config);
                case 'put':
                    return apiAxios.put(path, params, config);
                case 'delete':
                    config.params = params;
                    return apiAxios.delete(path, config);
                default:
                    throw new Error(`Error: unknown api method in route: "${route}"`);
            }
        }
    },

    safeRequest: async (route: string, data = {}, formData = {}) => {
        let result;
        try {
            //console.log(route, data, formData);
            const response = await api.request(route, data, formData);

            const success = (
                (!response.data.errors || response.data.errors.length === 0) &&
                !response.data.message
            );

            result =  {
                ...response,
                data: {
                    errors: false, warnings: false, success: success, data: {}, message: '',
                    ...response.data,

                }
            };
        } catch (err) {
            console.error("API Request Error:", err);
            //message.error("Error while requesting data");
            result = {data: {errors: true, warnings: false, success: false, data: {}, message: ''}};
        }

        return result;
    },

    safeRequestWithAlert: async (route: string, data = {}, formData = {}) => {
        const result =  await api.safeRequest(route, data, formData);

        if (Array.isArray(result.data.errors) && result.data.errors.length) {
            message.error(`${result.data.errors.map((error: string) => `${error}`).join('\r\n')}`);
        }

        if (Array.isArray(result.data.warnings) && result.data.warnings.length) {
            message.warning(`${result.data.warnings.map((warning: string) => `${warning}`).join('\r\n')}`);
        }

        return result;
    }
};
