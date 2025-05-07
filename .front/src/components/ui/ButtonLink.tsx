import React from "react";
import { Link } from "react-router-dom";

interface ButtonLinkProps {
    to: string;       // URL для перехода
    type: string;       // URL для перехода
    children: React.ReactNode;  // Содержимое кнопки (текст или другие элементы)
    className?: string; // Дополнительные классы (необязательно)
}

export const ButtonLink: React.FC<ButtonLinkProps> = ({
                                                          to,
                                                          type,
                                                          children,
                                                          className = ""
                                                      }) => {

    let baseClasses = "ant-btn css-dev-only-do-not-override-1d4w9r2 ant-btn-default ant-btn-color-default ant-btn-variant-outlined";
    // Базовые классы Ant Design для кнопки
    if (type == 'primary') {
        baseClasses = "ant-btn css-dev-only-do-not-override-1d4w9r2 ant-btn-primary ant-btn-color-primary ant-btn-variant-solid";
    }


    return (
        <Link
            to={to}
            className={`${baseClasses} ${className}`}
        >
            {children}
        </Link>
    );
};
