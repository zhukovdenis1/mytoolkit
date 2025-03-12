import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom';
import {ModalComponent, ModalConfig} from './ModalComponent';

export const showModal = (
    element: React.ReactElement,
    config: ModalConfig = {
        width: '50vw',
        height: '50vh',
        styleName: '',
    }
) => {
    const modalContainer = document.createElement('div');
    document.getElementById('root')?.appendChild(modalContainer);
    const root = createRoot(modalContainer);

    const close = (data?: any) => {
        setTimeout(() => {
            root.unmount();
            document.getElementById('root')?.removeChild(modalContainer);
            config.onClose?.(data);
        }, 0); // Задержка 0 для следующего цикла событий
    };

    // Меняем URL при открытии модального окна
    if (config.url) {
        window.history.pushState(null, "", config.url);
    }

    root.render(
        <BrowserRouter>
            <ModalComponent config={config} close={close}>
                {element}
            </ModalComponent>
        </BrowserRouter>
    );
};
