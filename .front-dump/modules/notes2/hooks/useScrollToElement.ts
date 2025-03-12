import { useEffect } from "react";

export const useScrollToElement = (id: string | null) => {
    useEffect(() => {
        if (id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: "smooth", block: "center" });
            }
        }
    }, [id]);
};
