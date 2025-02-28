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
    content?: React.ReactNode;
}

const ModalComponent: React.FC<{ config: ModalConfig; close: (data?: any) => void }> = ({ config, close }) => {
    const [isOpen, setIsOpen] = useState(true);
    const [loading, setLoading] = useState(config.loading ?? false);

    const handleClose = (data?: any) => {
        setIsOpen(false);
        setTimeout(() => close(data), 300);
    };

    const modalConfig: {
        title: string | null;
        width?: string;
        style?: React.CSSProperties;
    } = {
        title: config.title || null
    };

    if (config?.styleName === 'wide') {
        modalConfig.width = "90vw";
        modalConfig.style = {
            top: "2vh",
            height: "100vh",
            paddingBottom: "10px"
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
                {React.isValidElement(config.content) ? (
                    React.cloneElement(config.content as React.ReactElement, {
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

export const showModal = (element: React.ReactNode, config: ModalConfig = {
    width: '50vw',
    height: '50vh',
    styleName: ''
}) => {
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
                    ? React.cloneElement(element as React.ReactElement, {
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
