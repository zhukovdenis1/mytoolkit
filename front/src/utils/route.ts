import { IRoute, routes } from "@/routes";

function findRouteByName(name: string, routes: IRoute[], basePath: string = ""): string | undefined {
    for (const route of routes) {
        const fullPath = route.path ? `${basePath}/${route.path}`.replace(/\/\//g, "/") : basePath;
        if (route.name === name) return fullPath;

        if (route.children) {
            const childPath = findRouteByName(name, route.children, fullPath);
            if (childPath) return childPath;
        }
    }

//    console.log(routes)
    return undefined;
}

/*function substituteParams(path: string, params: Record<string, string | number>): string {
    return Object.keys(params).reduce((result, key) => {
        return result.replace(`:${key}`, String(params[key]));
    }, path);
}*/

function substituteParams(path: string, params: Record<string, string | number>): string {
    let url = path;
    const queryParams: Record<string, string> = {};

    Object.keys(params).forEach((key) => {
        const value = params[key];

        if (url.includes(`:${key}`)) {
            // Заменяем `:param` в пути
            url = url.replace(`:${key}`, String(value));
        } else {
            // Если `:param` нет в пути — добавляем в query string
            if (value !== null) {  // Убираем null
                queryParams[key] = String(value);  // Преобразуем value в строку
            }
        }
    });

    // Если остались параметры, добавляем их как `?key=value`
    const queryString = new URLSearchParams(queryParams).toString();  // Теперь queryParams содержит только строки
    return queryString ? `${url}?${queryString}` : url;
}

export const route = (name: string, params: Record<string, string | number> = {}): string => {
    const path = findRouteByName(name, routes);
    return path ? substituteParams(path, params) : '#';
};
