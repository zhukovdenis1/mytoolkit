import React, { createContext, useContext, useEffect, useState } from "react";
import { useMatches } from "react-router-dom";
import { Link } from "react-router-dom";

export interface Breadcrumb {
    title?: string;
    path: string;
}

interface BreadcrumbsContextProps {
    breadcrumbs: Breadcrumb[];
    add: (title: string, path: string) => void;
    removeLast: () => void;
    clear: () => void;
    reset: () => void;
    isInitialized: boolean;
}

const BreadcrumbsContext = createContext<BreadcrumbsContextProps | undefined>(undefined);

export const useBreadCrumbs = (): BreadcrumbsContextProps => {
    const context = useContext(BreadcrumbsContext);
    if (!context) {
        throw new Error("useBreadCrumbs must be used within a BreadcrumbsProvider");
    }
    return context;
};

interface MatchHandle {
    title?: string;
}

interface Match {
    handle?: MatchHandle;
    pathname: string;
}

export const BreadcrumbsProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const matches = useMatches() as Match[];
    const [breadcrumbs, setBreadcrumbs] = useState<Breadcrumb[]>([]);
    const [isInitialized, setIsInitialized] = useState(false);

    const defaultBreadcrumbs = matches
        .filter((match) => match.handle?.title)
        .map((match) => ({
            title: match.handle!.title,
            path: match.pathname,
        }));

    useEffect(() => {
        setIsInitialized(true);
        reset();
    }, [matches]);

    const add = (title: string, path: string) => {
        setBreadcrumbs((prev) => [...prev, { title, path }]);
    };

    const removeLast = () => {
        setBreadcrumbs((prev) => (prev.length > 0 ? prev.slice(0, -1) : prev));
    };

    const clear = () => {
        setBreadcrumbs([]);
    };

    const reset = () => {
        setBreadcrumbs(defaultBreadcrumbs);
    };

    return (
        <BreadcrumbsContext.Provider value={{ breadcrumbs, add, removeLast, clear, reset, isInitialized }}>
            {children}
        </BreadcrumbsContext.Provider>
    );
};

export const Breadcrumbs: React.FC = () => {
    const { breadcrumbs } = useBreadCrumbs();

    return (
        <nav className="brcr">
            {breadcrumbs.map((crumb, index) => (
                <span key={`${crumb.path}-${index}`}>
                    <Link to={crumb.path}>{crumb.title}</Link>
                    {index < breadcrumbs.length - 1 && " > "}
                </span>
            ))}
        </nav>
    );
};
