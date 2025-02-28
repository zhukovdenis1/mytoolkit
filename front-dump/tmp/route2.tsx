import React from "react";
import { HomePage } from './pages/Home';
import { About } from './pages/About.tsx';
import { Contacts } from './pages/Contacts';
import {Layout} from "@/layouts/Layout.tsx";
import {RequireAuth} from "@/modules/auth/components/RequireAuth.tsx";
import {LoginLayout} from "@/layouts/LoginLayout.tsx";
import {LoginPage} from "@/modules/auth/pages/LoginPage.tsx";
import {RouteObject} from "react-router-dom";


export const routes: RouteObject[] = [
    {
        path: "/",
        element: (
            <RequireAuth>
                <Layout />
            </RequireAuth>
        ),
        handle: {title: "Главная"},
        children: [
            {
                index: true,
                element: <HomePage />,
                handle: {name: "home", index: true},
            },
            {

                path: "about",
                element: <About />,
                handle: {title: "О нас", name: "about"},
                children: [
                    {
                        path: "contacts",
                        element: <Contacts />,
                        handle: {name: "about.contacts", title: "Контакты"}
                    },
                    {
                        path: "t/e/s/:id",
                        element: <p>Test...</p>,
                        handle: {name: "about.test", title: "Test"}
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
                element: <LoginPage />,
                handle: {name: "login", title: "О Login", index: true,}
            },
        ],
    },
];
