import React from "react";
import { HomePage } from './pages/Home';
import { About } from './pages/About.tsx';
import {Layout} from "@/layouts/Layout.tsx";
import {RequireAuth} from "@/modules/auth/components/RequireAuth.tsx";
import {routes} from "@/router.tsx";
import {LoginLayout} from "@/layouts/LoginLayout.tsx";
import {LoginPage} from "@/modules/auth/pages/LoginPage.tsx";

export interface IRoute {
    name?: string;
    path: string;
    component: React.ReactNode;
    title?: string;
    children?: IRoute[];
}

export const routesNew: IRoute[] = [
    {
        path: "/",
        component: <RequireAuth><Layout /></RequireAuth>,
        children: [
            { name: "home", path: '', component: <HomePage />, title: "Главная" },
            {
                name: "about",
                path: "about",
                component: <About />,
                title: "О нас",
                children: [
                    {
                        name: "about.contacts",
                        path: "contacts",
                        component: <p>Our contact...</p>,
                        title: "Контакты",
                    },
                    {
                        name: "about.test",
                        path: "t/e/s/:id",
                        component: <p>Test...</p>,
                        title: "Test",
                    },
                ],
            }
        ]
    },
    {
        path: "/login",
        component: <LoginLayout />,
        children: [
            { name: "login", path: '', component: <LoginPage />, title: "Login" },
        ]
    }
];
