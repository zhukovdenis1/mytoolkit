import React, { useState } from "react";
import { createRoot } from "react-dom/client";
import { Modal} from "antd";
import { Spin } from "ui";

interface ModalConfig {
    title?: string;
    data?: Record<string, any>;
    loading?: boolean;
    closeButton?: boolean;
    onClose?: (data?: any) => void;
    width: string;
    height: string;
    styleName: string;
}

const ModalComponent: React.FC<{ config: ModalConfig; close: (data?: any) => void }> = ({ config, close }) => {
    const [isOpen, setIsOpen] = useState(true);
    const [loading, setLoading] = useState(config.loading ?? false);

    const handleClose = (data?: any) => {
        setIsOpen(false);
        setTimeout(() => close(data), 300);
    };

    const modalConfig = {
        title: config.title || null
    };

    if(config?.styleName == 'wide') {
        modalConfig.width = "90vw";
        modalConfig.style = { top: "2vh", height: "100vh", paddingBottom: "10px", body: {
                //height: "90vh", // Контент занимает всю высоту окна
                overflowY: "auto", // Включаем внутренний скролл, если контент длинный
            } };
    }


    return (
        <Modal
            {...modalConfig}
            open={isOpen}
            onCancel={() => handleClose()}
            footer={null}

            //footer={config.closeButton !== false ? <Button onClick={() => handleClose()}>Закрыть</Button> : null}
        >
            <Spin spinning={loading} >
                {React.isValidElement(config.content) ? (
                    React.cloneElement(config.content, {
                        modal: {
                            data: config.data,
                            loading,
                            setLoading,
                            close: handleClose
                        }
                    })
                ) : (
                    config.content
                )}
            </Spin>
        </Modal>
    );
};

export const showModal = (element: React.ReactNode, config: ModalConfig = {}) => {
    const modalContainer = document.createElement("div");
    document.body.appendChild(modalContainer);
    const root = createRoot(modalContainer);

    const close = (data?: any) => {
        root.unmount();
        document.body.removeChild(modalContainer);
        config.onClose?.(data);
    };

    root.render(
        <ModalComponent
            config={{
                ...config,
                content: React.isValidElement(element)
                    ? React.cloneElement(element, {
                        modal: {
                            data: config.data,
                            onClose: close
                        }
                    })
                    : element
            }}
            close={close}
        />
    );
};
