import React, {createContext, useContext, useEffect, useState} from "react";
import { useMatches } from "react-router-dom";

export interface Breadcrumb {
    title?: string;
    path: string;
}

interface BreadcrumbsContextProps {
    breadcrumbs: Breadcrumb[];
    addBreadcrumb: (breadcrumb: Breadcrumb) => void;
    removeLastBreadcrumb: () => void;
    clearBreadcrumbs: () => void;
    resetBreadCrumbs: () => void;
    isInitialized: boolean;
    toggleChanging: boolean;

}

const BreadcrumbsContext = createContext<BreadcrumbsContextProps | undefined>(
    undefined
);

export const useBreadcrumbsContext = (): BreadcrumbsContextProps => {
    const context = useContext(BreadcrumbsContext);
    if (!context) {
        throw new Error(
            "useBreadcrumbs must be used within a BreadcrumbsProvider"
        );
    }
    return context;
};

interface MatchHandle {
    title?: string;
}

interface Match {
    handle?: MatchHandle; // Тип handle
    pathname: string;
}

export const BreadcrumbsProvider: React.FC<{ children: React.ReactNode }> = ({
                                                                                 children,
                                                                             }) => {
    const matches = useMatches()  as Match[];
    const [breadcrumbs, setBreadcrumbs] = useState<Breadcrumb[]>([]);
    const [toggleChanging, setToggleChanging] = useState<boolean>(false);
    const [isInitialized, setIsInitialized] = useState(false);

    const defaultBreadcrumbs = matches
        .filter((match) => match.handle?.title)
        .map((match) => ({
            title: match.handle!.title,
            path: match.pathname,
        }));


    useEffect(() => {//вызывается каждый раз при переходе по link-ам
        setToggleChanging(!toggleChanging);
        setIsInitialized(true);
        resetBreadCrumbs();
    }, [matches]);

    const removeLastBreadcrumb = () => {
        setBreadcrumbs((prev) => {
            if (prev.length === 0) {
                return prev; // Если массив уже пустой, возвращаем его же
            }
            return prev.slice(0, -1); // Удаляем последний элемент
        });
    };

    const clearBreadcrumbs = () => {
        setBreadcrumbs([]);
    };

    const addBreadcrumb = (breadcrumb: Breadcrumb) => {
        setBreadcrumbs((prev) => [...prev, breadcrumb]);
    };

    const resetBreadCrumbs = () => {
        setBreadcrumbs(defaultBreadcrumbs);
    };


    return (
        <BreadcrumbsContext.Provider
            value={{
                breadcrumbs,
                addBreadcrumb,
                removeLastBreadcrumb,
                clearBreadcrumbs,
                resetBreadCrumbs,
                isInitialized,
                toggleChanging
            }}
        >
            {children}
        </BreadcrumbsContext.Provider>
    );
};


export const useChangeBreadCrumbs = () => {
    const { isInitialized, toggleChanging } = useBreadcrumbsContext();

    return (callback: () => void) => {
        useEffect(() => {
            if (isInitialized) {
                callback();
            }
        }, [toggleChanging]);
    };
};


export const addBreadCrumbs = (breadcrumbs: Breadcrumb[]) => {
    const {
        addBreadcrumb,
    } = useBreadcrumbsContext();

    useChangeBreadCrumbs()(() => {
        breadcrumbs.forEach((breadcrumb) => {
            addBreadcrumb(breadcrumb); // Добавляем каждый элемент из массива
        });
    });
}


