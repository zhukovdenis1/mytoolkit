import React from "react";
import { HomePage } from './pages/Home';
import { About } from './pages/About.tsx';
import { Contacts } from './pages/Contacts';
import {Layout} from "@/layouts/Layout.tsx";
import {RequireAuth} from "@/modules/auth/components/RequireAuth.tsx";
import {routes} from "@/router.tsx";
import {LoginLayout} from "@/layouts/LoginLayout.tsx";
import {LoginPage} from "@/modules/auth/pages/LoginPage.tsx";
import {RouteObject} from "react-router-dom";

export interface IRoute extends RouteObject {
    name?: string;
    title?: string;
    children?: IRoute[]; // Указываем, что children — это массив объектов IRoute
}

export const routesNew: IRoute[] = [
    {
        path: "/",
        element: (
            <RequireAuth>
                <Layout />
            </RequireAuth>
        ),
        handle: { title: "Главная" },
        children: [
            {
                name: "home",
                index: true,
                element: <HomePage />,
                handle: {index: true}
            },
            {
                name: "about",
                path: "about",
                element: <About />,
                handle: { title: "О нас" },
                children: [
                    {
                        name: "about.contacts",
                        path: "contacts",
                        element: <Contacts />,
                        handle: { title: "Контакты" },
                    },
                    {
                        name: "about.test",
                        path: "t/e/s/:id",
                        element: <p>Test...</p>,
                        handle: { title: "Test" }
                    },
                ],
            },
        ],
    },
    {
        path: "/login",
        element: <LoginLayout />,
        children: [
            {
                name: "login",
                index: true,
                element: <LoginPage />,
                handle: { title: "О Login" },
            },
        ],
    },
];
