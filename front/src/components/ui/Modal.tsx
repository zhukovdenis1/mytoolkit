import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Modal } from "antd";
import { Spin } from "ui";

interface ModalConfig {
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

// interface ModalProps {
//     modal: {
//         data?: Record<string, any>;
//         loading: boolean;
//         setLoading: (loading: boolean) => void;
//         close: (data?: any) => void;
//     };
// }

const ModalComponent: React.FC<{
    config: ModalConfig;
    close: (data?: any) => void;
    children: React.ReactElement;
}> = ({ config, close, children }) => {
    const [isOpen, setIsOpen] = useState(true);
    const [loading, setLoading] = useState(config.loading ?? false);

    const handleClose = (data?: any) => {
        setIsOpen(false);
        setTimeout(() => close(data), 300);
        // Восстанавливаем предыдущий URL при закрытии
        window.history.back();
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

    return (
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
    );
};

export const showModal = (
    element: React.ReactElement,
    config: ModalConfig = {
        width: '50vw',
        height: '50vh',
        styleName: '',
    }
) => {
    const modalContainer = document.createElement("div");
    document.body.appendChild(modalContainer);
    const root = createRoot(modalContainer);

    const close = (data?: any) => {
        root.unmount();
        document.body.removeChild(modalContainer);
        config.onClose?.(data);
    };

    // Меняем URL при открытии модального окна
    if (config.url) {
        window.history.pushState(null, "", config.url);
    }

    root.render(
        <ModalComponent config={config} close={close}>
            {element}
        </ModalComponent>
    );
};
