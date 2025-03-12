
import React from "react";
import { Outlet } from "react-router-dom";

export const LoginLayout: React.FC = () => {
    return (
        <div className="wrapper">
            <div className="center-wrap">
                <header>

                </header>
                <main>
                    <Outlet />
                </main>
            </div>
        </div>
    );
};
