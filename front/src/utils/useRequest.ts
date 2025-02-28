import { useLocation } from "react-router-dom";
import { useMemo } from "react";

export const useRequest = () => {
    const location = useLocation();

    return useMemo(() => {
        const queryParams = new URLSearchParams(location.search);

        return {
            query: (key: string): string | null => {
                return queryParams.has(key) ? queryParams.get(key) : null;
            },
            allQuery: (): Record<string, string | null> => {
                const params: Record<string, string | null> = {};
                queryParams.forEach((value, key) => {
                    params[key] = value;
                });
                return params;
            },
        };
    }, [location.search]); // Следим за изменением query-параметров
};
