import React, { useState, useEffect } from "react";
import { Modal } from "antd";
import { Spin } from "ui";
import { useLocation, useNavigate } from 'react-router-dom';
import Portal from './Portal'; // Импортируем портал

export interface ModalConfig {
    title?: string;
    data?: Record<string, any>;
    loading?: boolean;
    closeButton?: boolean;
    onClose?: (data?: any) => void;
    width?: string;
    height?: string;
    styleName?: string;
    url?: string;
}

export const ModalComponent: React.FC<{
    config: ModalConfig;
    close: (data?: any) => void;
    children: React.ReactElement;
}> = ({ config, close, children }) => {
    const [isOpen, setIsOpen] = useState(true);
    const [loading, setLoading] = useState(config.loading ?? false);
    const location = useLocation();
    const navigate = useNavigate();

    const handleClose = (data?: any) => {
        setIsOpen(false);
        setTimeout(() => close(data), 300);
        // Восстанавливаем предыдущий URL при закрытии
        if (config.url) {
            navigate(-1); // Используем navigate для возврата назад
        }
    };

    const modalConfig: {
        title: string | null;
        width?: string;
        style?: React.CSSProperties;
    } = {
        title: config.title || null,
    };

    if (config?.styleName === 'wide') {
        modalConfig.width = "90vw";
        modalConfig.style = {
            top: "2vh",
            height: "100vh",
            paddingBottom: "10px",
        };
    }

    // Обновляем состояние при изменении URL
    useEffect(() => {
        console.log('URL changed:', location.pathname);
    }, [location.pathname]);

    return (
        <Portal>
            <Modal
                {...modalConfig}
                open={isOpen}
                onCancel={() => handleClose()}
                footer={null}
            >
                <Spin spinning={loading}>
                    {React.cloneElement(children, {
                        modal: {
                            data: config.data,
                            loading,
                            setLoading,
                            close: handleClose,
                        },
                    })}
                </Spin>
            </Modal>
        </Portal>
    );
};
