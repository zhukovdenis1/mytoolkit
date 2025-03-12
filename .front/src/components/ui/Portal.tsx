import React, { useEffect, useState } from 'react';
import { createPortal } from 'react-dom';

const Portal: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [container] = useState(() => document.createElement('div'));

    useEffect(() => {
        document.getElementById('root')?.appendChild(container);
        return () => {
            document.getElementById('root')?.removeChild(container);
        };
    }, [container]);

    return createPortal(children, container);
};

export default Portal;
