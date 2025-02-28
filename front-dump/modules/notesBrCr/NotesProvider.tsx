import React, {createContext, ReactNode, useEffect, useState} from "react";
import api from "./services/noteApi";

interface NotesContextType {
    flag: string;
    func;
    categories: any[];
}

export const NotesContext = createContext<NotesContextType | null>(null);

interface NotesProviderProps {
    children: ReactNode; // Дети компонента, которые будут обернуты в этот провайдер
}
/*
function useAsyncStateOnce<T>(asyncFunction: () => Promise<T>, initialValue: T): T {
    const [data, setData] = useState<T>(initialValue);

    useEffect(() => {
        let isMounted = true;

        asyncFunction().then((result) => {
            if (isMounted) {
                setData(result); // Устанавливаем данные только один раз
            }
        });

        return () => {
            isMounted = false; // Очищаем флаг при размонтировании
        };
    }, [asyncFunction]);

    return data;
}*/

export const NotesProvider: React.FC<NotesProviderProps> = ({ children }) => {

    /*const categories = useAsyncStateOnce(
        async () => {
            const response = await api.fetchCategories();
            return response.data; // Возвращаем данные из API
        },
        [] // Начальное значение
    );*/

    const [categories, setCategories] = useState<any[]>([]);

    useEffect(() => {
        let onceFlag = true;

        api.fetchCategories().then((result) => {
            if (onceFlag) {
                setCategories(result.data.data);
            }
        });

        return () => {
            onceFlag = false;
        };
    }, []);

    const flag = 'asdfasdff';

    const func = () => {
        alert('hi');
    };

    console.log(categories);

    return (
        <NotesContext.Provider value={{ flag, func, categories }}>
            {children} {/* Отображаем дочерние компоненты */}
        </NotesContext.Provider>
    );
};
